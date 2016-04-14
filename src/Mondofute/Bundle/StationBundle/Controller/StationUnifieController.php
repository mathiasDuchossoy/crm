<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite;
use Mondofute\Bundle\StationBundle\Entity\StationTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\StationBundle\Form\StationUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
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

            $this->supprimerStations($stationUnifie, $sitesAEnregistrer);

            // ***** Carte d'identité *****
            /** @var Station $station */
            foreach ($stationUnifie->getStations() as $station) {
                if (!empty($request->get('cboxStationCI_' . $station->getSite()->getId()))) {
//                    foreach ($stationUnifie->getStations() as $station) {
                    $station->setStationCarteIdentite($station->getStationMere()->getStationCarteIdentite());
//                    }
                } else {
                    $stationCarteIdentiteController = new StationCarteIdentiteUnifieController();
                    $stationCarteIdentiteController->setContainer($this->container);
//                    $stationCarteIdentiteUnifie = $stationCarteIdentiteController->newEntity($stationUnifie);
                    $stationCarteIdentiteUnifie = $stationCarteIdentiteController->newEntity($station);

//                    foreach ($stationUnifie->getStations() as $station) {
                    $site = $station->getSite();
                    $stationCarteIdentite = $stationCarteIdentiteUnifie->getStationCarteIdentites()->filter(function (StationCarteIdentite $element) use ($site) {
                        return $site == $element->getSite();
                    })->first();
                    $station->setStationCarteIdentite($stationCarteIdentite);
//                    }
                }
            }
            // ***** Fin Carte d'identité *****

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
        /** @var Station $station */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getStations() as $station) {
                if ($station->getSite() == $site) {
                    if (empty($station->getStationCarteIdentite())) {
                        $stationCarteIdentite = new StationCarteIdentite();
                        $stationCarteIdentite->setSite($site);
                        $station->setStationCarteIdentite($stationCarteIdentite);
                    }
                    if (empty($station->getStationCarteIdentite()->getMoyenComs())) {
                        $station->getStationCarteIdentite()->addMoyenCom(new Adresse());
                    }

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
                $station->setStationCarteIdentite(new StationCarteIdentite());
                $station->getStationCarteIdentite()->addMoyenCom(new Adresse());
                $station->getStationCarteIdentite()->setSite($site);
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
        /** @var Station $station */
        foreach ($entity->getStations() as $station) {
            if ($station->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($station->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $station->getSite()->getId()));
                if (!empty($station->getZoneTouristiques())) {
                    $zoneTouristiques = new ArrayCollection();
                    foreach ($station->getZoneTouristiques() as $zoneTouristique) {
                        $zoneTouristiqueSite = $emSite->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $zoneTouristique->getZoneTouristiqueUnifie()));
                        $zoneTouristiques->add($zoneTouristiqueSite);
                    }
                } else {
                    $zoneTouristiques = null;
                }
                if (!empty($station->getSecteurs())) {
                    $secteurs = new ArrayCollection();
                    foreach ($station->getSecteurs() as $secteur) {
                        $secteurSite = $emSite->getRepository(Secteur::class)->findOneBy(array('secteurUnifie' => $secteur->getSecteurUnifie()));
                        $secteurs->add($secteurSite);
                    }
                } else {
                    $secteurs = null;
                }
                if (!empty($station->getProfils())) {
                    $profils = new ArrayCollection();
                    foreach ($station->getProfils() as $profil) {
                        $profilSite = $emSite->getRepository(Profil::class)->findOneBy(array('profilUnifie' => $profil->getProfilUnifie()));
                        $profils->add($profilSite);
                    }
                } else {
                    $profils = null;
                }
                if (!empty($station->getDomaine())) {
                    $domaine = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $station->getDomaine()->getDomaineUnifie()));
                } else {
                    $domaine = null;
                }
                if (!empty($station->getDepartement())) {
                    $departement = $emSite->getRepository(Departement::class)->findOneBy(array('departementUnifie' => $station->getDepartement()->getDepartementUnifie()));
                } else {
                    $departement = null;
                }
                if (!empty($station->getStationMere())) {
                    $stationMere = $emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $station->getStationMere()->getStationUnifie()));
                    $emSite->refresh($stationMere);
                    foreach ($stationMere->getStationCarteIdentite()->getMoyenComs() as $moyenCom) {
                        $emSite->refresh($moyenCom);
                    }
                    $emSite->refresh($stationMere->getStationCarteIdentite()->getAltitudeVillage());
                } else {
                    $stationMere = null;
                }
                if (!empty($station->getStationCarteIdentite())) {
                    $stationCarteIdentite = $emSite->getRepository(StationCarteIdentite::class)->findOneBy(array('stationCarteIdentiteUnifie' => $station->getStationCarteIdentite()->getStationCarteIdentiteUnifie()));
                } else {
                    $stationCarteIdentite = null;
                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->getRepository(StationUnifie::class)->find($entity->getId())))) {
                    $entitySite = new StationUnifie();
                }
