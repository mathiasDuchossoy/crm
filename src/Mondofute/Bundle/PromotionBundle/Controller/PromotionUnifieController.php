<?php

namespace Mondofute\Bundle\PromotionBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use JMS\JobQueueBundle\Entity\Job;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseur;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionHebergement;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogement;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogementPeriode;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionPeriodeValiditeDate;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionPeriodeValiditeJour;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionStation;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionTypeAffectation;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionUnifie;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\PromotionBundle\Entity\TypePeriodeSejour;
use Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * PromotionUnifie controller.
 *
 */
class PromotionUnifieController extends Controller
{
    const PromotionPeriodeValidite = "HiDev\\Bundle\\PromotionBundle\\Entity\\PromotionPeriodeValidite";

    /**
     * Lists all PromotionUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(PromotionUnifie::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofutePromotionBundle:PromotionUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'promotion_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofutePromotionBundle:PromotionUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofutePromotion/promotionunifie/index.html.twig', array(
            'promotionUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new PromotionUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $affectations = TypeAffectation::$libelles;

        $sitesAEnregistrer = $request->get('sites');

        $promotionUnifie = new PromotionUnifie();

        $this->ajouterPromotionsDansForm($promotionUnifie);
        $this->promotionsSortByAffichage($promotionUnifie);

        $form = $this->createForm('Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType', $promotionUnifie);
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Promotion $entity */

            // *** gestion promotion typeAffectation ***
            $this->gestionPromotionTypeAffectation($promotionUnifie);
            // *** fin gestion promotion typeAffectation ***

