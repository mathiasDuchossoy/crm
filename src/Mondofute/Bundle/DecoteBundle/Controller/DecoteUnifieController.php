<?php

namespace Mondofute\Bundle\DecoteBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use JMS\JobQueueBundle\Entity\Job;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\DecoteBundle\Entity\CanalDecote;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFamillePrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseur;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseurPrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteHebergement;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteLogement;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteLogementPeriode;
use Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeSejourDate;
use Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeValiditeDate;
use Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeValiditeJour;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteStation;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteTraduction;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteTypeAffectation;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteUnifie;
use Mondofute\Bundle\DecoteBundle\Entity\TypeAffectation;
use Mondofute\Bundle\DecoteBundle\Entity\TypePeriodeSejour;
use Mondofute\Bundle\DecoteBundle\Entity\TypePeriodeValidite;
use Mondofute\Bundle\DecoteBundle\Form\DecoteUnifieType;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * DecoteUnifie controller.
 *
 */
class DecoteUnifieController extends Controller
{
    const DecotePeriodeValidite = "HiDev\\Bundle\\DecoteBundle\\Entity\\DecotePeriodeValidite";

    /**
     * Lists all DecoteUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(DecoteUnifie::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofuteDecoteBundle:DecoteUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'decote_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteDecoteBundle:DecoteUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteDecote/decoteunifie/index.html.twig', array(
            'decoteUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new DecoteUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();
        $affectations = TypeAffectation::$libelles;

        $sitesAEnregistrer = $request->get('sites');

        $decoteUnifie = new DecoteUnifie();

        $this->ajouterDecotesDansForm($decoteUnifie);
        $this->decotesSortByAffichage($decoteUnifie);

        $coreController = $this->get('mondofute_core_bundle_controller');
        $coreController->setContainer($this->container);
        $coreController->addTraductions($decoteUnifie, 'decote');

        $form = $this->createForm('Mondofute\Bundle\DecoteBundle\Form\DecoteUnifieType', $decoteUnifie);
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirRadioVides();remplirChampsVide();')));

        $form->handleRequest($request);

        $errorCompatibiliteType = $this->testCompatibiliteType($decoteUnifie);

        if ($form->isSubmitted() && $form->isValid() && !$errorCompatibiliteType) {

            /** @var Decote $entity */

            // *** gestion decote typeAffectation ***
            $this->gestionDecoteTypeAffectation($decoteUnifie);
            // *** fin gestion decote typeAffectation ***

            /** @var Decote $decote */
            foreach ($decoteUnifie->getDecotes() as $decote) {
                if (false === in_array($decote->getSite()->getId(), $sitesAEnregistrer)) {
                    $decote->setActif(false);
                }
            }

            // *** gestion typePeriodeValidite ***
            $this->gestionTypePeriodeValidite($decoteUnifie);
            // *** fin gestion typePeriodeValidite ***

            // *** gestion typePeriodeSejour ***
            $this->gestionTypePeriodeSejour($decoteUnifie);
            // *** fin gestion typePeriodeSejour ***

            $em = $this->getDoctrine()->getManager();

            $em->persist($decoteUnifie);

