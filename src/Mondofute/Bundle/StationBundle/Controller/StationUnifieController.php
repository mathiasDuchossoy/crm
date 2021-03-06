<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use Aamant\Distance\Distance;
use Aamant\Distance\Providers\GoogleMapProvider;
use Application\Sonata\MediaBundle\Entity\Media;
use ArrayIterator;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\GrandeVille;
use Mondofute\Bundle\GeographieBundle\Entity\GrandeVilleTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenir;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie;
use Mondofute\Bundle\StationBundle\Entity\StationDateVisibilite;
use Mondofute\Bundle\StationBundle\Entity\StationDescription;
use Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie;
use Mondofute\Bundle\StationBundle\Entity\StationLabel;
use Mondofute\Bundle\StationBundle\Entity\StationTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationUnifie;
use Mondofute\Bundle\StationBundle\Entity\StationVisuel;
use Mondofute\Bundle\StationBundle\Entity\StationVisuelTraduction;
use Mondofute\Bundle\StationBundle\Entity\TypeTaxeSejour;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findAll();
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $ciUnifies = $emSite->getRepository(StationCarteIdentiteUnifie::class)->findAll();
//            foreach ($ciUnifies as $ciUnify){
//                $emSite->remove($ciUnify);
//            }
//            $ciUnifies = $emSite->getRepository(StationCommentVenirUnifie::class)->findAll();
//            foreach ($ciUnifies as $ciUnify){
//                $emSite->remove($ciUnify);
//            }
//            $ciUnifies = $emSite->getRepository(StationDescriptionUnifie::class)->findAll();
//            foreach ($ciUnifies as $ciUnify){
//                $emSite->remove($ciUnify);
//            }
//            $ciUnifies = $emSite->getRepository(StationUnifie::class)->findAll();
//            foreach ($ciUnifies as $ciUnify){
//                $emSite->remove($ciUnify);
//            }
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofuteStationBundle:StationUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'station_station_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteStationBundle:StationUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteStation/stationunifie/index.html.twig', array(
            'stationUnifies' => $unifies,
            'pagination' => $pagination
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
        /** @var Station $station */
        foreach ($stationUnifie->getStations() as $station) {
            $station->setStationDeSki($em->find(OuiNonNC::class, 3));
        }
        $this->stationsSortByAffichage($stationUnifie);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationUnifieType', $stationUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));

        $form->handleRequest($request);

        // *******************************
        // **** VALIDATION FORMULAIRE ****
        // *******************************
        if ($form->isSubmitted() && $form->isValid()) {
            $this->deleteDateVisibiliteCrm($stationUnifie);
            $this->gestionDateVisibilite($stationUnifie);

            /** @var Station $entity */
            foreach ($stationUnifie->getStations() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            /** @var Station $station */
            foreach ($stationUnifie->getStations() as $station) {
                if (empty($station->getStationMere())) {
                    $station
                        ->setPhotosParent(false)
                        ->setVideosParent(false);
                }
            }

            // Récupérer le controleur et lui donner le container de celui dans lequel on est
            $stationCarteIdentiteController = new StationCarteIdentiteUnifieController();
            $stationCarteIdentiteController->setContainer($this->container);

            $commentVenirController = new StationCommentVenirUnifieController();
            $commentVenirController->setContainer($this->container);

            $descriptionController = new StationDescriptionUnifieController();
            $descriptionController->setContainer($this->container);

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

            // ***** Gestion des Medias *****
            foreach ($request->get('station_unifie')['stations'] as $key => $station) {
                if (!empty($stationUnifie->getStations()->get($key)) && $stationUnifie->getStations()->get($key)->getSite()->getCrm() == 1) {
                    $stationCrm = $stationUnifie->getStations()->get($key);
                    if (!empty($station['visuels'])) {
                        foreach ($station['visuels'] as $keyVisuel => $visuel) {
                            /** @var StationVisuel $visuelCrm */
                            $visuelCrm = $stationCrm->getVisuels()[$keyVisuel];
                            $visuelCrm->setActif(true);
                            $visuelCrm->setStation($stationCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Station $stationSite */
                                    $stationSite = $stationUnifie->getStations()->filter(function (Station $element) use (
                                        $site
                                    ) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($stationSite)) {
//                                      $typeVisuel = (new ReflectionClass($visuelCrm))->getShortName();
                                        $typeVisuel = (new ReflectionClass($visuelCrm))->getName();

                                        /** @var StationVisuel $stationVisuel */
                                        $stationVisuel = new $typeVisuel();
                                        $stationVisuel->setStation($stationSite);
                                        $stationVisuel->setVisuel($visuelCrm->getVisuel());
                                        $stationSite->addVisuel($stationVisuel);
                                        foreach ($visuelCrm->getTraductions() as $traduction) {
                                            $traductionSite = new StationVisuelTraduction();
                                            /** @var StationVisuelTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $stationVisuel->addTraduction($traductionSite);
                                        }
                                        if (!empty($visuel['sites']) && in_array($site->getId(), $visuel['sites'])) {
                                            $stationVisuel->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $this->gestionStationLabel($stationUnifie);

            $this->gestionTaxeSejour($stationUnifie);

            $em->persist($stationUnifie);
            $em->flush();

//            if (!$error) {
            foreach ($stationUnifie->getStations() as $station) {
                $stationCarteIdentiteController->copieVersSites($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                $commentVenirController->copieVersSites($station->getStationCommentVenir()->getStationCommentVenirUnifie());
                $descriptionController->copieVersSites($station->getStationDescription()->getStationDescriptionUnifie());
            }
            $this->copieVersSites($stationUnifie);

            $this->addFlash('success', 'La station a bien été créé.');

            return $this->redirectToRoute('station_station_edit', array('id' => $stationUnifie->getId()));
//            }
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
                        if ($stationCommentVenir->getTraductions()->filter(function (
                            StationCommentVenirTraduction $element
                        ) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationCommentVenirTraduction();
                            $traduction->setLangue($langue);
                            $stationCommentVenir->addTraduction($traduction);
                        }
                    }

//                    foreach ($grandeVilles as $grandeVille) {
//                        if ($stationCommentVenir->getGrandeVilles()->filter(function (StationCommentVenirGrandeVille $element) use ($grandeVille) {
//                            return $element->getGrandeVille() == $grandeVille;
//                        })->isEmpty()
//                        ) {
//                            $stationCommentVenirGranceVille = new StationCommentVenirGrandeVille();
//                            $stationCommentVenirGranceVille->setGrandeVille($grandeVille);
//                            $stationCommentVenirGranceVille->setStationCommentVenir($stationCommentVenir);
//                            $stationCommentVenir->addGrandeVille($stationCommentVenirGranceVille);
//                        }
//                    }
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
                        if ($stationDescription->getTraductions()->filter(function (
                            StationDescriptionTraduction $element
                        ) use ($langue) {
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

//                foreach ($grandeVilles as $grandeVille) {
//                    $stationCommentVenirGranceVille = new StationCommentVenirGrandeVille();
//                    $stationCommentVenirGranceVille->setGrandeVille($grandeVille);
//                    $stationCommentVenir->addGrandeVille($stationCommentVenirGranceVille);
//                }

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
     * @param StationUnifie $stationUnifie
     */
    private function deleteDateVisibiliteCrm($stationUnifie)
    {
        /** @var Station $stationCrm */
        $stationCrm = $stationUnifie->getStations()->filter(function (Station $element) {
            return $element->getSite()->getCrm() == 1;
        })->first();
        $stationCrm->setDateVisibilite();
    }

    /**
     * @param StationUnifie $stationUnifie
     */
    private function gestionDateVisibilite($stationUnifie)
    {
        $now = new DateTime();
        /** @var Station $station */
        foreach ($stationUnifie->getStations() as $station) {
            if ($station->getSite()->getCrm() === false) {
                $stationDateVisibilite = $station->getDateVisibilite();
                if (!empty($stationDateVisibilite)) {
                    if (empty($stationDateVisibilite->getDateDebut()) && empty($stationDateVisibilite->getDateFin())) {
                        $station->setDateVisibilite();
                    } else if (empty($stationDateVisibilite->getDateDebut())) {
                        if ($stationDateVisibilite->getDateFin() < $now) {
                            $stationDateVisibilite->setDateDebut($stationDateVisibilite->getDateFin());
                        } else {
                            $stationDateVisibilite->setDateDebut($now);
                        }
                    } else {
                        if ($stationDateVisibilite->getDateDebut() > $now) {
                            $stationDateVisibilite->setDateFin($stationDateVisibilite->getDateDebut());
                        } else {
                            $stationDateVisibilite->setDateFin($now);
                        }
                    }
                }
            }
        }
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
            if (!empty($request->get('cboxStationCI_' . $station->getSite()->getId())) && !empty($station->getStationMere())) {
                if (!empty($station->getStationMere())) {
                    $station->setStationCarteIdentite($station->getStationMere()->getStationCarteIdentite());
                }
            } else {
                // sinon on on en créé une nouvelle
                $stationCarteIdentiteUnifie = $stationCarteIdentiteController->newEntity($station);

                $site = $station->getSite();
                $stationCarteIdentite = $stationCarteIdentiteUnifie->getStationCarteIdentites()->filter(function (
                    StationCarteIdentite $element
                ) use ($site) {
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
            if (!empty($request->get('cboxStationCommentVenir_' . $station->getSite()->getId())) && !empty($station->getStationMere())) {
                if (!empty($station->getStationMere())) {
                    $station->setStationCommentVenir($station->getStationMere()->getStationCommentVenir());
                }
            } else {
                // sinon on on en créé une nouvelle
                $stationCommentVenirUnifie = $stationCommentVenirController->newEntity($station);

                $site = $station->getSite();
                $stationCommentVenir = $stationCommentVenirUnifie->getStationCommentVenirs()->filter(function (
                    StationCommentVenir $element
                ) use ($site) {
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
            if (!empty($request->get('cboxStationDescription_' . $station->getSite()->getId())) && !empty($station->getStationMere())) {
                if (!empty($station->getStationMere())) {
                    $station->setStationDescription($station->getStationMere()->getStationDescription());
                }
            } else {
                // sinon on on en créé une nouvelle
                $stationDescriptionUnifie = $stationDescriptionController->newEntity($station);

                $site = $station->getSite();
                $stationDescription = $stationDescriptionUnifie->getStationDescriptions()->filter(function (
                    StationDescription $element
                ) use ($site) {
                    return $site == $element->getSite();
                })->first();
                $station->setStationDescription($stationDescription);
            }
        }
    }

    /**
     * @param StationUnifie $stationUnifie
     */
    private function gestionStationLabel($stationUnifie)
    {
        /** @var Station $stationSite */
        /** @var Station $stationCrm */
        $stationCrm = $stationUnifie->getStations()->filter(function (Station $element) {
            return $element->getSite()->getCrm() == 1;
        })->first();
        $stationSites = $stationUnifie->getStations()->filter(function (Station $element) {
            return $element->getSite()->getCrm() == 0;
        });
        foreach ($stationCrm->getStationLabels() as $stationLabel) {
            foreach ($stationSites as $stationSite) {
                $stationLabelSite = $stationSite->getStationLabels()->filter(function (StationLabel $element) use (
                    $stationLabel
                ) {
                    return $element == $stationLabel;
                })->first();
                if (false === $stationLabelSite) {
                    $stationSite->addStationLabel($stationLabel);
                }
            }
        }
    }

    /**
     * @param StationUnifie $stationUnifie
     */
    private function gestionTaxeSejour($stationUnifie)
    {

        foreach ($stationUnifie->getStations() as $station) {
            if ($station->getTypeTaxeSejour() != TypeTaxeSejour::prix) {
                $station->setTaxeSejourPrix(null)->setTaxeSejourAge(null);
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param StationUnifie $entity
     */
    private function copieVersSites(StationUnifie $entity, $originalStationVisuels = null)
    {
        /** @var EntityManager $emSite */
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
                    $photosParent = $station->getPhotosParent();
                    $videosParent = $station->getVideosParent();
                } else {
                    $stationMere = null;
                    $photosParent = false;
                    $videosParent = false;
                }

                if (!empty($station->getStationCarteIdentite())) {
                    $stationCarteIdentite = $emSite->getRepository(StationCarteIdentite::class)->findOneBy(array('stationCarteIdentiteUnifie' => $station->getStationCarteIdentite()->getStationCarteIdentiteUnifie()));
                } else {
                    $stationCarteIdentite = null;
                }
                if (!empty($station->getStationCommentVenir())) {
                    $stationCommentVenirSite = $emSite->getRepository(StationCommentVenir::class)->findOneBy(array('stationCommentVenirUnifie' => $station->getStationCommentVenir()->getStationCommentVenirUnifie()));
                    /** @var StationCommentVenir $stationCommentVenirSite */
                    if (!empty($stationCommentVenirSite)) {
                        foreach ($stationCommentVenirSite->getGrandeVilles() as $grandeVilleSite) {
                            $grandeVille = $station->getStationCommentVenir()->getGrandeVilles()->filter(function (
                                GrandeVille $element
                            ) use ($grandeVilleSite) {
                                return $element->getId() == $grandeVilleSite->getId();
                            })->first();
                            if (empty($grandeVille)) {
                                $stationCommentVenirSite->removeGrandeVille($grandeVilleSite);
                            }
                        }
                        foreach ($station->getStationCommentVenir()->getGrandeVilles() as $grandeVille) {
                            $grandeVilleSite = $stationCommentVenirSite->getGrandeVilles()->filter(function (
                                GrandeVille $element
                            ) use ($grandeVille) {
                                return $element->getId() == $grandeVille->getId();
                            })->first();
                            if (empty($grandeVilleSite)) {
                                $stationCommentVenirSite->addGrandeVille($emSite->find(GrandeVille::class,
                                    $grandeVille));
                            }
                        }
                    }
                } else {
                    $stationCommentVenirSite = null;
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
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }
//                if (is_null(($entitySite = $em->getRepository('MondofuteStationBundle:StationUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new StationUnifie();
//                }


//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationSite = $emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $entitySite))))) {
                    $stationSite = new Station();
                    $entitySite->addStation($stationSite);
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
                    ->setStationCommentVenir($stationCommentVenirSite)
                    ->setStationDescription($stationDescription)
                    ->setPhotosParent($photosParent)
                    ->setVideosParent($videosParent)
                    ->setActif($station->getActif())
                    ->setStationDeSki($emSite->find(OuiNonNC::class, $station->getStationDeSki()->getId()))
                    ->setTypeTaxeSejour($station->getTypeTaxeSejour())
                    ->setTaxeSejourPrix($station->getTaxeSejourPrix())
                    ->setTaxeSejourAge($station->getTaxeSejourAge());

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


                // ********** GESTION DES MEDIAS **********

                $stationVisuels = $station->getVisuels(); // ce sont les hebegementVisuels ajouté

                // si il y a des Medias pour l'station de référence
                if (!empty($stationVisuels) && !$stationVisuels->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($stationSite->getVisuels()) && !$stationSite->getVisuels()->isEmpty()) {
                        // on ajoute les hébergementVisuels dans un tableau afin de travailler dessus
                        $stationVisuelSites = new ArrayCollection();
                        foreach ($stationSite->getVisuels() as $stationvisuelSite) {
                            $stationVisuelSites->add($stationvisuelSite);
                        }
                        // on parcourt les hébergmeentVisuels de la base
                        /** @var StationVisuel $stationVisuel */
                        foreach ($stationVisuels as $stationVisuel) {
                            // *** récupération de l'hébergementVisuel correspondant sur la bdd distante ***
                            // récupérer l'stationVisuel original correspondant sur le crm
                            /** @var ArrayCollection $originalStationVisuels */
                            $originalStationVisuel = $originalStationVisuels->filter(function (StationVisuel $element
                            ) use ($stationVisuel) {
                                return $element->getVisuel() == $stationVisuel->getVisuel();
                            })->first();
                            unset($stationVisuelSite);
                            if ($originalStationVisuel !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalStationVisuels as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalVisuel = $tab->indexOf($originalStationVisuel);

                                $stationVisuelSite = $stationVisuelSites->get($keyoriginalVisuel);
                            }
                            // *** fin récupération de l'hébergementVisuel correspondant sur la bdd distante ***

                            // si l'stationVisuel existe sur la bdd distante, on va le modifier
                            /** @var StationVisuel $stationVisuelSite */
                            if (!empty($stationVisuelSite)) {
                                // Si le visuel a été modifié
                                // (que le crm_ref_id est différent de de l'id du visuel de l'stationVisuel du crm)
                                if ($stationVisuelSite->getVisuel()->getMetadataValue('crm_ref_id') != $stationVisuel->getVisuel()->getId()) {
                                    $cloneVisuel = clone $stationVisuel->getVisuel();
                                    $cloneVisuel->setMetadataValue('crm_ref_id', $stationVisuel->getVisuel()->getId());
                                    $cloneVisuel->setContext('station_visuel_' . $station->getSite()->getLibelle());

                                    // on supprime l'ancien visuel
                                    $emSite->remove($stationVisuelSite->getVisuel());
                                    $this->deleteFile($stationVisuelSite->getVisuel());

                                    $stationVisuelSite->setVisuel($cloneVisuel);
                                }

                                $stationVisuelSite->setActif($stationVisuel->getActif());

                                // on parcourt les traductions
                                /** @var StationVisuelTraduction $traduction */
                                foreach ($stationVisuel->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var StationVisuelTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $stationVisuelSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (
                                            StationVisuelTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new StationVisuelTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class,
                                                $traduction->getLangue()->getId()));
                                        $stationVisuelSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createStationVisuel($stationVisuel, $stationSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'station de référence
                        /** @var StationVisuel $stationVisuel */
                        foreach ($stationVisuels as $stationVisuel) {
                            $this->createStationVisuel($stationVisuel, $stationSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($stationVisuelSites)) {
                        /** @var StationVisuel $stationVisuelSite */
                        foreach ($stationVisuelSites as $stationVisuelSite) {
                            $stationVisuelSite->setStation(null);
                            $emSite->remove($stationVisuelSite->getVisuel());
                            $this->deleteFile($stationVisuelSite->getVisuel());
                            $emSite->remove($stationVisuelSite);
                        }
                    }
                }
                // ********** FIN GESTION DES MEDIAS **********

                // ***** gestion station label *****
                /** @var StationLabel $stationLabel */
                foreach ($station->getStationLabels() as $stationLabel) {
                    $stationLabelSite = $stationSite->getStationLabels()->filter(function (StationLabel $element) use (
                        $stationLabel
                    ) {
                        return $element->getId() == $stationLabel->getId();
                    })->first();
                    if (false === $stationLabelSite) {
                        $stationSite->addStationLabel($emSite->find(StationLabel::class, $stationLabel->getId()));
                    }
                }
                /** @var StationLabel $stationLabelSite */
                foreach ($stationSite->getStationLabels() as $stationLabelSite) {
                    $stationLabel = $station->getStationLabels()->filter(function (StationLabel $element) use (
                        $stationLabelSite
                    ) {
                        return $element->getId() == $stationLabelSite->getId();
                    })->first();
                    if (false === $stationLabel) {
                        $stationSite->removeStationLabel($stationLabelSite);
                    }
                }
                // ***** fin gestion station label *****

                // *** gestion date visibilite ***
                if (!empty($station->getDateVisibilite())) {
                    if (empty($dateVisibilite = $stationSite->getDateVisibilite())) {
                        $dateVisibilite = new StationDateVisibilite();
                        $stationSite->setDateVisibilite($dateVisibilite);
                    }
                    $dateVisibilite->setDateDebut($station->getDateVisibilite()->getDateDebut())
                        ->setDateFin($station->getDateVisibilite()->getDateFin());
                } else {
                    $stationSite->setDateVisibilite();
                }
                // *** fin gestion date visibilite ***

                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterStationUnifieSiteDistant($entity->getId(), $entity->getStations());
    }

    private function deleteFile($visuel)
    {
        /** @var Media $visuel */
        if (file_exists($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg')) {
            unlink($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg');
        }
    }

    /**
     * Création d'un nouveau stationVisuel
     * @param StationVisuel $stationVisuel
     * @param Station $stationSite
     * @param EntityManager $emSite
     */
    private function createStationVisuel(StationVisuel $stationVisuel, Station $stationSite, EntityManager $emSite)
    {
        /** @var StationVisuel $stationVisuelSite */
        // on récupère la classe correspondant au visuel (photo ou video)
        $typeVisuel = (new ReflectionClass($stationVisuel))->getName();
        // on cré un nouveau StationVisuel on fonction du type
        $stationVisuelSite = new $typeVisuel();
        $stationVisuelSite->setStation($stationSite);
        $stationVisuelSite->setActif($stationVisuel->getActif());
        // on lui clone l'image
        $cloneVisuel = clone $stationVisuel->getVisuel();

        // **** récupération du visuel physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneVisuel->getProviderName());
        $provider->getReferenceImage($cloneVisuel);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneVisuel->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneVisuel));
        $cloneVisuel->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneVisuel));

        $cloneVisuel->setProviderReference($stationVisuel->getVisuel()->getProviderReference());
        $cloneVisuel->setName($stationVisuel->getVisuel()->getName());
        // **** fin récupération du visuel physique ****

        // on donne au nouveau visuel, le context correspondant en fonction du site
        $cloneVisuel->setContext('station_visuel_' . $stationSite->getSite()->getLibelle());
        // on lui attache l'id de référence du visuel correspondant sur la bdd crm
        $cloneVisuel->setMetadataValue('crm_ref_id', $stationVisuel->getVisuel()->getId());

        $stationVisuelSite->setVisuel($cloneVisuel);

        $stationSite->addVisuel($stationVisuelSite);
        // on ajoute les traductions correspondante
        foreach ($stationVisuel->getTraductions() as $traduction) {
            $traductionSite = new StationVisuelTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $stationVisuelSite->addTraduction($traductionSite);
        }
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
        /** @var StationCommentVenirTraduction $stationVilleFr */
        /** @var GrandeVilleTraduction $grandeVilleFr */
        /** @var GrandeVille $grandeVille */
        /** @var Station $station */
        /** @var StationCommentVenir $item */
        $deleteForm = $this->createDeleteForm($stationUnifie);
        $distances = new ArrayCollection();
        foreach ($stationUnifie->getStations() as $station) {
            foreach ($station->getStationCommentVenir()->getGrandeVilles() as $grandeVille) {
                $grandeVilleFr = $grandeVille->getTraductions()->filter(function (GrandeVilleTraduction $element) {
                    return $element->getLangue()->getCode() == 'fr_FR';
                })->first();
                $distance = new Distance(
                    new GoogleMapProvider(new CurlHttpAdapter, 'fr-FR', GoogleMapProvider::MODE_DRIVING));
                $result = $distance->distance($grandeVilleFr->getLibelle(),
                    $station->getStationCarteIdentite()->getAdresse()->getVille());
                $distances->set($grandeVille->getId(), $result);
            }
        }

        return $this->render('@MondofuteStation/stationunifie/show.html.twig', array(
            'stationUnifie' => $stationUnifie,
            'delete_form' => $deleteForm->createView(),
            'distances' => $distances
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
            ->add('delete', SubmitType::class, ['label' => 'Supprimer'])
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
            /** @var Station $entity */
            foreach ($stationUnifie->getStations() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalStationVisuels = new ArrayCollection();
        $originalVisuels = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        /** @var Station $station */
        foreach ($stationUnifie->getStations() as $station) {
            // si l'station est celui du CRM
            if ($station->getSite()->getCrm() == 1) {
                // on parcourt les stationVisuel pour les comparer ensuite
                /** @var StationVisuel $stationVisuel */
                foreach ($station->getVisuels() as $stationVisuel) {
                    // on ajoute les visuel dans la collection de sauvegarde
                    $originalStationVisuels->add($stationVisuel);
                    $originalVisuels->add($stationVisuel->getVisuel());
                }
            }
        }

        $this->ajouterStationsDansForm($stationUnifie);
//        $this->affilierEntities($stationUnifie);

        $this->stationsSortByAffichage($stationUnifie);
        $deleteForm = $this->createDeleteForm($stationUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationUnifieType',
            $stationUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'Mettre à jour',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));

        $editForm->handleRequest($request);

        // *******************************
        // **** VALIDATION FORMULAIRE ****
        // *******************************
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->deleteDateVisibiliteCrm($stationUnifie);
            $this->gestionDateVisibilite($stationUnifie);
            foreach ($stationUnifie->getStations() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                } else {
                    $entity->setActif(true);
                }
            }

            foreach ($stationUnifie->getStations() as $station) {
                if (empty($station->getStationMere())) {
                    $station
                        ->setPhotosParent(false)
                        ->setVideosParent(false);
                }
            }

            $stationCarteIdentiteUnifieController = new StationCarteIdentiteUnifieController();
            $stationCarteIdentiteUnifieController->setContainer($this->container);

            $stationCommentVenirUnifieController = new StationCommentVenirUnifieController();
            $stationCommentVenirUnifieController->setContainer($this->container);

            $stationDescriptionUnifieController = new StationDescriptionUnifieController();
            $stationDescriptionUnifieController->setContainer($this->container);


            // ************* suppression visuels *************
            // ** CAS OU L'ON SUPPRIME UN "STATION VISUEL" **
            // on récupère les StationVisuel de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Station $stationCrm */
            $stationCrm = $stationUnifie->getStations()->filter(function (Station $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $stationSites = $stationUnifie->getStations()->filter(function (Station $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newStationVisuels = new ArrayCollection();
            foreach ($stationCrm->getVisuels() as $stationVisuel) {
                $newStationVisuels->add($stationVisuel);
            }
            /** @var StationVisuel $originalStationVisuel */
            foreach ($originalStationVisuels as $key => $originalStationVisuel) {

                if (false === $newStationVisuels->contains($originalStationVisuel)) {
                    $originalStationVisuel->setStation(null);
                    $em->remove($originalStationVisuel->getVisuel());
                    $this->deleteFile($originalStationVisuel->getVisuel());
                    $em->remove($originalStationVisuel);
                    // on doit supprimer l'hébergementVisuel des autres sites
                    // on parcourt les station des sites
                    /** @var Station $stationSite */
                    foreach ($stationSites as $stationSite) {
                        $stationVisuelSite = $em->getRepository(StationVisuel::class)->findOneBy(
                            array(
                                'station' => $stationSite,
                                'visuel' => $originalStationVisuel->getVisuel()
                            ));
                        if (!empty($stationVisuelSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($stationVisuelSite->getStation()->getSite()->getLibelle());
                            $stationSite = $emSite->getRepository(Station::class)->findOneBy(
                                array(
                                    'stationUnifie' => $stationVisuelSite->getStation()->getStationUnifie()
                                ));
                            $stationVisuelSiteSites = new ArrayCollection($emSite->getRepository(StationVisuel::class)->findBy(
                                array(
                                    'station' => $stationSite
                                ))
                            );
                            $stationVisuelSiteSite = $stationVisuelSiteSites->filter(function (StationVisuel $element)
                            use ($stationVisuelSite) {
//                            return $element->getVisuel()->getProviderReference() == $stationVisuelSite->getVisuel()->getProviderReference();
                                return $element->getVisuel()->getMetadataValue('crm_ref_id') == $stationVisuelSite->getVisuel()->getId();
                            })->first();
                            if (!empty($stationVisuelSiteSite)) {
                                $emSite->remove($stationVisuelSiteSite->getVisuel());
                                $this->deleteFile($stationVisuelSiteSite->getVisuel());
                                $stationVisuelSiteSite->setStation(null);
                                $emSite->remove($stationVisuelSiteSite);
                                $emSite->flush();
                            }
                            $stationVisuelSite->setStation(null);
                            $em->remove($stationVisuelSite->getVisuel());
                            $this->deleteFile($stationVisuelSite->getVisuel());
                            $em->remove($stationVisuelSite);
                        }
                    }
                }
            }
            // ************* fin suppression visuels *************

            // ***** carte d'identité *****
            $this->carteIdentiteEdit($request, $stationUnifie);
            // ***** fin carte d'identité *****

            // ***** comment venir *****
            $this->commentVenirEdit($request, $stationUnifie);
            // ***** fin comment venir *****

            // ***** comment venir *****
            $this->descriptionEdit($request, $stationUnifie);
            // ***** fin comment venir *****


            // ***** Gestion des Medias *****
            // CAS D'UN NOUVEAU 'STATION VISUEL' OU DE MODIFICATION D'UN "STATION VISUEL"
            /** @var StationVisuel $stationVisuel */
            // tableau pour la suppression des anciens visuels
            $visuelToRemoveCollection = new ArrayCollection();
            $keyCrm = $stationUnifie->getStations()->indexOf($stationCrm);
            // on parcourt les stationVisuels de l'station crm
            foreach ($stationCrm->getVisuels() as $key => $stationVisuel) {
                // on active le nouveau stationVisuel (CRM) => il doit être toujours actif
                $stationVisuel->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementVisuel pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Station $stationSite */
                        $stationSite = $stationUnifie->getStations()->filter(function (Station $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($stationSite)) {
                            // on réinitialise la variable
                            unset($stationVisuelSite);
                            // s'il ne s'agit pas d'un nouveau stationVisuel
                            if (!empty($stationVisuel->getId())) {
                                // on récupère l'stationVisuel pour le modifier
                                $stationVisuelSite = $em->getRepository(StationVisuel::class)->findOneBy(array(
                                    'station' => $stationSite,
                                    'visuel' => $originalVisuels->get($key)
                                ));
                            }
                            // si l'stationVisuel est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($stationVisuel->getId()) || empty($stationVisuelSite)) {
                                // on récupère la classe correspondant au visuel (photo ou video)
                                $typeVisuel = (new ReflectionClass($stationVisuel))->getName();
                                // on créé un nouveau StationVisuel on fonction du type
                                /** @var StationVisuel $stationVisuelSite */
                                $stationVisuelSite = new $typeVisuel();
                                $stationVisuelSite->setStation($stationSite);
                            }
                            // si l'hébergemenent visuel existe déjà pour le site
                            if (!empty($stationVisuelSite)) {
                                if ($stationVisuelSite->getVisuel() != $stationVisuel->getVisuel()) {
//                                    // si l'hébergementVisuelSite avait déjà un visuel
//                                    if (!empty($stationVisuelSite->getVisuel()) && !$visuelToRemoveCollection->contains($stationVisuelSite->getVisuel()))
//                                    {
//                                        // on met l'ancien visuel dans un tableau afin de le supprimer plus tard
//                                        $visuelToRemoveCollection->add($stationVisuelSite->getVisuel());
//                                    }
                                    // on met le nouveau visuel
                                    $stationVisuelSite->setVisuel($stationVisuel->getVisuel());
                                }
                                $stationSite->addVisuel($stationVisuelSite);

                                /** @var StationVisuelTraduction $traduction */
                                foreach ($stationVisuel->getTraductions() as $traduction) {
                                    /** @var StationVisuelTraduction $traductionSite */
                                    $traductionSites = $stationVisuelSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (
                                            StationVisuelTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new StationVisuelTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $stationVisuelSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementVisuel doit être actif sur le site ou non
                                if (!empty($request->get('station_unifie')['stations'][$keyCrm]['visuels'][$key]['sites']) &&
                                    in_array($site->getId(),
                                        $request->get('station_unifie')['stations'][$keyCrm]['visuels'][$key]['sites'])
                                ) {
                                    $stationVisuelSite->setActif(true);
                                } else {
                                    $stationVisuelSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'stationVisuel CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($stationVisuel->getVisuel()->getId()) && !empty($originalVisuels->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $visuelToRemoveCollection->add($originalVisuels->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $this->gestionStationLabel($stationUnifie);

            $this->gestionTaxeSejour($stationUnifie);

            $em->persist($stationUnifie);
            $em->flush();

            try {
                $error = false;
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                $error = true;
            }
            if (!$error) {

                foreach ($stationUnifie->getStations() as $station) {
                    $stationCarteIdentiteUnifieController->copieVersSites($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                    $stationCommentVenirUnifieController->copieVersSites($station->getStationCommentVenir()->getStationCommentVenirUnifie());
                    $stationDescriptionUnifieController->copieVersSites($station->getStationDescription()->getStationDescriptionUnifie());
                }
                $this->copieVersSites($stationUnifie, $originalStationVisuels);

                // on parcourt les médias à supprimer
                if (!empty($visuelToRemoveCollection)) {
                    foreach ($visuelToRemoveCollection as $item) {
                        if (!empty($item)) {
                            $this->deleteFile($item);
                            $em->remove($item);
                        }
                    }
                    $em->flush();
                }

                $this->addFlash('success', 'La station a bien été modifié.');

                return $this->redirectToRoute('station_station_edit', array('id' => $stationUnifie->getId()));
            }

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
            if (!empty($station->getStationMere()) && !empty($request->get('cboxStationCI_' . $station->getSite()->getId()))) {
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
                    $adresse->setCoordonneeGps($newGPS);
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
            if (!empty($station->getStationMere()) && !empty($request->get('cboxStationCommentVenir_' . $station->getSite()->getId()))) {
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
            if (!empty($station->getStationMere()) && !empty($request->get('cboxStationDescription_' . $station->getSite()->getId()))) {
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
        /** @var Station $station */
        $form = $this->createDeleteForm($stationUnifie);
        $form->handleRequest($request);
        $stationCarteIdentiteUnifieController = new StationCarteIdentiteUnifieController();
        $stationCarteIdentiteUnifieController->setContainer($this->container);
        $stationCommentVenirUnifieController = new StationCommentVenirUnifieController();
        $stationCommentVenirUnifieController->setContainer($this->container);
        $stationDescriptionUnifieController = new StationDescriptionUnifieController();
        $stationDescriptionUnifieController->setContainer($this->container);

        $referer = $request->headers->get('referer');

        foreach ($stationUnifie->getStations() as $station) {
            if (!$station->getStations()->isEmpty()) {
                $this->addFlash('error', 'Impossible de supprimer cette station car elle est une station mère.');
                return $this->redirect($referer);
            }
        }

        foreach ($stationUnifie->getStations() as $station) {
            if (!$station->getHebergements()->isEmpty()) {
                $this->addFlash('error', 'Impossible de supprimer cette station car elle est lié à un hébergement.');
                return $this->redirect($referer);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $stationUnifieSite = $emSite->find(StationUnifie::class, $stationUnifie->getId());

                if (!empty($stationUnifieSite)) {

                    $arrayStationCarteIdentiteUnifieSites = new ArrayCollection();
                    $arrayStationCommentVenirUnifieSites = new ArrayCollection();
                    $arrayStationDescriptionUnifieSites = new ArrayCollection();

                    /** @var Station $stationSite */
                    foreach ($stationUnifieSite->getStations() as $stationSite) {

                        if ((empty($stationSite->getStationMere()) || (!empty($stationSite->getStationMere()) && $stationSite->getStationCarteIdentite() != $stationSite->getStationMere()->getStationCarteIdentite())) && !empty($stationSite->getStationCarteIdentite())) {
                            if (!$arrayStationCarteIdentiteUnifieSites->contains($stationSite->getStationCarteIdentite()->getStationCarteIdentiteUnifie())) {
                                $arrayStationCarteIdentiteUnifieSites->add($stationSite->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                            }
//                                $stationSite->getStationCarteIdentite()->removeStation($stationSite);
//                                $stationSite->setStationCarteIdentite(null);
                        }
                        if ((empty($stationSite->getStationMere()) || (!empty($stationSite->getStationMere()) && $stationSite->getStationCommentVenir() != $stationSite->getStationMere()->getStationCommentVenir())) && !empty($stationSite->getStationCommentVenir())) {
                            if (!$arrayStationCommentVenirUnifieSites->contains($stationSite->getStationCommentVenir()->getStationCommentVenirUnifie())) {
                                $arrayStationCommentVenirUnifieSites->add($stationSite->getStationCommentVenir()->getStationCommentVenirUnifie());
                            }
//                                $stationSite->getStationCommentVenir()->removeStation($stationSite);
//                                $stationSite->setStationCommentVenir(null);
                        }
                        if ((empty($stationSite->getStationMere()) || (!empty($stationSite->getStationMere()) && $stationSite->getStationDescription() != $stationSite->getStationMere()->getStationDescription())) && !empty($stationSite->getStationDescription())) {
                            if (!$arrayStationDescriptionUnifieSites->contains($stationSite->getStationDescription()->getStationDescriptionUnifie())) {
                                $arrayStationDescriptionUnifieSites->add($stationSite->getStationDescription()->getStationDescriptionUnifie());
                            }
//                                $stationSite->getStationDescription()->removeStation($stationSite);
//                                $stationSite->setStationDescription(null);
                        }

                        // si il y a des visuels pour l'entité, les supprimer
                        if (!empty($stationSite->getVisuels())) {
                            /** @var StationVisuel $stationVisuelSite */
                            foreach ($stationSite->getVisuels() as $stationVisuelSite) {
                                $visuelSite = $stationVisuelSite->getVisuel();
                                $stationVisuelSite->setVisuel(null);
                                if (!empty($visuelSite)) {
                                    $emSite->remove($visuelSite);
                                    $this->deleteFile($visuelSite);
                                }
                            }
                        }
                    }

                    $emSite->remove($stationUnifieSite);
//                        $emSite->flush();

                    foreach ($arrayStationCarteIdentiteUnifieSites as $stationCarteIdentiteUnify) {
                        $emSite->remove($stationCarteIdentiteUnify);
                    }
                    foreach ($arrayStationCommentVenirUnifieSites as $stationCommentVenirUnify) {
                        $emSite->remove($stationCommentVenirUnify);
                    }
                    foreach ($arrayStationDescriptionUnifieSites as $stationDescriptionUnify) {
                        $emSite->remove($stationDescriptionUnify);
                    }
                    $emSite->flush();

                }
            }
            if (!empty($stationUnifie)) {

                $arrayStationCarteIdentiteUnifies = new ArrayCollection();
                $arrayStationCommentVenirUnifies = new ArrayCollection();
                $arrayStationDescriptionUnifies = new ArrayCollection();
                /** @var Station $station */
                foreach ($stationUnifie->getStations() as $station) {
                    if (!empty($station->getStationCarteIdentite()) && (empty($station->getStationMere()) || (!empty($station->getStationMere()) && $station->getStationCarteIdentite() != $station->getStationMere()->getStationCarteIdentite()))) {
                        if (!$arrayStationCarteIdentiteUnifies->contains($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie())) {
                            $arrayStationCarteIdentiteUnifies->add($station->getStationCarteIdentite()->getStationCarteIdentiteUnifie());
                        }
//                            $station->getStationCarteIdentite()->removeStation($station);
//                            $station->setStationCarteIdentite(null);
                    }
                    if (!empty($station->getStationCommentVenir()) && (empty($station->getStationMere()) || (!empty($station->getStationMere()) && $station->getStationCommentVenir() != $station->getStationMere()->getStationCommentVenir()))) {
                        if (!$arrayStationCommentVenirUnifies->contains($station->getStationCommentVenir()->getStationCommentVenirUnifie())) {
                            $arrayStationCommentVenirUnifies->add($station->getStationCommentVenir()->getStationCommentVenirUnifie());
                        }
//                            $station->getStationCommentVenir()->removeStation($station);
//                            $station->setStationCommentVenir(null);
                    }
                    if (!empty($station->getStationDescription()) && (empty($station->getStationMere()) || (!empty($station->getStationMere()) && $station->getStationDescription() != $station->getStationMere()->getStationDescription()))) {
                        if (!$arrayStationDescriptionUnifies->contains($station->getStationDescription()->getStationDescriptionUnifie())) {
                            $arrayStationDescriptionUnifies->add($station->getStationDescription()->getStationDescriptionUnifie());
                        }
//                            $station->getStationDescription()->removeStation($station);
//                            $station->setStationDescription(null);
                    }
                }

                if (!empty($stationUnifie->getStations())) {
                    /** @var Station $station */
                    foreach ($stationUnifie->getStations() as $station) {
                        // si il y a des visuels pour l'entité, les supprimer
                        if (!empty($station->getVisuels())) {
                            /** @var StationVisuel $stationVisuel */
                            foreach ($station->getVisuels() as $stationVisuel) {
                                $visuel = $stationVisuel->getVisuel();
                                $stationVisuel->setVisuel(null);
                                $em->remove($visuel);
                                $this->deleteFile($visuel);
                            }
                        }
                    }
                }

                $em->remove($stationUnifie);
//                    $em->flush();

                foreach ($arrayStationCarteIdentiteUnifies as $stationCarteIdentiteUnify) {
//                        $stationCarteIdentiteUnifieController->deleteEntity($stationCarteIdentiteUnify);
//                        dump('supprimer');
                    $em->remove($stationCarteIdentiteUnify);
                }
                foreach ($arrayStationCommentVenirUnifies as $stationCommentVenirUnify) {
//                        $stationCommentVenirUnifieController->deleteEntity($stationCommentVenirUnify);
//                        dump('supprimer');
                    $em->remove($stationCommentVenirUnify);
                }
                foreach ($arrayStationDescriptionUnifies as $stationDescriptionUnify) {
//                        $stationDescriptionUnifieController->deleteEntity($stationDescriptionUnify);
//                        dump('supprimer');
                    $em->remove($stationDescriptionUnify);
                }
                $em->flush();
//                    die;

                $this->addFlash('success', 'La station a été supprimé avec succès.');
            }
        }

        return $this->redirectToRoute('station_station_index');
    }

}