            /** @var Promotion $promotion */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                if (false === in_array($promotion->getSite()->getId(), $sitesAEnregistrer)) {
                    $promotion->setActif(false);
                }
            }

            // *** gestion typePeriodeSejour ***
            $this->gestionTypePeriodeSejour($promotionUnifie);
            // *** fin gestion typePeriodeSejour ***

            $em = $this->getDoctrine()->getManager();

            $em->persist($promotionUnifie);

            try {
                $em->flush();

                $this->copieVersSites($promotionUnifie);

                $this->gestionPromotionTypeFournisseur($promotionUnifie->getId());

                $this->addFlash('success', 'La promotion a bien été créé.');

                return $this->redirectToRoute('promotion_edit', array('id' => $promotionUnifie->getId()));
            } catch (Exception $e) {
//                switch ($e->getCode()){
//                    case 0:
//                        $this->addFlash('error', "Le code " . $promotionUnifie->getCode() . " existe déjà.");
//                        break;
//                    default:
//                        $this->addFlash('error', "Add not done: " . $e->getMessage());
//                        break;
//                }
                $this->addFlash('error', "Add not done: " . $e->getMessage());
            }
        }

        return $this->render('@MondofutePromotion/promotionunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $promotionUnifie,
            'form' => $form->createView(),
            'affectations' => $affectations,
            'fournisseursTypeHebergement' => new ArrayCollection(),
            'fournisseursPrestationAnnexe' => new ArrayCollection(),
        ));
    }

    /**
     * Ajouter les promotions qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param PromotionUnifie $entityUnifie
     */
    private function ajouterPromotionsDansForm(PromotionUnifie $entityUnifie)
    {
        /** @var Promotion $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $entity = $entityUnifie->getPromotions()->filter(function (Promotion $element) use ($site) {
                return $element->getSite() == $site;
            })->first();
            if (false === $entity) {
                $entity = new Promotion();
                $entityUnifie->addPromotion($entity);
                $entity->setSite($site);
            }
        }
    }

    /**
     * Classe les promotions par classementAffichage
     * @param PromotionUnifie $entity
     */
    private function promotionsSortByAffichage(PromotionUnifie $entity)
    {
        // Trier les promotions en fonction de leurs ordre d'affichage
        $promotions = $entity->getPromotions(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $promotions->getIterator();
        unset($promotions);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (Promotion $a, Promotion $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $promotions = new ArrayCollection(iterator_to_array($iterator));

        // remplacé les promotions par ce nouveau tableau (une fonction 'set' a été créé dans Promotion unifié)
        $entity->setPromotions($promotions);
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionTypeAffectation($promotionUnifie)
    {
        /** @var PromotionTypeAffectation $promotionTypeAffectationCrm */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if (false === $promotion->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::logement;
                })->first()
            ) {
                $promotion->getPromotionHebergements()->clear();
                $promotion->getLogementPeriodes()->clear();
                $promotionFournisseurs = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) {
                    return $element->getType() == TypeAffectation::logement;
                });
                foreach ($promotionFournisseurs as $promotionFournisseur) {
                    $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
                }
            }
            if (false === $promotion->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::prestationAnnexe;
                })->first()
            ) {
                $promotion->getPromotionFamillePrestationAnnexes()->clear();
                $promotion->getPromotionFournisseurPrestationAnnexes()->clear();
                $promotionFournisseurs = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) {
                    return $element->getType() == TypeAffectation::prestationAnnexe;
                });
                foreach ($promotionFournisseurs as $promotionFournisseur) {
                    $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
                }
            }
            if (false === $promotion->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::type;
                })->first()
            ) {
                $promotionFournisseurs = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) {
                    return $element->getType() == TypeAffectation::type;
                });
                foreach ($promotionFournisseurs as $promotionFournisseur) {
                    $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
                }
            }
            foreach ($promotion->getPromotionTypeAffectations() as $typeAffectation) {
                $typeAffectation->setPromotion($promotion);
            }
        }
        $promotionTypeAffectationCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionTypeAffectations();
        $typeAffectations = new ArrayCollection();
        foreach ($promotionTypeAffectationCrms as $promotionTypeAffectationCrm) {
            $typeAffectations->add($promotionTypeAffectationCrm->getTypeAffectation());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $typeAffectationSites = new ArrayCollection();
                foreach ($promotion->getPromotionTypeAffectations() as $promotionTypeAffectationSite) {
                    $typeAffectationSites->add($promotionTypeAffectationSite->getTypeAffectation());
                }
                foreach ($typeAffectations as $typeAffectation) {
                    if (false === $typeAffectationSites->contains($typeAffectation)) {
                        $newTypeAffectation = new PromotionTypeAffectation();
                        $promotion->addPromotionTypeAffectation($newTypeAffectation);
                        $newTypeAffectation->setTypeAffectation($typeAffectation);
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionTypePeriodeSejour($promotionUnifie)
    {
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            switch ($promotion->getTypePeriodeSejour()) {
                case TypePeriodeSejour::permanent:
                    $promotion->setPromotionPeriodeValiditeDate();
                    $promotion->setPromotionPeriodeValiditeJour();
                    break;
                case TypePeriodeSejour::dateADate:
                    $promotion->setPromotionPeriodeValiditeJour();
                    break;
                case TypePeriodeSejour::periode:
                    $promotion->setPromotionPeriodeValiditeDate();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité promotion
     * @param PromotionUnifie $entityUnifie
     */
    private function copieVersSites(PromotionUnifie $entityUnifie)
    {
        /** @var EntityManager $emSite */
        /** @var Promotion $entity */
        /** @var Promotion $entitySite */
        /** @var Promotion $entityCrm */
//        Boucle sur les promotions afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entityUnifie->getPromotions() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());
                $site = $emSite->find(Site::class, $entity->getSite());

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (empty($entityUnifieSite = $emSite->find(PromotionUnifie::class, $entityUnifie))) {
                    $entityUnifieSite = new PromotionUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                }

                //  Récupération de la promotion sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty($entitySite = $emSite->getRepository(Promotion::class)->findOneBy(array('promotionUnifie' => $entityUnifieSite)))) {
                    $entitySite = new Promotion();
                    $entitySite
                        ->setSite($site)
                        ->setPromotionUnifie($entityUnifieSite);

                    $entityUnifieSite->addPromotion($entitySite);
                }

                // *** gestion promotion typeAffectation ***
                if (!empty($entity->getPromotionTypeAffectations()) && !$entity->getPromotionTypeAffectations()->isEmpty()) {
                    /** @var PromotionTypeAffectation $promotionTypeAffectation */
                    foreach ($entity->getPromotionTypeAffectations() as $promotionTypeAffectation) {
                        $promotionTypeAffectationSite = $entitySite->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) use ($promotionTypeAffectation) {
                            return $element->getId() == $promotionTypeAffectation->getId();
                        })->first();
                        if (false === $promotionTypeAffectationSite) {
                            $promotionTypeAffectationSite = new PromotionTypeAffectation();
                            $entitySite->addPromotionTypeAffectation($promotionTypeAffectationSite);
                            $promotionTypeAffectationSite
                                ->setId($promotionTypeAffectation->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionTypeAffectationSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $promotionTypeAffectationSite
                            ->setTypeAffectation($promotionTypeAffectation->getTypeAffectation());
                    }
                }

                if (!empty($entitySite->getPromotionTypeAffectations()) && !$entitySite->getPromotionTypeAffectations()->isEmpty()) {
                    /** @var PromotionTypeAffectation $promotionTypeAffectation */
                    foreach ($entitySite->getPromotionTypeAffectations() as $promotionTypeAffectationSite) {
                        $promotionTypeAffectation = $entity->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) use ($promotionTypeAffectationSite) {
                            return $element->getId() == $promotionTypeAffectationSite->getId();
                        })->first();
                        if (false === $promotionTypeAffectation) {
//                            $entitySite->removePromotionTypeAffectation($promotionTypeAffectationSite);
                            $emSite->remove($promotionTypeAffectationSite);
                        }
                    }
                }
                // *** fin gestion promotion typeAffectation ***

                // *** gestion promotion fournisseur ***
                if (!empty($entity->getPromotionFournisseurs()) && !$entity->getPromotionFournisseurs()->isEmpty()) {
                    /** @var PromotionFournisseur $promotionFournisseur */
                    foreach ($entity->getPromotionFournisseurs() as $promotionFournisseur) {
                        $promotionFournisseurSite = $entitySite->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($promotionFournisseur) {
                            return ($element->getPromotion()->getPromotionUnifie()->getId() == $promotionFournisseur->getPromotion()->getPromotionUnifie()->getId()
                                and $element->getFournisseur()->getId() == $promotionFournisseur->getFournisseur()->getId()
                                and $element->getType() == $promotionFournisseur->getType()
                            );
                        })->first();
                        if (false === $promotionFournisseurSite) {
                            $promotionFournisseurSite = new PromotionFournisseur();
                            $entitySite->addPromotionFournisseur($promotionFournisseurSite);
                        }
                        $promotionFournisseurSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionFournisseur->getFournisseur()))
                            ->setType($promotionFournisseur->getType());
                    }
                }

                if (!empty($entitySite->getPromotionFournisseurs()) && !$entitySite->getPromotionFournisseurs()->isEmpty()) {
                    /** @var PromotionFournisseur $promotionFournisseur */
                    foreach ($entitySite->getPromotionFournisseurs() as $promotionFournisseurSite) {
                        $promotionFournisseur = $entity->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($promotionFournisseurSite) {
                            return ($element->getPromotion()->getPromotionUnifie()->getId() == $promotionFournisseurSite->getPromotion()->getPromotionUnifie()->getId()
                                and $element->getFournisseur()->getId() == $promotionFournisseurSite->getFournisseur()->getId()
                                and $element->getType() == $promotionFournisseurSite->getType());
                        })->first();
                        if (false === $promotionFournisseur) {
//                            $entitySite->removePromotionFournisseur($promotionFournisseurSite);
                            $emSite->remove($promotionFournisseurSite);
                        }
                    }
                }
                // *** fin gestion promotion fournisseur ***

                // *** gestion promotion hebergement ***
                if (!empty($entity->getPromotionHebergements()) && !$entity->getPromotionHebergements()->isEmpty()) {
                    /** @var PromotionHebergement $promotionHebergement */
                    foreach ($entity->getPromotionHebergements() as $promotionHebergement) {
                        $promotionHebergementSite = $entitySite->getPromotionHebergements()->filter(function (PromotionHebergement $element) use ($promotionHebergement) {
                            return $element->getId() == $promotionHebergement->getId();
                        })->first();
                        if (false === $promotionHebergementSite) {
                            $promotionHebergementSite = new PromotionHebergement();
                            $entitySite->addPromotionHebergement($promotionHebergementSite);
                            $promotionHebergementSite
                                ->setId($promotionHebergement->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionHebergementSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionHebergementSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionHebergement->getFournisseur()))
                            ->setHebergement($emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $promotionHebergement->getHebergement()->getHebergementUnifie())));
                    }
                }

                if (!empty($entitySite->getPromotionHebergements()) && !$entitySite->getPromotionHebergements()->isEmpty()) {
                    /** @var PromotionHebergement $promotionHebergement */
                    foreach ($entitySite->getPromotionHebergements() as $promotionHebergementSite) {
                        $promotionHebergement = $entity->getPromotionHebergements()->filter(function (PromotionHebergement $element) use ($promotionHebergementSite) {
                            return $element->getId() == $promotionHebergementSite->getId();
                        })->first();
                        if (false === $promotionHebergement) {
//                            $entitySite->removePromotionHebergement($promotionHebergementSite);
                            $emSite->remove($promotionHebergementSite);
                        }
                    }
                }
                // *** fin gestion promotion hebergement ***

                // *** gestion promotion station ***
                if (!empty($entity->getPromotionStations()) && !$entity->getPromotionStations()->isEmpty()) {
                    /** @var PromotionStation $promotionStation */
                    foreach ($entity->getPromotionStations() as $promotionStation) {
                        $promotionStationSite = $entitySite->getPromotionStations()->filter(function (PromotionStation $element) use ($promotionStation) {
                            return $element->getId() == $promotionStation->getId();
                        })->first();
                        if (false === $promotionStationSite) {
                            $promotionStationSite = new PromotionStation();
                            $entitySite->addPromotionStation($promotionStationSite);
                            $promotionStationSite
                                ->setId($promotionStation->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionStationSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionStationSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionStation->getFournisseur()))
                            ->setStation($emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $promotionStation->getStation()->getStationUnifie())));
                    }
                }

                if (!empty($entitySite->getPromotionStations()) && !$entitySite->getPromotionStations()->isEmpty()) {
                    /** @var PromotionStation $promotionStation */
                    foreach ($entitySite->getPromotionStations() as $promotionStationSite) {
                        $promotionStation = $entity->getPromotionStations()->filter(function (PromotionStation $element) use ($promotionStationSite) {
                            return $element->getId() == $promotionStationSite->getId();
                        })->first();
                        if (false === $promotionStation) {
//                            $entitySite->removePromotionStation($promotionStationSite);
                            $emSite->remove($promotionStationSite);
                        }
                    }
                }
                // *** fin gestion promotion station ***

                // *** gestion promotion logement periode ***
                /** @var PromotionLogementPeriode $logementPeriode */
                /** @var PromotionLogementPeriode $logementPeriodeSite */
                if (!empty($entity->getLogementPeriodes()) && !$entity->getLogementPeriodes()->isEmpty()) {
                    foreach ($entity->getLogementPeriodes() as $logementPeriode) {
                        $logementPeriodeSite = $entitySite->getLogementPeriodes()->filter(function (PromotionLogementPeriode $element) use ($logementPeriode) {
                            return ($element->getPeriode()->getId() == $logementPeriode->getPeriode()->getId()
                                and $element->getLogement()->getLogementUnifie()->getId() == $logementPeriode->getLogement()->getLogementUnifie()->getId()
                                and $element->getPromotion()->getPromotionUnifie()->getId() == $logementPeriode->getPromotion()->getPromotionUnifie()->getId()
                            );
                        })->first();
                        if (false === $logementPeriodeSite) {
                            $logementPeriodeSite = new PromotionLogementPeriode();
                            $entitySite->addLogementPeriode($logementPeriodeSite);

                            $logementPeriodeSite
                                ->setLogement($emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $logementPeriode->getLogement()->getLogementUnifie())))
                                ->setPeriode($emSite->find(Periode::class, $logementPeriode->getPeriode()));
                        }

                    }
                }

                if (!empty($entitySite->getLogementPeriodes()) && !$entitySite->getLogementPeriodes()->isEmpty()) {
                    foreach ($entitySite->getLogementPeriodes() as $logementPeriodeSite) {
                        $logementPeriode = $entity->getLogementPeriodes()->filter(function (PromotionLogementPeriode $element) use ($logementPeriodeSite) {
                            return ($element->getPeriode()->getId() == $logementPeriodeSite->getPeriode()->getId()
                                and $element->getLogement()->getLogementUnifie()->getId() == $logementPeriodeSite->getLogement()->getLogementUnifie()->getId()
                                and $element->getPromotion()->getPromotionUnifie()->getId() == $logementPeriodeSite->getPromotion()->getPromotionUnifie()->getId()
                            );
                        })->first();
                        if (false === $logementPeriode) {
                            $emSite->remove($logementPeriodeSite);
                        }
                    }
                }
                // *** fin gestion promotion logement periode ***

                // *** gestion promotion fournisseurPrestationAnnexe ***
                if (!empty($entity->getPromotionFournisseurPrestationAnnexes()) && !$entity->getPromotionFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexe */
                    foreach ($entity->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexe) {
                        $promotionFournisseurPrestationAnnexeSite = $entitySite->getPromotionFournisseurPrestationAnnexes()->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($promotionFournisseurPrestationAnnexe) {
                            return $element->getId() == $promotionFournisseurPrestationAnnexe->getId();
                        })->first();
                        if (false === $promotionFournisseurPrestationAnnexeSite) {
                            $promotionFournisseurPrestationAnnexeSite = new PromotionFournisseurPrestationAnnexe();
                            $entitySite->addPromotionFournisseurPrestationAnnex($promotionFournisseurPrestationAnnexeSite);
                            $promotionFournisseurPrestationAnnexeSite
                                ->setId($promotionFournisseurPrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionFournisseurPrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionFournisseurPrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionFournisseurPrestationAnnexe->getFournisseur()))
                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class, $promotionFournisseurPrestationAnnexe->getFournisseurPrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getPromotionFournisseurPrestationAnnexes()) && !$entitySite->getPromotionFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexe */
                    foreach ($entitySite->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexeSite) {
                        $promotionFournisseurPrestationAnnexe = $entity->getPromotionFournisseurPrestationAnnexes()->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($promotionFournisseurPrestationAnnexeSite) {
                            return $element->getId() == $promotionFournisseurPrestationAnnexeSite->getId();
                        })->first();
                        if (false === $promotionFournisseurPrestationAnnexe) {
//                            $entitySite->removePromotionFournisseurPrestationAnnexe($promotionFournisseurPrestationAnnexeSite);
                            $emSite->remove($promotionFournisseurPrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion promotion fournisseurPrestationAnnexe ***

                // *** gestion promotion famillePrestationAnnexe ***
                if (!empty($entity->getPromotionFamillePrestationAnnexes()) && !$entity->getPromotionFamillePrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnexe */
                    foreach ($entity->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnexe) {
                        $promotionFamillePrestationAnnexeSite = $entitySite->getPromotionFamillePrestationAnnexes()->filter(function (PromotionFamillePrestationAnnexe $element) use ($promotionFamillePrestationAnnexe) {
                            return $element->getId() == $promotionFamillePrestationAnnexe->getId();
                        })->first();
                        if (false === $promotionFamillePrestationAnnexeSite) {
                            $promotionFamillePrestationAnnexeSite = new PromotionFamillePrestationAnnexe();
                            $entitySite->addPromotionFamillePrestationAnnex($promotionFamillePrestationAnnexeSite);
                            $promotionFamillePrestationAnnexeSite
                                ->setId($promotionFamillePrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionFamillePrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionFamillePrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionFamillePrestationAnnexe->getFournisseur()))
                            ->setFamillePrestationAnnexe($emSite->find(FamillePrestationAnnexe::class, $promotionFamillePrestationAnnexe->getFamillePrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getPromotionFamillePrestationAnnexes()) && !$entitySite->getPromotionFamillePrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnexe */
                    foreach ($entitySite->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnexeSite) {
                        $promotionFamillePrestationAnnexe = $entity->getPromotionFamillePrestationAnnexes()->filter(function (PromotionFamillePrestationAnnexe $element) use ($promotionFamillePrestationAnnexeSite) {
                            return $element->getId() == $promotionFamillePrestationAnnexeSite->getId();
                        })->first();
                        if (false === $promotionFamillePrestationAnnexe) {
//                            $entitySite->removePromotionFamillePrestationAnnexe($promotionFamillePrestationAnnexeSite);
                            $emSite->remove($promotionFamillePrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion promotion famillePrestationAnnexe ***

                // *** gestion type fournisseur ***
                /** @var FamillePrestationAnnexe $typeFournisseur */
                /** @var FamillePrestationAnnexe $typeFournisseurSite */
                foreach ($entity->getTypeFournisseurs() as $typeFournisseur) {
                    $typeFournisseurSite = $entitySite->getTypeFournisseurs()->filter(function (FamillePrestationAnnexe $element) use ($typeFournisseur) {
                        return $element->getId() == $typeFournisseur->getId();
                    })->first();
                    if (false === $typeFournisseurSite) {
                        $entitySite->addTypeFournisseur($emSite->find(FamillePrestationAnnexe::class, $typeFournisseur));
                    }
                }
                foreach ($entitySite->getTypeFournisseurs() as $typeFournisseurSite) {
                    $typeFournisseur = $entity->getTypeFournisseurs()->filter(function (FamillePrestationAnnexe $element) use ($typeFournisseurSite) {
                        return $element->getId() == $typeFournisseurSite->getId();
                    })->first();
                    if (false === $typeFournisseur) {
                        $entitySite->removeTypeFournisseur($typeFournisseurSite);
                    }
                }
                // *** fin gestion type fournisseur ***

                // *** gestion promotion periode validite ***
                /** @var PeriodeValidite $periodeValidite */
                /** @var PeriodeValidite $periodeValiditeSite */
                foreach ($entity->getPeriodeValidites() as $periodeValidite) {
                    $periodeValiditeSite = $entitySite->getPeriodeValidites()->filter(function (PeriodeValidite $element) use ($periodeValidite) {
                        return $element->getId() == $periodeValidite->getId();
                    })->first();
                    if (false === $periodeValiditeSite) {
                        $entitySite->addPeriodeValidite($emSite->find(PeriodeValidite::class, $periodeValidite));
                    }
                }
                foreach ($entitySite->getPeriodeValidites() as $periodeValiditeSite) {
                    $periodeValidite = $entity->getPeriodeValidites()->filter(function (PeriodeValidite $element) use ($periodeValiditeSite) {
                        return $element->getId() == $periodeValiditeSite->getId();
                    })->first();
                    if (false === $periodeValidite) {
                        $entitySite->removePeriodeValidite($periodeValiditeSite);
                    }
                }
                // *** fin gestion promotion periode validite ***

                // *** gestion periode validite jour ***
                if (!empty($entity->getPromotionPeriodeValiditeJour())) {
                    if (empty($promotionPeriodeValiditeJour = $entitySite->getPromotionPeriodeValiditeJour())) {
                        $promotionPeriodeValiditeJour = new PromotionPeriodeValiditeJour();
                        $entitySite->setPromotionPeriodeValiditeJour($promotionPeriodeValiditeJour);
                    }
                    $promotionPeriodeValiditeJour
                        ->setJourDebut($entity->getPromotionPeriodeValiditeJour()->getJourDebut())
                        ->setJourFin($entity->getPromotionPeriodeValiditeJour()->getJourFin());
                } else {
                    $entitySite->setPromotionPeriodeValiditeJour();
                }
                // *** fin type periode validite jour ***

                // *** gestion periode validite date ***
                if (!empty($entity->getPromotionPeriodeValiditeDate())) {
                    if (empty($promotionPeriodeValiditeDate = $entitySite->getPromotionPeriodeValiditeDate())) {
                        $promotionPeriodeValiditeDate = new PromotionPeriodeValiditeDate();
                        $entitySite->setPromotionPeriodeValiditeDate($promotionPeriodeValiditeDate);
                    }
                    $promotionPeriodeValiditeDate
                        ->setDateDebut($entity->getPromotionPeriodeValiditeDate()->getDateDebut())
                        ->setDateFin($entity->getPromotionPeriodeValiditeDate()->getDateFin());
                } else {
                    $entitySite->setPromotionPeriodeValiditeDate();
                }
                // *** fin type periode validite date ***

                //  copie des données promotion
                $entitySite
                    ->setActif($entity->getActif())
                    ->setLibelle($entity->getLibelle())
                    ->setValeurRemise($entity->getValeurRemise())
                    ->setTypePeriodeSejour($entity->getTypePeriodeSejour())
                    ->setTypeApplication($entity->getTypeApplication())
                    ->setTypeRemise($entity->getTypeRemise());

                $emSite->persist($entityUnifieSite);

                $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $emSite->flush();
            }
        }
    }

    private function gestionPromotionTypeFournisseur($promotionUnifieId)
    {
        $kernel = $this->get('kernel');

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_promotion:promotion_type_fournisseur_command',
            'promotionUnifieId' => $promotionUnifieId,
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * Finds and displays a PromotionUnifie entity.
     *
     */
    public function showAction(PromotionUnifie $promotionUnifie)
    {
        $deleteForm = $this->createDeleteForm($promotionUnifie);

        return $this->render('@MondofutePromotion/promotionunifie/show.html.twig', array(
            'promotionUnifie' => $promotionUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a PromotionUnifie entity.
     *
     * @param PromotionUnifie $promotionUnifie The PromotionUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PromotionUnifie $promotionUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('promotion_delete', array('id' => $promotionUnifie->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing PromotionUnifie entity.
     *
     */
    public function editAction(Request $request, PromotionUnifie $promotionUnifie)
    {
        /** @var Promotion $promotion */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));

        // *** gestion promotion typeAffectation ***
        $affectations = TypeAffectation::$libelles;

        $originalPromotionTypeAffectations = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionTypeAffectations->set($promotion->getSite()->getId(), new ArrayCollection());
            foreach ($promotion->getPromotionTypeAffectations() as $typeAffectation) {
                $originalPromotionTypeAffectations->get($promotion->getSite()->getId())->add($typeAffectation);
            }
        }
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        // *** fin gestion promotion typeAffectation ***

        // *** gestion promotion fournisseur ***
        $originalPromotionFournisseurs = new ArrayCollection();

        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionFournisseurs->set($promotion->getSite()->getId(), new ArrayCollection());
            foreach ($promotion->getPromotionFournisseurs() as $promotionFournisseur) {
                $originalPromotionFournisseurs->get($promotion->getSite()->getId())->add($promotionFournisseur);
            }
        }
        // *** fin gestion promotion fournisseur ***

        // *** gestion promotion hebergement ***
        $originalPromotionHebergements = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
//            $fournisseurHebergements->set($promotion->getId(), new ArrayCollection());
            $originalPromotionHebergements->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionHebergement $promotionHebergement */
            foreach ($promotion->getPromotionHebergements() as $promotionHebergement) {
                $originalPromotionHebergements->get($promotion->getSite()->getId())->add($promotionHebergement);
            }
        }
        // *** fin gestion promotion hebergement ***

        // *** gestion promotion station ***
        $originalPromotionStations = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
//            $fournisseurStations->set($promotion->getId(), new ArrayCollection());
            $originalPromotionStations->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionStation $promotionStation */
            foreach ($promotion->getPromotionStations() as $promotionStation) {
                $originalPromotionStations->get($promotion->getSite()->getId())->add($promotionStation);
            }
        }
        // *** fin gestion promotion station ***

        // *** gestion promotion fournisseurPrestationAnnexe ***
        $originalPromotionFournisseurPrestationAnnexes = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionFournisseurPrestationAnnexes->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexe */
            foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexe) {
                $originalPromotionFournisseurPrestationAnnexes->get($promotion->getSite()->getId())->add($promotionFournisseurPrestationAnnexe);
            }
        }
        // *** fin gestion promotion fournisseurPrestationAnnexe ***

        // *** gestion promotion famillePrestationAnnexe ***
        $originalPromotionFamillePrestationAnnexes = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionFamillePrestationAnnexes->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex */
            foreach ($promotion->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnex) {
                $originalPromotionFamillePrestationAnnexes->get($promotion->getSite()->getId())->add($promotionFamillePrestationAnnex);
            }
        }
        // *** fin gestion promotion fournisseurPrestationAnnexe ***

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Promotion $promotion */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                if ($promotion->getActif()) {
                    array_push($sitesAEnregistrer, $promotion->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $this->ajouterPromotionsDansForm($promotionUnifie);

        $this->promotionsSortByAffichage($promotionUnifie);
        $deleteForm = $this->createDeleteForm($promotionUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType',
            $promotionUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                if (false === in_array($promotion->getSite()->getId(), $sitesAEnregistrer)) {
                    $promotion->setActif(false);
                } else {
                    $promotion->setActif(true);
                }
            }

            // *** gestion promotion typeAffectation ***
            $this->gestionPromotionTypeAffectation($promotionUnifie);

            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionTypeAffectationSites = $originalPromotionTypeAffectations->get($promotion->getSite()->getId());
                foreach ($originalPromotionTypeAffectationSites as $originalPromotionTypeAffectation) {
                    if (false === $promotion->getPromotionTypeAffectations()->contains($originalPromotionTypeAffectation)) {
                        $em->remove($originalPromotionTypeAffectation);
                    }
                }
            }
            // *** fin gestion promotion typeAffectation ***

            // *** gestion promotion fournisseur ***
            $this->gestionPromotionFournisseur($promotionUnifie);

            foreach ($promotionUnifie->getPromotions() as $promotion) {
//                if($promotion->getPromotionTypeAffectations()->contains(TypeAffectation::prestationAnnexe)){
//                    $em->refresh($promotion->getPromotionFournisseurs());
//                }
                $originalPromotionFournisseurSites = $originalPromotionFournisseurs->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionFournisseurs() as $promotionFournisseur) {
                    /** @var ArrayCollection $originalPromotionFournisseurSites */
                    /** @var PromotionFournisseur $promotionFournisseur */
                    $originalPromotionFournisseur = $originalPromotionFournisseurSites->filter(function (PromotionFournisseur $element) use ($promotionFournisseur) {
                        return ($element->getFournisseur() == $promotionFournisseur->getFournisseur()
                            and $element->getType() == $promotionFournisseur->getType()
                            and $element->getPromotion() == $promotionFournisseur->getPromotion());
                    })->first();
                    if (!empty($originalPromotionFournisseur)) {
//                        $delete = true;
                        // todo: voir suppression de sofurnisseur
                        // à bloquer si type existant
//                        foreach ($promotion->getTypeFournisseurs() as $typeFournisseur) {
//                            $type = $promotionFournisseur->getFournisseur()->getTypes()->filter(function (FamillePrestationAnnexe $element) use ($typeFournisseur) {
//                                return $element->getId() == $typeFournisseur->getId();
//                            })->first();
//                            if (false === $type) {
//                                $delete = false;
//                            }
//                        }
//                        if ($delete) {
                        $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
                        $promotion->addPromotionFournisseur($originalPromotionFournisseur);
//                        }
                    }
                }
                foreach ($originalPromotionFournisseurSites as $originalPromotionFournisseur) {
                    /** @var PromotionFournisseur $originalPromotionFournisseur */
                    if (false === $promotion->getPromotionFournisseurs()->contains($originalPromotionFournisseur)) {
                        $delete = true;
                        foreach ($promotion->getTypeFournisseurs() as $typeFournisseur) {
                            $type = $originalPromotionFournisseur->getFournisseur()->getTypes()->filter(function (FamillePrestationAnnexe $element) use ($typeFournisseur) {
                                return $element->getId() == $typeFournisseur->getId();
                            })->first();
                            if (false === $type) {
                                $delete = false;
                            }
                        }
                        if ($delete) {
                            $em->remove($originalPromotionFournisseur);
                        }
                    }
                }

//                foreach ($promotion->getPromotionFournisseurs() as $promotionFournisseur){
//                    dump($promotionFournisseur);
//                    if(!empty($em->getRepository(PromotionFournisseur::class)->findOneBy(['type' => $promotionFournisseur->getType(), 'promotion' => $promotionFournisseur->getPromotion(), 'fournisseur' => $promotionFournisseur->getFournisseur()] ))){
//                        dump('ici');
//                        $em->detach($promotionFournisseur);
//                    }
//                }
//                dump($promotion->getPromotionFournisseurs());
            }
//            die;

            // *** fin gestion promotion fournisseur ***

            // *** gestion promotion hebergement ***
            $this->gestionPromotionHebergement($promotionUnifie);

            /** @var PromotionHebergement $originalPromotionHebergement */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionHebergementSites = $originalPromotionHebergements->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionHebergements() as $promotionHebergement) {
                    /** @var ArrayCollection $originalPromotionHebergementSites */
                    /** @var PromotionHebergement $promotionHebergement */
                    $originalPromotionHebergement = $originalPromotionHebergementSites->filter(function (PromotionHebergement $element) use ($promotionHebergement) {
                        return ($element->getHebergement() == $promotionHebergement->getHebergement()
                            and $element->getFournisseur() == $promotionHebergement->getFournisseur()
                            and $element->getPromotion() == $promotionHebergement->getPromotion());
                    })->first();
                    if (!empty($originalPromotionHebergement)) {
                        $promotion->getPromotionHebergements()->removeElement($promotionHebergement);
                        $promotion->addPromotionHebergement($originalPromotionHebergement);
                    }
                }
                foreach ($originalPromotionHebergementSites as $originalPromotionHebergement) {
                    if (false === $promotion->getPromotionHebergements()->contains($originalPromotionHebergement)) {
                        $promotion->getPromotionHebergements()->removeElement($originalPromotionHebergement);
                        $em->remove($originalPromotionHebergement);
                    }
                }
            }
            // *** fin gestion promotion hebergement ***

            // *** gestion promotion station ***
            $this->gestionPromotionStation($promotionUnifie);

            /** @var PromotionStation $originalPromotionStation */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionStationSites = $originalPromotionStations->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionStations() as $promotionStation) {
                    /** @var ArrayCollection $originalPromotionStationSites */
                    /** @var PromotionStation $promotionStation */
                    $originalPromotionStation = $originalPromotionStationSites->filter(function (PromotionStation $element) use ($promotionStation) {
                        return ($element->getStation() == $promotionStation->getStation()
                            and $element->getFournisseur() == $promotionStation->getFournisseur()
                            and $element->getPromotion() == $promotionStation->getPromotion());
                    })->first();
                    if (!empty($originalPromotionStation)) {
                        $promotion->getPromotionStations()->removeElement($promotionStation);
                        $promotion->addPromotionStation($originalPromotionStation);
                    }
                }
                foreach ($originalPromotionStationSites as $originalPromotionStation) {
                    if (false === $promotion->getPromotionStations()->contains($originalPromotionStation)) {
                        $promotion->getPromotionStations()->removeElement($originalPromotionStation);
                        $em->remove($originalPromotionStation);
                    }
                }
            }
            // *** fin gestion promotion station ***

            // *** gestion promotion fournisseurPrestationAnnexe ***
            $this->gestionPromotionFournisseurPrestationAnnexe($promotionUnifie);

            /** @var PromotionFournisseurPrestationAnnexe $originalPromotionFournisseurPrestationAnnexe */
            /** @var PromotionFournisseurPrestationAnnexe $originalPromotionFournisseurPrestationAnnexe */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionFournisseurPrestationAnnexeSites = $originalPromotionFournisseurPrestationAnnexes->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnex) {
                    /** @var ArrayCollection $originalPromotionFournisseurPrestationAnnexeSites */
                    /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex */
                    $originalPromotionFournisseurPrestationAnnexe = $originalPromotionFournisseurPrestationAnnexeSites->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($promotionFournisseurPrestationAnnex) {
                        return ($element->getFournisseurPrestationAnnexe() == $promotionFournisseurPrestationAnnex->getFournisseurPrestationAnnexe()
                            and $element->getFournisseur() == $promotionFournisseurPrestationAnnex->getFournisseur()
                            and $element->getPromotion() == $promotionFournisseurPrestationAnnex->getPromotion());
                    })->first();
                    if (!empty($originalPromotionFournisseurPrestationAnnexe)) {
                        $promotion->getPromotionFournisseurPrestationAnnexes()->removeElement($promotionFournisseurPrestationAnnex);
                        $promotion->addPromotionFournisseurPrestationAnnex($originalPromotionFournisseurPrestationAnnexe);
                    }
                }
                foreach ($originalPromotionFournisseurPrestationAnnexeSites as $originalPromotionFournisseurPrestationAnnexe) {
                    if (false === $promotion->getPromotionFournisseurPrestationAnnexes()->contains($originalPromotionFournisseurPrestationAnnexe)) {
                        $promotion->getPromotionFournisseurPrestationAnnexes()->removeElement($originalPromotionFournisseurPrestationAnnexe);
                        $em->remove($originalPromotionFournisseurPrestationAnnexe);
                    }
                }
            }
            // *** fin gestion promotion fournisseurPrestationAnnexe ***

            // *** gestion promotion famillePrestationAnnexe ***
            $this->gestionPromotionFamillePrestationAnnexe($promotionUnifie);

            /** @var PromotionFamillePrestationAnnexe $originalPromotionFamillePrestationAnnexe */
            /** @var PromotionFamillePrestationAnnexe $originalPromotionFamillePrestationAnnexe */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionFamillePrestationAnnexeSites = $originalPromotionFamillePrestationAnnexes->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnex) {
                    /** @var ArrayCollection $originalPromotionFamillePrestationAnnexeSites */
                    /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex */
                    $originalPromotionFamillePrestationAnnexe = $originalPromotionFamillePrestationAnnexeSites->filter(function (PromotionFamillePrestationAnnexe $element) use ($promotionFamillePrestationAnnex) {
                        return ($element->getFamillePrestationAnnexe() == $promotionFamillePrestationAnnex->getFamillePrestationAnnexe()
                            and $element->getFournisseur() == $promotionFamillePrestationAnnex->getFournisseur()
                            and $element->getPromotion() == $promotionFamillePrestationAnnex->getPromotion());
                    })->first();
                    if (!empty($originalPromotionFamillePrestationAnnexe)) {
                        $promotion->getPromotionFamillePrestationAnnexes()->removeElement($promotionFamillePrestationAnnex);
                        $promotion->addPromotionFamillePrestationAnnex($originalPromotionFamillePrestationAnnexe);
                    }
                }
                foreach ($originalPromotionFamillePrestationAnnexeSites as $originalPromotionFamillePrestationAnnexe) {
                    if (false === $promotion->getPromotionFamillePrestationAnnexes()->contains($originalPromotionFamillePrestationAnnexe)) {
                        $promotion->getPromotionFamillePrestationAnnexes()->removeElement($originalPromotionFamillePrestationAnnexe);
                        $em->remove($originalPromotionFamillePrestationAnnexe);
                    }
                }
            }
            // *** fin gestion promotion famillePrestationAnnexe ***

            // *** gestion promotion logement periode ***
            /** @var PromotionLogementPeriode $logementPeriode */
            foreach ($promotionUnifie->getPromotions() as $key => $promotion) {
                if (!empty($request->get('promotion_logement_periode')[$key]) and !empty($request->get('promotion_logement_periode')[$key]['logements']) and !empty($request->get('promotion_logement_periode')[$key]['periodes'])) {
                    $promotion_logement_periode = $request->get('promotion_logement_periode')[$key];
                    foreach ($promotion_logement_periode['logements'] as $logement) {
                        $logementEntity = $em->find(Logement::class, $logement);
                        foreach ($promotion_logement_periode['periodes'] as $periode) {
                            $promotionLogementPeriode = $promotion->getLogementPeriodes()->filter(function (PromotionLogementPeriode $element) use ($logement, $periode, $promotion) {
                                return ($element->getLogement()->getId() == $logement and $element->getPeriode()->getId() == $periode and $element->getPromotion() == $promotion);
                            })->first();
                            if (false === $promotionLogementPeriode) {
                                $promotionLogementPeriode = new PromotionLogementPeriode();
                                $promotion->addLogementPeriode($promotionLogementPeriode);
                                $promotionLogementPeriode
                                    ->setPromotion($promotion)
                                    ->setLogement($logementEntity)
                                    ->setPeriode($em->find(Periode::class, $periode));
                            }
                        }
                    }
                    foreach ($promotion->getLogementPeriodes() as $logementPeriode) {
                        if (!in_array($logementPeriode->getLogement()->getId(), $promotion_logement_periode['logements'])) {
                            $promotion->getLogementPeriodes()->removeElement($logementPeriode);
                            $em->remove($logementPeriode);
                        }
                        if (!in_array($logementPeriode->getPeriode()->getId(), $promotion_logement_periode['periodes'])) {
                            $promotion->getLogementPeriodes()->removeElement($logementPeriode);
                            $em->remove($logementPeriode);
                        }
                    }
                } else {
                    foreach ($promotion->getLogementPeriodes() as $logementPeriode) {
                        $promotion->getLogementPeriodes()->removeElement($logementPeriode);
                        $em->remove($logementPeriode);
                    }
                }
            }
            // *** fin gestion promotion logement periode ***

            // *** gestion typePeriodeSejour ***
            $this->gestionTypePeriodeSejour($promotionUnifie);
            // *** fin gestion typePeriodeSejour ***

            $em->persist($promotionUnifie);
            $em->flush();

            $this->copieVersSites($promotionUnifie);

            $this->gestionPromotionTypeFournisseur($promotionUnifie->getId());