//                if (is_null(($entitySite = $em->getRepository('MondofuteStationBundle:StationUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new StationUnifie();
//                }


//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationSite = $emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $entitySite))))) {
                    $stationSite = new Station();
                }

//            copie des données station
                $stationSite
                    ->setSite($site)
                    ->setStationUnifie($entitySite)
                    ->setZoneTouristiques($zoneTouristiques)
                    ->setSecteurs($secteurs)
                    ->setProfils($profils)
                    ->setDomaine($domaine)
                    ->setDepartement($departement)
                    ->setStationMere($stationMere)
                    ->setStationCarteIdentite($stationCarteIdentite);;

//            Gestion des traductions
                foreach ($station->getTraductions() as $stationTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $stationTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($stationTraducSite = $emSite->getRepository(StationTraduction::class)->findOneBy(array(
                        'station' => $stationSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $stationTraducSite = new StationTraduction();
                    }

//                copie des données traductions
                    $stationTraducSite->setLangue($langue)
                        ->setLibelle($stationTraduc->getLibelle())
                        ->setParking($stationTraduc->getParking())
                        ->setStation($stationSite);

//                ajout a la collection de traduction de la station distante
                    $stationSite->addTraduction($stationTraducSite);
                }

                $entitySite->addStation($stationSite);
                $emSite->persist($entitySite);
                $emSite->flush();
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
//        echo $idUnifie;
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
        /** @var Station $station */
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

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $stationCarteIdentiteController = new StationCarteIdentiteUnifieController();
            $stationCarteIdentiteController->setContainer($this->container);

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
                    $station->setStationUnifie(null);
                    $stationCarteIdentiteUnifie = $station->getStationCarteIdentite()->getStationCarteIdentiteUnifie();
                    $station->getStationCarteIdentite()->removeStation($station);
//                    $em->persist($station->getStationCarteIdentite());
                    $station->setStationCarteIdentite(null);
                    $em->remove($station);
                    $stationCarteIdentiteController->deleteEntity($stationCarteIdentiteUnifie);
                }
            }

            // ***** carte d'identité *****

            foreach ($stationUnifie->getStations() as $station) {
                // si on choisit de lié la carte ID de la mère à la station
                if (!empty($request->get('cboxStationCI_' . $station->getSite()->getId()))) {
                    $station->getStationCarteIdentite()->removeStation($station);
                    $oldCIUnifie = $station->getStationCarteIdentite()->getStationCarteIdentiteUnifie();

                    $em->refresh($station->getStationMere()->getStationCarteIdentite());
                    $station->setStationCarteIdentite($station->getStationMere()->getStationCarteIdentite());
                    if ($station->getStationMere()->getStationCarteIdentite()->getStationCarteIdentiteUnifie() != $oldCIUnifie) {
                        $this->copieVersSites($station->getStationUnifie());
                        $stationCarteIdentiteController->deleteEntity($oldCIUnifie);
                    }
                } else {
                    if (!empty($station->getStationMere()) && $station->getStationMere()->getStationCarteIdentite() === $station->getStationCarteIdentite()) {
                        $cIMere = $station->getStationMere()->getStationCarteIdentite();

                        $newCI = new StationCarteIdentite();
                        $adresse = new Adresse();
                        $adresse->setVille($station->getStationCarteIdentite()->getMoyenComs()->first()->getVille())
                            ->setCodePostal($station->getStationCarteIdentite()->getMoyenComs()->first()->getCodePostal())
                            ->setDateCreation();
                        $newGPS = new CoordonneesGPS();
                        $adresse->setCoordonneeGPS($newGPS);
                        $newCI->addMoyenCom($adresse);
                        $altitudeVillage = new Distance();
                        $altitudeVillage->setUnite($station->getStationCarteIdentite()->getAltitudeVillage()->getUnite())
                            ->setValeur($station->getStationCarteIdentite()->getAltitudeVillage()->getValeur());
                        $newCI
                            ->setJourOuverture($station->getStationCarteIdentite()->getJourOuverture())
                            ->setMoisOuverture($station->getStationCarteIdentite()->getMoisOuverture())
                            ->setJourFermeture($station->getStationCarteIdentite()->getJourFermeture())
                            ->setMoisFermeture($station->getStationCarteIdentite()->getMoisFermeture())
                            ->setAltitudeVillage($altitudeVillage)
                            ->setSite($station->getStationCarteIdentite()->getSite());
                        $em->persist($newCI);
                        $station->setStationCarteIdentite($newCI);

                        $em->refresh($cIMere);
                        foreach ($cIMere->getMoyenComs() as $moyenCom) {
                            $em->refresh($moyenCom);
                        }
                        $em->refresh($cIMere->getAltitudeVillage());
                    }
                }
            }

            foreach ($stationUnifie->getStations() as $station) {

                if (!empty($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie())) {
                    $stationCarteIdentiteUnifie = $stationCarteIdentiteController->editEntity($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                } else {

                    $stationCarteIdentiteUnifie = $stationCarteIdentiteController->newEntity($station);
                }

//                $em->persist($station);
//                $em->flush();
            }
//            die;
            // ***** fin carte d'identité *****

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
        $stationCarteIdentiteUnifieController = new StationCarteIdentiteUnifieController();
        $stationCarteIdentiteUnifieController->setContainer($this->container);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $stationUnifieSite = $emSite->find(StationUnifie::class, $stationUnifie->getId());


//                    /** @var Station $stationSite */
//                    foreach ($stationUnifieSite->getStations() as $stationSite)
//                    {
//                        $stationSite->getStationCarteIdentite()->removeStation($stationSite);
//                        $emSite->persist($stationSite);
//                        $emSite->flush();
////                        $stationCarteIdentiteUnifieController->deleteEntity($stationSite->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
//                    }

                    if (!empty($stationUnifieSite)) {
                        $emSite->remove($stationUnifieSite);
                        $emSite->flush();
                    }
                }
                $arrayStationCarteIdentiteUnifies = new ArrayCollection();
                /** @var Station $station */
                foreach ($stationUnifie->getStations() as $station) {
                    //                $station->setStationMere(null);
//                    $em->detach($station->getStationMere());
//                    $station->getStationCarteIdentite()->removeStation($station);
//                    $stationCarteIdentiteUnifieController->deleteEntity($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                    $arrayStationCarteIdentiteUnifies->add($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                }

                $em = $this->getDoctrine()->getManager();

                $em->remove($stationUnifie);
                $em->flush();
//                die;
            } catch (ForeignKeyConstraintViolationException $except) {
                //                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer la station, elle est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }
            foreach ($arrayStationCarteIdentiteUnifies as $arrayStationCarteIdentiteUnify) {
                $stationCarteIdentiteUnifieController->deleteEntity($arrayStationCarteIdentiteUnify);
            }

//            /** @var Station $station */
//            foreach ($stationUnifie->getStations() as $station)
//            {
//                if (count($station->getStationCarteIdentite()->getStations()) == 1 )
//                {
//                    dump($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
//                    $stationCarteIdentiteUnifieController->deleteEntity($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
//                }
//            }

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