            try {
                $em->flush();

                $this->copieVersSites($decoteUnifie);

                $this->gestionDecoteTypeFournisseur($decoteUnifie->getId());

                $this->addFlash('success', 'La decote a bien été créé.');

                return $this->redirectToRoute('decote_edit', array('id' => $decoteUnifie->getId()));
            } catch (Exception $e) {
//                switch ($e->getCode()){
//                    case 0:
//                        $this->addFlash('error', "Le code " . $decoteUnifie->getCode() . " existe déjà.");
//                        break;
//                    default:
//                        $this->addFlash('error', "Add not done: " . $e->getMessage());
//                        break;
//                }
                $this->addFlash('error', "Add not done: " . $e->getMessage());
            }
        }

        return $this->render('@MondofuteDecote/decoteunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $decoteUnifie,
            'form' => $form->createView(),
            'affectations' => $affectations,
            'fournisseursTypeHebergement' => new ArrayCollection(),
            'fournisseursPrestationAnnexe' => new ArrayCollection(),
            'langues' => $langues
        ));
    }

    /**
     * Ajouter les decotes qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DecoteUnifie $entityUnifie
     */
    private function ajouterDecotesDansForm(DecoteUnifie $entityUnifie)
    {
        /** @var Decote $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $entity = $entityUnifie->getDecotes()->filter(function (Decote $element) use ($site) {
                return $element->getSite() == $site;
            })->first();
            if (false === $entity) {
                $entity = new Decote();
                $entityUnifie->addDecote($entity);
                $entity->setSite($site);
            }
        }
    }

    /**
     * Classe les decotes par classementAffichage
     * @param DecoteUnifie $entity
     */
    private function decotesSortByAffichage(DecoteUnifie $entity)
    {
        // Trier les decotes en fonction de leurs ordre d'affichage
        $decotes = $entity->getDecotes(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $decotes->getIterator();
        unset($decotes);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (Decote $a, Decote $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $decotes = new ArrayCollection(iterator_to_array($iterator));

        // remplacé les decotes par ce nouveau tableau (une fonction 'set' a été créé dans Decote unifié)
        $entity->setDecotes($decotes);
    }

    /**
     * @param DecoteUnifie $entityUnifie
     * @return bool
     */
    private function testCompatibiliteType($entityUnifie)
    {
        /** @var Decote $entity */
        foreach ($entityUnifie->getDecotes() as $entity) {
            if (!$entity->getDecoteTypeAffectations()->isEmpty()) {
                if ($entity->getDecoteTypeAffectations()->first()->getTypeAffectation() == TypeAffectation::type && $entity->getTypePeriodeSejour() == TypePeriodeSejour::periode) {
                    $this->addFlash('error', 'Sur la fiche ' . $entity->getSite()->getLibelle() . ', une decote ne peut pas avoir l\'affectation "Type de fournisseur" et le type de periode de sejour à "Période"');
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteTypeAffectation($decoteUnifie)
    {
        /** @var DecoteTypeAffectation $decoteTypeAffectationCrm */
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            foreach ($decote->getDecoteTypeAffectations() as $affectation) {
                $affectation->setDecote($decote);
            }
            if (false === $decote->getDecoteTypeAffectations()->filter(function (DecoteTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::logement;
                })->first()
            ) {
                $decote->getDecoteHebergements()->clear();
                $decote->getLogementPeriodes()->clear();
                $decoteFournisseurs = $decote->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) {
                    return $element->getType() == TypeAffectation::logement;
                });
                foreach ($decoteFournisseurs as $decoteFournisseur) {
                    $decote->getDecoteFournisseurs()->removeElement($decoteFournisseur);
                }
            }
            if (false === $decote->getDecoteTypeAffectations()->filter(function (DecoteTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::prestationAnnexe;
                })->first()
            ) {
                $decote->getDecoteFamillePrestationAnnexes()->clear();
                $decote->getDecoteFournisseurPrestationAnnexes()->clear();
                $decoteFournisseurs = $decote->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) {
                    return $element->getType() == TypeAffectation::prestationAnnexe;
                });
//                $decoteFournisseurs = $em->getRepository(DecoteFournisseur::class)->findBy(['decote' => $decote, 'type' => TypeAffectation::prestationAnnexe]);
                foreach ($decoteFournisseurs as $decoteFournisseur) {
                    $decote->removeDecoteFournisseur($decoteFournisseur);
                }
            }
            if (false === $decote->getDecoteTypeAffectations()->filter(function (DecoteTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::type;
                })->first()
            ) {
                foreach ($decote->getTypeFournisseurs() as $typeFournisseur) {
                    $decote->removeTypeFournisseur($typeFournisseur);
                }
                $decoteFournisseurs = $decote->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) {
                    return $element->getType() == TypeAffectation::type;
                });
                foreach ($decoteFournisseurs as $decoteFournisseur) {
                    $decote->getDecoteFournisseurs()->removeElement($decoteFournisseur);
                }
            }
        }

        /* *** cas où l'on peut choisir plusieurs type affectations ***
       $decoteTypeAffectationCrms = $decoteUnifie->getDecotes()->filter(function (Decote $element) {
           return $element->getSite()->getCrm() == 1;
       })->first()->getDecoteTypeAffectations();
       $typeAffectations = new ArrayCollection();
       foreach ($decoteTypeAffectationCrms as $decoteTypeAffectationCrm) {
           $typeAffectations->add($decoteTypeAffectationCrm->getTypeAffectation());
       }
       foreach ($decoteUnifie->getDecotes() as $decote) {
           foreach ($decote->getDecoteTypeAffectations() as $affectation) {
               $affectation->setDecote($decote);
           }
           if ($decote->getSite()->getCrm() == 0) {
               $typeAffectationSites = new ArrayCollection();
               foreach ($decote->getDecoteTypeAffectations() as $decoteTypeAffectationSite) {
                   $typeAffectationSites->add($decoteTypeAffectationSite->getTypeAffectation());
               }
               foreach ($typeAffectations as $typeAffectation) {
                   if (false === $typeAffectationSites->contains($typeAffectation)) {
                       $newTypeAffectation = new DecoteTypeAffectation();
                       $decote->addDecoteTypeAffectation($newTypeAffectation);
                       $newTypeAffectation->setTypeAffectation($typeAffectation);
                   }
               }
           }
       }
        */

    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionTypePeriodeValidite($decoteUnifie)
    {
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            switch ($decote->getTypePeriodeValidite()) {
                case TypePeriodeValidite::permanent:
                    $decote->setDecotePeriodeValiditeDate();
                    $decote->setDecotePeriodeValiditeJour();
                    break;
                case TypePeriodeValidite::dateADate:
                    $decote->setDecotePeriodeValiditeJour();
                    break;
                case TypePeriodeValidite::periode:
                    $decote->setDecotePeriodeValiditeDate();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionTypePeriodeSejour($decoteUnifie)
    {
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            switch ($decote->getTypePeriodeSejour()) {
                case TypePeriodeSejour::permanent:
                    $decote->setDecotePeriodeSejourDate();
//                    $decote->setDecotePeriodeSejourJour();
                    break;
                case TypePeriodeSejour::dateADate:
//                    $decote->setDecotePeriodeSejourJour();
                    break;
                case TypePeriodeSejour::periode:
                    $decote->setDecotePeriodeSejourDate();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité decote
     * @param DecoteUnifie $entityUnifie
     */
    private function copieVersSites(DecoteUnifie $entityUnifie)
    {
        /** @var EntityManager $emSite */
        /** @var Decote $entity */
        /** @var Decote $entitySite */
        /** @var Decote $entityCrm */
//        Boucle sur les decotes afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entityUnifie->getDecotes() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (empty($entityUnifieSite = $emSite->find(DecoteUnifie::class, $entityUnifie))) {
                    $entityUnifieSite = new DecoteUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

                //  Récupération de la decote sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty($entitySite = $emSite->getRepository(Decote::class)->findOneBy(array('decoteUnifie' => $entityUnifieSite)))) {
                    $entitySite = new Decote();
                    $entityUnifieSite->addDecote($entitySite);
                    $entitySite->setSite($emSite->find(Site::class, $entity->getSite()));
                }

                // ***** gestion traductions *****
                /** @var DecoteTraduction $traduction */
                foreach ($entity->getTraductions() as $traduction) {
                    $traductionSite = $entitySite->getTraductions()->filter(function (DecoteTraduction $element) use ($traduction) {
                        return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                    })->first();
                    if (false === $traductionSite) {
                        $traductionSite = new DecoteTraduction();
                        $entitySite->addTraduction($traductionSite);
                        $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                    }
                    $traductionSite
                        ->setTitre($traduction->getTitre())
                        ->setDescription($traduction->getDescription());
                }
                // ***** fin gestion traductions *****

                // *** gestion decote typeAffectation ***
                if (!empty($entity->getDecoteTypeAffectations()) && !$entity->getDecoteTypeAffectations()->isEmpty()) {
                    /** @var DecoteTypeAffectation $decoteTypeAffectation */
                    foreach ($entity->getDecoteTypeAffectations() as $decoteTypeAffectation) {
                        $decoteTypeAffectationSite = $entitySite->getDecoteTypeAffectations()->filter(function (DecoteTypeAffectation $element) use ($decoteTypeAffectation) {
                            return $element->getId() == $decoteTypeAffectation->getId();
                        })->first();
                        if (false === $decoteTypeAffectationSite) {
                            $decoteTypeAffectationSite = new DecoteTypeAffectation();
                            $entitySite->addDecoteTypeAffectation($decoteTypeAffectationSite);
                            $decoteTypeAffectationSite
                                ->setId($decoteTypeAffectation->getId());

                            $metadata = $emSite->getClassMetadata(get_class($decoteTypeAffectationSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $decoteTypeAffectationSite
                            ->setTypeAffectation($decoteTypeAffectation->getTypeAffectation());
                    }
                }

                if (!empty($entitySite->getDecoteTypeAffectations()) && !$entitySite->getDecoteTypeAffectations()->isEmpty()) {
                    /** @var DecoteTypeAffectation $decoteTypeAffectation */
                    foreach ($entitySite->getDecoteTypeAffectations() as $decoteTypeAffectationSite) {
                        $decoteTypeAffectation = $entity->getDecoteTypeAffectations()->filter(function (DecoteTypeAffectation $element) use ($decoteTypeAffectationSite) {
                            return $element->getId() == $decoteTypeAffectationSite->getId();
                        })->first();
                        if (false === $decoteTypeAffectation) {
//                            $entitySite->removeDecoteTypeAffectation($decoteTypeAffectationSite);
                            $emSite->remove($decoteTypeAffectationSite);
                        }
                    }
                }
                // *** fin gestion decote typeAffectation ***

                // *** gestion decote fournisseur ***
                if (!empty($entity->getDecoteFournisseurs()) && !$entity->getDecoteFournisseurs()->isEmpty()) {
                    /** @var DecoteFournisseur $decoteFournisseur */
                    foreach ($entity->getDecoteFournisseurs() as $decoteFournisseur) {
                        $decoteFournisseurSite = $entitySite->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) use ($decoteFournisseur) {
                            return ($element->getDecote()->getDecoteUnifie()->getId() == $decoteFournisseur->getDecote()->getDecoteUnifie()->getId()
                                and $element->getFournisseur()->getId() == $decoteFournisseur->getFournisseur()->getId()
                                and $element->getType() == $decoteFournisseur->getType()
                            );
                        })->first();
                        if (false === $decoteFournisseurSite) {
                            $decoteFournisseurSite = new DecoteFournisseur();
                            $entitySite->addDecoteFournisseur($decoteFournisseurSite);
                        }
                        $decoteFournisseurSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $decoteFournisseur->getFournisseur()))
                            ->setType($decoteFournisseur->getType());
                    }
                }

                if (!empty($entitySite->getDecoteFournisseurs()) && !$entitySite->getDecoteFournisseurs()->isEmpty()) {
                    /** @var DecoteFournisseur $decoteFournisseur */
                    foreach ($entitySite->getDecoteFournisseurs() as $decoteFournisseurSite) {
                        $decoteFournisseur = $entity->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) use ($decoteFournisseurSite) {
                            return ($element->getDecote()->getDecoteUnifie()->getId() == $decoteFournisseurSite->getDecote()->getDecoteUnifie()->getId()
                                and $element->getFournisseur()->getId() == $decoteFournisseurSite->getFournisseur()->getId()
                                and $element->getType() == $decoteFournisseurSite->getType());
                        })->first();
                        if (false === $decoteFournisseur) {
                            $entitySite->removeDecoteFournisseur($decoteFournisseurSite);
                            $emSite->remove($decoteFournisseurSite);
                        }
                    }
                }
                // *** fin gestion decote fournisseur ***

                // *** gestion decote hebergement ***
                if (!empty($entity->getDecoteHebergements()) && !$entity->getDecoteHebergements()->isEmpty()) {
                    /** @var DecoteHebergement $decoteHebergement */
                    foreach ($entity->getDecoteHebergements() as $decoteHebergement) {
                        $decoteHebergementSite = $entitySite->getDecoteHebergements()->filter(function (DecoteHebergement $element) use ($decoteHebergement) {
                            return ($element->getFournisseur()->getId() == $decoteHebergement->getFournisseur()->getId() and
                                $element->getHebergement()->getHebergementUnifie()->getId() == $decoteHebergement->getHebergement()->getHebergementUnifie()->getId() and
                                $element->getDecote()->getDecoteUnifie()->getId() == $element->getDecote()->getDecoteUnifie()->getId());
                        })->first();
                        if (false === $decoteHebergementSite) {
                            $decoteHebergementSite = new DecoteHebergement();
                            $entitySite->addDecoteHebergement($decoteHebergementSite);
                        }

                        $decoteHebergementSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $decoteHebergement->getFournisseur()))
                            ->setHebergement($emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $decoteHebergement->getHebergement()->getHebergementUnifie())));
                    }
                }

                if (!empty($entitySite->getDecoteHebergements()) && !$entitySite->getDecoteHebergements()->isEmpty()) {
                    /** @var DecoteHebergement $decoteHebergement */
                    foreach ($entitySite->getDecoteHebergements() as $decoteHebergementSite) {
                        $decoteHebergement = $entity->getDecoteHebergements()->filter(function (DecoteHebergement $element) use ($decoteHebergementSite) {
                            return ($element->getFournisseur()->getId() == $decoteHebergementSite->getFournisseur()->getId() and
                                $element->getHebergement()->getHebergementUnifie()->getId() == $decoteHebergementSite->getHebergement()->getHebergementUnifie()->getId() and
                                $element->getDecote()->getDecoteUnifie()->getId() == $decoteHebergementSite->getDecote()->getDecoteUnifie()->getId());
                        })->first();
                        if (false === $decoteHebergement) {
                            $entitySite->removeDecoteHebergement($decoteHebergementSite);
                            $emSite->remove($decoteHebergementSite);
                        }
                    }
                }
                // *** fin gestion decote hebergement ***

                // *** gestion decote station ***
                if (!empty($entity->getDecoteStations()) && !$entity->getDecoteStations()->isEmpty()) {
                    /** @var DecoteStation $decoteStation */
                    foreach ($entity->getDecoteStations() as $decoteStation) {
                        $decoteStationSite = $entitySite->getDecoteStations()->filter(function (DecoteStation $element) use ($decoteStation) {
                            return ($element->getDecote()->getDecoteUnifie()->getId() == $decoteStation->getDecote()->getDecoteUnifie()->getId() and
                                $element->getFournisseur()->getId() == $decoteStation->getFournisseur()->getId() and
                                $element->getStation()->getStationUnifie()->getId() == $decoteStation->getStation()->getStationUnifie()->getId());
                        })->first();
                        if (false === $decoteStationSite) {
                            $decoteStationSite = new DecoteStation();
                            $entitySite->addDecoteStation($decoteStationSite);
                        }

                        $decoteStationSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $decoteStation->getFournisseur()))
                            ->setStation($emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $decoteStation->getStation()->getStationUnifie())));
                    }
                }

                if (!empty($entitySite->getDecoteStations()) && !$entitySite->getDecoteStations()->isEmpty()) {
                    /** @var DecoteStation $decoteStation */
                    foreach ($entitySite->getDecoteStations() as $decoteStationSite) {
                        $decoteStation = $entity->getDecoteStations()->filter(function (DecoteStation $element) use ($decoteStationSite) {
                            return ($element->getDecote()->getDecoteUnifie()->getId() == $decoteStationSite->getDecote()->getDecoteUnifie()->getId() and
                                $element->getFournisseur()->getId() == $decoteStationSite->getFournisseur()->getId() and
                                $element->getStation()->getStationUnifie()->getId() == $decoteStationSite->getStation()->getStationUnifie()->getId());
                        })->first();
                        if (false === $decoteStation) {
                            $entitySite->removeDecoteStation($decoteStationSite);
                            $emSite->remove($decoteStationSite);
                        }
                    }
                }
                // *** fin gestion decote station ***

                // *** gestion decote logement periode ***
                /** @var DecoteLogementPeriode $logementPeriode */
                /** @var DecoteLogementPeriode $logementPeriodeSite */
                if (!empty($entity->getLogementPeriodes()) && !$entity->getLogementPeriodes()->isEmpty()) {
                    foreach ($entity->getLogementPeriodes() as $logementPeriode) {
                        $logementPeriodeSite = $entitySite->getLogementPeriodes()->filter(function (DecoteLogementPeriode $element) use ($logementPeriode) {
                            return ($element->getPeriode()->getId() == $logementPeriode->getPeriode()->getId()
                                and $element->getLogement()->getLogementUnifie()->getId() == $logementPeriode->getLogement()->getLogementUnifie()->getId()
                                and $element->getDecote()->getDecoteUnifie()->getId() == $logementPeriode->getDecote()->getDecoteUnifie()->getId()
                            );
                        })->first();
                        if (false === $logementPeriodeSite) {
                            $logementPeriodeSite = new DecoteLogementPeriode();
                            $entitySite->addLogementPeriode($logementPeriodeSite);

                            $logementPeriodeSite
                                ->setLogement($emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $logementPeriode->getLogement()->getLogementUnifie())))
                                ->setPeriode($emSite->find(Periode::class, $logementPeriode->getPeriode()));
                        }

                    }
                }

                if (!empty($entitySite->getLogementPeriodes()) && !$entitySite->getLogementPeriodes()->isEmpty()) {
                    foreach ($entitySite->getLogementPeriodes() as $logementPeriodeSite) {
                        $logementPeriode = $entity->getLogementPeriodes()->filter(function (DecoteLogementPeriode $element) use ($logementPeriodeSite) {
                            return ($element->getPeriode()->getId() == $logementPeriodeSite->getPeriode()->getId()
                                and $element->getLogement()->getLogementUnifie()->getId() == $logementPeriodeSite->getLogement()->getLogementUnifie()->getId()
                                and $element->getDecote()->getDecoteUnifie()->getId() == $logementPeriodeSite->getDecote()->getDecoteUnifie()->getId()
                            );
                        })->first();
                        if (false === $logementPeriode) {
                            $emSite->remove($logementPeriodeSite);
                        }
                    }
                }
                // *** fin gestion decote logement periode ***

                // *** gestion decote logement ***
                /** @var DecoteLogement $logement */
                /** @var DecoteLogement $logementSite */
                foreach ($entity->getDecoteLogements() as $logement) {
                    $logementSite = $entitySite->getDecoteLogements()->filter(function (DecoteLogement $element) use ($logement) {
                        return ($element->getLogement()->getLogementUnifie()->getId() == $logement->getLogement()->getLogementUnifie()->getId()
                            and $element->getDecote()->getDecoteUnifie()->getId() == $logement->getDecote()->getDecoteUnifie()->getId()
                        );
                    })->first();
                    if (false === $logementSite) {
                        $logementSite = new DecoteLogement();
                        $entitySite->addDecoteLogement($logementSite);
                        $logementSite
                            ->setLogement($emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $logement->getLogement()->getLogementUnifie())));
                    }
                }

                foreach ($entitySite->getDecoteLogements() as $logementSite) {
                    $logement = $entity->getDecoteLogements()->filter(function (DecoteLogement $element) use ($logementSite) {
                        return ($element->getLogement()->getLogementUnifie()->getId() == $logementSite->getLogement()->getLogementUnifie()->getId()
                            and $element->getDecote()->getDecoteUnifie()->getId() == $logementSite->getDecote()->getDecoteUnifie()->getId()
                        );
                    })->first();
                    if (false === $logement) {
                        $entitySite->removeDecoteLogement($logementSite);
                        $emSite->remove($logementSite);
                    }
                }
                // *** fin gestion decote logement  ***

                // *** gestion decote fournisseurPrestationAnnexe ***
                if (!empty($entity->getDecoteFournisseurPrestationAnnexes()) && !$entity->getDecoteFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnexe */
                    foreach ($entity->getDecoteFournisseurPrestationAnnexes() as $decoteFournisseurPrestationAnnexe) {
                        $decoteFournisseurPrestationAnnexeSite = $entitySite->getDecoteFournisseurPrestationAnnexes()->filter(function (DecoteFournisseurPrestationAnnexe $element) use ($decoteFournisseurPrestationAnnexe) {
                            return ($element->getFournisseurPrestationAnnexe()->getId() == $decoteFournisseurPrestationAnnexe->getFournisseurPrestationAnnexe()->getId() and
                                $element->getFournisseur()->getId() == $decoteFournisseurPrestationAnnexe->getFournisseur()->getId() and
                                $element->getDecote()->getDecoteUnifie()->getId() == $decoteFournisseurPrestationAnnexe->getDecote()->getDecoteUnifie()->getId());
                        })->first();
                        if (false === $decoteFournisseurPrestationAnnexeSite) {
                            $decoteFournisseurPrestationAnnexeSite = new DecoteFournisseurPrestationAnnexe();
                            $entitySite->addDecoteFournisseurPrestationAnnex($decoteFournisseurPrestationAnnexeSite);
                        }

                        $decoteFournisseurPrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $decoteFournisseurPrestationAnnexe->getFournisseur()))
                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class, $decoteFournisseurPrestationAnnexe->getFournisseurPrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getDecoteFournisseurPrestationAnnexes()) && !$entitySite->getDecoteFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnexe */
                    foreach ($entitySite->getDecoteFournisseurPrestationAnnexes() as $decoteFournisseurPrestationAnnexeSite) {
                        $decoteFournisseurPrestationAnnexe = $entity->getDecoteFournisseurPrestationAnnexes()->filter(function (DecoteFournisseurPrestationAnnexe $element) use ($decoteFournisseurPrestationAnnexeSite) {
                            return ($element->getFournisseurPrestationAnnexe()->getId() == $decoteFournisseurPrestationAnnexeSite->getFournisseurPrestationAnnexe()->getId() and
                                $element->getFournisseur()->getId() == $decoteFournisseurPrestationAnnexeSite->getFournisseur()->getId() and
                                $element->getDecote()->getDecoteUnifie()->getId() == $decoteFournisseurPrestationAnnexeSite->getDecote()->getDecoteUnifie()->getId());
                        })->first();
                        if (false === $decoteFournisseurPrestationAnnexe) {
                            $entitySite->removeDecoteFournisseurPrestationAnnex($decoteFournisseurPrestationAnnexeSite);
                            $emSite->remove($decoteFournisseurPrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion decote fournisseurPrestationAnnexe ***

                // *** gestion decote famillePrestationAnnexe ***
                if (!empty($entity->getDecoteFamillePrestationAnnexes()) && !$entity->getDecoteFamillePrestationAnnexes()->isEmpty()) {
                    /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnexe */
                    foreach ($entity->getDecoteFamillePrestationAnnexes() as $decoteFamillePrestationAnnexe) {
                        $decoteFamillePrestationAnnexeSite = $entitySite->getDecoteFamillePrestationAnnexes()->filter(function (DecoteFamillePrestationAnnexe $element) use ($decoteFamillePrestationAnnexe) {
                            return ($element->getFournisseur()->getId() == $decoteFamillePrestationAnnexe->getFournisseur()->getId() and
                                $element->getFamillePrestationAnnexe()->getId() == $decoteFamillePrestationAnnexe->getFamillePrestationAnnexe()->getId() and
                                $element->getDecote()->getDecoteUnifie()->getId() == $decoteFamillePrestationAnnexe->getDecote()->getDecoteUnifie()->getId());
                        })->first();
                        if (false === $decoteFamillePrestationAnnexeSite) {
                            $decoteFamillePrestationAnnexeSite = new DecoteFamillePrestationAnnexe();
                            $entitySite->addDecoteFamillePrestationAnnex($decoteFamillePrestationAnnexeSite);
                        }

                        $decoteFamillePrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $decoteFamillePrestationAnnexe->getFournisseur()))
                            ->setFamillePrestationAnnexe($emSite->find(FamillePrestationAnnexe::class, $decoteFamillePrestationAnnexe->getFamillePrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getDecoteFamillePrestationAnnexes()) && !$entitySite->getDecoteFamillePrestationAnnexes()->isEmpty()) {
                    /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnexe */
                    foreach ($entitySite->getDecoteFamillePrestationAnnexes() as $decoteFamillePrestationAnnexeSite) {
                        $decoteFamillePrestationAnnexe = $entity->getDecoteFamillePrestationAnnexes()->filter(function (DecoteFamillePrestationAnnexe $element) use ($decoteFamillePrestationAnnexeSite) {
                            return $element->getFournisseur()->getId() == $decoteFamillePrestationAnnexeSite->getFournisseur()->getId() and
                                $element->getFamillePrestationAnnexe()->getId() == $decoteFamillePrestationAnnexeSite->getFamillePrestationAnnexe()->getId() and
                                $element->getDecote()->getDecoteUnifie()->getId() == $decoteFamillePrestationAnnexeSite->getDecote()->getDecoteUnifie()->getId();
                        })->first();
                        if (false === $decoteFamillePrestationAnnexe) {
                            $entitySite->removeDecoteFamillePrestationAnnex($decoteFamillePrestationAnnexeSite);
                            $emSite->remove($decoteFamillePrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion decote famillePrestationAnnexe ***

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

                // *** gestion decote periode validite ***
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
                // *** fin gestion decote periode validite ***

                // *** gestion decote canal decote ***
                /** @var CanalDecote $canalDecote */
                /** @var CanalDecote $canalDecoteSite */
                foreach ($entity->getCanalDecotes() as $canalDecote) {
                    $canalDecoteSite = $entitySite->getCanalDecotes()->filter(function (CanalDecote $element) use ($canalDecote) {
                        return $element->getId() == $canalDecote->getId();
                    })->first();
                    if (false === $canalDecoteSite) {
                        $entitySite->addCanalDecote($emSite->find(CanalDecote::class, $canalDecote));
                    }
                }
                foreach ($entitySite->getCanalDecotes() as $canalDecoteSite) {
                    $canalDecote = $entity->getCanalDecotes()->filter(function (CanalDecote $element) use ($canalDecoteSite) {
                        return $element->getId() == $canalDecoteSite->getId();
                    })->first();
                    if (false === $canalDecote) {
                        $entitySite->removeCanalDecote($canalDecoteSite);
                    }
                }
                // *** fin gestion decote periode validite ***

                // *** gestion periode validite jour ***
                if (!empty($entity->getDecotePeriodeValiditeJour())) {
                    if (empty($decotePeriodeValiditeJour = $entitySite->getDecotePeriodeValiditeJour())) {
                        $decotePeriodeValiditeJour = new DecotePeriodeValiditeJour();
                        $entitySite->setDecotePeriodeValiditeJour($decotePeriodeValiditeJour);
                    }
                    $decotePeriodeValiditeJour
                        ->setJourDebut($entity->getDecotePeriodeValiditeJour()->getJourDebut())
                        ->setJourFin($entity->getDecotePeriodeValiditeJour()->getJourFin());
                } else {
                    $entitySite->setDecotePeriodeValiditeJour();
                }
                // *** fin type periode validite jour ***

                // *** gestion periode validite date ***
                if (!empty($entity->getDecotePeriodeValiditeDate())) {
                    if (empty($decotePeriodeValiditeDate = $entitySite->getDecotePeriodeValiditeDate())) {
                        $decotePeriodeValiditeDate = new DecotePeriodeValiditeDate();
                        $entitySite->setDecotePeriodeValiditeDate($decotePeriodeValiditeDate);
                    }
                    $decotePeriodeValiditeDate
                        ->setDateDebut($entity->getDecotePeriodeValiditeDate()->getDateDebut())
                        ->setDateFin($entity->getDecotePeriodeValiditeDate()->getDateFin());
                } else {
                    $entitySite->setDecotePeriodeValiditeDate();
                }
                // *** fin type periode validite date ***

                // *** gestion periode sejour date ***
                if (!empty($entity->getDecotePeriodeSejourDate())) {
                    if (empty($decotePeriodeSejourDate = $entitySite->getDecotePeriodeSejourDate())) {
                        $decotePeriodeSejourDate = new DecotePeriodeSejourDate();
                        $entitySite->setDecotePeriodeSejourDate($decotePeriodeSejourDate);
                    }
                    $decotePeriodeSejourDate
                        ->setDateDebut($entity->getDecotePeriodeSejourDate()->getDateDebut())
                        ->setDateFin($entity->getDecotePeriodeSejourDate()->getDateFin());
                } else {
                    $entitySite->setDecotePeriodeSejourDate();
                }
                // *** fin type periode sejour date ***

                //  copie des données decote
                $entitySite
                    ->setActif($entity->getActif())
                    ->setLibelle($entity->getLibelle())
                    ->setValeurRemise($entity->getValeurRemise())
                    ->setTypePeriodeValidite($entity->getTypePeriodeValidite())
                    ->setTypePeriodeSejour($entity->getTypePeriodeSejour())
                    ->setTypeApplication($entity->getTypeApplication())
                    ->setType($entity->getType())
                    ->setTypeRemise($entity->getTypeRemise());

                $emSite->persist($entityUnifieSite);

                $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $emSite->flush();
            }
        }
    }

    private function gestionDecoteTypeFournisseur($decoteUnifieId)
    {
        $kernel = $this->get('kernel');

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_decote:decote_type_fournisseur_command',
            'decoteUnifieId' => $decoteUnifieId,
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * Finds and displays a DecoteUnifie entity.
     *
     */
    public function showAction(DecoteUnifie $decoteUnifie)
    {
        $deleteForm = $this->createDeleteForm($decoteUnifie);

        return $this->render('@MondofuteDecote/decoteunifie/show.html.twig', array(
            'decoteUnifie' => $decoteUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DecoteUnifie entity.
     *
     * @param DecoteUnifie $decoteUnifie The DecoteUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DecoteUnifie $decoteUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('decote_delete', array('id' => $decoteUnifie->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DecoteUnifie entity.
     *
     */
    public function editAction(Request $request, DecoteUnifie $decoteUnifie)
    {
        /** @var Decote $decote */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();

        // *** gestion decote typeAffectation ***
        $affectations = TypeAffectation::$libelles;

        $originalDecoteTypeAffectations = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
            $originalDecoteTypeAffectations->set($decote->getSite()->getId(), new ArrayCollection());
            foreach ($decote->getDecoteTypeAffectations() as $typeAffectation) {
                $originalDecoteTypeAffectations->get($decote->getSite()->getId())->add($typeAffectation);
            }
        }
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        // *** fin gestion decote typeAffectation ***

        // *** gestion decote fournisseur ***
        $originalDecoteFournisseurs = new ArrayCollection();

        foreach ($decoteUnifie->getDecotes() as $decote) {
            $originalDecoteFournisseurs->set($decote->getSite()->getId(), new ArrayCollection());
            foreach ($decote->getDecoteFournisseurs() as $decoteFournisseur) {
                $originalDecoteFournisseurs->get($decote->getSite()->getId())->add($decoteFournisseur);
            }
        }
        // *** fin gestion decote fournisseur ***

        // *** gestion decote hebergement ***
        $originalDecoteHebergements = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
//            $fournisseurHebergements->set($decote->getId(), new ArrayCollection());
            $originalDecoteHebergements->set($decote->getSite()->getId(), new ArrayCollection());
            /** @var DecoteHebergement $decoteHebergement */
            foreach ($decote->getDecoteHebergements() as $decoteHebergement) {
                $originalDecoteHebergements->get($decote->getSite()->getId())->add($decoteHebergement);
            }
        }
        // *** fin gestion decote hebergement ***

        // *** gestion decote station ***
        $originalDecoteStations = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
//            $fournisseurStations->set($decote->getId(), new ArrayCollection());
            $originalDecoteStations->set($decote->getSite()->getId(), new ArrayCollection());
            /** @var DecoteStation $decoteStation */
            foreach ($decote->getDecoteStations() as $decoteStation) {
                $originalDecoteStations->get($decote->getSite()->getId())->add($decoteStation);
            }
        }
        // *** fin gestion decote station ***

        // *** gestion decote fournisseurPrestationAnnexe ***
        $originalDecoteFournisseurPrestationAnnexes = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
            $originalDecoteFournisseurPrestationAnnexes->set($decote->getSite()->getId(), new ArrayCollection());
            /** @var DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnexe */
            foreach ($decote->getDecoteFournisseurPrestationAnnexes() as $decoteFournisseurPrestationAnnexe) {
                $originalDecoteFournisseurPrestationAnnexes->get($decote->getSite()->getId())->add($decoteFournisseurPrestationAnnexe);
            }
        }
        // *** fin gestion decote fournisseurPrestationAnnexe ***

        // *** gestion decote famillePrestationAnnexe ***
        $originalDecoteFamillePrestationAnnexes = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
            $originalDecoteFamillePrestationAnnexes->set($decote->getSite()->getId(), new ArrayCollection());
            /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex */
            foreach ($decote->getDecoteFamillePrestationAnnexes() as $decoteFamillePrestationAnnex) {
                $originalDecoteFamillePrestationAnnexes->get($decote->getSite()->getId())->add($decoteFamillePrestationAnnex);
            }
        }
        // *** fin gestion decote fournisseurPrestationAnnexe ***

        // *** gestion decote logement periode ***
        $originalDecoteLogementPeriodes = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
            $originalDecoteLogementPeriodes->set($decote->getSite()->getId(), new ArrayCollection());
            /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex */
            foreach ($decote->getLogementPeriodes() as $logementPeriode) {
                $originalDecoteLogementPeriodes->get($decote->getSite()->getId())->add($logementPeriode);
            }
        }
        // *** fin gestion decote logement periode ***

        // *** gestion decote logement ***
        $originalDecoteLogements = new ArrayCollection();
        foreach ($decoteUnifie->getDecotes() as $decote) {
            $originalDecoteLogements->set($decote->getSite()->getId(), new ArrayCollection());
            /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex */
            foreach ($decote->getDecoteLogements() as $logement) {
                $originalDecoteLogements->get($decote->getSite()->getId())->add($logement);
            }
        }
        // *** fin gestion decote logement ***

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Decote $decote */
            foreach ($decoteUnifie->getDecotes() as $decote) {
                if ($decote->getActif()) {
                    array_push($sitesAEnregistrer, $decote->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $this->ajouterDecotesDansForm($decoteUnifie);

        $this->decotesSortByAffichage($decoteUnifie);

        $coreController = $this->get('mondofute_core_bundle_controller');
        $coreController->setContainer($this->container);
        $coreController->addTraductions($decoteUnifie, 'decote');

        $deleteForm = $this->createDeleteForm($decoteUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\DecoteBundle\Form\DecoteUnifieType',
            $decoteUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        $errorCompatibiliteType = $this->testCompatibiliteType($decoteUnifie);

        // **********************************************
        // ********** VALIDATION DU FORMULAIRE **********
        // **********************************************
        if ($editForm->isSubmitted() && $editForm->isValid() && !$errorCompatibiliteType) {
            foreach ($decoteUnifie->getDecotes() as $decote) {
                if (false === in_array($decote->getSite()->getId(), $sitesAEnregistrer)) {
                    $decote->setActif(false);
                } else {
                    $decote->setActif(true);
                }
            }

            // *** gestion TypePeriodeSejour ***
            $this->gestionDecoteTypePeriodeSejour($decoteUnifie);
            // *** fin gestion TypePeriodeSejour ***

            // *** gestion decote typeAffectation ***
            $this->gestionDecoteTypeAffectation($decoteUnifie);

            foreach ($decoteUnifie->getDecotes() as $decote) {
                $originalDecoteTypeAffectationSites = $originalDecoteTypeAffectations->get($decote->getSite()->getId());
                foreach ($originalDecoteTypeAffectationSites as $originalDecoteTypeAffectation) {
                    if (false === $decote->getDecoteTypeAffectations()->contains($originalDecoteTypeAffectation)) {
                        $em->remove($originalDecoteTypeAffectation);
                    }
                }
            }
            // *** fin gestion decote typeAffectation ***

            // *** gestion decote fournisseur ***
            $this->gestionDecoteFournisseur($decoteUnifie);

            foreach ($decoteUnifie->getDecotes() as $decote) {
                $originalDecoteFournisseurSites = $originalDecoteFournisseurs->get($decote->getSite()->getId());
                foreach ($decote->getDecoteFournisseurs() as $decoteFournisseur) {
                    /** @var ArrayCollection $originalDecoteFournisseurSites */
                    /** @var DecoteFournisseur $decoteFournisseur */
                    $originalDecoteFournisseur = $originalDecoteFournisseurSites->filter(function (DecoteFournisseur $element) use ($decoteFournisseur) {
                        return ($element->getFournisseur() == $decoteFournisseur->getFournisseur()
                            and $element->getType() == $decoteFournisseur->getType()
                            and $element->getDecote() == $decoteFournisseur->getDecote());
                    })->first();
                    if (!empty($originalDecoteFournisseur)) {
                        $decote->getDecoteFournisseurs()->removeElement($decoteFournisseur);
                        $decote->addDecoteFournisseur($originalDecoteFournisseur);
                    }
                }
                foreach ($originalDecoteFournisseurSites as $originalDecoteFournisseur) {
                    /** @var DecoteFournisseur $originalDecoteFournisseur */
                    if (false === $decote->getDecoteFournisseurs()->contains($originalDecoteFournisseur)) {
                        $delete = true;
                        $typeAffectation = $decote->getDecoteTypeAffectations()->filter(function (DecoteTypeAffectation $element) {
                            return $element->getTypeAffectation() == TypeAffectation::type;
                        })->first();
                        if (false !== $typeAffectation and $originalDecoteFournisseur->getType() == TypeAffectation::type) {
                            $delete = false;
                            foreach ($originalDecoteFournisseur->getFournisseur()->getTypes() as $typeFournisseur) {
                                $type = $decote->getTypeFournisseurs()->filter(function (FamillePrestationAnnexe $element) use ($typeFournisseur) {
                                    return $element->getId() == $typeFournisseur->getId();
                                })->first();
                                if (false === $type) {
                                    $delete = true;
                                }
                            }
                        }
                        if ($delete) {
                            $em->remove($originalDecoteFournisseur);
                        }
                    }
                }
            }

            // *** fin gestion decote fournisseur ***

            // *** gestion decote hebergement ***
            $this->gestionDecoteHebergement($decoteUnifie);

            /** @var DecoteHebergement $originalDecoteHebergement */
            foreach ($decoteUnifie->getDecotes() as $decote) {
                $originalDecoteHebergementSites = $originalDecoteHebergements->get($decote->getSite()->getId());
                foreach ($decote->getDecoteHebergements() as $decoteHebergement) {
                    /** @var ArrayCollection $originalDecoteHebergementSites */
                    /** @var DecoteHebergement $decoteHebergement */
                    $originalDecoteHebergement = $originalDecoteHebergementSites->filter(function (DecoteHebergement $element) use ($decoteHebergement) {
                        return ($element->getHebergement() == $decoteHebergement->getHebergement()
                            and $element->getFournisseur() == $decoteHebergement->getFournisseur()
                            and $element->getDecote() == $decoteHebergement->getDecote());
                    })->first();
                    if (!empty($originalDecoteHebergement)) {
                        $decote->getDecoteHebergements()->removeElement($decoteHebergement);
                        $decote->addDecoteHebergement($originalDecoteHebergement);
                    }
                }
                foreach ($originalDecoteHebergementSites as $originalDecoteHebergement) {
                    if (false === $decote->getDecoteHebergements()->contains($originalDecoteHebergement)) {
                        $decote->getDecoteHebergements()->removeElement($originalDecoteHebergement);
                        $em->remove($originalDecoteHebergement);
                    }
                }
            }
            // *** fin gestion decote hebergement ***

            // *** gestion decote station ***
            $this->gestionDecoteStation($decoteUnifie);

            /** @var DecoteStation $originalDecoteStation */
            foreach ($decoteUnifie->getDecotes() as $decote) {
                $originalDecoteStationSites = $originalDecoteStations->get($decote->getSite()->getId());
                foreach ($decote->getDecoteStations() as $decoteStation) {
                    /** @var ArrayCollection $originalDecoteStationSites */
                    /** @var DecoteStation $decoteStation */
                    $originalDecoteStation = $originalDecoteStationSites->filter(function (DecoteStation $element) use ($decoteStation) {
                        return ($element->getStation() == $decoteStation->getStation()
                            and $element->getFournisseur() == $decoteStation->getFournisseur()
                            and $element->getDecote() == $decoteStation->getDecote());
                    })->first();
                    if (!empty($originalDecoteStation)) {
                        $decote->getDecoteStations()->removeElement($decoteStation);
                        $decote->addDecoteStation($originalDecoteStation);
                    }
                }
                foreach ($originalDecoteStationSites as $originalDecoteStation) {
                    if (false === $decote->getDecoteStations()->contains($originalDecoteStation)) {
                        $decote->getDecoteStations()->removeElement($originalDecoteStation);
                        $em->remove($originalDecoteStation);
                    }
                }
            }
            // *** fin gestion decote station ***

            // *** gestion decote fournisseurPrestationAnnexe ***
            $this->gestionDecoteFournisseurPrestationAnnexe($decoteUnifie);

            /** @var DecoteFournisseurPrestationAnnexe $originalDecoteFournisseurPrestationAnnexe */
            /** @var DecoteFournisseurPrestationAnnexe $originalDecoteFournisseurPrestationAnnexe */
            foreach ($decoteUnifie->getDecotes() as $decote) {
                $originalDecoteFournisseurPrestationAnnexeSites = $originalDecoteFournisseurPrestationAnnexes->get($decote->getSite()->getId());
                foreach ($decote->getDecoteFournisseurPrestationAnnexes() as $decoteFournisseurPrestationAnnex) {
                    /** @var ArrayCollection $originalDecoteFournisseurPrestationAnnexeSites */
                    /** @var DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnex */
                    $originalDecoteFournisseurPrestationAnnexe = $originalDecoteFournisseurPrestationAnnexeSites->filter(function (DecoteFournisseurPrestationAnnexe $element) use ($decoteFournisseurPrestationAnnex) {
                        return ($element->getFournisseurPrestationAnnexe() == $decoteFournisseurPrestationAnnex->getFournisseurPrestationAnnexe()
                            and $element->getFournisseur() == $decoteFournisseurPrestationAnnex->getFournisseur()
                            and $element->getDecote() == $decoteFournisseurPrestationAnnex->getDecote());
                    })->first();
                    if (!empty($originalDecoteFournisseurPrestationAnnexe)) {
                        $decote->getDecoteFournisseurPrestationAnnexes()->removeElement($decoteFournisseurPrestationAnnex);
                        $decote->addDecoteFournisseurPrestationAnnex($originalDecoteFournisseurPrestationAnnexe);
                    }
                }
                foreach ($originalDecoteFournisseurPrestationAnnexeSites as $originalDecoteFournisseurPrestationAnnexe) {
                    if (false === $decote->getDecoteFournisseurPrestationAnnexes()->contains($originalDecoteFournisseurPrestationAnnexe)) {
                        $decote->getDecoteFournisseurPrestationAnnexes()->removeElement($originalDecoteFournisseurPrestationAnnexe);
                        $em->remove($originalDecoteFournisseurPrestationAnnexe);
                    }
                }
            }
            // *** fin gestion decote fournisseurPrestationAnnexe ***

            // *** gestion decote famillePrestationAnnexe ***
            $this->gestionDecoteFamillePrestationAnnexe($decoteUnifie);

            /** @var DecoteFamillePrestationAnnexe $originalDecoteFamillePrestationAnnexe */
            /** @var DecoteFamillePrestationAnnexe $originalDecoteFamillePrestationAnnexe */
            foreach ($decoteUnifie->getDecotes() as $decote) {
                $originalDecoteFamillePrestationAnnexeSites = $originalDecoteFamillePrestationAnnexes->get($decote->getSite()->getId());
                foreach ($decote->getDecoteFamillePrestationAnnexes() as $decoteFamillePrestationAnnex) {
                    /** @var ArrayCollection $originalDecoteFamillePrestationAnnexeSites */
                    /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex */
                    $originalDecoteFamillePrestationAnnexe = $originalDecoteFamillePrestationAnnexeSites->filter(function (DecoteFamillePrestationAnnexe $element) use ($decoteFamillePrestationAnnex) {
                        return ($element->getFamillePrestationAnnexe() == $decoteFamillePrestationAnnex->getFamillePrestationAnnexe()
                            and $element->getFournisseur() == $decoteFamillePrestationAnnex->getFournisseur()
                            and $element->getDecote() == $decoteFamillePrestationAnnex->getDecote());
                    })->first();
                    if (!empty($originalDecoteFamillePrestationAnnexe)) {
                        $decote->getDecoteFamillePrestationAnnexes()->removeElement($decoteFamillePrestationAnnex);
                        $decote->addDecoteFamillePrestationAnnex($originalDecoteFamillePrestationAnnexe);
                    }
                }
                foreach ($originalDecoteFamillePrestationAnnexeSites as $originalDecoteFamillePrestationAnnexe) {
                    if (false === $decote->getDecoteFamillePrestationAnnexes()->contains($originalDecoteFamillePrestationAnnexe)) {
                        $decote->getDecoteFamillePrestationAnnexes()->removeElement($originalDecoteFamillePrestationAnnexe);
                        $em->remove($originalDecoteFamillePrestationAnnexe);
                    }
                }
            }
            // *** fin gestion decote famillePrestationAnnexe ***

            // *** gestion decote logement periode ***
            /** @var DecoteLogementPeriode $logementPeriode */
            foreach ($decoteUnifie->getDecotes() as $key => $decote) {
                if ($decote->getTypePeriodeSejour() == TypePeriodeSejour::periode and !empty($request->get('decote_logement_periode')[$key]) and !empty($request->get('decote_logement_periode')[$key]['logements']) and !empty($request->get('decote_logement_periode')[$key]['periodes'])) {
                    $decote_logement_periode = $request->get('decote_logement_periode')[$key];
                    foreach ($decote_logement_periode['logements'] as $logement) {
                        $logementEntity = $em->find(Logement::class, $logement);
                        foreach ($decote_logement_periode['periodes'] as $periode) {
                            $decoteLogementPeriode = $originalDecoteLogementPeriodes->get($decote->getSite()->getId())->filter(function (DecoteLogementPeriode $element) use ($logement, $periode, $decote) {
                                return ($element->getLogement()->getId() == $logement and $element->getPeriode()->getId() == $periode and $element->getDecote() == $decote);
                            })->first();
                            if (false === $decoteLogementPeriode) {
                                $decoteLogementPeriode = new DecoteLogementPeriode();
                                $decote->addLogementPeriode($decoteLogementPeriode);
                                $decoteLogementPeriode
                                    ->setDecote($decote)
                                    ->setLogement($logementEntity)
                                    ->setPeriode($em->find(Periode::class, $periode));
                            }
                        }
                    }
                    foreach ($originalDecoteLogementPeriodes->get($decote->getSite()->getId()) as $logementPeriode) {
                        if (!in_array($logementPeriode->getLogement()->getId(), $decote_logement_periode['logements'])) {
                            $decote->getLogementPeriodes()->removeElement($logementPeriode);
                            $em->remove($logementPeriode);
                        }
                        if (!in_array($logementPeriode->getPeriode()->getId(), $decote_logement_periode['periodes'])) {
                            $decote->getLogementPeriodes()->removeElement($logementPeriode);
                            $em->remove($logementPeriode);
                        }
                    }
                } else {
                    foreach ($originalDecoteLogementPeriodes->get($decote->getSite()->getId()) as $logementPeriode) {
//                        $decote->getLogementPeriodes()->removeElement($logementPeriode);
                        $em->remove($logementPeriode);
                    }
                }
                // *** gestion decoteLogement ***
                if ($decote->getTypePeriodeSejour() != TypePeriodeSejour::periode and !empty($request->get('decote_logement_periode')[$key]) and !empty($request->get('decote_logement_periode')[$key]['logements'])) {
                    $decote_logement_periode = $request->get('decote_logement_periode')[$key];
                    foreach ($decote_logement_periode['logements'] as $logement) {
                        $logementEntity = $em->find(Logement::class, $logement);
                        $decoteLogement = $originalDecoteLogements->get($decote->getSite()->getId())->filter(function (DecoteLogement $element) use ($logement, $decote) {
                            return ($element->getLogement()->getId() == $logement and $element->getDecote() == $decote);
                        })->first();
                        if (false === $decoteLogement) {
                            $decoteLogement = new DecoteLogement();
                            $decote->addDecoteLogement($decoteLogement);
                            $decoteLogement
                                ->setDecote($decote)
                                ->setLogement($logementEntity);
                        }
                    }
                    /** @var DecoteLogement $decotelogement */
                    foreach ($originalDecoteLogements->get($decote->getSite()->getId()) as $decotelogement) {
                        if (!in_array($decotelogement->getLogement()->getId(), $decote_logement_periode['logements'])) {
                            $decote->getDecoteLogements()->removeElement($decotelogement);
                            $em->remove($decotelogement);
                        }
                    }
                } else {
                    foreach ($originalDecoteLogements->get($decote->getSite()->getId()) as $logement) {
                        $em->remove($logement);
                    }
                }
                // *** fin gestion decoteLogement ***
            }
            // *** fin gestion decote logement periode ***

            // *** gestion typePeriodeSejour ***
            $this->gestionTypePeriodeSejour($decoteUnifie);
            // *** fin gestion typePeriodeSejour ***

            $em->persist($decoteUnifie);
            $em->flush();

            $this->copieVersSites($decoteUnifie);

            $this->gestionDecoteTypeFournisseur($decoteUnifie->getId());

            // add flash messages
            $this->addFlash('success', 'La decote a bien été modifié.');

            return $this->redirectToRoute('decote_edit', array('id' => $decoteUnifie->getId()));
        }

        return $this->render('@MondofuteDecote/decoteunifie/edit.html.twig', array(
            'entity' => $decoteUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
//            'decoteClients' => $originalDecoteClients,
            'affectations' => $affectations,
            'panelDecote' => true,
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
//            'fournisseurHebergements' => $fournisseurHebergements,
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
//            'fournisseurFournisseurPrestationAnnexes' => $fournisseurFournisseurPrestationAnnexes,
            'langues' => $langues
        ));
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteTypePeriodeSejour($decoteUnifie)
    {
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $key => $decote) {
            switch ($decote->getTypePeriodeSejour()) {
                case TypePeriodeSejour::permanent:
                    $this->clearPeriodes($decote);
                    $decote->getDecotePeriodeSejourDate();
                    break;
                case TypePeriodeSejour::dateADate:
                    $this->clearPeriodes($decote);
                    break;
                case TypePeriodeSejour::periode:
                    $decote->getDecotePeriodeSejourDate();
                    $decote->getDecoteLogements()->clear();
                    break;
                default:
                    break;
            }
        }
    }

    private function clearPeriodes(Decote $decote)
    {
        $decote->getPeriodeValidites()->clear();
        $decote->getLogementPeriodes()->clear();
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteFournisseur($decoteUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var DecoteFournisseur $decoteFournisseurSite */
        /** @var DecoteFournisseur $decoteFournisseurCrm */
        /** @var DecoteFournisseur $fournisseur */
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            foreach ($decote->getDecoteFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
                    $decote->getDecoteFournisseurs()->removeElement($fournisseur);
                } else {
                    $fournisseur->setDecote($decote);
                }
            }
        }
//        $decoteFournisseurCrms = $decoteUnifie->getDecotes()->filter(function (Decote $element) {
//            return $element->getSite()->getCrm() == 1;
//        })->first()->getDecoteFournisseurs();
//        $fournisseurs = new ArrayCollection();
//        foreach ($decoteFournisseurCrms as $decoteFournisseurCrm) {
//            $fournisseurs->add($decoteFournisseurCrm);
//        }
//        foreach ($decoteUnifie->getDecotes() as $decote) {
//            if ($decote->getSite()->getCrm() == 0) {
//                foreach ($decoteFournisseurCrms as $key => $fournisseur) {
//                    $fournisseurSite = $decote->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) use ($fournisseur) {
//                        return ($element->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId()
//                            and $element->getType() == $fournisseur->getType()
//                            and $element->getDecote()->getId() == $fournisseur->getDecote()->getId()
//                        );
//                    })->first();
//                    if (false === $fournisseurSite) {
//                        if (empty($em->getRepository(DecoteFournisseur::class)->findOneBy(['type' => $fournisseur->getType(), 'decote' => $decote, 'fournisseur' => $fournisseur->getFournisseur()]))) {
//                            $newFournisseur = new DecoteFournisseur();
//                            $decote->addDecoteFournisseur($newFournisseur);
//                            $newFournisseur
//                                ->setFournisseur($fournisseur->getFournisseur())
//                                ->setType($fournisseur->getType());
//                        }
//                    }
//                }
//            }
//        }
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteHebergement($decoteUnifie)
    {
        /** @var Hebergement $hebergement */
        /** @var DecoteHebergement $decoteHebergementCrm */
        /** @var DecoteHebergement $decoteHebergement */
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            foreach ($decote->getDecoteHebergements() as $decoteHebergement) {
                if (empty($decoteHebergement->getHebergement())) {
                    $decote->getDecoteHebergements()->removeElement($decoteHebergement);
                } else {
                    $decoteHebergement->setDecote($decote);
                }
            }
        }
        $decoteHebergementCrms = $decoteUnifie->getDecotes()->filter(function (Decote $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getDecoteHebergements();
        $hebergements = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($decoteHebergementCrms as $decoteHebergementCrm) {
            $hebergements->add($decoteHebergementCrm->getHebergement());
            $fournisseurs->add($decoteHebergementCrm->getFournisseur());
        }
        foreach ($decoteUnifie->getDecotes() as $decote) {
            if ($decote->getSite()->getCrm() == 0) {
                $hebergementSites = new ArrayCollection();
                foreach ($decote->getDecoteHebergements() as $decoteHebergementSite) {
                    $hebergementSites->add($decoteHebergementSite->getHebergement());
                }

                foreach ($hebergements as $key => $hebergement) {
                    $hebergementSite = $hebergement->getHebergementUnifie()->getHebergements()->filter(function (Hebergement $element) use ($decote) {
                        return $element->getSite() == $decote->getSite();
                    })->first();
                    if (false === $hebergementSites->contains($hebergementSite)) {
                        $newHebergement = new DecoteHebergement();
                        $decote->addDecoteHebergement($newHebergement);
                        $newHebergement
                            ->setHebergement($hebergementSite)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteStation($decoteUnifie)
    {
        /** @var Station $station */
        /** @var DecoteStation $decoteStationCrm */
        /** @var DecoteStation $decoteStation */
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            foreach ($decote->getDecoteStations() as $decoteStation) {
                if (empty($decoteStation->getStation())) {
                    $decote->getDecoteStations()->removeElement($decoteStation);
                } else {
                    $decoteStation->setDecote($decote);
                }
            }
        }
//        $decoteStationCrms = $decoteUnifie->getDecotes()->filter(function (Decote $element) {
//            return $element->getSite()->getCrm() == 1;
//        })->first()->getDecoteStations();
//        $stations = new ArrayCollection();
//        $fournisseurs = new ArrayCollection();
//        foreach ($decoteStationCrms as $decoteStationCrm) {
//            $stations->add($decoteStationCrm->getStation());
//            $fournisseurs->add($decoteStationCrm->getFournisseur());
//        }
//        foreach ($decoteUnifie->getDecotes() as $decote) {
//            if ($decote->getSite()->getCrm() == 0) {
//                $stationSites = new ArrayCollection();
//                foreach ($decote->getDecoteStations() as $decoteStationSite) {
//                    $stationSites->add($decoteStationSite->getStation());
//                }
//
//                foreach ($stations as $key => $station) {
//                    $stationSite = $station->getStationUnifie()->getStations()->filter(function (Station $element) use ($decote) {
//                        return $element->getSite() == $decote->getSite();
//                    })->first();
//                    if (false === $stationSites->contains($stationSite)) {
//                        $newStation = new DecoteStation();
//                        $decote->addDecoteStation($newStation);
//                        $newStation
//                            ->setStation($stationSite)
//                            ->setFournisseur($fournisseurs->get($key));
//                    }
//                }
//            }
//        }
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteFournisseurPrestationAnnexe($decoteUnifie)
    {
        /** @var DecoteFournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        /** @var DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnexeCrm */
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            foreach ($decote->getDecoteFournisseurPrestationAnnexes() as $fournisseurPrestationAnnexe) {
                if (empty($fournisseurPrestationAnnexe->getFournisseurPrestationAnnexe())) {
                    $decote->getDecoteFournisseurPrestationAnnexes()->removeElement($fournisseurPrestationAnnexe);
                } else {
                    $fournisseurPrestationAnnexe->setDecote($decote);
                }
            }
        }
//        $decoteFournisseurPrestationAnnexeCrms = $decoteUnifie->getDecotes()->filter(function (Decote $element) {
//            return $element->getSite()->getCrm() == 1;
//        })->first()->getDecoteFournisseurPrestationAnnexes();
//        $fournisseurPrestationAnnexes = new ArrayCollection();
//        $fournisseurs = new ArrayCollection();
//        foreach ($decoteFournisseurPrestationAnnexeCrms as $decoteFournisseurPrestationAnnexeCrm) {
//            $fournisseurPrestationAnnexes->add($decoteFournisseurPrestationAnnexeCrm->getFournisseurPrestationAnnexe());
//            $fournisseurs->add($decoteFournisseurPrestationAnnexeCrm->getFournisseur());
//        }
//        foreach ($decoteUnifie->getDecotes() as $decote) {
//            if ($decote->getSite()->getCrm() == 0) {
//                $fournisseurPrestationAnnexeSites = new ArrayCollection();
//                foreach ($decote->getDecoteFournisseurPrestationAnnexes() as $decoteFournisseurPrestationAnnexeSite) {
//                    $fournisseurPrestationAnnexeSites->add($decoteFournisseurPrestationAnnexeSite->getFournisseurPrestationAnnexe());
//                }
//                foreach ($fournisseurPrestationAnnexes as $key => $fournisseurPrestationAnnexe) {
//                    if (false === $fournisseurPrestationAnnexeSites->contains($fournisseurPrestationAnnexe)) {
//                        $newFournisseurPrestationAnnexe = new DecoteFournisseurPrestationAnnexe();
//                        $decote->addDecoteFournisseurPrestationAnnex($newFournisseurPrestationAnnexe);
//                        $newFournisseurPrestationAnnexe
//                            ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe)
//                            ->setFournisseur($fournisseurs->get($key));
//                    }
//                }
//            }
//        }
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteFamillePrestationAnnexe($decoteUnifie)
    {
        /** @var DecoteFamillePrestationAnnexe $famillePrestationAnnexe */
        /** @var DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnexeCrm */
        /** @var Decote $decote */
        foreach ($decoteUnifie->getDecotes() as $decote) {
            foreach ($decote->getDecoteFamillePrestationAnnexes() as $famillePrestationAnnexe) {
                if (empty($famillePrestationAnnexe->getFamillePrestationAnnexe())) {
                    $decote->getDecoteFamillePrestationAnnexes()->removeElement($famillePrestationAnnexe);
                } else {
                    $famillePrestationAnnexe->setDecote($decote);
                }
            }
        }
//        $decoteFamillePrestationAnnexeCrms = $decoteUnifie->getDecotes()->filter(function (Decote $element) {
//            return $element->getSite()->getCrm() == 1;
//        })->first()->getDecoteFamillePrestationAnnexes();
//        $famillePrestationAnnexes = new ArrayCollection();
//        $fournisseurs = new ArrayCollection();
//        foreach ($decoteFamillePrestationAnnexeCrms as $decoteFamillePrestationAnnexeCrm) {
//            $famillePrestationAnnexes->add($decoteFamillePrestationAnnexeCrm->getFamillePrestationAnnexe());
//            $fournisseurs->add($decoteFamillePrestationAnnexeCrm->getFournisseur());
//        }
//        foreach ($decoteUnifie->getDecotes() as $decote) {
//            if ($decote->getSite()->getCrm() == 0) {
//                $famillePrestationAnnexeSites = new ArrayCollection();
//                foreach ($decote->getDecoteFamillePrestationAnnexes() as $decoteFamillePrestationAnnexeSite) {
//                    $famillePrestationAnnexeSites->add($decoteFamillePrestationAnnexeSite->getFamillePrestationAnnexe());
//                }
//                foreach ($famillePrestationAnnexes as $key => $famillePrestationAnnexe) {
//                    if (false === $famillePrestationAnnexeSites->contains($famillePrestationAnnexe)) {
//                        $newFamillePrestationAnnexe = new DecoteFamillePrestationAnnexe();
//                        $decote->addDecoteFamillePrestationAnnex($newFamillePrestationAnnexe);
//                        $newFamillePrestationAnnexe
//                            ->setFamillePrestationAnnexe($famillePrestationAnnexe)
//                            ->setFournisseur($fournisseurs->get($key));
//                    }
//                }
//            }
//        }
    }

    public function getFournisseurHebergementsAction($decoteId, $fournisseurId, $siteId)
    {
        $em = $this->getDoctrine()->getManager();
        $hebergements = $em->getRepository(HebergementUnifie::class)->getFournisseurHebergements($fournisseurId, $this->container->getParameter('locale'), $siteId);

        $decoteHebergements = $em->getRepository(DecoteHebergement::class)->findBy(array('decote' => $decoteId, 'fournisseur' => $fournisseurId));

        $decoteUnifie = new DecoteUnifie();
//        $decote = new Decote();
        $decote = $em->find(Decote::class, $decoteId);
        $decoteUnifie->addDecote($decote);
        foreach ($decoteHebergements as $decoteHebergement) {
            $decote->addDecoteHebergement($decoteHebergement);
        }

        $form = $this->createForm(DecoteUnifieType::class, $decoteUnifie)->createView();

        return $this->render('@MondofuteDecote/decoteunifie/get-decote-fournisseur-hebergements.html.twig', array(
            'hebergements' => $hebergements,
            'decoteId' => $decoteId,
            'fournisseurId' => $fournisseurId,
            'keyDecote' => '__keyDecote__',
            'decote' => $form->children['decotes'][0],
        ));
    }

    public function getFournisseurPrestationAnnexesAction($decoteId, $fournisseurId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurPrestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->getFournisseurPrestationAnnexes($fournisseurId, $this->container->getParameter('locale'));
//        $prestationAnnexes = new ArrayCollection();

        $decoteFournisseurPrestationAnnexes = $em->getRepository(DecoteFournisseurPrestationAnnexe::class)->findBy(array('decote' => $decoteId, 'fournisseur' => $fournisseurId));
        $decoteFamillePrestationAnnexes = $em->getRepository(DecoteFamillePrestationAnnexe::class)->findBy(array('decote' => $decoteId, 'fournisseur' => $fournisseurId));
        $decoteUnifie = new DecoteUnifie();
//        $decote = new Decote();
        $decote = $em->find(Decote::class, $decoteId);
        $decoteUnifie->addDecote($decote);
        foreach ($decoteFournisseurPrestationAnnexes as $decoteFournisseurPrestationAnnex) {
            $decote->addDecoteFournisseurPrestationAnnex($decoteFournisseurPrestationAnnex);
        }
        foreach ($decoteFamillePrestationAnnexes as $decoteFamillePrestationAnnex) {
            $decote->addDecoteFamillePrestationAnnex($decoteFamillePrestationAnnex);
        }

        $form = $this->createForm(DecoteUnifieType::class, $decoteUnifie)->createView();

        return $this->render('@MondofuteDecote/decoteunifie/get-decote-fournisseur-prestation-annexes.html.twig', array(
            'fournisseurPrestationAnnexes' => $fournisseurPrestationAnnexes,
            'decoteId' => $decoteId,
            'fournisseurId' => $fournisseurId,
            'keyDecote' => '__keyDecote__',
            'decote' => $form->children['decotes'][0],
        ));
    }

    public function getFournisseurPrestationannexePeriodeValiditeAction($fournisseurPrestationAnnexeId, $keyDecote)
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

//        $decoteUnifie = new DecoteUnifie();
//        $decote = new Decote();
//        $decoteUnifie->getDecotes()->set($keyDecote , $decote);
//
//        $form = $this->createForm(DecoteUnifieType::class, $decoteUnifie)->createView();

        return $this->render('@MondofuteDecote/decoteunifie/modal-body-fournisseur-prestation-annexe-periode-validite.html.twig', array(
            'periodeValidites' => $periodeValidites,
            'keyDecote' => $keyDecote,
            'fournisseurPrestationAnnexeId' => $fournisseurPrestationAnnexeId,
//            'form' => $form
        ));
    }

    public function getFournisseurPrestationannexePeriodeValiditeValuesAction($fournisseurPrestationAnnexeId, $keyDecote, $decoteId)
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

        $decote = $em->find(Decote::class, $decoteId);
        $decotePeriodeValidites = $decote->getPeriodeValidites();

        return $this->render('@MondofuteDecote/decoteunifie/get-fournisseur-prestation-annexe-periode-validite-values.html.twig', array(
            'periodeValidites' => $periodeValidites,
            'decoteId' => $decoteId,
            'keyDecote' => $keyDecote,
            'fournisseurPrestationAnnexeId' => $fournisseurPrestationAnnexeId,
            'decotePeriodeValidites' => $decotePeriodeValidites
        ));
    }

    public function getLogementValuesAction($fournisseurId, $hebergementId, $keyDecote, $decoteId)
    {
        $em = $this->getDoctrine()->getManager();

        $hebergement = $em->find(Hebergement::class, $hebergementId);

        $logements = $em->getRepository(Logement::class)->findByFournisseurHebergement($fournisseurId, $hebergement->getHebergementUnifie()->getId(), $hebergement->getSite()->getId());


        $decote = $em->find(Decote::class, $decoteId);
        $decoteLogementPeriodes = $decote->getLogementPeriodes();
        $decoteLogements = $decote->getDecoteLogements();

        return $this->render('@MondofuteDecote/decoteunifie/get-decote-logement.html.twig', array(
            'keyDecote' => $keyDecote,
            'hebergementId' => $hebergementId,
            'logements' => $logements,
            'fournisseurId' => $fournisseurId,
            'decoteLogementPeriodes' => $decoteLogementPeriodes,
            'decoteLogements' => $decoteLogements
        ));


    }

    public function getLogementsAction($fournisseurId, $hebergementId, $keyDecote)
    {
        $em = $this->getDoctrine()->getManager();
//        $periodes = $em->getRepository(LogementPeriodeLocatif::class)->findByPrixPublicNotEmpty($fournisseurId, $hebergementId);
//        dump($periodes);
        $hebergement = $em->find(Hebergement::class, $hebergementId);

        $logements = $em->getRepository(Logement::class)->findByFournisseurHebergement($fournisseurId, $hebergement->getHebergementUnifie()->getId(), $hebergement->getSite()->getId());

        return $this->render('@MondofuteDecote/decoteunifie/modal-body-logement.html.twig', array(
//            'logementPeriodes' => $periodes,
//            'decoteId' => $decoteId,
            'keyDecote' => $keyDecote,
            'hebergementId' => $hebergementId,
            'logements' => $logements,
            'fournisseurId' => $fournisseurId,
//            'decotePeriodeValidites' => $decotePeriodeValidites
        ));

    }

    /**
     * Deletes a DecoteUnifie entity.
     *
     */
    public function deleteAction(Request $request, DecoteUnifie $decoteUnifie)
    {
        /** @var Decote $decoteSite */
        /** @var Decote $decote */
        $form = $this->createDeleteForm($decoteUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $decoteUnifieSite = $emSite->find(DecoteUnifie::class, $decoteUnifie);
                if (!empty($decoteUnifieSite)) {
                    $emSite->remove($decoteUnifieSite);
                    $emSite->flush();
                }
            }

            $em->remove($decoteUnifie);
            $em->flush();

            $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('decote_index');
    }

    public function getPanelHebergementAction($decoteId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $decoteUnifie = new DecoteUnifie();
        $decote = $em->find(Decote::class, $decoteId);
        $decoteUnifie->addDecote($decote);
        $form = $this->createForm(DecoteUnifieType::class, $decoteUnifie)->createView();

        return $this->render('@MondofuteDecote/decoteunifie/panel-hebergement.html.twig', array(
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
            'decote' => $form->children['decotes'][0],
            'keyDecote' => '_keyDecote_'
        ));
    }

    public function getPeriodesAction($keyDecote, $decoteId)
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

        $decote = $em->find(Decote::class, $decoteId);
        $decoteLogementPeriodes = $decote->getLogementPeriodes();

        return $this->render('@MondofuteDecote/decoteunifie/get-decote-periode.html.twig', array(
            'collectionPeriodes' => $collectionPeriodes,
            'keyDecote' => $keyDecote,
            'typePeriodes' => $typePeriodes,
            'decoteLogementPeriodes' => $decoteLogementPeriodes,
        ));
    }

    public function getPrestationAnnexeAction($decoteId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        $decoteUnifie = new DecoteUnifie();
        $decote = $em->find(Decote::class, $decoteId);
        $decoteUnifie->addDecote($decote);
        $form = $this->createForm(DecoteUnifieType::class, $decoteUnifie)->createView();


        return $this->render('@MondofuteDecote/decoteunifie/panel-prestation-annexe.html.twig', array(
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
            'decote' => $form->children['decotes'][0],
            'keyDecote' => '_keyDecote_'
        ));
    }

    public function getdecoteslikeAction(Request $request)
    {
        $locale = $request->getLocale();
        $like = $request->get('q');
        $site = $request->get('site');
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Decote::class)->findByLike($like, $site, $locale);

        return new Response(json_encode($data));
    }

    /**
     * @param DecoteUnifie $decoteUnifie
     */
    private function gestionDecoteLogement($decoteUnifie)
    {
        $em = $this->getDoctrine()->getManager();

        $job = new Job('creer:decoteLogementByDecoteUnifie',
            array(
                'decoteUnifieId' => $decoteUnifie->getId()
            ), true, 'decoteLogementByDecoteUnifie');
        $em->persist($job);
        $em->flush();
    }
}