//            // *** gestion promotion logement ***
//            $this->gestionPromotionLogement($promotionUnifie);
//            // *** fin gestion promotion logement ***

            // add flash messages
            /** @var Session $session */
            $this->addFlash('success', 'La promotion a bien été modifié.');

            return $this->redirectToRoute('promotion_edit', array('id' => $promotionUnifie->getId()));
        }

        return $this->render('@MondofutePromotion/promotionunifie/edit.html.twig', array(
            'entity' => $promotionUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
//            'promotionClients' => $originalPromotionClients,
            'affectations' => $affectations,
            'panelPromotion' => true,
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
//            'fournisseurHebergements' => $fournisseurHebergements,
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
//            'fournisseurFournisseurPrestationAnnexes' => $fournisseurFournisseurPrestationAnnexes,
        ));
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionFournisseur($promotionUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PromotionFournisseur $promotionFournisseurSite */
        /** @var PromotionFournisseur $promotionFournisseurCrm */
        /** @var PromotionFournisseur $fournisseur */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
                    $promotion->getPromotionFournisseurs()->removeElement($fournisseur);
                } else {
                    $fournisseur->setPromotion($promotion);
                }
            }
        }
        $promotionFournisseurCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionFournisseurs();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionFournisseurCrms as $promotionFournisseurCrm) {
            $fournisseurs->add($promotionFournisseurCrm);
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                foreach ($promotionFournisseurCrms as $key => $fournisseur) {
                    $fournisseurSite = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($fournisseur) {
                        return ($element->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId()
                            and $element->getType() == $fournisseur->getType()
                            and $element->getPromotion()->getId() == $fournisseur->getPromotion()->getId()
                        );
                    })->first();
                    if (false === $fournisseurSite) {
                        if (empty($em->getRepository(PromotionFournisseur::class)->findOneBy(['type' => $fournisseur->getType(), 'promotion' => $promotion, 'fournisseur' => $fournisseur->getFournisseur()]))) {
                            $newFournisseur = new PromotionFournisseur();
                            $promotion->addPromotionFournisseur($newFournisseur);
                            $newFournisseur
                                ->setFournisseur($fournisseur->getFournisseur())
                                ->setType($fournisseur->getType());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionHebergement($promotionUnifie)
    {
        /** @var Hebergement $hebergement */
        /** @var PromotionHebergement $promotionHebergementCrm */
        /** @var PromotionHebergement $promotionHebergement */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionHebergements() as $promotionHebergement) {
                if (empty($promotionHebergement->getHebergement())) {
                    $promotion->getPromotionHebergements()->removeElement($promotionHebergement);
                } else {
                    $promotionHebergement->setPromotion($promotion);
                }
            }
        }
        $promotionHebergementCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionHebergements();
        $hebergements = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionHebergementCrms as $promotionHebergementCrm) {
            $hebergements->add($promotionHebergementCrm->getHebergement());
            $fournisseurs->add($promotionHebergementCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $hebergementSites = new ArrayCollection();
                foreach ($promotion->getPromotionHebergements() as $promotionHebergementSite) {
                    $hebergementSites->add($promotionHebergementSite->getHebergement());
                }

                foreach ($hebergements as $key => $hebergement) {
                    $hebergementSite = $hebergement->getHebergementUnifie()->getHebergements()->filter(function (Hebergement $element) use ($promotion) {
                        return $element->getSite() == $promotion->getSite();
                    })->first();
                    if (false === $hebergementSites->contains($hebergementSite)) {
                        $newHebergement = new PromotionHebergement();
                        $promotion->addPromotionHebergement($newHebergement);
                        $newHebergement
                            ->setHebergement($hebergementSite)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionStation($promotionUnifie)
    {
        /** @var Station $station */
        /** @var PromotionStation $promotionStationCrm */
        /** @var PromotionStation $promotionStation */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionStations() as $promotionStation) {
                if (empty($promotionStation->getStation())) {
                    $promotion->getPromotionStations()->removeElement($promotionStation);
                } else {
                    $promotionStation->setPromotion($promotion);
                }
            }
        }
        $promotionStationCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionStations();
        $stations = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionStationCrms as $promotionStationCrm) {
            $stations->add($promotionStationCrm->getStation());
            $fournisseurs->add($promotionStationCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $stationSites = new ArrayCollection();
                foreach ($promotion->getPromotionStations() as $promotionStationSite) {
                    $stationSites->add($promotionStationSite->getStation());
                }

                foreach ($stations as $key => $station) {
                    $stationSite = $station->getStationUnifie()->getStations()->filter(function (Station $element) use ($promotion) {
                        return $element->getSite() == $promotion->getSite();
                    })->first();
                    if (false === $stationSites->contains($stationSite)) {
                        $newStation = new PromotionStation();
                        $promotion->addPromotionStation($newStation);
                        $newStation
                            ->setStation($stationSite)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionFournisseurPrestationAnnexe($promotionUnifie)
    {
        /** @var PromotionFournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexeCrm */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $fournisseurPrestationAnnexe) {
                if (empty($fournisseurPrestationAnnexe->getFournisseurPrestationAnnexe())) {
                    $promotion->getPromotionFournisseurPrestationAnnexes()->removeElement($fournisseurPrestationAnnexe);
                } else {
                    $fournisseurPrestationAnnexe->setPromotion($promotion);
                }
            }
        }
        $promotionFournisseurPrestationAnnexeCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionFournisseurPrestationAnnexes();
        $fournisseurPrestationAnnexes = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionFournisseurPrestationAnnexeCrms as $promotionFournisseurPrestationAnnexeCrm) {
            $fournisseurPrestationAnnexes->add($promotionFournisseurPrestationAnnexeCrm->getFournisseurPrestationAnnexe());
            $fournisseurs->add($promotionFournisseurPrestationAnnexeCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $fournisseurPrestationAnnexeSites = new ArrayCollection();
                foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexeSite) {
                    $fournisseurPrestationAnnexeSites->add($promotionFournisseurPrestationAnnexeSite->getFournisseurPrestationAnnexe());
                }
                foreach ($fournisseurPrestationAnnexes as $key => $fournisseurPrestationAnnexe) {
                    if (false === $fournisseurPrestationAnnexeSites->contains($fournisseurPrestationAnnexe)) {
                        $newFournisseurPrestationAnnexe = new PromotionFournisseurPrestationAnnexe();
                        $promotion->addPromotionFournisseurPrestationAnnex($newFournisseurPrestationAnnexe);
                        $newFournisseurPrestationAnnexe
                            ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionFamillePrestationAnnexe($promotionUnifie)
    {
        /** @var PromotionFamillePrestationAnnexe $famillePrestationAnnexe */
        /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnexeCrm */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionFamillePrestationAnnexes() as $famillePrestationAnnexe) {
                if (empty($famillePrestationAnnexe->getFamillePrestationAnnexe())) {
                    $promotion->getPromotionFamillePrestationAnnexes()->removeElement($famillePrestationAnnexe);
                } else {
                    $famillePrestationAnnexe->setPromotion($promotion);
                }
            }
        }
        $promotionFamillePrestationAnnexeCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionFamillePrestationAnnexes();
        $famillePrestationAnnexes = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionFamillePrestationAnnexeCrms as $promotionFamillePrestationAnnexeCrm) {
            $famillePrestationAnnexes->add($promotionFamillePrestationAnnexeCrm->getFamillePrestationAnnexe());
            $fournisseurs->add($promotionFamillePrestationAnnexeCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $famillePrestationAnnexeSites = new ArrayCollection();
                foreach ($promotion->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnexeSite) {
                    $famillePrestationAnnexeSites->add($promotionFamillePrestationAnnexeSite->getFamillePrestationAnnexe());
                }
                foreach ($famillePrestationAnnexes as $key => $famillePrestationAnnexe) {
                    if (false === $famillePrestationAnnexeSites->contains($famillePrestationAnnexe)) {
                        $newFamillePrestationAnnexe = new PromotionFamillePrestationAnnexe();
                        $promotion->addPromotionFamillePrestationAnnex($newFamillePrestationAnnexe);
                        $newFamillePrestationAnnexe
                            ->setFamillePrestationAnnexe($famillePrestationAnnexe)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    public function getFournisseurHebergementsAction($promotionId, $fournisseurId, $siteId)
    {
        $em = $this->getDoctrine()->getManager();
        $hebergements = $em->getRepository(HebergementUnifie::class)->getFournisseurHebergements($fournisseurId, $this->container->getParameter('locale'), $siteId);

        $promotionHebergements = $em->getRepository(PromotionHebergement::class)->findBy(array('promotion' => $promotionId, 'fournisseur' => $fournisseurId));

        $promotionUnifie = new PromotionUnifie();
//        $promotion = new Promotion();
        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionUnifie->addPromotion($promotion);
        foreach ($promotionHebergements as $promotionHebergement) {
            $promotion->addPromotionHebergement($promotionHebergement);
        }

        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

        return $this->render('@MondofutePromotion/promotionunifie/get-promotion-fournisseur-hebergements.html.twig', array(
            'hebergements' => $hebergements,
            'promotionId' => $promotionId,
            'fournisseurId' => $fournisseurId,
            'keyPromotion' => '__keyPromotion__',
            'promotion' => $form->children['promotions'][0],
        ));
    }

    public function getFournisseurPrestationAnnexesAction($promotionId, $fournisseurId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurPrestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->getFournisseurPrestationAnnexes($fournisseurId, $this->container->getParameter('locale'));
//        $prestationAnnexes = new ArrayCollection();

        $promotionFournisseurPrestationAnnexes = $em->getRepository(PromotionFournisseurPrestationAnnexe::class)->findBy(array('promotion' => $promotionId, 'fournisseur' => $fournisseurId));
        $promotionFamillePrestationAnnexes = $em->getRepository(PromotionFamillePrestationAnnexe::class)->findBy(array('promotion' => $promotionId, 'fournisseur' => $fournisseurId));
        $promotionUnifie = new PromotionUnifie();
        $promotion = new Promotion();
        $promotionUnifie->addPromotion($promotion);
        foreach ($promotionFournisseurPrestationAnnexes as $promotionFournisseurPrestationAnnex) {
            $promotion->addPromotionFournisseurPrestationAnnex($promotionFournisseurPrestationAnnex);
        }
        foreach ($promotionFamillePrestationAnnexes as $promotionFamillePrestationAnnex) {
            $promotion->addPromotionFamillePrestationAnnex($promotionFamillePrestationAnnex);
        }

        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

        return $this->render('@MondofutePromotion/promotionunifie/get-promotion-fournisseur-prestation-annexes.html.twig', array(
            'fournisseurPrestationAnnexes' => $fournisseurPrestationAnnexes,
            'promotionId' => $promotionId,
            'fournisseurId' => $fournisseurId,
            'keyPromotion' => '__keyPromotion__',
            'promotion' => $form->children['promotions'][0],
        ));
    }

    public function getFournisseurPrestationannexePeriodeValiditeAction($fournisseurPrestationAnnexeId, $keyPromotion)
    {
        /** @var PeriodeValidite $periodeValidite */
        /** @var PrestationAnnexeTarif $tarif */
        /** @var FournisseurPrestationAnnexeParam $param */
        $em = $this->getDoctrine()->getManager();
        $fournisseurPrestationAnnexe = $em->find(FournisseurPrestationAnnexe::class, $fournisseurPrestationAnnexeId);
        $periodeValidites = new ArrayCollection();
        foreach ($fournisseurPrestationAnnexe->getParams() as $param) {
            foreach ($param->getTarifs() as $tarif) {
                foreach ($tarif->getPeriodeValidites() as $periodeValidite) {
                    $periodeValidites->add($periodeValidite);
                }
            }
        }

//        $promotionUnifie = new PromotionUnifie();
//        $promotion = new Promotion();
//        $promotionUnifie->getPromotions()->set($keyPromotion , $promotion);
//
//        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

        return $this->render('@MondofutePromotion/promotionunifie/modal-body-fournisseur-prestation-annexe-periode-validite.html.twig', array(
            'periodeValidites' => $periodeValidites,
            'keyPromotion' => $keyPromotion,
            'fournisseurPrestationAnnexeId' => $fournisseurPrestationAnnexeId,
//            'form' => $form
        ));
    }

    public function getFournisseurPrestationannexePeriodeValiditeValuesAction($fournisseurPrestationAnnexeId, $keyPromotion, $promotionId)
    {
        /** @var PeriodeValidite $periodeValidite */
        /** @var PrestationAnnexeTarif $tarif */
        /** @var FournisseurPrestationAnnexeParam $param */
        $em = $this->getDoctrine()->getManager();
        $fournisseurPrestationAnnexe = $em->find(FournisseurPrestationAnnexe::class, $fournisseurPrestationAnnexeId);
        $periodeValidites = new ArrayCollection();
        foreach ($fournisseurPrestationAnnexe->getParams() as $param) {
            foreach ($param->getTarifs() as $tarif) {
                foreach ($tarif->getPeriodeValidites() as $periodeValidite) {
                    $periodeValidites->add($periodeValidite);
                }
            }
        }

        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionPeriodeValidites = $promotion->getPeriodeValidites();

        return $this->render('@MondofutePromotion/promotionunifie/get-fournisseur-prestation-annexe-periode-validite-values.html.twig', array(
            'periodeValidites' => $periodeValidites,
            'promotionId' => $promotionId,
            'keyPromotion' => $keyPromotion,
            'fournisseurPrestationAnnexeId' => $fournisseurPrestationAnnexeId,
            'promotionPeriodeValidites' => $promotionPeriodeValidites
        ));
    }

    public function getLogementValuesAction($fournisseurId, $hebergementId, $keyPromotion, $promotionId)
    {
        $em = $this->getDoctrine()->getManager();

        $hebergement = $em->find(Hebergement::class, $hebergementId);

        $logements = $em->getRepository(Logement::class)->findByFournisseurHebergement($fournisseurId, $hebergement->getHebergementUnifie()->getId(), $hebergement->getSite()->getId());


        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionLogementPeriodes = $promotion->getLogementPeriodes();


        return $this->render('@MondofutePromotion/promotionunifie/get-promotion-logement.html.twig', array(
            'keyPromotion' => $keyPromotion,
            'hebergementId' => $hebergementId,
            'logements' => $logements,
            'fournisseurId' => $fournisseurId,
            'promotionLogementPeriodes' => $promotionLogementPeriodes
        ));


    }

    public function getLogementsAction($fournisseurId, $hebergementId, $keyPromotion)
    {
        $em = $this->getDoctrine()->getManager();
//        $periodes = $em->getRepository(LogementPeriodeLocatif::class)->findByPrixPublicNotEmpty($fournisseurId, $hebergementId);
//        dump($periodes);
        $hebergement = $em->find(Hebergement::class, $hebergementId);

        $logements = $em->getRepository(Logement::class)->findByFournisseurHebergement($fournisseurId, $hebergement->getHebergementUnifie()->getId(), $hebergement->getSite()->getId());

        return $this->render('@MondofutePromotion/promotionunifie/modal-body-logement.html.twig', array(
//            'logementPeriodes' => $periodes,
//            'promotionId' => $promotionId,
            'keyPromotion' => $keyPromotion,
            'hebergementId' => $hebergementId,
            'logements' => $logements,
            'fournisseurId' => $fournisseurId,
//            'promotionPeriodeValidites' => $promotionPeriodeValidites
        ));

    }

    /**
     * Deletes a PromotionUnifie entity.
     *
     */
    public function deleteAction(Request $request, PromotionUnifie $promotionUnifie)
    {
        /** @var Promotion $promotionSite */
        /** @var Promotion $promotion */
        $form = $this->createDeleteForm($promotionUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $promotionUnifieSite = $emSite->find(PromotionUnifie::class, $promotionUnifie);
                if (!empty($promotionUnifieSite)) {
                    $emSite->remove($promotionUnifieSite);
                    $emSite->flush();
                }
            }

            $em->remove($promotionUnifie);
            $em->flush();

            $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('promotion_index');
    }

    public function getPanelHebergementAction($promotionId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $promotionUnifie = new PromotionUnifie();
        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionUnifie->addPromotion($promotion);
        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

        return $this->render('@MondofutePromotion/promotionunifie/panel-hebergement.html.twig', array(
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
            'promotion' => $form->children['promotions'][0],
            'keyPromotion' => '_keyPromotion_'
        ));
    }

    public function getPeriodesAction($keyPromotion, $promotionId)
    {
        $em = $this->getDoctrine()->getManager();
        $periodes = $em->getRepository(LogementPeriodeLocatif::class)->findByPrixPublicNotEmpty();
        $typePeriodes = $em->getRepository(TypePeriode::class)->findAll();
        $collectionPeriodes = new ArrayCollection();
        foreach ($typePeriodes as $typePeriode) {
            $collectionPeriodes->set($typePeriode->getId(), new ArrayCollection());
        }
        foreach ($periodes as $periode) {
            $collectionPeriodes->get($periode['typeId'])->add($periode);
        }

        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionLogementPeriodes = $promotion->getLogementPeriodes();

        return $this->render('@MondofutePromotion/promotionunifie/get-promotion-periode.html.twig', array(
            'collectionPeriodes' => $collectionPeriodes,
            'keyPromotion' => $keyPromotion,
            'typePeriodes' => $typePeriodes,
            'promotionLogementPeriodes' => $promotionLogementPeriodes,
        ));
    }

    public function getPrestationAnnexeAction($promotionId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        $promotionUnifie = new PromotionUnifie();
        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionUnifie->addPromotion($promotion);
        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();


        return $this->render('@MondofutePromotion/promotionunifie/panel-prestation-annexe.html.twig', array(
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
            'promotion' => $form->children['promotions'][0],
            'keyPromotion' => '_keyPromotion_'
        ));
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionLogement($promotionUnifie)
    {
        $em = $this->getDoctrine()->getManager();

        $job = new Job('creer:promotionLogementByPromotionUnifie',
            array(
                'promotionUnifieId' => $promotionUnifie->getId()
            ), true, 'promotionLogementByPromotionUnifie');
        $em->persist($job);
        $em->flush();
    }
}
