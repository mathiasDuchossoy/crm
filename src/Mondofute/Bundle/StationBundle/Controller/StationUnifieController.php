<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Entity\StationTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\StationBundle\Form\StationUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * StationUnifie controller.
 *
 */
class StationUnifieController extends Controller
{
    /**
     * Lists all StationUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stationUnifies = $em->getRepository('MondofuteStationBundle:StationUnifie')->findAll();

        return $this->render('@MondofuteStation/stationunifie/index.html.twig', array(
            'stationUnifies' => $stationUnifies,
        ));
    }

    /**
     * Creates a new StationUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $stationUnifie = new StationUnifie();

        $this->ajouterStationsDansForm($stationUnifie);
        $this->stationsSortByAffichage($stationUnifie);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationUnifieType', $stationUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            // affilier les entités liés
//            $this->affilierEntities($stationUnifie);

            $this->supprimerStations($stationUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationUnifie);
            $em->flush();

            $this->copieVersSites($stationUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La station a bien été créé.'
            );

            return $this->redirectToRoute('station_station_edit', array('id' => $stationUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $stationUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param StationUnifie $entity
     */
    private function ajouterStationsDansForm(StationUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getStations() as $station) {
                if ($station->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($station->getTraductions()->filter(function (StationTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationTraduction();
                            $traduction->setLangue($langue);
                            $station->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $station = new Station();
                $station->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationTraduction();
                    $traduction->setLangue($langue);
                    $station->addTraduction($traduction);
                }
                $entity->addStation($station);
            }
        }
    }


    /**
     * Classe les stations par classementAffichage
     * @param StationUnifie $entity
     */
    private function stationsSortByAffichage(StationUnifie $entity)
    {

        // Trier les stations en fonction de leurs ordre d'affichage
        $stations = $entity->getStations(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $stations->getIterator();
        unset($stations);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (Station $a, Station $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $stations = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($stations);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setStations($stations);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $stations
     */
    private function traductionsSortByLangue($stations)
    {
        /** @var ArrayIterator $iterator */
        /** @var Station $station */
        foreach ($stations as $station) {
            $traductions = $station->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (StationTraduction $a, StationTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $station->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les stations qui ne doivent pas être enregistrer
     * @param StationUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerStations(StationUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getStations() as $station) {
            if (!in_array($station->getSite()->getId(), $sitesAEnregistrer)) {
                $station->setStationUnifie(null);
                $entity->removeStation($station);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param StationUnifie $entity
     */
    private function copieVersSites(StationUnifie $entity)
    {
        /** @var StationTraduction $stationTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getStations() as $station) {
            if ($station->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($station->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $station->getSite()->getId()));
                if (!empty($station->getZoneTouristique())) {
                    $zoneTouristique = $em->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $station->getZoneTouristique()->getZoneTouristiqueUnifie()));
                } else {
                    $zoneTouristique = null;
                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(StationUnifie::class)->find($entity->getId())))) {
                    $entitySite = new StationUnifie();
                }
//                if (is_null(($entitySite = $em->getRepository('MondofuteStationBundle:StationUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new StationUnifie();
//                }


//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationSite = $em->getRepository(Station::class)->findOneBy(array('stationUnifie' => $entitySite))))) {
                    $stationSite = new Station();
                }

//            copie des données station
                $stationSite
                    ->setSite($site)
                    ->setStationUnifie($entitySite)
                    ->setZoneTouristique($zoneTouristique);

//            Gestion des traductions
                foreach ($station->getTraductions() as $stationTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $stationTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($stationTraducSite = $em->getRepository(StationTraduction::class)->findOneBy(array(
                        'station' => $stationSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $stationTraducSite = new StationTraduction();
                    }

//                copie des données traductions
                    $stationTraducSite->setLangue($langue)
                        ->setLibelle($stationTraduc->getLibelle())
                        ->setStation($stationSite);

//                ajout a la collection de traduction de la station distante
                    $stationSite->addTraduction($stationTraducSite);
                }

                $entitySite->addStation($stationSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterStationUnifieSiteDistant($entity->getId(), $entity->getStations());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $stations
     */
    private function ajouterStationUnifieSiteDistant($idUnifie, $stations)
    {
        /** @var ArrayCollection $stations */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($stations->matching($criteres)) == 0 && (empty($emSite->getRepository(StationUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new StationUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a StationUnifie entity.
     *
     */
    public function showAction(StationUnifie $stationUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationUnifie);

        return $this->render('@MondofuteStation/stationunifie/show.html.twig', array(
            'stationUnifie' => $stationUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a StationUnifie entity.
     *
     * @param StationUnifie $stationUnifie The StationUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationUnifie $stationUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('station_station_delete', array('id' => $stationUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing StationUnifie entity.
     *
     */
    public function editAction(Request $request, StationUnifie $stationUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($stationUnifie->getStations() as $station) {
                array_push($sitesAEnregistrer, $station->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalStations = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($stationUnifie->getStations() as $station) {
            $originalStations->add($station);
        }

        $this->ajouterStationsDansForm($stationUnifie);
//        $this->affilierEntities($stationUnifie);

        $this->stationsSortByAffichage($stationUnifie);
        $deleteForm = $this->createDeleteForm($stationUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationUnifieType',
            $stationUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

//        dump($editForm);die;

        $editForm->handleRequest($request);
//        dump($stationUnifie);die;

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerStations($stationUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalStations as $station) {
                if (!$stationUnifie->getStations()->contains($station)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($station->getSite()->getLibelle());
                    $entitySite = $emSite->find(StationUnifie::class, $stationUnifie->getId());
                    $stationSite = $entitySite->getStations()->first();
                    $emSite->remove($stationSite);
                    $emSite->flush();
//                    dump($station);
                    $station->setStationUnifie(null);
                    $em->remove($station);
                }
            }
            $em->persist($stationUnifie);
            $em->flush();


            $this->copieVersSites($stationUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La station a bien été modifié.'
            );

            return $this->redirectToRoute('station_station_edit', array('id' => $stationUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationunifie/edit.html.twig', array(
            'entity' => $stationUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a StationUnifie entity.
     *
     */
    public function deleteAction(Request $request, StationUnifie $stationUnifie)
    {
        $form = $this->createDeleteForm($stationUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $stationUnifieSite = $emSite->find(StationUnifie::class, $stationUnifie->getId());
                if (!empty($stationUnifieSite)) {
                    $emSite->remove($stationUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($stationUnifie);
            $em->flush();


            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La station a été supprimé avec succès.'
            );

        }

        return $this->redirectToRoute('station_station_index');
    }

}
