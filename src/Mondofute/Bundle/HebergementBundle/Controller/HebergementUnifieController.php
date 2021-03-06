<?php

namespace Mondofute\Bundle\HebergementBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoHebergement;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoLogement;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteHebergement;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseurUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStationUnifie;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\HebergementBundle\Entity\Emplacement;
use Mondofute\Bundle\HebergementBundle\Entity\EmplacementHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementCoupDeCoeur;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\MotClefBundle\Entity\MotClefTraduction;
use Mondofute\Bundle\MotClefBundle\Entity\MotClefTraductionHebergement;
use Mondofute\Bundle\PasserelleBundle\Entity\CodePasserelle;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionHebergement;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\SaisonBundle\Entity\Saison;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonFournisseur;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonHebergement;
use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Mondofute\Bundle\ServiceBundle\Entity\Service;
use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement;
use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif;
use Mondofute\Bundle\ServiceBundle\Entity\TarifService;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Pays;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HebergementUnifie controller.
 *
 */
class HebergementUnifieController extends Controller
{
    /**
     * Lists all HebergementUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteHebergementBundle:HebergementUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'hebergement_hebergement_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.nom' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteHebergementBundle:HebergementUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        $formsDeletes = new  ArrayCollection();
        foreach ($unifies->getQuery()->getResult() as $unifie) {
            $formsDeletes->set($unifie->getId(), $this->createCoupdeCoeurDeleteForm($unifie)->createView());
        }


        return $this->render('@MondofuteHebergement/hebergementunifie/index.html.twig', array(
            'hebergementUnifies' => $unifies,
            'pagination' => $pagination,
            'formsDeletes' => $formsDeletes
        ));
    }

    /**
     *
     * @param HebergementUnifie $entityUnifie The HebergementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCoupdeCoeurDeleteForm(HebergementUnifie $entityUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hebergement_coup_de_coeur_delete',
                array('id' => $entityUnifie->getId())))
//            ->add('delete', SubmitType::class, array('label' => 'Supprimer coup de coeur'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Creates a new HebergementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        $sitesAEnregistrer = $request->get('sites');

        $entityUnifie = new HebergementUnifie();

        $this->ajouterHebergementsDansForm($entityUnifie);
        $this->hebergementsSortByAffichage($entityUnifie);
        $this->addSaisons($entityUnifie);

        $form = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType', $entityUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            /** @var Hebergement $entity */
            /** @var EmplacementHebergement $emplacement */
            foreach ($entityUnifie->getHebergements() as $keyHebergement => $entity) {
                foreach ($entity->getEmplacements() as $keyEmplacement => $emplacement) {
                    if (empty($request->request->get('hebergement_unifie')['hebergements'][$keyHebergement]['emplacements'][$keyEmplacement]['checkbox'])) {
                        $entity->removeEmplacement($emplacement);
//                        $em->remove($emplacement);
                    } else {
                        if (!empty($emplacement->getDistance2())) {
                            if (empty($emplacement->getDistance2()->getUnite())) {
//                                $em->remove($emplacement->getDistance2());
                                $emplacement->setDistance2();
                            }
                        }
                    }
                }
            }

            // *** gestion fournisseur hebergement ***
            /** @var FournisseurHebergement $fournisseurHebergement */
            foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                if (empty($fournisseurHebergement->getFournisseur())) {
//                    supprime le fournisseurHebergement car plus présent
                    $entityUnifie->removeFournisseur($fournisseurHebergement);
                    $em->remove($fournisseurHebergement);
                } else {
                    $fournisseurHebergement->setHebergement($entityUnifie);
                    $fournisseurHebergement->getFournisseur()->addHebergement($fournisseurHebergement);
                }
            }
            // *** fin gestion fournisseur hebergement ***

            foreach ($entityUnifie->getServices() as $key => $serviceHebergement) {
                if (empty($request->request->get('hebergement_unifie')['services'][$key]['checkbox'])) {
//                    foreach ($serviceHebergement->getTarifs() as $serviceHebergementTarif) {
////                        dump($serviceHebergementTarif);
//                        $serviceHebergement->removeTarif($serviceHebergementTarif);
//                        $em->remove($serviceHebergementTarif);
//                    }
//                    $serviceHebergement->setHebergementUnifie(null);
                    $entityUnifie->removeService($serviceHebergement);
                    $em->remove($serviceHebergement);
                } else {
                    $serviceHebergement->setHebergementUnifie($entityUnifie);
                    /** @var ServiceHebergementTarif $serviceHebergementTarif */
                    foreach ($serviceHebergement->getTarifs() as $serviceHebergementTarif) {
                        $serviceHebergementTarif->setService($serviceHebergement);
                    }
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('hebergement_unifie')['hebergements'] as $key => $entity) {
                if (!empty($entityUnifie->getHebergements()->get($key)) && $entityUnifie->getHebergements()->get($key)->getSite()->getCrm() == 1) {
                    $entityCrm = $entityUnifie->getHebergements()->get($key);
                    if (!empty($entity['visuels'])) {
                        foreach ($entity['visuels'] as $keyVisuel => $visuel) {
                            /** @var HebergementVisuel $visuelCrm */
                            $visuelCrm = $entityCrm->getVisuels()[$keyVisuel];
                            $visuelCrm->setActif(true);
                            $visuelCrm->setHebergement($entityCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Hebergement $entitySite */
                                    $entitySite = $entityUnifie->getHebergements()->filter(function (
                                        Hebergement $element
                                    ) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($entitySite)) {
//                                      $typeVisuel = (new ReflectionClass($visuelCrm))->getShortName();
                                        $typeVisuel = (new ReflectionClass($visuelCrm))->getName();

                                        /** @var HebergementVisuel $entityVisuel */
                                        $entityVisuel = new $typeVisuel();
                                        $entityVisuel->setHebergement($entitySite);
                                        $entityVisuel->setVisuel($visuelCrm->getVisuel());
                                        $entitySite->addVisuel($entityVisuel);
                                        foreach ($visuelCrm->getTraductions() as $traduction) {
                                            $traductionSite = new HebergementVisuelTraduction();
                                            /** @var HebergementVisuelTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $entityVisuel->addTraduction($traductionSite);
                                        }
                                        if (!empty($visuel['sites']) && in_array($site->getId(), $visuel['sites'])) {
                                            $entityVisuel->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            // ***** GESTION DES PRESTATIONS ANNEXE *****
            $this->creationPrestationsAnnnexe($entityUnifie);
            // ***** FIN GESTION DES PRESTATIONS ANNEXE *****

            // ***** GESTION DES CODE PROMO ***
            $this->gestionCodePromoHebergement($entityUnifie);
            // ***** FIN GESTION DES CODE PROMO ***

            // *** gestion coup de coeur ***
            $this->gestionCoupDeCoeur($entityUnifie);
            // *** fin gestion coup de coeur ***

            $this->gestionSaisons($entityUnifie);

            $em->persist($entityUnifie);
            try {
                $error = false;
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                $error = true;
            }
            if (!$error) {
                $this->copieVersSites($entityUnifie);

                // *** gestion promotionStation ***
                /** @var FournisseurHebergement $fournisseurHebergement */
                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                    $this->gestionPromotionStation($entityUnifie, $fournisseurHebergement->getFournisseur()->getId());
                }
                // *** fin gestion promotionStation ***

                // *** gestion promotionHebergement ***
                $this->gestionPromotionHebergement($entityUnifie);
                // *** fin gestion promotionHebergement ***

                // *** gestion decoteStation ***
                /** @var FournisseurHebergement $fournisseurHebergement */
                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                    $this->gestionDecoteStation($entityUnifie, $fournisseurHebergement->getFournisseur()->getId());
                }
                // *** fin gestion decoteStation ***

                // *** gestion decoteHebergement ***
                $this->gestionDecoteHebergement($entityUnifie);
                // *** fin gestion decoteHebergement ***

                $this->addFlash('success', 'l\'hébergement a bien été créé');
                return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $entityUnifie->getId()));
            }
        }
        $formView = $form->createView();
        return $this->render('@MondofuteHebergement/hebergementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $entityUnifie,
            'form' => $formView,
            'saisons' => $em->getRepository(Saison::class)->findAll()
        ));
    }

    /**
     * Ajouter les hébergements qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param HebergementUnifie $entityUnifie
     */
    private function ajouterHebergementsDansForm(HebergementUnifie $entityUnifie)
    {
        /** @var Hebergement $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        $emplacements = $em->getRepository(Emplacement::class)->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entityUnifie->getHebergements() as $entity) {
                if ($entity->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($entity->getTraductions()->filter(function (HebergementTraduction $element) use (
                            $langue
                        ) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new HebergementTraduction();
                            $traduction->setLangue($langue);
                            $entity->addTraduction($traduction);
                        }
                    }
                    /** @var Emplacement $emplacement */
                    foreach ($emplacements as $emplacement) {
                        if ($entity->getEmplacements()->filter(function (EmplacementHebergement $element) use (
                            $emplacement
                        ) {
                            return $element->getTypeEmplacement() == $emplacement;
                        })->isEmpty()
                        ) {
                            $emplacementHebergement = new EmplacementHebergement();
                            $emplacementHebergement->setTypeEmplacement($emplacement);
                            $entity->addEmplacement($emplacementHebergement);
                        }

                    }
                    $entity->triEmplacements($this->get('translator'));
                }
            }
            if (!$siteExiste) {
//                si l'hébergement n'existe pas on créer un nouvel hébergemùent
                $entity = new Hebergement();
//                création d'une adresse
                $adresse = new Adresse();
//                $adresse->setDateCreation();
                $entity->addMoyenCom($adresse);

                $entity->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new HebergementTraduction();
                    $traduction->setLangue($langue);
                    $entity->addTraduction($traduction);
                }
                foreach ($emplacements as $emplacement) {
                    $emplacementHebergement = new EmplacementHebergement();
                    $emplacementHebergement->setTypeEmplacement($emplacement);
                    $entity->addEmplacement($emplacementHebergement);
                }
                $entity->triEmplacements($this->get('translator'));
                $entityUnifie->addHebergement($entity);
            }
        }
    }

    /**
     * Classe les departements par classementAffichage
     * @param HebergementUnifie $entityUnifie
     */
    private function hebergementsSortByAffichage(HebergementUnifie $entityUnifie)
    {
        /** @var ArrayIterator $iterator */

        // Trier les hébergements en fonction de leurs ordre d'affichage
        $entities = $entityUnifie->getHebergements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $entities->getIterator();
        unset($entities);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Hebergement $a, Hebergement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $entities = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($entities);

        // remplacé les hébergements par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entityUnifie->setHebergements($entities);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $entities
     */
    private function traductionsSortByLangue($entities)
    {
        /** @var ArrayIterator $iterator */
        /** @var Hebergement $entity */
        foreach ($entities as $entity) {
            $traductions = $entity->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (HebergementTraduction $a, HebergementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $entity->setTraductions($traductions);
        }
    }

    private function addSaisons(HebergementUnifie $hebergementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $saisons = $em->getRepository(Saison::class)->findAll();
        $hebergementCrm = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) {
            return $element->getSite()->getCrm();
        })->first();
        foreach ($saisons as $saison) {
            $saisonHebergement = $hebergementCrm->getSaisonHebergements()->filter(function (SaisonHebergement $element) use ($saison) {
                return $element->getSaison() == $saison;
            })->first();
            if (false === $saisonHebergement) {
                $saisonHebergement = new SaisonHebergement();
                $hebergementCrm->addSaisonHebergement($saisonHebergement);
                $saisonHebergement->setSaison($saison);
            }
        }
    }

    private function creationPrestationsAnnnexe(HebergementUnifie $entityUnifie)
    {
        /** @var Station $stationRef */
        /** @var PrestationAnnexeHebergement $itemPrestationAnnexeHebergement */
        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
        /** @var PrestationAnnexeFournisseurUnifie $prestationAnnexeFournisseurUnifie */
        /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
        /** @var PrestationAnnexeHebergement $PrestationAnnexeHebergement */
        /** @var PrestationAnnexeStation $prestationAnnexeStation */
        /** @var FournisseurHebergement $fournisseurHebergement */
        /** @var PrestationAnnexeStationUnifie $prestationAnnexeStationUnifie */
        /** @var Hebergement $hebergement */
        /** @var Hebergement $hebergementRef */
        $em = $this->getDoctrine()->getManager();
        $hebergementRef = $entityUnifie->getHebergements()->first();
        $stationRef = $hebergementRef->getStation();
        $prestationAnnexeHebergementUnifies = new ArrayCollection();
        $siteActifCollection = new ArrayCollection();

        // *** prestation annexe station ***
        $prestationAnnexeStationUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeStationUnifie::class)->findByStationUnifie($hebergementRef->getStation()->getStationUnifie()));

        foreach ($prestationAnnexeStationUnifies as $prestationAnnexeStationUnifie) {
            foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergementUnifie);
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    $prestationAnnexeStation = $prestationAnnexeStationUnifie->getPrestationAnnexeStations()->filter(function (
                        PrestationAnnexeStation $element
                    ) use ($hebergement) {
                        return $element->getSite() == $hebergement->getSite();
                    })->first();
                    $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                    $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                    $prestationAnnexeHebergement
                        ->setHebergement($hebergement)
                        ->setFournisseur($fournisseurHebergement->getFournisseur())
                        ->setParam($prestationAnnexeStation->getParam())
                        ->setActif($prestationAnnexeStation->getActif())
                        ->setSite($hebergement->getSite());
                    if ($prestationAnnexeStation->getActif() && !$siteActifCollection->contains($hebergement->getSite())) {
                        $siteActifCollection->add($hebergement->getSite());
                    }
                }
            }
        }
        // *** fin prestation annexe station ***

        // *** prestation annexe fournisseur ***
        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
        $sites = $em->getRepository(Site::class)->findAll();

        $whereStationUnifie = $stationRef->getStationUnifie()->getId();
        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            $prestationAnnexeFournisseurUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeFournisseurUnifie::class)->findByFournisseur($fournisseurHebergement->getFournisseur(),
                null, $whereStationUnifie));
            if (!$prestationAnnexeFournisseurUnifies->isEmpty()) {
                foreach ($sites as $site) {
                    foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
                        $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (
                            PrestationAnnexeFournisseur $element
                        ) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        if (!empty($prestationAnnexeFournisseur)) {
                            foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                                $prestationAnnexeHebergement = $prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements()->filter(function (
                                    PrestationAnnexeHebergement $element
                                ) use ($prestationAnnexeFournisseur) {
                                    return (
                                        $element->getParam() == $prestationAnnexeFournisseur->getParam()
                                        and $element->getSite() == $prestationAnnexeFournisseur->getSite()
                                    );
                                })->first();
                                if (!empty($prestationAnnexeHebergement)) {
                                    if ($prestationAnnexeFournisseur->getActif()) {
                                        $prestationAnnexeHebergement->setActif($prestationAnnexeFournisseur->getActif());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        $whereStationUnifie = $stationRef->getStationUnifie()->getId();
        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            $prestationAnnexeFournisseurUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeFournisseurUnifie::class)->findByFournisseur($fournisseurHebergement->getFournisseur(),
                null, $whereStationUnifie));

            foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
                $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergementUnifie);
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (
                        PrestationAnnexeFournisseur $element
                    ) use ($hebergement) {
                        return $element->getSite() == $hebergement->getSite();
                    })->first();
                    $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                    $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                    $prestationAnnexeHebergement
                        ->setHebergement($hebergement)
                        ->setFournisseur($fournisseurHebergement->getFournisseur())
                        ->setParam($prestationAnnexeFournisseur->getParam())
                        ->setActif($prestationAnnexeFournisseur->getActif())
                        ->setSite($hebergement->getSite());
                    if ($prestationAnnexeFournisseur->getActif() && !$siteActifCollection->contains($hebergement->getSite())) {
                        $siteActifCollection->add($hebergement->getSite());
                    }
                }
            }
        }


        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            $whereStation = ' IS NULL';
            $prestationAnnexeFournisseurUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeFournisseurUnifie::class)->findByFournisseur($fournisseurHebergement->getFournisseur(),
                $whereStation));

            foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
