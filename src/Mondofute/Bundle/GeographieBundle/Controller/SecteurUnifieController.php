<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurUnifie;
use Mondofute\Bundle\GeographieBundle\Form\SecteurUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * SecteurUnifie controller.
 *
 */
class SecteurUnifieController extends Controller
{
    /**
     * Lists all SecteurUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $secteurUnifies = $em->getRepository('MondofuteGeographieBundle:SecteurUnifie')->findAll();

        return $this->render('@MondofuteGeographie/secteurunifie/index.html.twig', array(
            'secteurUnifies' => $secteurUnifies,
        ));
    }

    /**
     * Creates a new SecteurUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $secteurUnifie = new SecteurUnifie();

        $this->ajouterSecteursDansForm($secteurUnifie);
        $this->secteursSortByAffichage($secteurUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\SecteurUnifieType', $secteurUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->supprimerSecteurs($secteurUnifie, $sitesAEnregistrer);

//            $em = $this->getDoctrine()->getManager();
            $em->persist($secteurUnifie);
            $em->flush();

            $this->copieVersSites($secteurUnifie);
            $this->addFlash('success', 'le secteur a bien été créé');
            return $this->redirectToRoute('geographie_secteur_edit', array('id' => $secteurUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/secteurunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $secteurUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les secteurs qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param SecteurUnifie $entity
     */
    private function ajouterSecteursDansForm(SecteurUnifie $entity)
    {
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getSecteurs() as $secteur) {
                if ($secteur->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter au secteur
                        if ($secteur->getTraductions()->filter(function (SecteurTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new SecteurTraduction();
                            $traduction->setLangue($langue);
                            $secteur->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $secteur = new Secteur();
                $secteur->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new SecteurTraduction();
                    $traduction->setLangue($langue);
                    $secteur->addTraduction($traduction);
                }
                $entity->addSecteur($secteur);
            }
        }
    }

    /**
     * Classe les secteurs par classementAffichage
     * @param SecteurUnifie $entity
     */
    private function secteursSortByAffichage(SecteurUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les secteurs en fonction de leurs ordre d'affichage
        $secteurs = $entity->getSecteurs(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $secteurs->getIterator();
        unset($secteurs);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Secteur $a, Secteur $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $secteurs = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($secteurs);

        // remplacé les secteurs par ce nouveau tableau (une fonction 'set' a été créé dans Secteur unifié)
        $entity->setSecteurs($secteurs);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param ArrayCollection $secteurs
     */
    private function traductionsSortByLangue(ArrayCollection $secteurs)
    {
        /** @var Secteur $secteur */
        /** @var ArrayIterator $iterator */
        foreach ($secteurs as $secteur) {
            $traductions = $secteur->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (SecteurTraduction $a, SecteurTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $secteur->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les secteurs qui ne doivent pas être enregistrer
     * @param SecteurUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerSecteurs(SecteurUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getSecteurs() as $secteur) {
            if (!in_array($secteur->getSite()->getId(), $sitesAEnregistrer)) {
                $secteur->setSecteurUnifie(null);
                $entity->removeSecteur($secteur);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité secteur
     * @param SecteurUnifie $entity
     */
    public function copieVersSites(SecteurUnifie $entity)
    {
        /** @var SecteurTraduction $secteurTraduc */
//        Boucle sur les secteurs afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getSecteurs() as $secteur) {
            if ($secteur->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($secteur->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $secteur->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->find(SecteurUnifie::class, $entity->getId())))) {
                    $entitySite = new SecteurUnifie();
                }

//            Récupération du secteur sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($secteurSite = $em->getRepository(Secteur::class)->findOneBy(array('secteurUnifie' => $entitySite))))) {
                    $secteurSite = new Secteur();
                }
//            copie des données secteur
                $secteurSite
                    ->setSite($site)
                    ->setSecteurUnifie($entitySite);

//            Gestion des traductions
                foreach ($secteur->getTraductions() as $secteurTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $secteurTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($secteurTraducSite = $em->getRepository(SecteurTraduction::class)->findOneBy(array(
                        'secteur' => $secteurSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $secteurTraducSite = new SecteurTraduction();
                    }

//                copie des données traductions
                    $secteurTraducSite->setLangue($langue)
                        ->setLibelle($secteurTraduc->getLibelle())
                        ->setDescription($secteurTraduc->getDescription())
                        ->setSecteur($secteurSite);

//                ajout a la collection de traduction de la station distante
                    $secteurSite->addTraduction($secteurTraducSite);
                }

                $entitySite->addSecteur($secteurSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterSecteurUnifieSiteDistant($entity->getId(), $entity->getSecteurs());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de secteur a enregistrer
     * @param $idUnifie
     * @param Collection $secteurs
     */
    public function ajouterSecteurUnifieSiteDistant($idUnifie, Collection $secteurs)
    {
        /** @var Site $site */
        /** @var ArrayCollection $secteurs */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($secteurs->matching($criteres)) == 0 && (empty($emSite->getRepository(SecteurUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new SecteurUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a SecteurUnifie entity.
     *
     */
    public function showAction(SecteurUnifie $secteurUnifie)
    {
        $deleteForm = $this->createDeleteForm($secteurUnifie);

        return $this->render('@MondofuteGeographie/secteurunifie/show.html.twig', array(
            'secteurUnifie' => $secteurUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a SecteurUnifie entity.
     *
     * @param SecteurUnifie $secteurUnifie The SecteurUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SecteurUnifie $secteurUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_secteur_delete', array('id' => $secteurUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing SecteurUnifie entity.
     *
     */
    public function editAction(Request $request, SecteurUnifie $secteurUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($secteurUnifie->getSecteurs() as $secteur) {
                array_push($sitesAEnregistrer, $secteur->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

//        $secteurCrm = $this->dissocierSecteurCrm($secteurUnifie);
        $originalSecteurs = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($secteurUnifie->getSecteurs() as $secteur) {
            $originalSecteurs->add($secteur);
        }

        $this->ajouterSecteursDansForm($secteurUnifie);
//        $this->dispacherDonneesCommune($secteurUnifie);
        $this->secteursSortByAffichage($secteurUnifie);
        $deleteForm = $this->createDeleteForm($secteurUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\SecteurUnifieType', $secteurUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerSecteurs($secteurUnifie, $sitesAEnregistrer);
//            $this->mettreAJourSecteurCrm($secteurUnifie, $secteurCrm);
//            $em->persist($secteurCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalSecteurs as $secteur) {
                if (!$secteurUnifie->getSecteurs()->contains($secteur)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($secteur->getSite()->getLibelle());
                    $entitySite = $emSite->find(SecteurUnifie::class, $secteurUnifie->getId());
                    $secteurSite = $entitySite->getSecteurs()->first();
                    $emSite->remove($secteurSite);
                    $emSite->flush();
                    $secteur->setSecteurUnifie(null);
                    $em->remove($secteur);
                }
            }
            $em->persist($secteurUnifie);
            $em->flush();


            $this->copieVersSites($secteurUnifie);
            $this->addFlash('success', 'le secteur a bien été modifié');
//            dump($secteurUnifie);
//            dump($secteurCrm);
//            die;
            return $this->redirectToRoute('geographie_secteur_edit', array('id' => $secteurUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/secteurunifie/edit.html.twig', array(
            'entity' => $secteurUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SecteurUnifie entity.
     *
     */
    public function deleteAction(Request $request, SecteurUnifie $secteurUnifie)
    {
        $form = $this->createDeleteForm($secteurUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $secteurUnifieSite = $emSite->find(SecteurUnifie::class, $secteurUnifie->getId());
                if (!empty($secteurUnifieSite)) {
                    $emSite->remove($secteurUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($secteurUnifie);
            $em->flush();
        }
        $this->addFlash('success', 'le secteur a bien été supprimé');
        return $this->redirectToRoute('geographie_secteur_index');
    }



}
