<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\GrandeVille;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenir;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie;
use Mondofute\Bundle\StationBundle\Entity\StationDescription;
use Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie;
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

        $commentVenir = new StationCommentVenirUnifieController();
        $commentVenir->testnewAction()
        
        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationUnifieType', $stationUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le controleur et lui donner le container de celui dans lequel on est
            $stationCarteIdentiteController = new StationCarteIdentiteUnifieController();
            $stationCarteIdentiteController->setContainer($this->container);

            $commentVenirController = new StationCommentVenirUnifieController();
            $commentVenirController->setContainer($this->container);

            $descriptionController = new StationDescriptionUnifieController();
            $descriptionController->setContainer($this->container);

            $this->supprimerStations($stationUnifie, $sitesAEnregistrer);

            // ***** Carte d'identité *****
            /** @var Station $station */
            $this->carteIdentiteNew($request, $stationUnifie);
            // ***** Fin Carte d'identité *****

            // ***** Comment venir *****
            $this->commentVenirNew($request, $stationUnifie);
            // ***** Fin Comment venir *****

            // ***** description *****
            $this->descriptionNew($request, $stationUnifie);
            // ***** Fin description *****

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationUnifie);

            $em->flush();

            foreach ($stationUnifie->getStations() as $station) {
                $stationCarteIdentiteController->copieVersSites($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                $commentVenirController->copieVersSites($station->getStationCommentVenir()->getStationCommentVenirUnifie());
                $descriptionController->copieVersSites($station->getStationDescription()->getStationDescriptionUnifie());
            }
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
        $grandeVilles = $em->getRepository(GrandeVille::class)->findAll();

//        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getStations() as $station) {
                if ($station->getSite() == $site) {
                    $siteExiste = true;

                    // station CI
                    if (empty($station->getStationCarteIdentite())) {
                        $stationCarteIdentite = new StationCarteIdentite();
                        $stationCarteIdentite->setSite($site);
                        $station->setStationCarteIdentite($stationCarteIdentite);
                    }
//                    if ($station->getStationCarteIdentite()->getMoyenComs()->isEmpty()) {
//                        $station->getStationCarteIdentite()->addMoyenCom(new Adresse());
//                    }
                    // fin station CI

                    // station CV
                    /** @var StationCommentVenir $stationCommentVenir */
                    $stationCommentVenir = $station->getStationCommentVenir();
                    if (empty($stationCommentVenir)) {
                        $stationCommentVenir = new StationCommentVenir();
                        $stationCommentVenir->setSite($site);
                        $station->setStationCommentVenir($stationCommentVenir);
                    }

                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($stationCommentVenir->getTraductions()->filter(function (StationCommentVenirTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationCommentVenirTraduction();
                            $traduction->setLangue($langue);
                            $stationCommentVenir->addTraduction($traduction);
                        }
                    }

                    foreach ($grandeVilles as $grandeVille) {
                        if ($stationCommentVenir->getGrandeVilles()->filter(function (StationCommentVenirGrandeVille $element) use ($grandeVille) {
                            return $element->getGrandeVille() == $grandeVille;
                        })->isEmpty()
                        ) {
                            $stationCommentVenirGranceVille = new StationCommentVenirGrandeVille();
                            $stationCommentVenirGranceVille->setGrandeVille($grandeVille);
                            $stationCommentVenirGranceVille->setStationCommentVenir($station);
                            $stationCommentVenir->addGrandeVille($stationCommentVenirGranceVille);
                        }
                    }
                    // station CV

                    // station description
                    /** @var StationDescription $stationDescription */
                    $stationDescription = $station->getStationDescription();
                    if (empty($stationDescription)) {
                        $stationDescription = new StationDescription();
                        $stationDescription->setSite($site);
                        $station->setStationDescription($stationDescription);
                    }

                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la station
                        if ($stationDescription->getTraductions()->filter(function (StationDescriptionTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationDescriptionTraduction();
                            $traduction->setLangue($langue);
                            $stationDescription->addTraduction($traduction);
                        }
                    }

                    // station description

                    foreach ($langues as $langue) {
//                      vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
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
//                $station->getStationCarteIdentite()->addMoyenCom(new Adresse());
                $station->getStationCarteIdentite()->setSite($site);
                $station->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationTraduction();
                    $traduction->setLangue($langue);
                    $station->addTraduction($traduction);
                }
                $entity->addStation($station);

                //comment venir
                $stationCommentVenir = new StationCommentVenir();
                $stationCommentVenir->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationCommentVenirTraduction();
                    $traduction->setLangue($langue);
                    $stationCommentVenir->addTraduction($traduction);
                }

                foreach ($grandeVilles as $grandeVille) {
                    $stationCommentVenirGranceVille = new StationCommentVenirGrandeVille();
                    $stationCommentVenirGranceVille->setGrandeVille($grandeVille);
                    $stationCommentVenir->addGrandeVille($stationCommentVenirGranceVille);
                }

                $station->setStationCommentVenir($stationCommentVenir);
                // fin comment venir

                // description
                $stationDescription = new StationDescription();
                $stationDescription->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationDescriptionTraduction();
                    $traduction->setLangue($langue);
                    $stationDescription->addTraduction($traduction);
                }


                $station->setStationDescription($stationDescription);
                // fin description



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
     * @param Request $request
     * @param StationUnifie $stationUnifie
     */
    private function carteIdentiteNew(Request $request, StationUnifie $stationUnifie)
    {
        $stationCarteIdentiteController = new StationCarteIdentiteUnifieController();
        $stationCarteIdentiteController->setContainer($this->container);

        foreach ($stationUnifie->getStations() as $station) {
            // Si la carte d'identité est lié à la station mère
            if (!empty($request->get('cboxStationCI_' . $station->getSite()->getId()))) {
                $station->setStationCarteIdentite($station->getStationMere()->getStationCarteIdentite());
            } else {
                // sinon on on en créé une nouvelle
                $stationCarteIdentiteUnifie = $stationCarteIdentiteController->newEntity($station);

                $site = $station->getSite();
                $stationCarteIdentite = $stationCarteIdentiteUnifie->getStationCarteIdentites()->filter(function (StationCarteIdentite $element) use ($site) {
                    return $site == $element->getSite();
                })->first();
                $station->setStationCarteIdentite($stationCarteIdentite);
            }
        }
    }

    /**
     * @param Request $request
     * @param StationUnifie $stationUnifie
     */
    private function commentVenirNew(Request $request, StationUnifie $stationUnifie)
    {
        /** @var StationCommentVenirUnifie $stationCommentVenirUnifie */
        /** @var Station $station */
        $stationCommentVenirController = new StationCommentVenirUnifieController();
        $stationCommentVenirController->setContainer($this->container);

        foreach ($stationUnifie->getStations() as $station) {
            // Si la carte d'identité est lié à la station mère
            if (!empty($request->get('cboxStationCommentVenir_' . $station->getSite()->getId()))) {
                $station->setStationCommentVenir($station->getStationMere()->getStationCommentVenir());
            } else {
                // sinon on on en créé une nouvelle
                $stationCommentVenirUnifie = $stationCommentVenirController->newEntity($station);

                $site = $station->getSite();
                $stationCommentVenir = $stationCommentVenirUnifie->getStationCommentVenirs()->filter(function (StationCommentVenir $element) use ($site) {
                    return $site == $element->getSite();
                })->first();
                $station->setStationCommentVenir($stationCommentVenir);
            }
        }
    }

    /**
     * @param Request $request
     * @param StationUnifie $stationUnifie
     */
    private function descriptionNew(Request $request, StationUnifie $stationUnifie)
    {
        /** @var StationDescriptionUnifie $stationDescriptionUnifie */
        /** @var Station $station */
        $stationDescriptionController = new StationDescriptionUnifieController();
        $stationDescriptionController->setContainer($this->container);

        foreach ($stationUnifie->getStations() as $station) {
            // Si la carte d'identité est lié à la station mère
            if (!empty($request->get('cboxStationDescription_' . $station->getSite()->getId()))) {
                $station->setStationDescription($station->getStationMere()->getStationDescription());
            } else {
                // sinon on on en créé une nouvelle
                $stationDescriptionUnifie = $stationDescriptionController->newEntity($station);

                $site = $station->getSite();
                $stationDescription = $stationDescriptionUnifie->getStationDescriptions()->filter(function (StationDescription $element) use ($site) {
                    return $site == $element->getSite();
                })->first();
                $station->setStationDescription($stationDescription);
            }
        }
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
//                    $emSite->refresh($stationMere);
//                    foreach ($stationMere->getStationCarteIdentite()->getMoyenComs() as $moyenCom) {
//                        $emSite->refresh($moyenCom);
//                    }
//                    $emSite->refresh($stationMere->getStationCarteIdentite()->getAltitudeVillage());
                } else {
                    $stationMere = null;
                }
                if (!empty($station->getStationCarteIdentite())) {
                    $stationCarteIdentite = $emSite->getRepository(StationCarteIdentite::class)->findOneBy(array('stationCarteIdentiteUnifie' => $station->getStationCarteIdentite()->getStationCarteIdentiteUnifie()));
                } else {
                    $stationCarteIdentite = null;
                }
                if (!empty($station->getStationCommentVenir())) {
                    $stationCommentVenir = $emSite->getRepository(StationCommentVenir::class)->findOneBy(array('stationCommentVenirUnifie' => $station->getStationCommentVenir()->getStationCommentVenirUnifie()));
                    /** @var StationCommentVenirGrandeVille $grandeVille */
                    if (!empty($stationCommentVenir->getGrandeVilles())) {
                        foreach ($station->getStationCommentVenir()->getGrandeVilles() as $grandeVille) {
                            if ($stationCommentVenir->getGrandeVilles()->filter(function (StationCommentVenirGrandeVille $element) use ($grandeVille) {
                                return $element->getGrandeVille()->getId() == $grandeVille->getGrandeVille()->getId();
                            })->isEmpty()
                            ) {
                                $stationCommentVenirGrandeVille = new StationCommentVenirGrandeVille();
                                $stationCommentVenirGrandeVille->setGrandeVille($emSite->find(GrandeVille::class, $grandeVille->getGrandeVille()));
                                $stationCommentVenir->addGrandeVille($stationCommentVenirGrandeVille);
                            }
                        }
                    } else {
                        foreach ($station->getStationCommentVenir()->getGrandeVilles() as $grandeVille) {
                            $stationCommentVenirGrandeVille = new StationCommentVenirGrandeVille();
                            $stationCommentVenirGrandeVille->setGrandeVille($emSite->find(GrandeVille::class, $grandeVille->getGrandeVille()->getId()));
                            $stationCommentVenir->addGrandeVille($stationCommentVenirGrandeVille);
                        }
                    }
                } else {
                    $stationCommentVenir = null;
                }
                if (!empty($station->getStationDescription())) {
                    $stationDescription = $emSite->getRepository(StationDescription::class)->findOneBy(array('stationDescriptionUnifie' => $station->getStationDescription()->getStationDescriptionUnifie()));
                } else {
                    $stationDescription = null;
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
                    ->setStationCarteIdentite($stationCarteIdentite)
                    ->setStationCommentVenir($stationCommentVenir)
                    ->setStationDescription($stationDescription);

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
            $stationCarteIdentiteUnifieController = new StationCarteIdentiteUnifieController();
            $stationCarteIdentiteUnifieController->setContainer($this->container);

            $stationCommentVenirUnifieController = new StationCommentVenirUnifieController();
            $stationCommentVenirUnifieController->setContainer($this->container);

            $stationDescriptionUnifieController = new StationDescriptionUnifieController();
            $stationDescriptionUnifieController->setContainer($this->container);

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
                    $stationUnifie->removeStation($station);
                    $station->setStationUnifie(null);
                    $stationMere = $station->getStationMere();

                    $stationCI = $station->getStationCarteIdentite();
                    $station->getStationCarteIdentite()->removeStation($station);

                    $stationCV = $station->getStationCommentVenir();
                    $station->getStationCommentVenir()->removeStation($station);

                    $stationDescription = $station->getStationDescription();
                    $station->getStationDescription()->removeStation($station);

                    $station->setStationMere(null);

                    $em->remove($station);
                    if (empty($stationMere) || $stationCI != $stationMere->getStationCarteIdentite()) {
                        $stationCarteIdentiteUnifieController->deleteEntity($stationCI->getStationCarteIdentiteUnifie());
                    }
                    if (empty($stationMere) || $stationCV != $stationMere->getStationCommentVenir()) {
                        $stationCommentVenirUnifieController->deleteEntity($stationCV->getStationCommentVenirUnifie());
                    }
                    if (empty($stationMere) || $stationDescription != $stationMere->getStationDescription()) {
                        $stationDescriptionUnifieController->deleteEntity($stationDescription->getStationDescriptionUnifie());
                    }
                }
            }
//            $em->flush();die;

            // ***** carte d'identité *****
            $this->carteIdentiteEdit($request, $stationUnifie);
            // ***** fin carte d'identité *****

            // ***** comment venir *****
            $this->commentVenirEdit($request, $stationUnifie);
            // ***** fin comment venir *****

            // ***** comment venir *****
            $this->descriptionEdit($request, $stationUnifie);
            // ***** fin comment venir *****

            $em->persist($stationUnifie);
            $em->flush();

            foreach ($stationUnifie->getStations() as $station) {
                $stationCarteIdentiteUnifieController->copieVersSites($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                $stationCommentVenirUnifieController->copieVersSites($station->getStationCommentVenir()->getStationCommentVenirUnifie());
                $stationDescriptionUnifieController->copieVersSites($station->getStationDescription()->getStationDescriptionUnifie());
            }
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

    private function carteIdentiteEdit(Request $request, StationUnifie $stationUnifie)
    {
        /** @var Station $station */
        $stationCarteIdentiteUnifieController = new StationCarteIdentiteUnifieController();
        $stationCarteIdentiteUnifieController->setContainer($this->container);
        $em = $this->getDoctrine()->getEntityManager();

        foreach ($stationUnifie->getStations() as $station) {
            // si on choisit de lié la carte ID de la mère à la station
            if (!empty($request->get('cboxStationCI_' . $station->getSite()->getId()))) {
                $oldCIUnifie = $station->getStationCarteIdentite()->getStationCarteIdentiteUnifie();
                $station->getStationCarteIdentite()->removeStation($station);
//                    $station->setStationCarteIdentite(null);

                $em->refresh($station->getStationMere()->getStationCarteIdentite());
                $station->setStationCarteIdentite($station->getStationMere()->getStationCarteIdentite());
                if ($station->getStationMere()->getStationCarteIdentite()->getStationCarteIdentiteUnifie() != $oldCIUnifie) {
                    $this->copieVersSites($station->getStationUnifie());
                    if (!empty($oldCIUnifie)) {
                        $stationCarteIdentiteUnifieController->deleteEntity($oldCIUnifie);
                    }
                }

            } else {
                //
                if (!empty($station->getStationMere()) && $station->getStationMere()->getStationCarteIdentite() === $station->getStationCarteIdentite()) {
                    // OIn fait ça
                    $cIMere = $station->getStationMere()->getStationCarteIdentite();

                    $newCI = new StationCarteIdentite();
                    $adresse = new Adresse();
                    $adresse->setVille($station->getStationCarteIdentite()->getAdresse()->getVille())
                        ->setCodePostal($station->getStationCarteIdentite()->getAdresse()->getCodePostal())
                        ->setDateCreation();
                    $newGPS = new CoordonneesGPS();
                    $adresse->setCoordonneeGPS($newGPS);
//                    $newCI->addMoyenCom($adresse);
                    $newCI->setAdresse($adresse);
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
//                    foreach ($cIMere->getAdresse() as $moyenCom) {
                    $em->refresh($cIMere->getAdresse());
//                    }
                    $em->refresh($cIMere->getAltitudeVillage());
                }
            }
        }

        foreach ($stationUnifie->getStations() as $station) {

            if (!empty($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie())) {
                $stationCarteIdentiteUnifieController->editEntity($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
            } else {
                $stationCarteIdentiteUnifieController->newEntity($station);
            }

            $em->persist($station);
//                $em->flush();
        }

    }

    private function commentVenirEdit(Request $request, StationUnifie $stationUnifie)
    {
        /** @var StationCommentVenirTraduction $traduction */
        /** @var Station $station */
        $stationCommentVenirUnifieController = new StationCommentVenirUnifieController();
        $stationCommentVenirUnifieController->setContainer($this->container);
        $em = $this->getDoctrine()->getEntityManager();

        foreach ($stationUnifie->getStations() as $station) {
            // si on choisit de lié la carte ID de la mère à la station
            if (!empty($request->get('cboxStationCommentVenir_' . $station->getSite()->getId()))) {
                $oldCVUnifie = $station->getStationCommentVenir()->getStationCommentVenirUnifie();
                $station->getStationCommentVenir()->removeStation($station);
//                    $station->setStationCommentVenir(null);

                $em->refresh($station->getStationMere()->getStationCommentVenir());
                $station->setStationCommentVenir($station->getStationMere()->getStationCommentVenir());
                if ($station->getStationMere()->getStationCommentVenir()->getStationCommentVenirUnifie() != $oldCVUnifie) {
                    $this->copieVersSites($station->getStationUnifie());
                    if (!empty($oldCVUnifie)) {
//                        dump($oldCVUnifie);
                        $stationCommentVenirUnifieController->deleteEntity($oldCVUnifie);
                    }
                }

            } else {
                //
                if (!empty($station->getStationMere()) && $station->getStationMere()->getStationCommentVenir() === $station->getStationCommentVenir()) {
                    // OIn fait ça
                    $cVMere = $station->getStationMere()->getStationCommentVenir();
                    $cVOld = $station->getStationCommentVenir();

                    $newCV = new StationCommentVenir();
                    foreach ($cVOld->getTraductions() as $traduction) {
                        $newTrad = new StationCommentVenirTraduction();
                        $newTrad
                            ->setLangue($traduction->getLangue())
                            ->setEnTrain($traduction->getEnTrain())
                            ->setEnVoiture($traduction->getEnVoiture())
                            ->setEnAvion($traduction->getEnAvion())
                            ->setDistancesGrandesVilles($traduction->getDistancesGrandesVilles());
                        $newCV->addTraduction($newTrad);
                    }
                    foreach ($cVOld->getGrandeVilles() as $grandeVille) {
                        $newGrandeVille = new StationCommentVenirGrandeVille();
                        $newGrandeVille->setGrandeVille($grandeVille->getGrandeVille());
                        $newCV->addGrandeVille($newGrandeVille);
                    }
                    $newCV
                        ->setSite($station->getStationCommentVenir()->getSite());
                    $em->persist($newCV);
                    $station->setStationCommentVenir($newCV);

                    $em->refresh($cVMere);
                    foreach ($cVMere->getTraductions() as $traduction) {
                        $em->refresh($traduction);
                    }
                    foreach ($cVMere->getGrandeVilles() as $grandeVille) {
                        $em->refresh($grandeVille);
                    }
                }
            }
        }

        foreach ($stationUnifie->getStations() as $station) {

            if (!empty($station->getStationCommentVenir()->getStationCommentVenirUnifie())) {
                $stationCommentVenirUnifieController->editEntity($station->getStationCommentVenir()->getStationCommentVenirUnifie());
            } else {
                $stationCommentVenirUnifieController->newEntity($station);
            }

            $em->persist($station);
//                $em->flush();
        }

    }

    private function descriptionEdit(Request $request, StationUnifie $stationUnifie)
    {
        /** @var StationDescriptionTraduction $traduction */
        /** @var Station $station */
        $stationDescriptionUnifieController = new StationDescriptionUnifieController();
        $stationDescriptionUnifieController->setContainer($this->container);
        $em = $this->getDoctrine()->getEntityManager();

        foreach ($stationUnifie->getStations() as $station) {
            // si on choisit de lié la carte ID de la mère à la station
            if (!empty($request->get('cboxStationDescription_' . $station->getSite()->getId()))) {
                $oldCVUnifie = $station->getStationDescription()->getStationDescriptionUnifie();
                $station->getStationDescription()->removeStation($station);
//                    $station->setStationDescription(null);

                $em->refresh($station->getStationMere()->getStationDescription());
                $station->setStationDescription($station->getStationMere()->getStationDescription());
                if ($station->getStationMere()->getStationDescription()->getStationDescriptionUnifie() != $oldCVUnifie) {
                    $this->copieVersSites($station->getStationUnifie());
                    if (!empty($oldCVUnifie)) {
//                        dump($oldCVUnifie);
                        $stationDescriptionUnifieController->deleteEntity($oldCVUnifie);
                    }
                }

            } else {
                //
                if (!empty($station->getStationMere()) && $station->getStationMere()->getStationDescription() === $station->getStationDescription()) {
                    // OIn fait ça
                    $cVMere = $station->getStationMere()->getStationDescription();
                    $cVOld = $station->getStationDescription();

                    $newCV = new StationDescription();
                    foreach ($cVOld->getTraductions() as $traduction) {
                        $newTrad = new StationDescriptionTraduction();
                        $newTrad
                            ->setLangue($traduction->getLangue())
                            ->setAccroche($traduction->getAccroche())
                            ->setGeneralite($traduction->getGeneralite());
                        $newCV->addTraduction($newTrad);
                    }
                    $newCV
                        ->setSite($station->getStationDescription()->getSite());
                    $em->persist($newCV);
                    $station->setStationDescription($newCV);

                    $em->refresh($cVMere);
                    foreach ($cVMere->getTraductions() as $traduction) {
                        $em->refresh($traduction);
                    }
                }
            }
        }

        foreach ($stationUnifie->getStations() as $station) {

            if (!empty($station->getStationDescription()->getStationDescriptionUnifie())) {
                $stationDescriptionUnifieController->editEntity($station->getStationDescription()->getStationDescriptionUnifie());
            } else {
                $stationDescriptionUnifieController->newEntity($station);
            }

            $em->persist($station);
//                $em->flush();
        }

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
        $stationCommentVenirUnifieController = new StationCommentVenirUnifieController();
        $stationCommentVenirUnifieController->setContainer($this->container);
        $stationDescriptionUnifieController = new StationDescriptionUnifieController();
        $stationDescriptionUnifieController->setContainer($this->container);

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

                    if (!empty($stationUnifieSite)) {
                        $emSite->remove($stationUnifieSite);
                        $emSite->flush();
                    }
                }
                $arrayStationCarteIdentiteUnifies = new ArrayCollection();
                $arrayStationCommentVenirUnifies = new ArrayCollection();
                $arrayStationDescriptionUnifies = new ArrayCollection();
                /** @var Station $station */
                foreach ($stationUnifie->getStations() as $station) {
                    if (empty($station->getStationMere()) || (!empty($station->getStationMere()) && $station->getStationCarteIdentite() != $station->getStationMere()->getStationCarteIdentite())) {
                        $arrayStationCarteIdentiteUnifies->add($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                    }
                    if (empty($station->getStationMere()) || !empty($station->getStationMere()) && $station->getStationCommentVenir() != $station->getStationMere()->getStationCommentVenir()) {
                        $arrayStationCommentVenirUnifies->add($station->getStationCommentVenir()->getStationCommentVenirUnifie());
                    }
                    if (empty($station->getStationMere()) || !empty($station->getStationMere()) && $station->getStationDescription() != $station->getStationMere()->getStationDescription()) {
                        $arrayStationDescriptionUnifies->add($station->getStationDescription()->getStationDescriptionUnifie());
                    }
                }


//                $em = $this->getDoctrine()->getManager();

                $em->remove($stationUnifie);

                foreach ($arrayStationCarteIdentiteUnifies as $stationCarteIdentiteUnify) {
                    $stationCarteIdentiteUnifieController->deleteEntity($stationCarteIdentiteUnify);
                }
                foreach ($arrayStationCommentVenirUnifies as $stationCommentVenirUnify) {
                    $stationCommentVenirUnifieController->deleteEntity($stationCommentVenirUnify);
                }
                foreach ($arrayStationDescriptionUnifies as $stationDescriptionUnify) {
                    $stationDescriptionUnifieController->deleteEntity($stationDescriptionUnify);
                }

                $em->flush();

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