//                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergementUnifie);
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (
                        PrestationAnnexeFournisseur $element
                    ) use ($hebergement) {
                        return $element->getSite() == $hebergement->getSite();
                    })->first();
                    $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                    $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                    $prestationAnnexeHebergement
                        ->setHebergement($hebergement)
                        ->setFournisseur($fournisseurHebergement->getFournisseur())
                        ->setParam($prestationAnnexeFournisseur->getParam())
                        ->setActif($prestationAnnexeFournisseur->getActif())
                        ->setSite($hebergement->getSite());
                    if ($prestationAnnexeFournisseur->getActif() && !$siteActifCollection->contains($hebergement->getSite())) {
                        $siteActifCollection->add($hebergement->getSite());
                    }
                }
            }
        }

        $tabPrestationAnnexeHebergements = new ArrayCollection();
        $tabPrestationAnnexeHebergementUnifies = new ArrayCollection();
        foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
//            foreach ($prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
//                $itemPrestationAnnexeHebergement = $tabPrestationAnnexeHebergements->filter(function (
//                    PrestationAnnexeHebergement $element
//                ) use ($prestationAnnexeHebergement) {
//                    return (
//                        $element->getFournisseur() == $prestationAnnexeHebergement->getFournisseur()
//                        and $element->getParam() == $prestationAnnexeHebergement->getParam()
//                        and $element->getHebergement() == $prestationAnnexeHebergement->getHebergement()
//                        and $element->getSite() == $prestationAnnexeHebergement->getSite()
//                    );
//                })->first();
//                if (false === $itemPrestationAnnexeHebergement) {
//                    $tabPrestationAnnexeHebergements->add($prestationAnnexeHebergement);
//                } else {
//                    if ($itemPrestationAnnexeHebergement->getActif()) {
//                        $prestationAnnexeHebergement->setActif(true);
//                    }
//                }
//            }
            $em->persist($prestationAnnexeHebergementUnifie);
        }

//        foreach ($tabPrestationAnnexeHebergements as $itemPrestationAnnexeHebergement) {
//            if (!$tabPrestationAnnexeHebergementUnifies->contains($itemPrestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
//                $tabPrestationAnnexeHebergementUnifies->add($itemPrestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
//            }
//        }
//
//        foreach ($tabPrestationAnnexeHebergementUnifies as $tabPrestationAnnexeHebergementUnifie) {
//            $em->persist($tabPrestationAnnexeHebergementUnifie);
//        }

    }

    /**
     * @param HebergementUnifie $entityUnifie
     */
    private function gestionCodePromoHebergement($entityUnifie)
    {
        /** @var Hebergement $hebergement */
        /** @var FournisseurHebergement $fournisseurHebergement */
        /** @var CodePromoFournisseur $codePromoFournisseur */
        $em = $this->getDoctrine()->getManager();

        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            if (empty($fournisseurHebergement->getId())) {
                $fournisseur = $fournisseurHebergement->getFournisseur();
                $codePromoFournisseurs = new ArrayCollection($em->getRepository(CodePromoFournisseur::class)->findBy(array(
                    'fournisseur' => $fournisseur->getId(),
                    'type' => 1
                )));
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    foreach ($codePromoFournisseurs as $codePromoFournisseur) {
                        if ($codePromoFournisseur->getCodePromo()->getSite() == $hebergement->getSite()) {
                            $codePromoHebergement = new CodePromoHebergement();
                            $em->persist($codePromoHebergement);
                            $codePromoHebergement
                                ->setCodePromo($codePromoFournisseur->getCodePromo())
                                ->setHebergement($hebergement)
                                ->setFournisseur($codePromoFournisseur->getFournisseur());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param HebergementUnifie $entityUnifie
     * @return ArrayCollection
     */
    private function gestionCoupDeCoeur($entityUnifie)
    {
        $coupDeCoeurRemove = new ArrayCollection();
        /** @var Hebergement $hebergement */
        foreach ($entityUnifie->getHebergements() as $hebergement) {
            $coupDeCoeur = $hebergement->getCoupDeCoeur();
            if (!empty($coupDeCoeur) && $coupDeCoeur->getDateHeureDebut() == '' && $coupDeCoeur->getDateHeureFin() == '') {
                $hebergement->setCoupDeCoeur(null);
                $coupDeCoeurRemove->add($coupDeCoeur);
            }
        }
        return $coupDeCoeurRemove;
    }

    private function gestionSaisons(HebergementUnifie $hebergementUnifie)
    {
        /** @var FournisseurHebergement $fournisseurHebergement */
        /** @var SaisonHebergement $saisonHebergementCrm */
        /** @var SaisonHebergement $saisonHebergement */
        /** @var Hebergement $hebergement */
        /** @var Hebergement $hebergementCrm */
        $em = $this->getDoctrine()->getManager();
        $saisons = $em->getRepository(Saison::class)->findAll();
        $hebergementCrm = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) {
            return $element->getSite()->getCrm();
        })->first();
        $hebergements = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) {
            return !$element->getSite()->getCrm();
        });
//        foreach ($saisons as $saison) {
//            $saisonHebergement = $hebergementCrm->getSaisonHebergements()->filter(function (SaisonHebergement $element) use ($saison) {
//                return $element->getSaison() == $saison;
//            })->first();
//            if (false === $saisonHebergement) {
//                $saisonHebergement = new SaisonHebergement();
//                $hebergementCrm->addSaisonHebergement($saisonHebergement);
//                $saisonHebergement->setSaison($saison);
//            }
//        }
        foreach ($hebergements as $hebergement) {
            foreach ($hebergementCrm->getSaisonHebergements() as $saisonHebergementCrm) {
                $saisonHebergement = $hebergement->getSaisonHebergements()->filter(function (SaisonHebergement $element) use ($saisonHebergementCrm) {
                    return $element->getSaison() == $saisonHebergementCrm->getSaison();
                })->first();
                if (false === $saisonHebergement) {
                    $saisonHebergement = new SaisonHebergement();
                    $hebergement->addSaisonHebergement($saisonHebergement);
                    $saisonHebergement->setSaison($saisonHebergementCrm->getSaison());
                }
                $saisonHebergement
                    ->setValideFiche($saisonHebergementCrm->getValideFiche())
                    ->setValideTarif($saisonHebergementCrm->getValideTarif())
                    ->setValidePhoto($saisonHebergementCrm->getValidePhoto())
                    ->setActif($saisonHebergementCrm->getActif());
            }
        }

        /** @var HebergementUnifie $hebergementUnifie */
        /** @var HebergementUnifie $hebergementUnifie2 */
        /** @var FournisseurHebergement $fournisseurHebergement2 */
        foreach ($hebergementUnifie->getFournisseurs() as $fournisseurHebergement) {
            $nbFicheTechniqueValide = new ArrayCollection();
            $nbPhotoTechniqueValide = new ArrayCollection();
            $nbTarifTechniqueValide = new ArrayCollection();
            foreach ($fournisseurHebergement->getFournisseur()->getHebergements() as $fournisseurHebergement2) {
                foreach ($fournisseurHebergement2->getHebergement()->getHebergements() as $hebergement) {
                    if ($hebergement->getSite()->getCrm()) {
                        foreach ($hebergement->getSaisonHebergements() as $saisonHebergement) {
                            if (empty($nbFicheTechniqueValide->get($saisonHebergement->getSaison()->getId()))) {
                                $nbFicheTechniqueValide->set($saisonHebergement->getSaison()->getId(), 0);
                            }
                            if ($saisonHebergement->getValideFiche()) {
                                $nbFicheTechniqueValide->set($saisonHebergement->getSaison()->getId(), $nbFicheTechniqueValide->get($saisonHebergement->getSaison()->getId()) + 1);
                            }
                            if (empty($nbPhotoTechniqueValide->get($saisonHebergement->getSaison()->getId()))) {
                                $nbPhotoTechniqueValide->set($saisonHebergement->getSaison()->getId(), 0);
                            }
                            if ($saisonHebergement->getValidePhoto()) {
                                $nbPhotoTechniqueValide->set($saisonHebergement->getSaison()->getId(), $nbPhotoTechniqueValide->get($saisonHebergement->getSaison()->getId()) + 1);
                            }
                            if (empty($nbTarifTechniqueValide->get($saisonHebergement->getSaison()->getId()))) {
                                $nbTarifTechniqueValide->set($saisonHebergement->getSaison()->getId(), 0);
                            }
                            if ($saisonHebergement->getValideTarif()) {
                                $nbTarifTechniqueValide->set($saisonHebergement->getSaison()->getId(), $nbTarifTechniqueValide->get($saisonHebergement->getSaison()->getId()) + 1);
                            }
                        }
                    }
                }
            }
            /** @var SaisonFournisseur $saisonFournisseur */
            foreach ($fournisseurHebergement->getFournisseur()->getSaisonFournisseurs() as $saisonFournisseur) {
                $saisonFournisseur->setFicheTechniques($nbFicheTechniqueValide->get($saisonFournisseur->getSaison()->getId()));
                $saisonFournisseur->setPhotosTechniques($nbPhotoTechniqueValide->get($saisonFournisseur->getSaison()->getId()));
                $saisonFournisseur->setTarifTechniques($nbTarifTechniqueValide->get($saisonFournisseur->getSaison()->getId()));
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité hébergement
     * @param HebergementUnifie $entityUnifie
     */
    private function copieVersSites(HebergementUnifie $entityUnifie, $originalHebergementVisuels = null)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var HebergementTraduction $entityTraduc */
//        Boucle sur les hébergements afin de savoir sur quel site nous devons l'enregistrer
        /** @var Hebergement $entity */
        foreach ($entityUnifie->getHebergements() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $entity->getSite()->getId()));
//                $region = $emSite->getRepository(Region::class)->findOneBy(array('regionUnifie' => $departement->getRegion()->getRegionUnifie()->getId()));
                if (!empty($entity->getStation())) {
                    $stationSite = $emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $entity->getStation()->getStationUnifie()->getId()));
                } else {
                    $stationSite = null;
                }
                if (!empty($entity->getTypeHebergement())) {
//                    $typeHebergementSite = $emSite->getRepository(TypeHebergement::class)->findOneBy(array('typeHebergementUnifie' => $entity->getTypeHebergement()->getTypeHebergementUnifie()->getId()));
                    $typeHebergementSite = $emSite->getRepository(TypeHebergement::class)->findOneBy(array('typeHebergementUnifie' => $entity->getTypeHebergement()->getTypeHebergementUnifie()));
                } else {
                    $typeHebergementSite = null;
                }
//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null($entityUnifieSite = $emSite->find(HebergementUnifie::class, $entityUnifie))) {
                    $new = true;
                    $entityUnifieSite = new HebergementUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

                /** @var FournisseurHebergement $fournisseur */
                /** @var FournisseurHebergement $fournisseurSite */
