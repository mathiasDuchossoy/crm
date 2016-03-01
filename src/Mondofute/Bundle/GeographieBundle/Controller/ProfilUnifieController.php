<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie;
use Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * ProfilUnifie controller.
 *
 */
class ProfilUnifieController extends Controller
{
    /**
     * Lists all ProfilUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $profilUnifies = $em->getRepository('MondofuteGeographieBundle:ProfilUnifie')->findAll();
        return $this->render('@MondofuteGeographie/profilunifie/index.html.twig', array(
            'profilUnifies' => $profilUnifies,
        ));
    }

    /**
     * Creates a new ProfilUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $profilUnifie = new ProfilUnifie();

        $this->ajouterProfilsDansForm($profilUnifie);
        $this->profilsSortByAffichage($profilUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType', $profilUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->supprimerProfils($profilUnifie, $sitesAEnregistrer);

            $em->persist($profilUnifie);
            $em->flush();

            $this->copieVersSites($profilUnifie);
            $this->addFlash('success', 'le profil a bien été créé');
            return $this->redirectToRoute('geographie_profil_edit', array('id' => $profilUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/profilunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $profilUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param ProfilUnifie $entity
     */
    private function ajouterProfilsDansForm(ProfilUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getProfils() as $profil) {
                if ($profil->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {
//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($profil->getTraductions()->filter(function (ProfilTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new ProfilTraduction();
                            $traduction->setLangue($langue);
                            $profil->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $profil = new Profil();
                $profil->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new ProfilTraduction();
                    $traduction->setLangue($langue);
                    $profil->addTraduction($traduction);
                }
                $entity->addProfil($profil);
            }
        }
    }

    /**
     * Classe les profils par classementAffichage
     * @param ProfilUnifie $entity
     */
    private function profilsSortByAffichage(ProfilUnifie $entity)
    {
        /** @var ArrayCollection $profils */
        // Trier les stations en fonction de leurs ordre d'affichage
        $profils = $entity->getProfils(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $profils->getIterator();
        unset($profils);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Profil $a, Profil $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $profils = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($profils);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setProfils($profils);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $profils
     */
    private function traductionsSortByLangue(ArrayCollection $profils)
    {
        /** @var Profil $profil */
        /** @var ArrayIterator $iterator */
        foreach ($profils as $profil) {
            $traductions = $profil->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (ProfilTraduction $a, ProfilTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $profil->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les profils qui ne doivent pas être enregistrer
     * @param ProfilUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerProfils(ProfilUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getProfils() as $profil) {
            if (!in_array($profil->getSite()->getId(), $sitesAEnregistrer)) {
                $profil->setProfilUnifie(null);
                $entity->removeProfil($profil);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param ProfilUnifie $entity
     */
    public function copieVersSites(ProfilUnifie $entity)
    {
        /** @var ProfilTraduction $profilTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getProfils() as $profil) {
            if ($profil->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($profil->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $profil->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->find(ProfilUnifie::class, $entity->getId())))) {
                    $entitySite = new ProfilUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($profilSite = $em->getRepository(Profil::class)->findOneBy(array('profilUnifie' => $entitySite))))) {
                    $profilSite = new Profil();
                }

//            copie des données station
                $profilSite
                    ->setSite($site)
                    ->setProfilUnifie($entitySite);

//            Gestion des traductions
                foreach ($profil->getTraductions() as $profilTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $profilTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($profilTraducSite = $em->getRepository(ProfilTraduction::class)->findOneBy(array(
                        'profil' => $profilSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $profilTraducSite = new ProfilTraduction();
                    }

//                copie des données traductions
                    $profilTraducSite->setLangue($langue)
                        ->setLibelle($profilTraduc->getLibelle())
                        ->setDescription($profilTraduc->getDescription())
                        ->setAccueil($profilTraduc->getAccueil())
                        ->setProfil($profilSite);

//                ajout a la collection de traduction de la station distante
                    $profilSite->addTraduction($profilTraducSite);
                }

                $entitySite->addProfil($profilSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterProfilUnifieSiteDistant($entity->getId(), $entity->getProfils());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param Collection $profils
     */
    public function ajouterProfilUnifieSiteDistant($idUnifie, Collection $profils)
    {
        /** @var Site $site */
        /** @var ArrayCollection $profils */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($profils->matching($criteres)) == 0 && (empty($emSite->getRepository(ProfilUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new ProfilUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a ProfilUnifie entity.
     *
     */
    public function showAction(ProfilUnifie $profilUnifie)
    {
        $deleteForm = $this->createDeleteForm($profilUnifie);

        return $this->render('@MondofuteGeographie/profilunifie/show.html.twig', array(
            'profilUnifie' => $profilUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ProfilUnifie entity.
     *
     * @param ProfilUnifie $profilUnifie The ProfilUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProfilUnifie $profilUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_profil_delete', array('id' => $profilUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ProfilUnifie entity.
     *
     */
    public function editAction(Request $request, ProfilUnifie $profilUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            foreach ($profilUnifie->getProfils() as $profil) {
                array_push($sitesAEnregistrer, $profil->getSite()->getId());
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalProfils = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($profilUnifie->getProfils() as $profil) {
            $originalProfils->add($profil);
        }

        $this->ajouterProfilsDansForm($profilUnifie);
//        $this->dispacherDonneesCommune($profilUnifie);
        $this->profilsSortByAffichage($profilUnifie);
        $deleteForm = $this->createDeleteForm($profilUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType', $profilUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerProfils($profilUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalProfils as $profil) {
                if (!$profilUnifie->getProfils()->contains($profil)) {
                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($profil->getSite()->getLibelle());
                    $entitySite = $emSite->find(ProfilUnifie::class, $profilUnifie->getId());
                    $profilSite = $entitySite->getProfils()->first();
                    $emSite->remove($profilSite);
                    $emSite->flush();
                    $profil->setProfilUnifie(null);
                    $em->remove($profil);
                }
            }
            $em->persist($profilUnifie);
            $em->flush();

            $this->copieVersSites($profilUnifie);
            $this->addFlash('success', 'le profil a bien été modifié');

            return $this->redirectToRoute('geographie_profil_edit', array('id' => $profilUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/profilunifie/edit.html.twig', array(
            'entity' => $profilUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProfilUnifie entity.
     *
     */
    public function deleteAction(Request $request, ProfilUnifie $profilUnifie)
    {
//        dump($profilUnifie);die;
        $form = $this->createDeleteForm($profilUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $profilUnifieSite = $emSite->find(ProfilUnifie::class, $profilUnifie->getId());
                if (!empty($profilUnifieSite)) {
                    $emSite->remove($profilUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($profilUnifie);
            $em->flush();
        }
        $this->addFlash('success', 'le profil a bien été supprimé');
        return $this->redirectToRoute('geographie_profil_index');
    }
}
