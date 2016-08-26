<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Entity\StationDescription;
use Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie;
use Mondofute\Bundle\StationBundle\Form\StationDescriptionUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * StationDescriptionUnifie controller.
 *
 */
class StationDescriptionUnifieController extends Controller
{
    /**
     * Lists all StationDescriptionUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stationDescriptionUnifies = $em->getRepository('MondofuteStationBundle:StationDescriptionUnifie')->findAll();

        return $this->render('@MondofuteStation/stationdescriptionunifie/index.html.twig', array(
            'stationDescriptionUnifies' => $stationDescriptionUnifies,
        ));
    }

    /**
     * Creates a new StationDescriptionUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $stationDescriptionUnifie = new StationDescriptionUnifie();

        $this->ajouterStationDescriptionsDansForm($stationDescriptionUnifie);
        $this->stationDescriptionsSortByAffichage($stationDescriptionUnifie);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationDescriptionUnifieType', $stationDescriptionUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // affilier les entités liés
//            $this->affilierEntities($stationDescriptionUnifie);

            $this->supprimerStationDescriptions($stationDescriptionUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationDescriptionUnifie);
            $em->flush();

            $this->copieVersSites($stationDescriptionUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La stationDescription a bien été créé.'
            );

            return $this->redirectToRoute('stationdescription_edit', array('id' => $stationDescriptionUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationdescriptionunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $stationDescriptionUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stationDescriptions qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param StationDescriptionUnifie $entity
     */
    private function ajouterStationDescriptionsDansForm(StationDescriptionUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getStationDescriptions() as $stationDescription) {
                if ($stationDescription->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($stationDescription->getTraductions()->filter(function (StationDescriptionTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationDescriptionTraduction();
                            $traduction->setLangue($langue);
                            $stationDescription->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $stationDescription = new StationDescription();
                $stationDescription->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationDescriptionTraduction();
                    $traduction->setLangue($langue);
                    $stationDescription->addTraduction($traduction);
                }
                $entity->addStationDescription($stationDescription);
            }
        }
    }

    /**
     * Classe les stationDescriptions par classementAffichage
     * @param StationDescriptionUnifie $entity
     */
    private function stationDescriptionsSortByAffichage(StationDescriptionUnifie $entity)
    {

        // Trier les stationDescriptions en fonction de leurs ordre d'affichage
        $stationDescriptions = $entity->getStationDescriptions(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $stationDescriptions->getIterator();
        unset($stationDescriptions);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (StationDescription $a, StationDescription $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $stationDescriptions = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($stationDescriptions);

        // remplacé les stationDescriptions par ce nouveau tableau (une fonction 'set' a été créé dans StationDescription unifié)
        $entity->setStationDescriptions($stationDescriptions);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $stationDescriptions
     */
    private function traductionsSortByLangue($stationDescriptions)
    {
        /** @var ArrayIterator $iterator */
        /** @var StationDescription $stationDescription */
        foreach ($stationDescriptions as $stationDescription) {
            $traductions = $stationDescription->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (StationDescriptionTraduction $a, StationDescriptionTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $stationDescription->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les stationDescriptions qui ne doivent pas être enregistrer
     * @param StationDescriptionUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerStationDescriptions(StationDescriptionUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getStationDescriptions() as $stationDescription) {
            if (!in_array($stationDescription->getSite()->getId(), $sitesAEnregistrer)) {
                $stationDescription->setStationDescriptionUnifie(null);
                $entity->removeStationDescription($stationDescription);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité stationDescription
     * @param StationDescriptionUnifie $entity
     */
    public function copieVersSites(StationDescriptionUnifie $entity)
    {
        /** @var StationDescriptionTraduction $stationDescriptionTraduc */
//        Boucle sur les stationDescriptions afin de savoir sur quel site nous devons l'enregistrer
        /** @var StationDescription $stationDescription */
        foreach ($entity->getStationDescriptions() as $stationDescription) {
            if ($stationDescription->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($stationDescription->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $stationDescription->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->getRepository(StationDescriptionUnifie::class)->find($entity->getId())))) {
                    $entitySite = new StationDescriptionUnifie();
                }
//                if (is_null(($entitySite = $em->getRepository('MondofuteStationDescriptionBundle:StationDescriptionUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new StationDescriptionUnifie();
//                }


//            Récupération de la stationDescription sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationDescriptionSite = $emSite->getRepository(StationDescription::class)->findOneBy(array('stationDescriptionUnifie' => $entitySite))))) {
                    $stationDescriptionSite = new StationDescription();
                }

//            copie des données stationDescription
                $stationDescriptionSite
                    ->setSite($site)
                    ->setStationDescriptionUnifie($entitySite);

//            Gestion des traductions
                foreach ($stationDescription->getTraductions() as $stationDescriptionTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $stationDescriptionTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($stationDescriptionTraducSite = $emSite->getRepository(StationDescriptionTraduction::class)->findOneBy(array(
                        'stationDescription' => $stationDescriptionSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $stationDescriptionTraducSite = new StationDescriptionTraduction();
                    }

//                copie des données traductions
                    $stationDescriptionTraducSite->setLangue($langue)
                        ->setAccroche($stationDescriptionTraduc->getAccroche())
                        ->setGeneralite($stationDescriptionTraduc->getGeneralite())
                        ->setStationDescription($stationDescriptionSite);

//                ajout a la collection de traduction de la stationDescription distante
                    $stationDescriptionSite->addTraduction($stationDescriptionTraducSite);
                }

                $entitySite->addStationDescription($stationDescriptionSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterStationDescriptionUnifieSiteDistant($entity->getId(), $entity->getStationDescriptions());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de stationDescription a enregistrer
     * @param $idUnifie
     * @param $stationDescriptions
     */
    private function ajouterStationDescriptionUnifieSiteDistant($idUnifie, $stationDescriptions)
    {
        /** @var ArrayCollection $stationDescriptions */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($stationDescriptions->matching($criteres)) == 0 && (empty($emSite->getRepository(StationDescriptionUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new StationDescriptionUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Creates a new StationDescriptionUnifie entity.
     *
     */
    public function newEntity(Station $station)
    {

        $stationDescriptionUnifie = new StationDescriptionUnifie();

        $em = $this->getDoctrine()->getManager();
        $stationDescriptionUnifie->addStationDescription($station->getStationDescription());

        $em->persist($stationDescriptionUnifie);

        return $stationDescriptionUnifie;
    }

    /**
     * Finds and displays a StationDescriptionUnifie entity.
     *
     */
    public function showAction(StationDescriptionUnifie $stationDescriptionUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationDescriptionUnifie);

        return $this->render('@MondofuteStation/stationdescriptionunifie/show.html.twig', array(
            'stationDescriptionUnifie' => $stationDescriptionUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a StationDescriptionUnifie entity.
     *
     * @param StationDescriptionUnifie $stationDescriptionUnifie The StationDescriptionUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationDescriptionUnifie $stationDescriptionUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stationdescription_delete', array('id' => $stationDescriptionUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing StationDescriptionUnifie entity.
     *
     */
    public function editAction(Request $request, StationDescriptionUnifie $stationDescriptionUnifie)
    {
        /** @var StationDescription $stationDescription */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            $stationDescriptions = $stationDescriptionUnifie->getStationDescriptions();
//            if(!empty($stationDescriptions))
//            {
//
//            }
            foreach ($stationDescriptions as $stationDescription) {
//                dump($stationDescription);die;
                array_push($sitesAEnregistrer, $stationDescription->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }
        $originalStationDescriptions = new ArrayCollection();
//          Créer un ArrayCollection des objets de stationDescriptions courants dans la base de données
        foreach ($stationDescriptionUnifie->getStationDescriptions() as $stationDescription) {
            $originalStationDescriptions->add($stationDescription);
        }

        $this->ajouterStationDescriptionsDansForm($stationDescriptionUnifie);
//        $this->affilierEntities($stationDescriptionUnifie);

        $this->stationDescriptionsSortByAffichage($stationDescriptionUnifie);
        $deleteForm = $this->createDeleteForm($stationDescriptionUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationDescriptionUnifieType',
            $stationDescriptionUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

//        dump($editForm);die;

        $editForm->handleRequest($request);
//        dump($stationDescriptionUnifie);die;

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerStationDescriptions($stationDescriptionUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la stationDescription et stationDescriptionUnifie
            foreach ($originalStationDescriptions as $stationDescription) {
                if (!$stationDescriptionUnifie->getStationDescriptions()->contains($stationDescription)) {

                    //  suppression de la stationDescription sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($stationDescription->getSite()->getLibelle());
                    $entitySite = $emSite->find(StationDescriptionUnifie::class, $stationDescriptionUnifie->getId());
                    $stationDescriptionSite = $entitySite->getStationDescriptions()->first();
                    $emSite->remove($stationDescriptionSite);
                    $emSite->flush();
//                    dump($stationDescription);
                    $stationDescription->setStationDescriptionUnifie(null);
                    $em->remove($stationDescription);
                }
            }
            $em->persist($stationDescriptionUnifie);
            $em->flush();


            $this->copieVersSites($stationDescriptionUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La stationDescription a bien été modifié.'
            );

            return $this->redirectToRoute('stationdescription_edit', array('id' => $stationDescriptionUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationdescriptionunifie/edit.html.twig', array(
            'entity' => $stationDescriptionUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing StationDescriptionUnifie entity.
     *
     */
    public function editEntity(StationDescriptionUnifie $stationDescriptionUnifie)
    {
        /** @var StationDescription $stationDescription */
        $em = $this->getDoctrine()->getManager();
        $em->persist($stationDescriptionUnifie);
    }

    /**
     * Deletes a StationDescriptionUnifie entity.
     *
     */
    public function deleteAction(Request $request, StationDescriptionUnifie $stationDescriptionUnifie)
    {
        $form = $this->createDeleteForm($stationDescriptionUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $stationDescriptionUnifieSite = $emSite->find(StationDescriptionUnifie::class, $stationDescriptionUnifie->getId());
                if (!empty($stationDescriptionUnifieSite)) {
                    $emSite->remove($stationDescriptionUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($stationDescriptionUnifie);
            $em->flush();


            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La stationDescription a été supprimé avec succès.'
            );

        }

        return $this->redirectToRoute('stationdescription_index');
    }

    /**
     * Deletes a StationDescriptionUnifie entity.
     *
     */
    public function deleteEntity(StationDescriptionUnifie $stationDescriptionUnifie)
    {
        /** @var Site $siteDistant */
        $em = $this->getDoctrine()->getEntityManager();
        $delete = true;
        $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        // Parcourir les sites non CRM
        foreach ($sitesDistants as $siteDistant) {
            // Récupérer le manager du site.
            $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
            // Récupérer l'entité sur le site distant puis la suprrimer.
            $stationDescriptionUnifieSite = $emSite->find(StationDescriptionUnifie::class, $stationDescriptionUnifie->getId());
            if (!empty($stationDescriptionUnifieSite)) {
                foreach ($stationDescriptionUnifieSite->getStationDescriptions() as $stationDescriptionSite){
                    if ($stationDescriptionSite->getStations()->count() <= 1 ){
                        $emSite->remove($stationDescriptionSite);
                    } else $delete = false;
                }
                if ($delete) {
                    $emSite->remove($stationDescriptionUnifieSite);
                    $emSite->flush();
                }
            }
        }
        foreach ($stationDescriptionUnifie->getStationDescriptions() as $stationDescription) {
            if ($stationDescription->getStations()->count() <= 1) {
                $em->remove($stationDescription);
            } else $delete = false;
        }

        if ($delete) {
            $em->remove($stationDescriptionUnifie);
        }
    }

}