//                supprime les fournisseurHebergement du site distant
                if (!empty($entityUnifieSite->getFournisseurs())) {
                    foreach ($entityUnifieSite->getFournisseurs() as $fournisseurSite) {
                        $present = false;
                        foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
                            if ($fournisseurSite->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId() && $fournisseurSite->getHebergement()->getId() == $fournisseur->getHebergement()->getId()) {
                                $present = true;
                            }
                        }
                        if ($present == false) {
                            // *** suppression des code promo logement ***
                            foreach ($entityUnifieSite->getHebergements() as $hebergement) {
                                $codePromoHebergements = $emSite->getRepository(CodePromoHebergement::class)->findBy(array(
                                    'hebergement' => $hebergement->getId(),
                                    'fournisseur' => $fournisseurSite->getFournisseur()->getId()
                                ));
                                foreach ($codePromoHebergements as $codePromoHebergement) {
                                    $emSite->remove($codePromoHebergement);
                                }
                            }
                            // *** fin suppression des code promo logement ***
                            $this->deletePrestationAnnexeLogements($fournisseurSite, $emSite);

                            /** @var Logement $logement */
                            /** @var LogementPeriode $logementPeriode */
//                            foreach ($fournisseurSite->getLogements() as $logement)
//                            {
//                                foreach ($logement->getPeriodes() as $logementPeriode)
//                                {
//                                    $logementPeriodeLocatifs = $emSite->getRepository(LogementPeriodeLocatif::class)->findBy(array('logement' => $logement , 'periode' => $logementPeriode->getPeriode()));
//                                    foreach ($logementPeriodeLocatifs as $logementPeriodeLocatif)
//                                    {
//                                        $emSite->remove($logementPeriodeLocatif);
//                                        dump('delete');
//                                    }
//                                }
//                            }
                            $entityUnifieSite->removeFournisseur($fournisseurSite);
                            $emSite->remove($fournisseurSite);
                        }
                    }
                }
//                copie des services hebergement vers les sites distants
                /** @var ServiceHebergement $service */
                foreach ($entityUnifie->getServices() as $service) {
                    if (empty($serviceSite = $emSite->getRepository(ServiceHebergement::class)->findOneBy(array(
                        'hebergementUnifie' => $entityUnifie->getId(),
                        'service' => $service->getId(),
                    )))
                    ) {
                        $serviceSite = new ServiceHebergement();
                        $serviceSite->setHebergementUnifie($entityUnifieSite);
                        $entityUnifieSite->addService($serviceSite);

                    }
                    $serviceSite->setService($emSite->getRepository(Service::class)->find($service->getService()->getId()));

                    /** @var ServiceHebergementTarif $serviceHebergementTarif */
                    foreach ($service->getTarifs() as $serviceHebergementTarif) {
                        if (empty($serviceHebergementTarifSite = $emSite->getRepository(ServiceHebergementTarif::class)->find(
                            $serviceHebergementTarif->getId()
                        ))
                        ) {
                            $serviceHebergementTarifSite = new ServiceHebergementTarif();
                            $serviceSite->addTarif($serviceHebergementTarifSite);
                        }

                        if (empty(($tarifSite = $serviceHebergementTarifSite->getTarif()))) {
                            $tarifSite = new Tarif();
                        }
                        /** @var Tarif $tarifSite */
                        $tarifSite->setUnite($emSite->getRepository(UniteTarif::class)->find($serviceHebergementTarif->getTarif()->getUnite()->getId()))
                            ->setValeur($serviceHebergementTarif->getTarif()->getValeur());
                        $serviceHebergementTarifSite->setService($serviceSite)
                            ->setTarif($tarifSite)
                            ->setTypePeriode($emSite->getRepository(TypePeriode::class)->find($serviceHebergementTarif->getTypePeriode()->getId()));
                        $emSite->persist($tarifSite);
                        $emSite->persist($serviceHebergementTarifSite);
                    }
                    $emSite->persist($serviceSite);
                }

//                balaye les fournisseurHebergement et copie les données
                foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
                    if (empty($fournisseurSite = $emSite->getRepository(FournisseurHebergement::class)->findOneBy(array(
                        'fournisseur' => $fournisseur->getFournisseur(),
                        'hebergement' => $fournisseur->getHebergement()
                    )))
                    ) {
//                        initialise un objet
                        $fournisseurSite = new FournisseurHebergement();
                    }

                    foreach ($fournisseurSite->getReceptions() as $receptionSite) {
                        $fournisseurSite->removeReception($receptionSite);
                    }
                    foreach ($fournisseur->getReceptions() as $reception) {
                        if (empty($receptionSite = $emSite->getRepository(Reception::class)->find($reception))) {

                        } else {
                            $fournisseurSite->addReception($receptionSite);
                        }
                    }
                    /** @var FournisseurHebergementTraduction $traduction */
                    foreach ($fournisseur->getTraductions() as $traduction) {
                        if (empty($fournisseurHebergementTraduction = $emSite->getRepository(FournisseurHebergementTraduction::class)->findOneBy(array(
                            'fournisseurHebergement' => $traduction->getFournisseurHebergement(),
                            'langue' => $traduction->getLangue()
                        )))
                        ) {
                            $fournisseurHebergementTraduction = new FournisseurHebergementTraduction();
                            $fournisseurHebergementTraduction->setLangue($emSite->getRepository(Langue::class)->findOneBy(array('id' => $traduction->getLangue()->getId())));
                            $fournisseurHebergementTraduction->setFournisseurHebergement($fournisseurSite);
                        }
                        $fournisseurHebergementTraduction->setAcces($traduction->getAcces());
                        $fournisseurSite->addTraduction($fournisseurHebergementTraduction);
                    }
                    $this->dupliqueFounisseurHebergement($fournisseur, $fournisseurSite, $emSite);
                    $fournisseurSite->setHebergement($entityUnifieSite)
                        ->setFournisseur($emSite->getRepository(Fournisseur::class)->findOneBy(array('id' => $fournisseur->getFournisseur()->getId())));
                    $fournisseurSite->setRemiseClef($emSite->getRepository(RemiseClef::class)->findOneBy(array('id' => $fournisseur->getRemiseClef()->getId())));
                    $entityUnifieSite->addFournisseur($fournisseurSite);

                    // *** gestion codes passerelle ***
                    $this->gestionCodePasserelleSite($fournisseurSite, $fournisseur, $emSite);
                    // *** fin gestion codes passerelle ***
                }
//            Récupération de l'hébergement sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($entitySite = $emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $entityUnifieSite))))) {
                    $entitySite = new Hebergement();
                    $entityUnifieSite->addHebergement($entitySite);
                }

                $classementSite = !empty($entitySite->getClassement()) ? $entitySite->getClassement() : clone $entity->getClassement();
                /** @var Adresse $adresse */
                /** @var CoordonneesGPS $coordonneesGPSSite */
                /** @var Adresse $adresseSite */
                $adresse = $entity->getMoyenComs()->first();
                if (!empty($entitySite->getMoyenComs()) && !$entitySite->getMoyenComs()->isEmpty()) {
                    $adresseSite = $entitySite->getMoyenComs()->first();
//                    $adresseSite->setDateModification(new DateTime());
                } else {
                    $adresseSite = new Adresse();
//                    $adresseSite->setDateCreation();
                    $adresseSite->setCoordonneeGps(new CoordonneesGPS());
                    $entitySite->addMoyenCom($adresseSite);
                }
                $adresseSite->setVille($adresse->getVille());
                $adresseSite->setAdresse1($adresse->getAdresse1());
                $adresseSite->setAdresse2($adresse->getAdresse2());
                $adresseSite->setAdresse3($adresse->getAdresse3());
                $adresseSite->setCodePostal($adresse->getCodePostal());
                $adresseSite->setPays($emSite->find(Pays::class, $adresse->getPays()));
                $adresseSite->getCoordonneeGps()
                    ->setLatitude($adresse->getCoordonneeGps()->getLatitude())
                    ->setLongitude($adresse->getCoordonneeGps()->getLongitude())
                    ->setPrecis($adresse->getCoordonneeGps()->getPrecis());
                if (!empty($classementSite->getUnite())) {
                    $uniteSite = $emSite->getRepository(Unite::class)->findOneBy(array('id' => $entity->getClassement()->getUnite()->getId()));
                } else {
                    $uniteSite = null;
                }
                $classementSite->setValeur($entity->getClassement()->getValeur());
                $classementSite->setUnite($uniteSite);

//            copie des données hébergement
                $entitySite
                    ->setSite($site)
                    ->setStation($stationSite)
                    ->setTypeHebergement($typeHebergementSite)
                    ->setClassement($classementSite)
                    ->setHebergementUnifie($entityUnifieSite)
                    ->setActif($entity->getActif());
//                GESTION DES EMPLACEMENTS
                $this->gestionEmplacementsSiteDistant($site, $entity, $entitySite);

//            Gestion des traductions
                foreach ($entity->getTraductions() as $entityTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $entityTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty($entityTraducSite = $emSite->getRepository(HebergementTraduction::class)->findOneBy(array(
                        'hebergement' => $entitySite,
                        'langue' => $langue
                    )))
                    ) {
                        $entityTraducSite = new HebergementTraduction();
                        $entitySite->addTraduction($entityTraducSite);
                        $entityTraducSite
                            ->setLangue($langue);
                    }

//                copie des données traductions
                    $entityTraducSite
                        ->setActivites($entityTraduc->getActivites())
                        ->setAvisMondofute($entityTraduc->getActivites())
                        ->setBienEtre($entityTraduc->getBienEtre())
                        ->setNom($entityTraduc->getNom())
                        ->setPourLesEnfants($entityTraduc->getPourLesEnfants())
                        ->setRestauration($entityTraduc->getRestauration())
                        ->setAccroche($entityTraduc->getAccroche())
                        ->setGeneralite($entityTraduc->getGeneralite())
                        ->setAvisHebergement($entityTraduc->getAvisHebergement())
                        ->setAvisLogement($entityTraduc->getAvisLogement());

