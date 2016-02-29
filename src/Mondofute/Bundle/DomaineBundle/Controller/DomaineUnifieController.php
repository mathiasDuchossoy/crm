<?php

namespace Mondofute\Bundle\DomaineBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineUnifie;
use Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DomaineUnifie controller.
 *
 */
class DomaineUnifieController extends Controller
{
    /**
     * Lists all DomaineUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $domaineUnifies = $em->getRepository(DomaineUnifie::class)->findAll();
        return $this->render('@MondofuteDomaine/domaineunifie/index.html.twig', array(
            'domaineUnifies' => $domaineUnifies,
        ));
    }

    /**
     * Creates a new DomaineUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $domaineUnifie = new DomaineUnifie();

        $this->ajouterDomainesDansForm($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);

        $form = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType', $domaineUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->supprimerDomaines($domaineUnifie, $sitesAEnregistrer);

            $em->persist($domaineUnifie);
            $em->flush();

            $this->copieVersSites($domaineUnifie);

            // add flash messages
            $this->addFlash(
                'success',
                'Le domaine a bien été créé.'
            );

            return $this->redirectToRoute('domaine_domaine_edit', array('id' => $domaineUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domaineunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $domaineUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DomaineUnifie $entity
     */
    private function ajouterDomainesDansForm(DomaineUnifie $entity)
    {
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDomaines() as $domaine) {
                if ($domaine->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaine->getTraductions()->filter(function (DomaineTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DomaineTraduction();
                            $traduction->setLangue($langue);
                            $domaine->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $domaine = new Domaine();
                $domaine->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DomaineTraduction();
                    $traduction->setLangue($langue);
                    $domaine->addTraduction($traduction);
                }
                $entity->addDomaine($domaine);
            }
        }
    }

    /**
     * Classe les domaines par classementAffichage
     * @param DomaineUnifie $entity
     */
    private function domainesSortByAffichage(DomaineUnifie $entity)
    {

        // Trier les stations en fonction de leurs ordre d'affichage
        $domaines = $entity->getDomaines(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        /** @var ArrayIterator $iterator */
        $iterator = $domaines->getIterator();
        unset($domaines);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Domaine $a, Domaine $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $domaines = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($domaines);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setDomaines($domaines);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $domaines
     */
    private function traductionsSortByLangue($domaines)
    {
        /** @var ArrayIterator $iterator */
        /** @var Domaine $domaine */
        foreach ($domaines as $domaine) {
            $traductions = $domaine->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (DomaineTraduction $a, DomaineTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $domaine->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les domaines qui ne doivent pas être enregistrer
     * @param DomaineUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDomaines(DomaineUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getDomaines() as $domaine) {
            if (!in_array($domaine->getSite()->getId(), $sitesAEnregistrer)) {
                $domaine->setDomaineUnifie(null);
                $entity->removeDomaine($domaine);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param DomaineUnifie $entity
     */
    public function copieVersSites(DomaineUnifie $entity)
    {
        /** @var DomaineTraduction $domaineTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDomaines() as $domaine) {
            if ($domaine->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($domaine->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $domaine->getSite()->getId()));
                if (!empty($domaine->getDomaineParent())) {
                    $domaineParent = $em->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $domaine->getDomaineParent()->getDomaineUnifie()));
                } else {
                    $domaineParent = null;
                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
//                $em->getRepository(DomaineUnifie::class)->find(array($entity->getId()
//                    $em->find( DomaineUnifie::class, $entity->getId());
                if (is_null(($entitySite = $em->find(DomaineUnifie::class, $entity->getId())))) {
                    $entitySite = new DomaineUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineSite = $em->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $entitySite))))) {
                    $domaineSite = new Domaine();
                }

//            copie des données station
                $domaineSite
                    ->setSite($site)
                    ->setDomaineUnifie($entitySite)
                    ->setDomaineParent($domaineParent);

//            Gestion des traductions
                foreach ($domaine->getTraductions() as $domaineTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $domaineTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($domaineTraducSite = $em->getRepository(DomaineTraduction::class)->findOneBy(array(
                        'domaine' => $domaineSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $domaineTraducSite = new DomaineTraduction();
                    }

//                copie des données traductions
                    $domaineTraducSite->setLangue($langue)
                        ->setLibelle($domaineTraduc->getLibelle())
                        ->setDomaine($domaineSite);

//                ajout a la collection de traduction de la station distante
                    $domaineSite->addTraduction($domaineTraducSite);
                }

                $entitySite->addDomaine($domaineSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterDomaineUnifieSiteDistant($entity->getId(), $entity->getDomaines());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param Collection $domaines
     */
    public function ajouterDomaineUnifieSiteDistant($idUnifie, Collection $domaines)
    {
        /** @var Site $site */
        /** @var ArrayCollection $domaines */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($domaines->matching($criteres)) == 0 && (empty($emSite->getRepository(DomaineUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DomaineUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a DomaineUnifie entity.
     *
     */
    public function showAction(DomaineUnifie $domaineUnifie)
    {
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        return $this->render('@MondofuteDomaine/domaineunifie/show.html.twig', array(
            'domaineUnifie' => $domaineUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DomaineUnifie entity.
     *
     * @param DomaineUnifie $domaineUnifie The DomaineUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DomaineUnifie $domaineUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('domaine_domaine_delete', array('id' => $domaineUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DomaineUnifie entity.
     *
     */
    public function editAction(Request $request, DomaineUnifie $domaineUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($domaineUnifie->getDomaines() as $domaine) {
                array_push($sitesAEnregistrer, $domaine->getSite()->getId());
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalDomaines = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($domaineUnifie->getDomaines() as $domaine) {
            $originalDomaines->add($domaine);
        }

        $this->ajouterDomainesDansForm($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType', $domaineUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->supprimerDomaines($domaineUnifie, $sitesAEnregistrer);

                // Supprimer la relation entre la station et stationUnifie
                foreach ($originalDomaines as $domaine) {
                    if (!$domaineUnifie->getDomaines()->contains($domaine)) {

                        //  suppression de la station sur le site
                        $emSite = $this->getDoctrine()->getEntityManager($domaine->getSite()->getLibelle());
                        $entitySite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                        $domaineSite = $entitySite->getDomaines()->first();
                        $emSite->remove($domaineSite);
                        $emSite->flush();
                        $domaine->setDomaineUnifie(null);
                        $em->remove($domaine);
                    }
                }
                $em->persist($domaineUnifie);
                $em->flush();


                $this->copieVersSites($domaineUnifie);
            } catch (ForeignKeyConstraintViolationException $except) {
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer le domaine, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirectToRoute('domaine_domaine_edit', array('id' => $domaineUnifie->getId()));
            }

            // add flash messages
            $this->addFlash(
                'success',
                'Le domaine a bien été modifié.'
            );

            return $this->redirectToRoute('domaine_domaine_edit', array('id' => $domaineUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domaineunifie/edit.html.twig', array(
            'entity' => $domaineUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DomaineUnifie entity.
     *
     */
    public function deleteAction(Request $request, DomaineUnifie $domaineUnifie)
    {
        $form = $this->createDeleteForm($domaineUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $domaineUnifieSite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                    if (!empty($domaineUnifieSite)) {
                        $emSite->remove($domaineUnifieSite);
                        $emSite->flush();
                    }
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($domaineUnifie);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer le domaine, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }

            // add flash messages
            $this->addFlash('success', 'Le domaine a été supprimé avec succès.');
        }

        return $this->redirectToRoute('domaine_domaine_index');
    }

}