//                ajout a la collection de traduction de l'hébergement
                }

                // ********** GESTION DES MEDIAS **********

                $entityVisuels = $entity->getVisuels(); // ce sont les hebegementVisuels ajouté

                // si il y a des Medias pour l'hebergement de référence
                if (!empty($entityVisuels) && !$entityVisuels->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($entitySite->getVisuels()) && !$entitySite->getVisuels()->isEmpty()) {
                        // on ajoute les hébergementVisuels dans un tableau afin de travailler dessus
                        $entityVisuelSites = new ArrayCollection();
                        foreach ($entitySite->getVisuels() as $entityvisuelSite) {
                            $entityVisuelSites->add($entityvisuelSite);
                        }
                        // on parcourt les hébergmeentVisuels de la base
                        /** @var HebergementVisuel $entityVisuel */
                        foreach ($entityVisuels as $entityVisuel) {
                            // *** récupération de l'hébergementVisuel correspondant sur la bdd distante ***
                            // récupérer l'hebergementVisuel original correspondant sur le crm
                            /** @var ArrayCollection $originalHebergementVisuels */
                            $originalHebergementVisuel = $originalHebergementVisuels->filter(function (
                                HebergementVisuel $element
                            ) use ($entityVisuel) {
                                return $element->getVisuel() == $entityVisuel->getVisuel();
                            })->first();
                            unset($entityVisuelSite);
                            if ($originalHebergementVisuel !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalHebergementVisuels as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalVisuel = $tab->indexOf($originalHebergementVisuel);

                                $entityVisuelSite = $entityVisuelSites->get($keyoriginalVisuel);
                            }
                            // *** fin récupération de l'hébergementVisuel correspondant sur la bdd distante ***

                            // si l'hebergementVisuel existe sur la bdd distante, on va le modifier
                            /** @var HebergementVisuel $entityVisuelSite */
                            if (!empty($entityVisuelSite)) {
                                // Si le visuel a été modifié
                                // (que le crm_ref_id est différent de de l'id du visuel de l'hebergementVisuel du crm)
                                if ($entityVisuelSite->getVisuel()->getMetadataValue('crm_ref_id') != $entityVisuel->getVisuel()->getId()) {
                                    $cloneVisuel = clone $entityVisuel->getVisuel();
                                    $cloneVisuel->setMetadataValue('crm_ref_id', $entityVisuel->getVisuel()->getId());
                                    $cloneVisuel->setContext('hebergement_visuel_' . $entity->getSite()->getLibelle());

                                    // on supprime l'ancien visuel
                                    $emSite->remove($entityVisuelSite->getVisuel());
                                    $this->deleteFile($entityVisuelSite->getVisuel());

                                    $entityVisuelSite->setVisuel($cloneVisuel);
                                }

                                $entityVisuelSite->setActif($entityVisuel->getActif());

                                // on parcourt les traductions
                                /** @var HebergementVisuelTraduction $traduction */
                                foreach ($entityVisuel->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var HebergementVisuelTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $entityVisuelSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (
                                            HebergementVisuelTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new HebergementVisuelTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class,
                                                $traduction->getLangue()->getId()));
                                        $entityVisuelSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createHebergementVisuel($entityVisuel, $entitySite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'hebergement de référence
                        /** @var HebergementVisuel $entityVisuel */
                        foreach ($entityVisuels as $entityVisuel) {
                            $this->createHebergementVisuel($entityVisuel, $entitySite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($entityVisuelSites)) {
                        /** @var HebergementVisuel $entityVisuelSite */
                        foreach ($entityVisuelSites as $entityVisuelSite) {
                            $entityVisuelSite->setHebergement(null);
                            $emSite->remove($entityVisuelSite->getVisuel());
                            $this->deleteFile($entityVisuelSite->getVisuel());
                            $emSite->remove($entityVisuelSite);
                        }
                    }
                }
                // ********** FIN GESTION DES MEDIAS **********

                // ********** GESTION DES PRESTATIONS ANNEXE AFFECTATION **********
                // *** prestationAnnexeHebergements ***
                if (!empty($new)) {
                    $prestationAnnexeHebergementUnifies = $em->getRepository(PrestationAnnexeHebergementUnifie::class)->findByHebergementUnifie($entityUnifie->getId());
                    /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
                    /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
                    foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                        $prestationAnnexeHebergementUnifieSite = new PrestationAnnexeHebergementUnifie();
                        $emSite->persist($prestationAnnexeHebergementUnifieSite);
                        $prestationAnnexeHebergementUnifieSite->setId($prestationAnnexeHebergementUnifie->getId());
                        $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeHebergementUnifieSite));
                        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                        $prestationAnnexeHebergement = $prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements()->filter(function (
                            PrestationAnnexeHebergement $element
                        ) use ($site) {
                            return $element->getSite()->getId() == $site->getId();
                        })->first();

                        $prestationAnnexeHebergementSite = new PrestationAnnexeHebergement();
                        $prestationAnnexeHebergementUnifieSite->addPrestationAnnexeHebergement($prestationAnnexeHebergementSite);
                        $prestationAnnexeHebergementSite
                            ->setFournisseur($emSite->find(Fournisseur::class,
                                $prestationAnnexeHebergement->getFournisseur()))
                            ->setHebergement($entitySite)
                            ->setParam($emSite->find(FournisseurPrestationAnnexeParam::class,
                                $prestationAnnexeHebergement->getParam()))
                            ->setActif($prestationAnnexeHebergement->getActif())
                            ->setSite($emSite->find(Site::class, $site))
//                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class,
//                                $prestationAnnexeHebergement->getFournisseurPrestationAnnexe()))
                        ;
                    }
                }
                // *** fin prestationAnnexeHebergements ***
                // ********** FIN GESTION DES PRESTATIONS ANNEXE AFFECTATION **********


                // *** gestion code promo hebergement ***
                /** @var FournisseurHebergement $fournisseurHebergement */
                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                    $fournisseur = $fournisseurHebergement->getFournisseur();
                    $codePromoHebergements = new ArrayCollection($em->getRepository(CodePromoHebergement::class)->findBySite($fournisseur->getId(),
                        $site->getId()));
                    $codePromoHebergementSites = new ArrayCollection($emSite->getRepository(CodePromoHebergement::class)->findBySite($fournisseur->getId(),
                        $site->getId()));
                    if (!empty($codePromoHebergements) && !$codePromoHebergements->isEmpty()) {
                        /** @var CodePromoHebergement $codePromoHebergement */
                        foreach ($codePromoHebergements as $codePromoHebergement) {
                            $codePromoHebergementSite = $codePromoHebergementSites->filter(function (
                                CodePromoHebergement $element
                            ) use ($codePromoHebergement) {
                                return $element->getId() == $codePromoHebergement->getId();
                            })->first();
                            if (false === $codePromoHebergementSite) {
                                $codePromoHebergementSite = new CodePromoHebergement();
//                            $entitySite->addCodePromoFournisseurPrestationAnnex($codePromoHebergementSite);
                                $emSite->persist($codePromoHebergementSite);
                                $codePromoHebergementSite
                                    ->setId($codePromoHebergement->getId());

                                $metadata = $emSite->getClassMetadata(get_class($codePromoHebergementSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            }

                            $codePromo = $emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $codePromoHebergement->getCodePromo()->getCodePromoUnifie()));

                            $codePromoHebergementSite
                                ->setCodePromo($codePromo)
                                ->setFournisseur($emSite->find(Fournisseur::class,
                                    $codePromoHebergement->getFournisseur()))
                                ->setHebergement($entitySite);
                        }
                    }

                    if (!empty($codePromoHebergementSites) && !$codePromoHebergementSites->isEmpty()) {
                        /** @var CodePromoHebergement $codePromoHebergement */
                        foreach ($codePromoHebergementSites as $codePromoHebergementSite) {
                            $codePromoHebergement = $codePromoHebergements->filter(function (
                                CodePromoHebergement $element
                            ) use ($codePromoHebergementSite) {
                                return $element->getId() == $codePromoHebergementSite->getId();
                            })->first();
                            if (false === $codePromoHebergement) {
//                            $entitySite->removeCodePromoHebergement($codePromoHebergementSite);
                                $emSite->remove($codePromoHebergementSite);
                            }
                        }
                    }
                }
                // *** fin gestion code promo hebergement ***

                // *** gestion motClef ***
                $this->gestionMotClefTraductionHebergement($entity, $entitySite, $emSite);
                // *** fin gestion motClef ***


                // *** gestion coupDeCoeur ***
                if (empty($entity->getCoupDeCoeur())) {
                    if (!empty($entitySite->getCoupDeCoeur())) {
                        $emSite->remove($entitySite->getCoupDeCoeur());
                        $entitySite->setCoupDeCoeur(null);
                    }
                } else {
                    if (empty($coupDeCoeur = $entitySite->getCoupDeCoeur())) {
                        $coupDeCoeur = new HebergementCoupDeCoeur();
                        $entitySite->setCoupDeCoeur($coupDeCoeur);
                    }
                    $coupDeCoeur
                        ->setDateHeureDebut($entity->getCoupDeCoeur()->getDateHeureDebut())
                        ->setDateHeureFin($entity->getCoupDeCoeur()->getDateHeureFin());
                }
                // *** fin gestion coupDeCoeur ***

                $this->gestionSaisonsSite($entity, $entitySite, $emSite);

                $emSite->persist($entityUnifieSite);
                $emSite->flush();
            }
        }
        $this->ajouterHebergementUnifieSiteDistant($entityUnifie->getId(), $entityUnifie);
    }

    /**
     * @param FournisseurHebergement $fournisseurHebergement
     * @param EntityManager $em
     */
    private function deletePrestationAnnexeLogements($fournisseurHebergement, $em)
    {
        /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
        /** @var Logement $logement */
        $prestationAnnexeLogementUnifies = new ArrayCollection();
        foreach ($fournisseurHebergement->getLogements() as $logement) {
            foreach ($logement->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                if (!$prestationAnnexeLogementUnifies->contains($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie())) {
                    $prestationAnnexeLogementUnifies->add($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie());
                }
            }
        }
        foreach ($prestationAnnexeLogementUnifies as $prestationAnnexeLogementUnifie) {
            $em->remove($prestationAnnexeLogementUnifie);
        }
    }

    /**
     * @param FournisseurHebergement $fournisseur
     * @param FournisseurHebergement $fournisseurSite
     * @param EntityManager $emSite
     */
    public function dupliqueFounisseurHebergement(
        $fournisseur,
        $fournisseurSite,
        $emSite
    )
    {
//        récupération des données fournisseur
        $adresseFournisseur = $fournisseur->getAdresse();
        $telFixeFournisseur = $fournisseur->getTelFixe();
        $telMobileFournisseur = $fournisseur->getTelMobile();
        /** @var CoordonneesGPS $coordonneesGPSFournisseur */
        $coordonneesGPSFournisseur = $fournisseur->getAdresse()->getCoordonneeGps();

//        récupération des données fournisseurSite
        $adresseFournisseurSite = $fournisseurSite->getAdresse();
        $coordonneesGPSFournisseurSite = $fournisseurSite->getAdresse()->getCoordonneeGps();
        $telFixeFournisseurSite = $fournisseurSite->getTelFixe();
        $telMobileFournisseurSite = $fournisseurSite->getTelMobile();

//                    Copie des données du fournisseurHebergement
        $coordonneesGPSFournisseurSite->setLatitude($coordonneesGPSFournisseur->getLatitude())
            ->setLongitude($coordonneesGPSFournisseur->getLongitude())
            ->setPrecis($coordonneesGPSFournisseur->getPrecis());
        $adresseFournisseurSite->setAdresse1($adresseFournisseur->getAdresse1())
            ->setAdresse2($adresseFournisseur->getAdresse2())
            ->setAdresse3($adresseFournisseur->getAdresse3())
            ->setCodePostal($adresseFournisseur->getCodePostal())
            ->setVille($adresseFournisseur->getVille())
            ->setPays($emSite->find(Pays::class, $adresseFournisseur->getPays()))
            ->setCoordonneeGps($coordonneesGPSFournisseurSite);
        $telFixeFournisseurSite->setNumero($telFixeFournisseur->getNumero());
        $telMobileFournisseurSite
            ->setSmsing($telMobileFournisseur->getSmsing())
            ->setNumero($telMobileFournisseur->getNumero());
    }

    /**
     * @param FournisseurHebergement $fournisseurSite
     * @param FournisseurHebergement $fournisseur
     * @param EntityManager $emSite
     */
    private function gestionCodePasserelleSite($fournisseurSite, $fournisseur, $emSite)
    {
        /** @var CodePasserelle $codePasserelle */
        /** @var CodePasserelle $codePasserelleSite */
        /** @var SaisonCodePasserelle $saisonCodePasserelleSite */
        /** @var SaisonCodePasserelle $saisonCodePasserelle */
        foreach ($fournisseurSite->getSaisonCodePasserelles() as $saisonCodePasserelleSite) {
            $saisonCodePasserelle = $fournisseur->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saisonCodePasserelleSite) {
                return $element->getId() == $saisonCodePasserelleSite->getId();
            })->first();
            if (false === $saisonCodePasserelle) {
                $fournisseurSite->removeSaisonCodePasserelle($saisonCodePasserelleSite);
            } else {
                foreach ($saisonCodePasserelleSite->getCodePasserelles() as $codePasserelleSite) {
                    $codePasserelle = $saisonCodePasserelle->getCodePasserelles()->filter(function (CodePasserelle $element) use ($codePasserelleSite) {
                        return $element->getId() == $codePasserelleSite->getId();
                    })->first();
                    if (false === $codePasserelle) {
                        $saisonCodePasserelleSite->removeCodePasserelle($codePasserelleSite);
                        $emSite->remove($codePasserelleSite);
                    }
                }
            }
        }
        foreach ($fournisseur->getSaisonCodePasserelles() as $saisonCodePasserelle) {
            $saisonCodePasserelleSite = $fournisseurSite->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saisonCodePasserelle) {
                return $element->getId() == $saisonCodePasserelle->getId();
            })->first();
            if (false === $saisonCodePasserelleSite) {
                $saisonCodePasserelleSite = new SaisonCodePasserelle();
                $saisonCodePasserelleSite->setId($saisonCodePasserelle->getId());
                $metadata = $emSite->getClassMetadata(get_class($saisonCodePasserelleSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $fournisseurSite->addSaisonCodePasserelle($saisonCodePasserelleSite);
                $saisonCodePasserelleSite->setSaison($emSite->find(Saison::class, $saisonCodePasserelle->getSaison()));
            }
            foreach ($saisonCodePasserelle->getCodePasserelles() as $codePasserelle) {
                $codePasserelleSite = $saisonCodePasserelleSite->getCodePasserelles()->filter(function (CodePasserelle $element) use ($codePasserelle) {
                    return $element->getId() == $codePasserelle->getId();
                })->first();
                if (false === $codePasserelleSite) {
                    $codePasserelleSite = new CodePasserelle();
                    $codePasserelleSite->setId($codePasserelle->getId());
                    $metadata = $emSite->getClassMetadata(get_class($codePasserelleSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    $saisonCodePasserelleSite->addCodePasserelle($codePasserelleSite);
                }
                $codePasserelleSite->setLibelle($codePasserelle->getLibelle());
            }
        }
    }

    public function gestionEmplacementsSiteDistant(Site $site, Hebergement $entity, Hebergement $entitySite)
    {
        /** @var EmplacementHebergement $emplacement */
        /** @var EmplacementHebergement $emplacementSite */
//        Suppression des emplacements qui ne sont plus présents
        $emSite = $this->getDoctrine()->getManager($site->getLibelle());
        $emplacementsSite = $emSite->getRepository(EmplacementHebergement::class)->findBy(array('hebergement' => $entitySite));
        foreach ($emplacementsSite as $emplacementSite) {
            $present = 0;
            foreach ($entity->getEmplacements() as $emplacement) {
                if ($emplacementSite->getTypeEmplacement() == $emplacement->getTypeEmplacement()) {
                    $present = 1;
                }
            }
            if ($present == 0) {
                $emSite->remove($emplacementSite);
            }
        }

        foreach ($entity->getEmplacements() as $emplacement) {
            if (!empty(($distance1 = $emplacement->getDistance1()))) {
                $uniteSite1 = $emSite->getRepository(Unite::class)->find($distance1->getUnite());
            } else {
                $uniteSite1 = null;
            }
            if (!empty(($distance2 = $emplacement->getDistance2()))) {
                $uniteSite2 = $emSite->getRepository(Unite::class)->find($distance2->getUnite());
            } else {
                $uniteSite2 = null;
            }
            $typeEmplacementSite = $emSite->getRepository(Emplacement::class)->find($emplacement->getTypeEmplacement());
            if (empty(($emplacementSite = $emSite->getRepository(EmplacementHebergement::class)->findOneBy(array(
                'typeEmplacement' => $typeEmplacementSite,
                'hebergement' => $entitySite
            ))))
            ) {
                $emplacementSite = new EmplacementHebergement();
                if (!empty($distance1)) {
                    $distanceSite1 = new Distance();
                }
                if (!empty($distance2)) {
                    $distanceSite2 = new Distance();
                }
            } else {
                if (!empty($distance1)) {
                    if (empty(($distanceSite1 = $emplacementSite->getDistance1()))) {
                        $distanceSite1 = new Distance();
                    }
                } else {
                    if (!empty(($distanceSite1 = $emplacementSite->getDistance1()))) {
                        $emSite->remove($distanceSite1);
                        $distanceSite1 = null;
                    }
                }
                if (!empty($distance2)) {
                    if (empty(($distanceSite2 = $emplacementSite->getDistance2()))) {
                        $distanceSite2 = new Distance();
                    }
                } else {
                    if (!empty(($distanceSite2 = $emplacementSite->getDistance2()))) {
                        $emSite->remove($distanceSite2);
                        $distanceSite2 = null;
                    }
                }
            }
            if (!empty($distance1)) {
                $distanceSite1->setValeur($distance1->getValeur());
                $distanceSite1->setUnite($uniteSite1);
                $emplacementSite->setDistance1($distanceSite1);
            }
            if (!empty($distance2)) {
                $distanceSite2->setValeur($distance2->getValeur());
                $distanceSite2->setUnite($uniteSite2);
                $emplacementSite->setDistance2($distanceSite2);
            }

            $emplacementSite->setTypeEmplacement($typeEmplacementSite)
                ->setDistance1($distanceSite1)
                ->setTypeEmplacement($typeEmplacementSite)
                ->setDistance2($distanceSite2);
            $entitySite->addEmplacement($emplacementSite);
        }
        $emSite->flush();
    }

    private function deleteFile($visuel)
    {
        if (file_exists($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg')) {
            unlink($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg');
        }
    }

    /**
     * Création d'un nouveau hebergementVisuel
     * @param HebergementVisuel $entityVisuel
     * @param Hebergement $entitySite
     * @param EntityManager $emSite
     */
    private function createHebergementVisuel(
        HebergementVisuel $entityVisuel,
        Hebergement $entitySite,
        EntityManager $emSite
    )
    {
        /** @var HebergementVisuel $entityVisuelSite */
        // on récupère la classe correspondant au visuel (photo ou video)
        $typeVisuel = (new ReflectionClass($entityVisuel))->getName();
        // on cré un nouveau HebergementVisuel on fonction du type
        $entityVisuelSite = new $typeVisuel();
        $entityVisuelSite->setHebergement($entitySite);
        $entityVisuelSite->setActif($entityVisuel->getActif());
        // on lui clone l'image
        $cloneVisuel = clone $entityVisuel->getVisuel();

        // **** récupération du visuel physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneVisuel->getProviderName());
        $provider->getReferenceImage($cloneVisuel);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneVisuel->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneVisuel));
        $cloneVisuel->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneVisuel));

        $cloneVisuel->setProviderReference($entityVisuel->getVisuel()->getProviderReference());
        $cloneVisuel->setName($entityVisuel->getVisuel()->getName());
        // **** fin récupération du visuel physique ****

        // on donne au nouveau visuel, le context correspondant en fonction du site
        $cloneVisuel->setContext('hebergement_visuel_' . $entitySite->getSite()->getLibelle());
        // on lui attache l'id de référence du visuel correspondant sur la bdd crm
        $cloneVisuel->setMetadataValue('crm_ref_id', $entityVisuel->getVisuel()->getId());

        $entityVisuelSite->setVisuel($cloneVisuel);

        $entitySite->addVisuel($entityVisuelSite);
        // on ajoute les traductions correspondante
        foreach ($entityVisuel->getTraductions() as $traduction) {
            $traductionSite = new HebergementVisuelTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $entityVisuelSite->addTraduction($traductionSite);
        }
    }

    /**
     * @param Hebergement $entity
     * @param Hebergement $entitySite
     * @param EntityManager $emSite
     */
    private function gestionMotClefTraductionHebergement($entity, $entitySite, $emSite)
    {
        /** @var MotClefTraductionHebergement $motClef */
        /** @var MotClefTraductionHebergement $motClefSite */
        foreach ($entitySite->getMotClefTraductionHebergements() as $motClefSite) {
            $motClef = $entity->getMotClefTraductionHebergements()->filter(function (MotClefTraductionHebergement $element) use ($motClefSite) {
                return $element->getMotClefTraduction()->getId() == $motClefSite->getMotClefTraduction()->getId() && $element->getHebergement()->getId() == $motClefSite->getHebergement()->getId();
            })->first();
            if (false === $motClef) {
                $entitySite->removeMotClefTraductionHebergement($motClefSite);
            }
        }

        foreach ($entity->getMotClefTraductionHebergements() as $motClef) {
            $motClefTraductionHebergementSite = $entitySite->getMotClefTraductionHebergements()->filter(function (MotClefTraductionHebergement $element) use ($motClef) {
                return $element->getMotClefTraduction()->getId() == $motClef->getMotClefTraduction()->getId() && $element->getHebergement()->getId() == $motClef->getHebergement()->getId();
            })->first();
            if (false === $motClefTraductionHebergementSite) {
                $motClefTraductionHebergementSite = new MotClefTraductionHebergement();
//                $motClefSite = $emSite->find(MotClefTraductionHebergement::class, $motClef);
                $hebergementSite = $emSite->getRepository(Hebergement::class)->findOneBy(['hebergementUnifie' => $motClef->getHebergement()->getHebergementUnifie()]);
                $motClefTraductionHebergementSite->setHebergement($hebergementSite);
                $motClefTraductionSite = $emSite->getRepository(MotClefTraduction::class)->findOneBy(['motClef' => $motClef->getMotClefTraduction()->getMotClef(), 'langue' => $motClef->getMotClefTraduction()->getLangue()]);
                $motClefTraductionHebergementSite->setMotClefTraduction($motClefTraductionSite);
//                $motClefSite = $emSite->getRepository(MotClefTraductionHebergement::class)->findOneBy(['hebergement']);
                $entitySite->addMotClefTraductionHebergement($motClefTraductionHebergementSite);
            }
            $motClefTraductionHebergementSite->setClassement($motClef->getClassement());
        }
    }

    /**
     * @param Hebergement $hebergement
     * @param Hebergement $hebergementSite
     * @param EntityManager $emSite
     */
    private function gestionSaisonsSite($hebergement, $hebergementSite, $emSite)
    {
        /** @var SaisonHebergement $saisonHebergement */
        foreach ($hebergement->getSaisonHebergements() as $saisonHebergement) {
            $saisonHebergementSite = $hebergementSite->getSaisonHebergements()->filter(function (SaisonHebergement $element) use ($saisonHebergement) {
                return $element->getId() == $saisonHebergement->getId();
            })->first();
            if (false === $saisonHebergementSite) {
                $saisonHebergementSite = new SaisonHebergement();
                $hebergementSite->addSaisonHebergement($saisonHebergementSite);
                $saisonHebergementSite->setId($saisonHebergement->getId());
                $metadata = $emSite->getClassMetadata(get_class($saisonHebergementSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $saisonHebergementSite->setSaison($emSite->find(Saison::class, $saisonHebergement->getSaison()));
            }
            $saisonHebergementSite
                ->setValideFiche($saisonHebergement->getValideFiche())
                ->setValideTarif($saisonHebergement->getValideTarif())
                ->setValidePhoto($saisonHebergement->getValidePhoto())
                ->setActif($saisonHebergement->getActif());
        }
        // *** copier aussi les saisonFournisseur ***
        // parcourir les fournisseursHebergements de l'hebergementUnfie
        /** @var FournisseurHebergement $fournisseurHebergement */
        foreach ($hebergement->getHebergementUnifie()->getFournisseurs() as $fournisseurHebergement) {
            // parcourir les saisonFournisseurs de chaque fournisseur
            /** @var SaisonFournisseur $saisonFournisseur */
            foreach ($fournisseurHebergement->getFournisseur()->getSaisonFournisseurs() as $saisonFournisseur) {
                // récupérer le saisonFournisseur correspondant sur la bdd distante
                $saisonFournisseurSite = $emSite->find(SaisonFournisseur::class, $saisonFournisseur);
                // mettre à jours ses champs (Fiche techniques,Tarif techniques & Photos techniques)
                $saisonFournisseurSite
                    ->setFicheTechniques($saisonFournisseur->getFicheTechniques())
                    ->setTarifTechniques($saisonFournisseur->getTarifTechniques())
                    ->setPhotosTechniques($saisonFournisseur->getPhotosTechniques());
                // persister la mise à jours
                $emSite->persist($saisonFournisseurSite);
            }
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas d'hébergement a enregistrer
     * @param $idUnifie
     * @param $entityUnifie
     */
    private function ajouterHebergementUnifieSiteDistant($idUnifie, HebergementUnifie $entityUnifie)
    {
        /** @var ArrayCollection $entities */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($entityUnifie->getHebergements()->matching($criteres)) == 0 && (empty($emSite->getRepository(HebergementUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entityUnifie = new HebergementUnifie();
//                foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
//                    $entityUnifie->addFournisseur($fournisseur);
//                }
                $emSite->persist($entityUnifie);
                $emSite->flush();
            }
        }
    }

    /**
     * @param HebergementUnifie $entityUnifie
     */
    public function gestionPromotionStation($entityUnifie, $fournisseurId)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_promotion:promotion_station_command',
            'hebergementUnifieId' => $entityUnifie->getId(),
            'fournisseurId' => $fournisseurId
        ));
        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }

    private function gestionPromotionHebergement(HebergementUnifie $hebergementUnifie)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_promotion:promotion_hebergement_command',
            'hebergementUnifieId' => $hebergementUnifie->getId(),
        ));
        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * @param HebergementUnifie $entityUnifie
     */
    public function gestionDecoteStation($entityUnifie, $fournisseurId)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_decote:decote_station_command',
            'hebergementUnifieId' => $entityUnifie->getId(),
            'fournisseurId' => $fournisseurId
        ));
        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }

    private function gestionDecoteHebergement(HebergementUnifie $hebergementUnifie)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_decote:promotion_hebergement_command',
            'hebergementUnifieId' => $hebergementUnifie->getId(),
        ));
        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }

    public function getForCommandeLigneSejourAction($stationId, $fournisseurId)
    {
        $em = $this->getDoctrine()->getManager();

        $hebergements = $em->getRepository(Hebergement::class)->getForCommandeLigneSejour($stationId, $fournisseurId);

        $hebergementsTraduction = new ArrayCollection();
        $locale = $this->getParameter('locale');
        foreach ($hebergements as $hebergement) {
            $traduction = $hebergement->getTraductions()->filter(function (HebergementTraduction $element) use ($locale) {
                return $element->getLangue()->getCode() == $locale;
            })->first();
            $hebergementsTraduction->add(
                ['id' => $hebergement->getId(),
                    'libelle' => $traduction->getNom()]
            );
        }

        return $this->render('@MondofuteHebergement/hebergementunifie/option-hebergements-for-commande-ligne-sejour.html.twig', array(
            'hebergements' => $hebergementsTraduction
        ));
    }

    public function coupdecoeurDeleteAction(Request $request, HebergementUnifie $entityUnifie)
    {
        /** @var HebergementUnifie $entityUnifieSite */
        $em = $this->getDoctrine()->getManager();
        try {
            $form = $this->createDeleteForm($entityUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $entityUnifieSite = $emSite->find(HebergementUnifie::class, $entityUnifie->getId());
                    if (!empty($entityUnifieSite)) {
                        if (!empty($entityUnifieSite->getHebergements())) {
                            /** @var Hebergement $entitySite */
                            foreach ($entityUnifieSite->getHebergements() as $entitySite) {
                                if (!empty($entitySite->getCoupDeCoeur())) {
                                    $emSite->remove($entitySite->getCoupDeCoeur());
                                }
                            }
                            $emSite->flush();
                        }
                    }
                }
                if (!empty($entityUnifie)) {
                    if (!empty($entityUnifie->getHebergements())) {
                        /** @var Hebergement $entity */
                        foreach ($entityUnifie->getHebergements() as $entity) {
                            if (!empty($entity->getCoupDeCoeur())) {
                                $em->remove($entity->getCoupDeCoeur());
                            }
                        }
                        $em->flush();
                    }
                }
            }
        } catch (ForeignKeyConstraintViolationException $except) {
            /** @var ForeignKeyConstraintViolationException $except */
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'Impossible de supprimer le coup de coeur.');
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'Le coup de coeur pour l\'hébergement ' . $entityUnifie->getId() . ' a bien été supprimé');
        return $this->redirectToRoute('hebergement_hebergement_index');
    }

    /**
     * Creates a form to delete a HebergementUnifie entity.
     *
     * @param HebergementUnifie $entityUnifie The HebergementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(HebergementUnifie $entityUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hebergement_hebergement_delete',
                array('id' => $entityUnifie->getId())))
            ->add('delete', SubmitType::class, array('label' => 'Supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and displays a HebergementUnifie entity.
     *
     */
    public function showAction(HebergementUnifie $entityUnifie)
    {
        $deleteForm = $this->createDeleteForm($entityUnifie);
        return $this->render('@MondofuteHebergement/hebergementunifie/show.html.twig', array(
            'hebergementUnifie' => $entityUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function chargerListeServicesFournisseurAction(Request $request, $idFournisseur)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            /** @var ArrayCollection $liste */
            $liste = $em->getRepository(ListeService::class)->chargerParFournisseur($idFournisseur)->getQuery()->getArrayResult();
//            $listeArray = $liste->toArray();
//            $serializer = $this->container->get('serializer');
            $response = new Response();
//            $data = $serializer->serialize($liste,'json');
            $data = json_encode($liste); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        } else {
            return new Response();
        }
    }

    public function chargerServicesXMLAction(Request $request, $idListeService, $idHebergementUnifie = null)
    {
        if ($request->isXmlHttpRequest()) {
//        $enseigne = $request->get('enseigne');
            $em = $this->getDoctrine()->getManager();
            if ($idHebergementUnifie <= 0) {
                $entityUnifie = new HebergementUnifie();
//            $services = $em->getRepository(Service::class)->findBy(array('listeService'=>$idListeService));

            } else {
                $entityUnifie = $em->getRepository(HebergementUnifie::class)->find($idHebergementUnifie);
                if (empty($entityUnifie->getListeService()) || ($entityUnifie->getListeService()->getId() != $idListeService)) {
                    $entityUnifie->getServices()->clear();
                }
            }
            $entityUnifie->setListeService($em->getRepository(ListeService::class)->find($idListeService));
            $this->genererServiceHebergements($entityUnifie);
            $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType',
                $entityUnifie, array('locale' => $request->getLocale()));
            $html = $this->render('@MondofuteHebergement/hebergementunifie/tableau_services_hebergement.html.twig',
                array('form' => $editForm->createView()));
//        $fournisseurs = $em->getRepository('MondofuteFournisseurBundle:Fournisseur')->rechercherTypeHebergement($enseigne)->getQuery()->getArrayResult();

//            $response = new Response();
//
//            $data = json_encode(null); // formater le résultat de la requête en json
//
//            $response->headers->set('Content-Type', 'application/json');
//            $response->setContent($data);

            return $html;
        }
        return new Response();
    }

    public function genererServiceHebergements(HebergementUnifie $entityUnifie)
    {
        $services = new ArrayCollection();
        /** @var ServiceHebergement $serv */
        foreach ($entityUnifie->getServices() as $serv) {
            $services->add($serv->getService());
        }
        /** @var Service $service */
        if (!empty($entityUnifie->getListeService())) {
            foreach ($entityUnifie->getListeService()->getServices() as $service) {
                if (!($services->contains($service))) {
                    $serviceHebergement = new ServiceHebergement();
                    $serviceHebergement->setService($service);
                    $serviceHebergement->setHebergementUnifie($entityUnifie);
                    /** @var TarifService $tarifService */
                    foreach ($service->getTarifs() as $tarifService) {
                        $tarifHebergement = new ServiceHebergementTarif();
                        $tarifHebergement->setService($serviceHebergement);
                        $tarifHebergement->setTypePeriode($tarifService->getTypePeriode());
                        $tarif = new Tarif();
                        $tarif->setUnite($tarifService->getTarif()->getUnite())
                            ->setValeur($tarifService->getTarif()->getValeur());
                        $tarifHebergement->setTarif($tarif);
                        $serviceHebergement->addTarif($tarifHebergement);
                    }
                    $entityUnifie->addService($serviceHebergement);
                }
            }
        }
    }

    /**
     * Displays a form to edit an existing HebergementUnifie entity.
     *
     */
    public function editAction(Request $request, HebergementUnifie $entityUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        $originalStations = new ArrayCollection();
        foreach ($entityUnifie->getHebergements() as $hebergement) {
            $originalStations->set($hebergement->getId(), $hebergement->getStation());
        }
        $originalServices = new ArrayCollection();
        $originalTarifs = new ArrayCollection();
        /** @var ServiceHebergement $serviceHebergement */
        foreach ($entityUnifie->getServices() as $serviceHebergement) {
            foreach ($serviceHebergement->getTarifs() as $originalTarif) {
                $originalTarifs->add($originalTarif);
            }
            $originalServices->add($serviceHebergement);
        }

        $this->genererServiceHebergements($entityUnifie);

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalHebergementVisuels = new ArrayCollection();
        $originalVisuels = new ArrayCollection();
        $originalMotClefTraductionHebergements = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        /** @var Hebergement $entity */
        foreach ($entityUnifie->getHebergements() as $entity) {
            // si l'hebergement est celui du CRM
            if ($entity->getSite()->getCrm() == 1) {
                // on parcourt les hebergementVisuel pour les comparer ensuite
                /** @var HebergementVisuel $entityVisuel */
                foreach ($entity->getVisuels() as $entityVisuel) {
                    // on ajoute les visuel dans la collection de sauvegarde
                    $originalHebergementVisuels->add($entityVisuel);
                    $originalVisuels->add($entityVisuel->getVisuel());
                }
            }
            $originalMotClefTraductionHebergements->set($entity->getId(), new ArrayCollection());
            foreach ($entity->getMotClefTraductionHebergements() as $motClef) {
                $originalMotClefTraductionHebergements->get($entity->getId())->add($motClef);
            }
        }

        $this->ajouterHebergementsDansForm($entityUnifie);
//        $this->dispacherDonneesCommune($departementUnifie);
        $this->hebergementsSortByAffichage($entityUnifie);
        $deleteForm = $this->createDeleteForm($entityUnifie);
        $this->addSaisons($entityUnifie);

        $saisons = $em->getRepository(Saison::class)->findAll();
        $this->addSaisonCodePasserelle($entityUnifie, $saisons);

        $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType',
            $entityUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'mettre.a.jour',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));


        // *** récupération originals fournisseurHebergement ***
        $originalFournisseurHebergements = new ArrayCollection();
        $originalCodePasserelles = new ArrayCollection();
        /** @var FournisseurHebergement $fournisseurHebergement */
        foreach ($entityUnifie->getFournisseurs() as $keyFournisseurHebergement => $fournisseurHebergement) {
            $originalFournisseurHebergements->add($fournisseurHebergement);
            // /* originales codePasserelles
            $originalCodePasserelles->set($keyFournisseurHebergement, new ArrayCollection());
            foreach ($fournisseurHebergement->getSaisonCodePasserelles() as $key => $saisonCodePasserelle) {
                $originalCodePasserelles->get($keyFournisseurHebergement)->set($key, new ArrayCollection());
                foreach ($saisonCodePasserelle->getCodePasserelles() as $codePasserelle) {
                    $originalCodePasserelles->get($keyFournisseurHebergement)->get($key)->add($codePasserelle);
                }
            }
            // fin originales codePasserelles */
        }
        // *** fin récupération originals gestion fournisseurHebergement ***


        $editForm->handleRequest($request);

//        dump($entityUnifie->getFournisseurs()->last()->getSaisonCodePasserelles());die;

        // ********************************
        // *** VALIDATION DU FORMULAIRE ***
        // ********************************
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($entityUnifie->getHebergements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                } else {
                    $entity->setActif(true);
                }
            }

            // ************* gestion des services *************
            /** @var ServiceHebergement $serviceHebergement */
            foreach ($originalServices as $originalService) {
                if (!($entityUnifie->getServices()->contains($originalService)) || empty($entityUnifie->getServices()) || empty($originalService->getService())) {
                    /** @var ServiceHebergement $originalService */
                    /** @var ServiceHebergementTarif $originalTarif */
                    foreach ($originalTarifs as $originalTarif) {
                        if ($originalTarif->getService() == $originalService) {
//                            $originalTarifs->remove($originalTarif);
                            $em->remove($originalTarif);
                            $this->deleteTarifSites($originalTarif);
                        }
                    }
                    $em->remove($originalService);
                    $this->deleteServiceSites($originalService);
                }
            }

            //  *** gestion des tarifs ***
            foreach ($originalTarifs as $originalTarif) {
                $effacer = true;
                foreach ($entityUnifie->getServices() as $serviceHebergement) {
                    if ($serviceHebergement->getTarifs()->contains($originalTarif)) {
                        $effacer = false;
                    }
                }
                if ($effacer == true) {
                    $em->remove($originalTarif);
                    $this->deleteTarifSites($originalTarif);
                }
            }
            // *** fin gestion des tarifs ***
            foreach ($entityUnifie->getServices() as $key => $serviceHebergement) {
                if (empty($request->request->get('hebergement_unifie')['services'][$key]['checkbox'])) {
                    $entityUnifie->removeService($serviceHebergement);
                    $em->remove($serviceHebergement);
                    $this->deleteServiceSites($serviceHebergement);
                } else {
                    $serviceHebergement->setHebergementUnifie($entityUnifie);
                    /** @var ServiceHebergementTarif $serviceHebergementTarif */
                    foreach ($serviceHebergement->getTarifs() as $serviceHebergementTarif) {
                        $serviceHebergementTarif->setService($serviceHebergement);
                    }
                }
            }
            // ************* fin gestion des services *************

            // ************* suppression visuels *************
            // ** CAS OU L'ON SUPPRIME UN "HEBERGEMENT VISUEL" **
            // on récupère les HebergementVisuel de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Hebergement $entityCrm */
            $entityCrm = $entityUnifie->getHebergements()->filter(function (Hebergement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $entitySites = $entityUnifie->getHebergements()->filter(function (Hebergement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newHebergementVisuels = new ArrayCollection();
            foreach ($entityCrm->getVisuels() as $entityVisuel) {
                $newHebergementVisuels->add($entityVisuel);
            }

            /** @var HebergementVisuel $originalHebergementVisuel */
            foreach ($originalHebergementVisuels as $key => $originalHebergementVisuel) {

                if (false === $newHebergementVisuels->contains($originalHebergementVisuel)) {
                    $originalHebergementVisuel->setHebergement(null);
                    $em->remove($originalHebergementVisuel->getVisuel());
                    $this->deleteFile($originalHebergementVisuel->getVisuel());
                    $em->remove($originalHebergementVisuel);
                    // on doit supprimer l'hébergementVisuel des autres sites
                    // on parcourt les hebergement des sites
                    /** @var Hebergement $entitySite */
                    foreach ($entitySites as $entitySite) {
                        $entityVisuelSite = $em->getRepository(HebergementVisuel::class)->findOneBy(
                            array(
                                'hebergement' => $entitySite,
                                'visuel' => $originalHebergementVisuel->getVisuel()
                            ));
                        if (!empty($entityVisuelSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($entityVisuelSite->getHebergement()->getSite()->getLibelle());
                            $entitySite = $emSite->getRepository(Hebergement::class)->findOneBy(
                                array(
                                    'hebergementUnifie' => $entityVisuelSite->getHebergement()->getHebergementUnifie()
                                ));
                            $entityVisuelSiteSites = new ArrayCollection($emSite->getRepository(HebergementVisuel::class)->findBy(
                                array(
                                    'hebergement' => $entitySite
                                ))
                            );
                            $entityVisuelSiteSite = $entityVisuelSiteSites->filter(function (HebergementVisuel $element)
                            use ($entityVisuelSite) {
//                            return $element->getVisuel()->getProviderReference() == $entityVisuelSite->getVisuel()->getProviderReference();
                                return $element->getVisuel()->getMetadataValue('crm_ref_id') == $entityVisuelSite->getVisuel()->getId();
                            })->first();
                            if (!empty($entityVisuelSiteSite)) {
                                $emSite->remove($entityVisuelSiteSite->getVisuel());
                                $this->deleteFile($entityVisuelSiteSite->getVisuel());
                                $entityVisuelSiteSite->setHebergement(null);
                                $emSite->remove($entityVisuelSiteSite);
                                $emSite->flush();
                            }
                            $entityVisuelSite->setHebergement(null);
                            $em->remove($entityVisuelSite->getVisuel());
                            $this->deleteFile($entityVisuelSite->getVisuel());
                            $em->remove($entityVisuelSite);
                        }
                    }
                }
            }
            // ************* fin suppression visuels *************

            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $keyHebergement => $entity) {
                // ************* gestion des emplacements *************
                foreach ($entity->getEmplacements() as $keyEmplacement => $emplacement) {
                    if (empty($request->request->get('hebergement_unifie')['hebergements'][$keyHebergement]['emplacements'][$keyEmplacement]['checkbox'])) {
                        $entity->removeEmplacement($emplacement);
                        $em->remove($emplacement);
                    } else {
                        if (!empty($emplacement->getDistance2())) {
                            if (empty($emplacement->getDistance2()->getUnite())) {
                                $em->remove($emplacement->getDistance2());
                                $emplacement->setDistance2(null);
                            }
                        }
                    }
                }
                // ************* fin gestion des emplacements *************
                // *** gestion des motclefs ***
                /** @var MotClefTraductionHebergement $motClef */
//                foreach ($entity->getMotClefTraductionHebergements() as $motClef) {
//                    dump($motClef->getHebergements());
//                    if (!$motClef->getHebergements()->contains($entity)) {
//                        $motClef->addHebergement($entity);
//                    }
//                }
//                die;

//            /** @var Hebergement $hebergement */
//            foreach ($entityUnifie->getHebergements() as $hebergement) {
//                foreach ($entity->getMotClefTraductionHebergements() as $motClefTraductionHebergement) {
//                    dump($motClefTraductionHebergement);
//                }
//            }

                foreach ($originalMotClefTraductionHebergements->get($entity->getId()) as $motClef) {
                    if (!$entity->getMotClefTraductionHebergements()->contains($motClef)) {
                        $entity->removeMotClefTraductionHebergement($motClef);
                        $em->remove($motClef);
                    }
                }
//                die;
                // *** fin gestion des motclefs ***
            }

            // *** gestion suppression fournisseurs hebergement ***
            /** @var FournisseurHebergement $originalFournisseurHebergement */
            foreach ($originalFournisseurHebergements as $keyFournisseurHebergement => $originalFournisseurHebergement) {
                if (false === $entityUnifie->getFournisseurs()->contains($originalFournisseurHebergement)) {
                    // *** suppression des code promo logement ***
                    foreach ($entityUnifie->getHebergements() as $hebergement) {
                        $codePromoHebergements = $em->getRepository(CodePromoHebergement::class)->findBy(array(
                            'hebergement' => $hebergement->getId(),
                            'fournisseur' => $originalFournisseurHebergement->getFournisseur()->getId()
                        ));
                        foreach ($codePromoHebergements as $codePromoHebergement) {
                            $em->remove($codePromoHebergement);
                        }
                    }
                    // *** fin suppression des code promo logement ***
                    // *** suppression des FournisseurPrestationAnnexeLogement ***
                    $this->deletePrestationAnnexeLogements($originalFournisseurHebergement, $em);
                    // *** fin suppression des FournisseurPrestationAnnexeLogement ***
                    // *** suppression des promotionHebergements correspondants ***
                    foreach ($entityUnifie->getHebergements() as $hebergement) {
                        $promotionHebergements = $em->getRepository(PromotionHebergement::class)->findBy(array(
                            'hebergement' => $hebergement->getId(),
                            'fournisseur' => $originalFournisseurHebergement->getFournisseur()->getId()
                        ));
                        foreach ($promotionHebergements as $promotionHebergement) {
                            $em->remove($promotionHebergement);
                        }
                    }
                    // *** fin suppression des promotionHebergements correspondants ***
                    // *** suppression des FournisseurPrestationAnnexeLogement ***
                    $this->deletePrestationAnnexeLogements($originalFournisseurHebergement, $em);
                    // *** fin suppression des FournisseurPrestationAnnexeLogement ***
                    // *** suppression des decoteHebergements correspondants ***
                    foreach ($entityUnifie->getHebergements() as $hebergement) {
                        $decoteHebergements = $em->getRepository(DecoteHebergement::class)->findBy(array(
                            'hebergement' => $hebergement->getId(),
                            'fournisseur' => $originalFournisseurHebergement->getFournisseur()->getId()
                        ));
                        foreach ($decoteHebergements as $decoteHebergement) {
                            $em->remove($decoteHebergement);
                        }
                    }
                    // *** fin suppression des decoteHebergements correspondants ***
                    $em->remove($originalFournisseurHebergement);
                } else {
                    foreach ($originalCodePasserelles->get($keyFournisseurHebergement) as $keySaisonCodePasserelle => $saisonCodePasserelles) {
                        foreach ($saisonCodePasserelles as $codePasserelle) {
                            if(false === $entityUnifie->getFournisseurs()->get($keyFournisseurHebergement)->getSaisonCodePasserelles()->get($keySaisonCodePasserelle)->getCodePasserelles()->contains($codePasserelle)) {
                                $entityUnifie->getFournisseurs()->get($keyFournisseurHebergement)->getSaisonCodePasserelles()->get($keySaisonCodePasserelle)->removeCodePasserelle($codePasserelle);
                                $em->remove($codePasserelle);
                            }
                        }
                    }
                }
            }
            // *** fin gestion suppression des fournisseurs hebergement ***

            // ***** Gestion des Medias *****
            // CAS D'UN NOUVEAU 'HEBERGEMENT VISUEL' OU DE MODIFICATION D'UN "HEBERGEMENT VISUEL"
            /** @var HebergementVisuel $entityVisuel */
            // tableau pour la suppression des anciens visuels
            $visuelToRemoveCollection = new ArrayCollection();
            $keyCrm = $entityUnifie->getHebergements()->indexOf($entityCrm);

            // on parcourt les hebergementVisuels de l'hebergement crm
            foreach ($entityCrm->getVisuels() as $key => $entityVisuel) {
                // on active le nouveau hebergementVisuel (CRM) => il doit être toujours actif
                $entityVisuel->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementVisuel pour chacun
                    if ($site->getCrm() == 0) {

                        // on récupère l'hébegergement du site
                        /** @var Hebergement $entitySite */
                        $entitySite = $entityUnifie->getHebergements()->filter(function (Hebergement $element) use (
                            $site
                        ) {
                            return $element->getSite() === $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($entitySite)) {
                            // on réinitialise la variable
                            unset($entityVisuelSite);
                            // s'il ne s'agit pas d'un nouveau hebergementVisuel
                            if (!empty($entityVisuel->getId())) {
                                // on récupère l'hebergementVisuel pour le modifier
                                $entityVisuelSite = $em->getRepository(HebergementVisuel::class)->findOneBy(array(
                                    'hebergement' => $entitySite,
                                    'visuel' => $originalVisuels->get($key)
                                ));
                            }
                            // si l'hebergementVisuel est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($entityVisuel->getId()) || empty($entityVisuelSite)) {
                                // on récupère la classe correspondant au visuel (photo ou video)
                                $typeVisuel = (new ReflectionClass($entityVisuel))->getName();
                                // on créé un nouveau HebergementVisuel on fonction du type
                                /** @var HebergementVisuel $entityVisuelSite */
                                $entityVisuelSite = new $typeVisuel();
                                $entityVisuelSite->setHebergement($entitySite);
                            }
                            // si l'hébergemenent visuel existe déjà pour le site
                            if (!empty($entityVisuelSite)) {
                                if ($entityVisuelSite->getVisuel() !== $entityVisuel->getVisuel()) {
//                                    // si l'hébergementVisuelSite avait déjà un visuel
//                                    if (!empty($entityVisuelSite->getVisuel()) && !$visuelToRemoveCollection->contains($entityVisuelSite->getVisuel()))
//                                    {
//                                        // on met l'ancien visuel dans un tableau afin de le supprimer plus tard
//                                        $visuelToRemoveCollection->add($entityVisuelSite->getVisuel());
//                                    }
                                    // on met le nouveau visuel
                                    $entityVisuelSite->setVisuel($entityVisuel->getVisuel());
                                }
                                $entitySite->addVisuel($entityVisuelSite);

                                /** @var HebergementVisuelTraduction $traduction */
                                foreach ($entityVisuel->getTraductions() as $traduction) {
                                    /** @var HebergementVisuelTraduction $traductionSite */
                                    $traductionSites = $entityVisuelSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (
                                            HebergementVisuelTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue() === $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new HebergementVisuelTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $entityVisuelSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementVisuel doit être actif sur le site ou non
                                if (!empty($request->get('hebergement_unifie')['hebergements'][$keyCrm]['visuels'][$key]['sites']) &&
                                    in_array($site->getId(),
                                        $request->get('hebergement_unifie')['hebergements'][$keyCrm]['visuels'][$key]['sites'])
                                ) {
                                    $entityVisuelSite->setActif(true);
                                } else {
                                    $entityVisuelSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'hebergementVisuel CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($entityVisuel->getVisuel()->getId()) && !empty($originalVisuels->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $visuelToRemoveCollection->add($originalVisuels->get($key));
                    }

                }

            }
            // ***** Fin Gestion des Medias *****

            $this->gestionCodePromoHebergement($entityUnifie);

            // *** gestion coup de coeur ***
            $coupDeCoeurRemove = $this->gestionCoupDeCoeur($entityUnifie);
            // *** fin gestion coup de coeur ***

            $this->gestionSaisons($entityUnifie);

            $em->persist($entityUnifie);

            try {
                $error = false;
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                $error = true;
            }
            if (!$error) {
                $this->copieVersSites($entityUnifie, $originalHebergementVisuels);

                // *** gestion promotionStation ***
                /** @var FournisseurHebergement $fournisseurHebergement */
                // on vérifie si l'on change de station pour un des hebergements
                /** @var Hebergement $hebergement */
                /** @var Logement $logement */
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    if ($hebergement->getStation()->getId() != $originalStations->get($hebergement->getId())->getId()) {
                        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                            $logement = $fournisseurHebergement->getLogements()->filter(function (Logement $element) use ($hebergement) {
                                return $element->getSite() === $hebergement->getSite();
                            })->first();
                            if (false !== $logement) {
                                $this->gestionPromotionStation($entityUnifie, $fournisseurHebergement->getFournisseur()->getId());
                            }
                        }
                    } else {
                        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                            $originalFournisseurHebergement = $originalFournisseurHebergements->filter(function (FournisseurHebergement $element) use ($fournisseurHebergement) {
                                return $element->getFournisseur()->getId() == $fournisseurHebergement->getFournisseur()->getId();
                            })->first();
                            // si fournisseurHebergement est un nouveau on envoi la fonction
                            if (false === $originalFournisseurHebergement) {
                                $this->gestionPromotionStation($entityUnifie, $fournisseurHebergement->getFournisseur()->getId());
                            }
                        }
                    }
                }
                // *** fin gestion promotionStation ***

                // *** gestion promotion hebergement ***
                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                    $originalFournisseurHebergement = $originalFournisseurHebergements->filter(function (FournisseurHebergement $element) use ($fournisseurHebergement) {
                        return $element->getFournisseur()->getId() == $fournisseurHebergement->getFournisseur()->getId();
                    })->first();
                    // si fournisseurHebergement est un nouveau on envoi la fonction
                    if (false === $originalFournisseurHebergement) {
                        $this->gestionPromotionHebergement($entityUnifie);
                    }
                }
                // *** fin gestion promotion hebergement ***

                // *** gestion decoteStation ***
                /** @var FournisseurHebergement $fournisseurHebergement */
                // on vérifie si l'on change de station pour un des hebergements
                /** @var Hebergement $hebergement */
                /** @var Logement $logement */
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    if ($hebergement->getStation()->getId() != $originalStations->get($hebergement->getId())->getId()) {
                        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                            $logement = $fournisseurHebergement->getLogements()->filter(function (Logement $element) use ($hebergement) {
                                return $element->getSite() === $hebergement->getSite();
                            })->first();
                            if (false !== $logement) {
                                $this->gestionDecoteStation($entityUnifie, $fournisseurHebergement->getFournisseur()->getId());
                            }
                        }
                    } else {
                        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                            $originalFournisseurHebergement = $originalFournisseurHebergements->filter(function (FournisseurHebergement $element) use ($fournisseurHebergement) {
                                return $element->getFournisseur()->getId() == $fournisseurHebergement->getFournisseur()->getId();
                            })->first();
                            // si fournisseurHebergement est un nouveau on envoi la fonction
                            if (false === $originalFournisseurHebergement) {
                                $this->gestionDecoteStation($entityUnifie, $fournisseurHebergement->getFournisseur()->getId());
                            }
                        }
                    }
                }
                // *** fin gestion decoteStation ***

                // *** gestion decote hebergement ***
                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                    $originalFournisseurHebergement = $originalFournisseurHebergements->filter(function (FournisseurHebergement $element) use ($fournisseurHebergement) {
                        return $element->getFournisseur()->getId() == $fournisseurHebergement->getFournisseur()->getId();
                    })->first();
                    // si fournisseurHebergement est un nouveau on envoi la fonction
                    if (false === $originalFournisseurHebergement) {
                        $this->gestionDecoteHebergement($entityUnifie);
                    }
                }
                // *** fin gestion decote hebergement ***

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
                // suppression coupDeCoeurs
                if (!$coupDeCoeurRemove->isEmpty()) {
                    foreach ($coupDeCoeurRemove as $item) {
                        $em->remove($item);
                    }
                    $em->flush();
                }

                $this->addFlash('success', 'L\'hébergement a bien été modifié');
                return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $entityUnifie->getId()));
            }
        }

        return $this->render('@MondofuteHebergement/hebergementunifie/edit.html.twig', array(
            'entity' => $entityUnifie,
            'sites' => $sites,
            'maxInputVars' => ini_get('max_input_vars'),
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'saisons' => $saisons
        ));
    }

    /**
     * @param HebergementUnifie $entityUnifie
     * @param ArrayCollection $saisons
     */
    private function addSaisonCodePasserelle(HebergementUnifie $entityUnifie, $saisons)
    {
        /** @var FournisseurHebergement $fournisseurHebergement */
        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            foreach ($saisons as $saison) {
                $saisonCodePasserelle = $fournisseurHebergement->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saison) {
                    return $element->getSaison() == $saison;
                })->first();
                if (false === $saisonCodePasserelle) {
                    $saisonCodePasserelle = new SaisonCodePasserelle();
                    $saisonCodePasserelle->setSaison($saison);
                    $fournisseurHebergement->addSaisonCodePasserelle($saisonCodePasserelle);
                }
            }
        }
    }

    private function deleteTarifSites(ServiceHebergementTarif $tarif)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $tarifSite = $emSite->find(ServiceHebergementTarif::class,
                $tarif->getId());
            if (!empty($tarifSite)) {
                $tarifSite->setService(null);
                $emSite->remove($tarifSite);
//                $listeServiceSite->setFournisseur(null);
//                $emSite->remove($listeServiceSite);
            }
        }
    }

    private function deleteServiceSites(ServiceHebergement $service)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            if (!empty($service->getId())) {
                $serviceSite = $emSite->find(ServiceHebergement::class,
                    $service->getId());
                if (!empty($serviceSite)) {
                    $serviceSite->setHebergementUnifie(null);
                    $emSite->remove($serviceSite);
//                $listeServiceSite->setFournisseur(null);
//                $emSite->remove($listeServiceSite);
                }
            }
        }
    }

    /**
     * @param HebergementUnifie $hebergementUnifie
     */
    public function chargerCatalogue($hebergementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var FournisseurHebergement $fournisseurHebergement */
        foreach ($hebergementUnifie->getFournisseurs() as $fournisseurHebergement) {
            /** @var Logement $logement */
            foreach ($fournisseurHebergement->getLogements() as $logement) {
                /** @var LogementPeriode $logementPeriode */
                foreach ($logement->getPeriodes() as $logementPeriode) {
                    $em->getRepository(LogementPeriode::class)->chargerLocatif($logementPeriode);
                }
            }
        }
    }

    public function chargerFournisseursStockslogementLocatif($fournisseurHebergements)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($fournisseurHebergements as $fournisseurHebergement) {
            /** @var Logement $logement */
            foreach ($fournisseurHebergement->getLogements() as $logement) {
                /** @var LogementPeriode $logementPeriode */
                foreach ($logement->getPeriodes() as $logementPeriode) {
                    $em->getRepository(LogementPeriode::class)->chargerLocatif($logementPeriode);
                }
            }
        }
    }

    public function chargerFournisseurHebergementAction(Request $request, $idHebergementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurHebergements = $em->getRepository(FournisseurHebergement::class)->findBy(array('hebergement' => $idHebergementUnifie));
        $reponse = new \stdClass();
        $nbLogements = 0;
        $fournisseurHebergementsJson = array();
        foreach ($fournisseurHebergements as $fournisseurHebergement) {
            $fournisseurHebergementJson = new \stdClass();
            $fournisseurHebergementJson->fournisseur = new \stdClass();
            $fournisseurHebergementJson->fournisseur->id = $fournisseurHebergement->getFournisseur()->getId();
            $fournisseurHebergementJson->fournisseur->enseigne = $fournisseurHebergement->getFournisseur()->getEnseigne();
            $fournisseurHebergementJson->logements = array();
            /** @var Logement $logement */
            foreach ($fournisseurHebergement->getLogements() as $logement) {
                if ($logement->getSite()->getCrm()) {
                    $logementJson = new \stdClass();
                    $logementJson->id = $logement->getId();
                    $logementJson->logementUnifie = new \stdClass();
                    $logementJson->logementUnifie->id = $logement->getLogementUnifie()->getId();
                    /** @var LogementTraduction $traduction */
                    foreach ($logement->getTraductions() as $traduction) {
                        if ($traduction->getLangue()->getCode() == $request->getLocale()) {
                            $logementJson->nom = $traduction->getNom();
                        }
                    }
                    array_push($fournisseurHebergementJson->logements, $logementJson);
                    $nbLogements++;
                }
            }
            array_push($fournisseurHebergementsJson, $fournisseurHebergementJson);
        }
        $reponse->fournisseurHebergements = $fournisseurHebergementsJson;
        $reponse->nbLogements = $nbLogements;
        return new JsonResponse($reponse);
    }

    public function creerTableauxStocksHebergementPeriodeAction(Request $request, $idTypePeriode, $idHebergementUnifie)
    {
        ini_set('memory_limit', '256M');
//        ini_set('max_execution_time',300);
//        set_time_limit(300);
        $em = $this->getDoctrine()->getManager();
        $fournisseurHebergements = $em->getRepository(FournisseurHebergement::class)->chargerPourStocks($idHebergementUnifie);
        $data = array();
        /** @var FournisseurHebergement $fournisseurHebergement */
        foreach ($fournisseurHebergements as $fournisseurHebergement) {
            $fournisseur = array();
            $fournisseur[1] = array();
            /** @var Logement $logement */
            foreach ($fournisseurHebergement->getLogements() as $logement) {
                $ligne = new \stdClass();
                $ligne->logementUnifieId = $logement->getLogementUnifie()->getId();
                /** @var LogementTraduction $traduction */
                foreach ($logement->getTraductions() as $traduction) {
                    if ($traduction->getLangue()->getCode() == $request->getLocale()) {
                        $ligne->logement = $traduction->getNom();
                    }
                }
                /** @var LogementPeriode $periode */
                foreach ($logement->getPeriodes() as $periode) {
                    if ($periode->getPeriode()->getType()->getId() == $idTypePeriode) {
//                        $ligne->{'periode' . $periode->getPeriode()->getId()} = '<input data-logement="' . $logement->getLogementUnifie()->getId() . '" data-periode="' . $periode->getPeriode()->getId() . '" name="stocks[' . $logement->getLogementUnifie()->getId() . '][' . $periode->getPeriode()->getId() . ']" class="form-control" type="text" size="2" maxlength="2" value="' . $periode->getLocatif()->getStock() . '"/>';
                        $ligne->{'periode' . $periode->getPeriode()->getId()} = $periode->getLocatif()->getStock();
                    }
                }
                array_push($fournisseur[1], $ligne);
            }
            $fournisseur[0] = $fournisseurHebergement->getFournisseur()->getEnseigne();
            $fournisseur[3] = $fournisseurHebergement->getFournisseur()->getId();
            array_push($data, $fournisseur);
        }
        return new JsonResponse($data);
    }

    /**
     * Deletes a HebergementUnifie entity.
     *
     */
    public function deleteAction(Request $request, HebergementUnifie $entityUnifie)
    {
        /** @var EntityManager $em */
        /** @var HebergementUnifie $entityUnifieSite */
        $em = $this->getDoctrine()->getManager();
        $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        try {
            $saisonHebergementCollection = new ArrayCollection();
            $form = $this->createDeleteForm($entityUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                // *** gestion saisons ***
                /** @var Hebergement $hebergement */
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    foreach ($hebergement->getSaisonHebergements() as $saisonHebergement) {
                        $hebergement->removeSaisonHebergement($saisonHebergement);
                        $em->remove($saisonHebergement);
                        if (empty($saisonHebergementCollection->get($hebergement->getId()))) {
                            $saisonHebergementCollection->set($hebergement->getId(), new ArrayCollection());
                        }
                        $saisonHebergementCollection->get($hebergement->getId())->add($saisonHebergement);
                    }
                }
                $this->gestionSaisons($entityUnifie);
                // *** fin gestion saisons ***

                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $entityUnifieSite = $emSite->find(HebergementUnifie::class, $entityUnifie->getId());
                    if (!empty($entityUnifieSite)) {
                        if (!$entityUnifieSite->getHebergements()->isEmpty()) {
                            $this->gestionSaisonsSite(
                                $entityUnifie->getHebergements()->filter(function (Hebergement $element) use ($siteDistant) {
                                    return $element->getSite()->getId() == $siteDistant->getId();
                                })->first(),
                                $entityUnifieSite->getHebergements()->first(),
                                $emSite);
                            /** @var Hebergement $entitySite */
                            foreach ($entityUnifieSite->getHebergements() as $entitySite) {
                                // si il y a des visuels pour l'entité, les supprimer
                                if (!empty($entitySite->getVisuels())) {
                                    /** @var HebergementVisuel $entityVisuelSite */
                                    foreach ($entitySite->getVisuels() as $entityVisuelSite) {
                                        $visuelSite = $entityVisuelSite->getVisuel();
                                        $entityVisuelSite->setVisuel(null);
                                        if (!empty($visuelSite)) {
                                            $emSite->remove($visuelSite);
                                            $this->deleteFile($visuelSite);
                                        }
                                    }
                                }
                            }
                            $emSite->flush();

                            foreach ($entityUnifieSite->getHebergements() as $hebergement) {
                                $codePromoHebergements = $emSite->getRepository(CodePromoHebergement::class)->findBy(array('hebergement' => $hebergement));
                                foreach ($codePromoHebergements as $codePromoHebergement) {
                                    $emSite->remove($codePromoHebergement);
                                }
                            }

                            /** @var FournisseurHebergement $fournisseurHebergement */
                            foreach ($entityUnifieSite->getFournisseurs() as $fournisseurHebergement) {
                                /** @var Logement $logement */
                                foreach ($fournisseurHebergement->getLogements() as $logement) {

                                    $codePromoLogements = $emSite->getRepository(CodePromoLogement::class)->findBy(array('logement' => $logement));
                                    foreach ($codePromoLogements as $codePromoLogement) {
                                        $emSite->remove($codePromoLogement);
                                    }

                                    /** @var LogementPeriode $logementPeriode */
                                    foreach ($logement->getPeriodes() as $logementPeriode) {
                                        // *** suprression logement periode locatif  ***
                                        $logementPeriodeLocatif = $emSite->getRepository(LogementPeriodeLocatif::class)->findOneBy(array(
                                            'logement' => $logement,
                                            'periode' => $logementPeriode->getPeriode()->getId(),
                                        ));
                                        if (!empty($logementPeriodeLocatif)) {
                                            $emSite->remove($logementPeriodeLocatif);
                                        }
                                        // *** fin suprression logement periode locatif  ***
                                    }

                                    $this->deletePrestationAnnexeLogements($fournisseurHebergement, $emSite);
                                }
                            }
                            $this->deletePrestationAnnexeHebergements($entityUnifieSite, $emSite);
                        }
                    }
                    $emSite->remove($entityUnifieSite);
                    $emSite->flush();
                }

                if (!empty($entityUnifie)) {

                    $prestationAnnexeHebergementUnifies = $em->getRepository(PrestationAnnexeHebergementUnifie::class)->findByHebergement($entityUnifie->getHebergements()->first()->getId());
                    foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                        $em->remove($prestationAnnexeHebergementUnifie);
                    }

                    if (!empty($entityUnifie->getHebergements())) {
                        /** @var Hebergement $entity */
                        foreach ($entityUnifie->getHebergements() as $entity) {
                            // si il y a des visuels pour l'entité, les supprimer
                            if (!empty($entity->getVisuels())) {
                                /** @var HebergementVisuel $entityVisuel */
                                foreach ($entity->getVisuels() as $entityVisuel) {
                                    $visuel = $entityVisuel->getVisuel();
                                    $entityVisuel->setVisuel(null);
                                    $em->remove($visuel);
                                    $this->deleteFile($visuel);
                                }
                            }

                        }
                        $em->flush();
                    }
                    /** @var FournisseurHebergement $fournisseurHebergement */
                    foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                        foreach ($fournisseurHebergement->getLogements() as $logement) {
                            /** @var LogementPeriode $logementPeriode */
                            foreach ($logement->getPeriodes() as $logementPeriode) {
                                // *** suprression logement periode locatif  ***
                                $logementPeriodeLocatif = $em->getRepository(LogementPeriodeLocatif::class)->findOneBy(array(
                                    'logement' => $logement,
                                    'periode' => $logementPeriode->getPeriode()->getId(),
                                ));
                                if (!empty($logementPeriodeLocatif)) {
                                    $em->remove($logementPeriodeLocatif);
                                }
                                // *** fin suprression logement periode locatif  ***
                            }
                        }
                        $this->deletePrestationAnnexeLogements($fournisseurHebergement, $em);

                    }

                    /** @var Hebergement $hebergement */
                    foreach ($entityUnifie->getHebergements() as $hebergement) {
                        $codePromoHebergements = $em->getRepository(CodePromoHebergement::class)->findBy(array('hebergement' => $hebergement));
                        foreach ($codePromoHebergements as $codePromoHebergement) {
                            $em->remove($codePromoHebergement);
                        }
                    }
                    /** @var FournisseurHebergement $fournisseurHebergement */
                    foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                        foreach ($fournisseurHebergement->getLogements() as $logement) {
                            $codePromoLogements = $em->getRepository(CodePromoLogement::class)->findBy(array('logement' => $logement));
                            foreach ($codePromoLogements as $codePromoLogement) {
                                $em->remove($codePromoLogement);
                            }
                            /** @var LogementPeriode $logementPeriode */
                            foreach ($logement->getPeriodes() as $logementPeriode) {
                                // *** suprression logement periode locatif  ***
                                $logementPeriodeLocatif = $em->getRepository(LogementPeriodeLocatif::class)->findOneBy(array(
                                    'logement' => $logement,
                                    'periode' => $logementPeriode->getPeriode()->getId(),
                                ));
                                if (!empty($logementPeriodeLocatif)) {
                                    $em->remove($logementPeriodeLocatif);
                                }
                                // *** fin suprression logement periode locatif  ***
                            }
                        }
                    }
                    $this->deletePrestationAnnexeHebergements($entityUnifie, $em);
                }

                $em->remove($entityUnifie);
                $em->flush();
            }

        } catch (ForeignKeyConstraintViolationException $except) {

            foreach ($entityUnifie->getHebergements() as $hebergement) {
                foreach ($saisonHebergementCollection->get($hebergement->getId()) as $saisonHebergement) {
                    $hebergement->addSaisonHebergement($saisonHebergement);
                }
            }
            $this->gestionSaisons($entityUnifie);
            $em->flush();
            foreach ($sitesDistants as $siteDistant) {
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                /** @var EntityManager $emSite */
                $this->gestionSaisonsSite(
                    $entityUnifie->getHebergements()->filter(function (Hebergement $element) use ($siteDistant) {
                        return $element->getSite()->getId() == $siteDistant->getId();
                    })->first(),
                    $entityUnifieSite->getHebergements()->first(),
                    $emSite);
            }

            /** @var ForeignKeyConstraintViolationException $except */
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
//                        'Impossible de supprimer l\'hébergement, il est utilisé par une autre entité');
                        $except->getMessage());
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'L\'hébergement a bien été supprimé');
        return $this->redirectToRoute('hebergement_hebergement_index');
    }

    /**
     * @param HebergementUnifie $entityUnifie
     * @param EntityManager $em
     */
    private function deletePrestationAnnexeHebergements($entityUnifie, $em)
    {
        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
        /** @var Hebergement $hebergement */
        $prestationAnnexeHebergementUnifies = new ArrayCollection();
        foreach ($entityUnifie->getHebergements() as $hebergement) {
            foreach ($hebergement->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                if (!$prestationAnnexeHebergementUnifies->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                    $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
                }
            }
        }
        foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
            $em->remove($prestationAnnexeHebergementUnifie);
        }
    }
}
