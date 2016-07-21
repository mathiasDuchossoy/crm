<?php

namespace Mondofute\Bundle\HebergementBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Emplacement;
use Mondofute\Bundle\HebergementBundle\Entity\EmplacementHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement;
use Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Pays;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $hebergementUnifies = $em->getRepository('MondofuteHebergementBundle:HebergementUnifie')->findAll();

        return $this->render('@MondofuteHebergement/hebergementunifie/index.html.twig', array(
            'hebergementUnifies' => $hebergementUnifies,
        ));
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

        $hebergementUnifie = new HebergementUnifie();

        $this->ajouterHebergementsDansForm($hebergementUnifie);
        $this->hebergementsSortByAffichage($hebergementUnifie);

        $form = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType', $hebergementUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Hebergement $hebergement */
            foreach ($hebergementUnifie->getHebergements() as $keyHebergement => $hebergement) {
                foreach ($hebergement->getEmplacements() as $keyEmplacement => $emplacement) {
                    if (empty($request->request->get('hebergement_unifie')['hebergements'][$keyHebergement]['emplacements'][$keyEmplacement]['checkbox'])) {
                        $hebergement->removeEmplacement($emplacement);
//                        $em->remove($emplacement);
                    } else {
                        if (!empty($emplacement->getDistance2())) {
                            if (empty($emplacement->getDistance2()->getUnite())) {
//                                $em->remove($emplacement->getDistance2());
                                $emplacement->setDistance2(null);
                            }
                        }
                    }
                }
            }
            // dispacher les données communes
//            $this->dispacherDonneesCommune($hebergementUnifie);

            $this->supprimerHebergements($hebergementUnifie, $sitesAEnregistrer);
//            foreach ($hebergementUnifie->getHebergements() as $hebergement) {
//                /** @var MoyenCommunication $moyenCom */
//                foreach ($hebergement->getMoyenComs() as $moyenCom) {
//                    $moyenCom->setDateCreation();
//                }
//            }
            /** @var FournisseurHebergement $fournisseur */
//            foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
//                $fournisseur->getTelFixe()->setDateCreation();
//                $fournisseur->getTelMobile()->setDateCreation();
//                $fournisseur->getAdresse()->setDateCreation();
//            }
//            $this->gestionDatesMoyenComs($hebergementUnifie);
            /** @var FournisseurHebergement $fournisseur */
            foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
//                    supprime le fournisseurHebergement car plus présent
                    $hebergementUnifie->removeFournisseur($fournisseur);
                    $em->remove($fournisseur);
                } else {
                    $fournisseur->setHebergement($hebergementUnifie);
//                    if (is_null($fournisseur->getAdresse()->getDateCreation())) {
//                        $fournisseur->getAdresse()->setDateCreation();
//                    } else {
//                        $fournisseur->getAdresse()->setDateModification(new DateTime());
//                    }
//                    if (is_null($fournisseur->getTelFixe()->getDateCreation())) {
//                        $fournisseur->getTelFixe()->setDateCreation();
//                    } else {
//                        $fournisseur->getTelFixe()->setDateModification(new DateTime());
//                    }
//                    if (is_null($fournisseur->getTelMobile()->getDateCreation())) {
//                        $fournisseur->getTelMobile()->setDateCreation();
//                    } else {
//                        $fournisseur->getTelMobile()->setDateModification(new DateTime());
//                    }
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('hebergement_unifie')['hebergements'] as $key => $hebergement) {
                if (!empty($hebergementUnifie->getHebergements()->get($key)) && $hebergementUnifie->getHebergements()->get($key)->getSite()->getCrm() == 1) {
                    $hebergementCrm = $hebergementUnifie->getHebergements()->get($key);
                    if (!empty($hebergement['visuels'])) {
                        foreach ($hebergement['visuels'] as $keyVisuel => $visuel) {
                            /** @var HebergementVisuel $visuelCrm */
                            $visuelCrm = $hebergementCrm->getVisuels()[$keyVisuel];
                            $visuelCrm->setActif(true);
                            $visuelCrm->setHebergement($hebergementCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Hebergement $hebergementSite */
                                    $hebergementSite = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($hebergementSite)) {
//                                      $typeVisuel = (new ReflectionClass($visuelCrm))->getShortName();
                                        $typeVisuel = (new ReflectionClass($visuelCrm))->getName();

                                        /** @var HebergementVisuel $hebergementVisuel */
                                        $hebergementVisuel = new $typeVisuel();
                                        $hebergementVisuel->setHebergement($hebergementSite);
                                        $hebergementVisuel->setVisuel($visuelCrm->getVisuel());
                                        $hebergementSite->addVisuel($hebergementVisuel);
                                        foreach ($visuelCrm->getTraductions() as $traduction) {
                                            $traductionSite = new HebergementVisuelTraduction();
                                            /** @var HebergementVisuelTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $hebergementVisuel->addTraduction($traductionSite);
                                        }
                                        if (!empty($visuel['sites']) && in_array($site->getId(), $visuel['sites'])) {
                                            $hebergementVisuel->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($hebergementUnifie);
            $em->flush();

            $this->copieVersSites($hebergementUnifie);
            $this->addFlash('success', 'l\'hébergement a bien été créé');
            return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $hebergementUnifie->getId()));
        }
        $formView = $form->createView();
        return $this->render('@MondofuteHebergement/hebergementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $hebergementUnifie,
            'form' => $formView,
        ));
    }

    /**
     * Ajouter les hébergements qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param HebergementUnifie $entity
     */
    private function ajouterHebergementsDansForm(HebergementUnifie $entity)
    {
        /** @var Hebergement $hebergement */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        $emplacements = $em->getRepository(Emplacement::class)->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getHebergements() as $hebergement) {
                if ($hebergement->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($hebergement->getTraductions()->filter(function (HebergementTraduction $element) use (
                            $langue
                        ) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new HebergementTraduction();
                            $traduction->setLangue($langue);
                            $hebergement->addTraduction($traduction);
                        }
                    }
                    /** @var Emplacement $emplacement */
                    foreach ($emplacements as $emplacement) {
                        if ($hebergement->getEmplacements()->filter(function (EmplacementHebergement $element) use (
                            $emplacement
                        ) {
                            return $element->getTypeEmplacement() == $emplacement;
                        })->isEmpty()
                        ) {
                            $emplacementHebergement = new EmplacementHebergement();
                            $emplacementHebergement->setTypeEmplacement($emplacement);
                            $hebergement->addEmplacement($emplacementHebergement);
                        }

                    }
                    $hebergement->triEmplacements($this->get('translator'));
                }
            }
            if (!$siteExiste) {
//                si l'hébergement n'existe pas on créer un nouvel hébergemùent
                $hebergement = new Hebergement();
//                création d'une adresse
                $adresse = new Adresse();
//                $adresse->setDateCreation();
                $hebergement->addMoyenCom($adresse);

                $hebergement->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new HebergementTraduction();
                    $traduction->setLangue($langue);
                    $hebergement->addTraduction($traduction);
                }
                foreach ($emplacements as $emplacement) {
                    $emplacementHebergement = new EmplacementHebergement();
                    $emplacementHebergement->setTypeEmplacement($emplacement);
                    $hebergement->addEmplacement($emplacementHebergement);
                }
                $hebergement->triEmplacements($this->get('translator'));
                $entity->addHebergement($hebergement);
            }
        }
    }

    /**
     * Classe les departements par classementAffichage
     * @param HebergementUnifie $entity
     */
    private function hebergementsSortByAffichage(HebergementUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les hébergements en fonction de leurs ordre d'affichage
        $hebergements = $entity->getHebergements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $hebergements->getIterator();
        unset($hebergements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Hebergement $a, Hebergement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $hebergements = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($hebergements);

        // remplacé les hébergements par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setHebergements($hebergements);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $hebergements
     */
    private function traductionsSortByLangue($hebergements)
    {
        /** @var ArrayIterator $iterator */
        /** @var Hebergement $hebergement */
        foreach ($hebergements as $hebergement) {
            $traductions = $hebergement->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (HebergementTraduction $a, HebergementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $hebergement->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les departements qui ne doivent pas être enregistrer
     * @param HebergementUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerHebergements(HebergementUnifie $entity, array $sitesAEnregistrer)
    {
        /** @var Hebergement $hebergement */
        foreach ($entity->getHebergements() as $hebergement) {
            if (!in_array($hebergement->getSite()->getId(), $sitesAEnregistrer)) {
//                $hebergement->setClassement(null);
                $hebergement->setHebergementUnifie(null);
                $entity->removeHebergement($hebergement);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité hébergement
     * @param HebergementUnifie $entity
     */
    private function copieVersSites(HebergementUnifie $entity, $originalHebergementVisuels = null)
    {
        /** @var HebergementTraduction $hebergementTraduc */
//        Boucle sur les hébergements afin de savoir sur quel site nous devons l'enregistrer
        /** @var Hebergement $hebergement */
        foreach ($entity->getHebergements() as $hebergement) {
            if ($hebergement->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($hebergement->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $hebergement->getSite()->getId()));
//                $region = $emSite->getRepository(Region::class)->findOneBy(array('regionUnifie' => $departement->getRegion()->getRegionUnifie()->getId()));
                if (!empty($hebergement->getStation())) {
                    $stationSite = $emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $hebergement->getStation()->getStationUnifie()->getId()));
                } else {
                    $stationSite = null;
                }
                if (!empty($hebergement->getTypeHebergement())) {
//                    $typeHebergementSite = $emSite->getRepository(TypeHebergement::class)->findOneBy(array('typeHebergementUnifie' => $hebergement->getTypeHebergement()->getTypeHebergementUnifie()->getId()));
                    $typeHebergementSite = $emSite->getRepository(TypeHebergement::class)->findOneBy(array('typeHebergementUnifie' => $hebergement->getTypeHebergement()->getTypeHebergementUnifie()));
                } else {
                    $typeHebergementSite = null;
                }
//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->getRepository(HebergementUnifie::class)->find($entity->getId())))) {
                    $entitySite = new HebergementUnifie();
                }
                /** @var FournisseurHebergement $fournisseur */
                /** @var FournisseurHebergement $fournisseurSite */
//                supprime les fournisseurHebergement du site distant
                if (!empty($entitySite->getFournisseurs())) {
                    foreach ($entitySite->getFournisseurs() as $fournisseurSite) {
                        $present = false;
                        foreach ($entity->getFournisseurs() as $fournisseur) {
                            if ($fournisseurSite->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId() && $fournisseurSite->getHebergement()->getId() == $fournisseur->getHebergement()->getId()) {
                                $present = true;
                            }
                        }
                        if ($present == false) {
                            $entitySite->removeFournisseur($fournisseurSite);
                            $emSite->remove($fournisseurSite);
                        }
                    }
                }
//                balaye les fournisseurHebergement et copie les données
                foreach ($entity->getFournisseurs() as $fournisseur) {
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
                    $fournisseurSite->setHebergement($entitySite)
                        ->setFournisseur($emSite->getRepository(Fournisseur::class)->findOneBy(array('id' => $fournisseur->getFournisseur()->getId())));
                    $fournisseurSite->setRemiseClef($emSite->getRepository(RemiseClef::class)->findOneBy(array('id' => $fournisseur->getRemiseClef()->getId())));
                    $entitySite->addFournisseur($fournisseurSite);
                }
//            Récupération de l'hébergement sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($hebergementSite = $emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $entitySite))))) {
                    $hebergementSite = new Hebergement();
                }

                $classementSite = !empty($hebergementSite->getClassement()) ? $hebergementSite->getClassement() : clone $hebergement->getClassement();
                /** @var Adresse $adresse */
                /** @var CoordonneesGPS $coordonneesGPSSite */
                /** @var Adresse $adresseSite */
                $adresse = $hebergement->getMoyenComs()->first();
                if (!empty($hebergementSite->getMoyenComs())) {
                    $adresseSite = $hebergementSite->getMoyenComs()->first();
//                    $adresseSite->setDateModification(new DateTime());
                } else {
                    $adresseSite = new Adresse();
//                    $adresseSite->setDateCreation();
                    $adresseSite->setCoordonneeGps(new CoordonneesGPS());
                    $hebergementSite->addMoyenCom($adresseSite);
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
                    $uniteSite = $emSite->getRepository(Unite::class)->findOneBy(array('id' => $hebergement->getClassement()->getUnite()->getId()));
                } else {
                    $uniteSite = null;
                }
                $classementSite->setValeur($hebergement->getClassement()->getValeur());
                $classementSite->setUnite($uniteSite);

//            copie des données hébergement
                $hebergementSite
                    ->setSite($site)
                    ->setStation($stationSite)
                    ->setTypeHebergement($typeHebergementSite)
                    ->setClassement($classementSite)
                    ->setHebergementUnifie($entitySite);
//                GESTION DES EMPLACEMENTS
                $this->gestionEmplacementsSiteDistant($site, $hebergement, $hebergementSite);

//            Gestion des traductions
                foreach ($hebergement->getTraductions() as $hebergementTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $hebergementTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($hebergementTraducSite = $emSite->getRepository(HebergementTraduction::class)->findOneBy(array(
                        'hebergement' => $hebergementSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $hebergementTraducSite = new HebergementTraduction();
                    }

//                copie des données traductions
                    $hebergementTraducSite->setLangue($langue)
                        ->setActivites($hebergementTraduc->getActivites())
                        ->setAvisMondofute($hebergementTraduc->getActivites())
                        ->setBienEtre($hebergementTraduc->getBienEtre())
                        ->setNom($hebergementTraduc->getNom())
                        ->setPourLesEnfants($hebergementTraduc->getPourLesEnfants())
                        ->setRestauration($hebergementTraduc->getRestauration())
                        ->setHebergement($hebergementTraduc->getHebergement());

//                ajout a la collection de traduction de l'hébergement
                    $hebergementSite->addTraduction($hebergementTraducSite);
                }

                // ********** GESTION DES MEDIAS **********

                $hebergementVisuels = $hebergement->getVisuels(); // ce sont les hebegementVisuels ajouté

                // si il y a des Medias pour l'hebergement de référence
                if (!empty($hebergementVisuels) && !$hebergementVisuels->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($hebergementSite->getVisuels()) && !$hebergementSite->getVisuels()->isEmpty()) {
                        // on ajoute les hébergementVisuels dans un tableau afin de travailler dessus
                        $hebergementVisuelSites = new ArrayCollection();
                        foreach ($hebergementSite->getVisuels() as $hebergementvisuelSite) {
                            $hebergementVisuelSites->add($hebergementvisuelSite);
                        }
                        // on parcourt les hébergmeentVisuels de la base
                        /** @var HebergementVisuel $hebergementVisuel */
                        foreach ($hebergementVisuels as $hebergementVisuel) {
                            // *** récupération de l'hébergementVisuel correspondant sur la bdd distante ***
                            // récupérer l'hebergementVisuel original correspondant sur le crm
                            /** @var ArrayCollection $originalHebergementVisuels */
                            $originalHebergementVisuel = $originalHebergementVisuels->filter(function (HebergementVisuel $element) use ($hebergementVisuel) {
                                return $element->getVisuel() == $hebergementVisuel->getVisuel();
                            })->first();
                            unset($hebergementVisuelSite);
                            if ($originalHebergementVisuel !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalHebergementVisuels as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalVisuel = $tab->indexOf($originalHebergementVisuel);

                                $hebergementVisuelSite = $hebergementVisuelSites->get($keyoriginalVisuel);
                            }
                            // *** fin récupération de l'hébergementVisuel correspondant sur la bdd distante ***

                            // si l'hebergementVisuel existe sur la bdd distante, on va le modifier
                            /** @var HebergementVisuel $hebergementVisuelSite */
                            if (!empty($hebergementVisuelSite)) {
                                // Si le visuel a été modifié
                                // (que le crm_ref_id est différent de de l'id du visuel de l'hebergementVisuel du crm)
                                if ($hebergementVisuelSite->getVisuel()->getMetadataValue('crm_ref_id') != $hebergementVisuel->getVisuel()->getId()) {
                                    $cloneVisuel = clone $hebergementVisuel->getVisuel();
                                    $cloneVisuel->setMetadataValue('crm_ref_id', $hebergementVisuel->getVisuel()->getId());
                                    $cloneVisuel->setContext('hebergement_visuel_' . $hebergement->getSite()->getLibelle());

                                    // on supprime l'ancien visuel
                                    $emSite->remove($hebergementVisuelSite->getVisuel());
                                    $this->deleteFile($hebergementVisuelSite->getVisuel());

                                    $hebergementVisuelSite->setVisuel($cloneVisuel);
                                }

                                $hebergementVisuelSite->setActif($hebergementVisuel->getActif());

                                // on parcourt les traductions
                                /** @var HebergementVisuelTraduction $traduction */
                                foreach ($hebergementVisuel->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var HebergementVisuelTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $hebergementVisuelSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (HebergementVisuelTraduction $element) use ($traduction) {
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
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $hebergementVisuelSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createHebergementVisuel($hebergementVisuel, $hebergementSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'hebergement de référence
                        /** @var HebergementVisuel $hebergementVisuel */
                        foreach ($hebergementVisuels as $hebergementVisuel) {
                            $this->createHebergementVisuel($hebergementVisuel, $hebergementSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($hebergementVisuelSites)) {
                        /** @var HebergementVisuel $hebergementVisuelSite */
                        foreach ($hebergementVisuelSites as $hebergementVisuelSite) {
                            $hebergementVisuelSite->setHebergement(null);
                            $emSite->remove($hebergementVisuelSite->getVisuel());
                            $this->deleteFile($hebergementVisuelSite->getVisuel());
                            $emSite->remove($hebergementVisuelSite);
                        }
                    }
                }
                // ********** FIN GESTION DES MEDIAS **********

                $entitySite->addHebergement($hebergementSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterHebergementUnifieSiteDistant($entity->getId(), $entity);
    }

    /**
     * @param $fournisseur
     * @param $fournisseurSite
     */
    public function dupliqueFounisseurHebergement(
        FournisseurHebergement $fournisseur,
        FournisseurHebergement $fournisseurSite,
        $emSite
    ) {
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

    public function gestionEmplacementsSiteDistant(Site $site, Hebergement $hebergement, Hebergement $hebergementSite)
    {
        /** @var EmplacementHebergement $emplacement */
        /** @var EmplacementHebergement $emplacementSite */
//        Suppression des emplacements qui ne sont plus présents
        $emSite = $this->getDoctrine()->getManager($site->getLibelle());
        $emplacementsSite = $emSite->getRepository(EmplacementHebergement::class)->findBy(array('hebergement' => $hebergementSite));
        foreach ($emplacementsSite as $emplacementSite) {
            $present = 0;
            foreach ($hebergement->getEmplacements() as $emplacement) {
                if ($emplacementSite->getTypeEmplacement() == $emplacement->getTypeEmplacement()) {
                    $present = 1;
                }
            }
            if ($present == 0) {
                $emSite->remove($emplacementSite);
            }
        }

        foreach ($hebergement->getEmplacements() as $emplacement) {
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
                'hebergement' => $hebergementSite
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
            $hebergementSite->addEmplacement($emplacementSite);
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
     * @param HebergementVisuel $hebergementVisuel
     * @param Hebergement $hebergementSite
     * @param EntityManager $emSite
     */
    private function createHebergementVisuel(HebergementVisuel $hebergementVisuel, Hebergement $hebergementSite, EntityManager $emSite)
    {
        /** @var HebergementVisuel $hebergementVisuelSite */
        // on récupère la classe correspondant au visuel (photo ou video)
        $typeVisuel = (new ReflectionClass($hebergementVisuel))->getName();
        // on cré un nouveau HebergementVisuel on fonction du type
        $hebergementVisuelSite = new $typeVisuel();
        $hebergementVisuelSite->setHebergement($hebergementSite);
        $hebergementVisuelSite->setActif($hebergementVisuel->getActif());
        // on lui clone l'image
        $cloneVisuel = clone $hebergementVisuel->getVisuel();

        // **** récupération du visuel physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneVisuel->getProviderName());
        $provider->getReferenceImage($cloneVisuel);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneVisuel->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneVisuel));
        $cloneVisuel->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneVisuel));

        $cloneVisuel->setProviderReference($hebergementVisuel->getVisuel()->getProviderReference());
        $cloneVisuel->setName($hebergementVisuel->getVisuel()->getName());
        // **** fin récupération du visuel physique ****

        // on donne au nouveau visuel, le context correspondant en fonction du site
        $cloneVisuel->setContext('hebergement_visuel_' . $hebergementSite->getSite()->getLibelle());
        // on lui attache l'id de référence du visuel correspondant sur la bdd crm
        $cloneVisuel->setMetadataValue('crm_ref_id', $hebergementVisuel->getVisuel()->getId());

        $hebergementVisuelSite->setVisuel($cloneVisuel);

        $hebergementSite->addVisuel($hebergementVisuelSite);
        // on ajoute les traductions correspondante
        foreach ($hebergementVisuel->getTraductions() as $traduction) {
            $traductionSite = new HebergementVisuelTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $hebergementVisuelSite->addTraduction($traductionSite);
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas d'hébergement a enregistrer
     * @param $idUnifie
     * @param $hebergementUnifie
     */
    private function ajouterHebergementUnifieSiteDistant($idUnifie, HebergementUnifie $hebergementUnifie)
    {
        /** @var ArrayCollection $hebergements */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($hebergementUnifie->getHebergements()->matching($criteres)) == 0 && (empty($emSite->getRepository(HebergementUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new HebergementUnifie();
//                foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
//                    $entity->addFournisseur($fournisseur);
//                }
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a HebergementUnifie entity.
     *
     */
    public function showAction(HebergementUnifie $hebergementUnifie)
    {
        $deleteForm = $this->createDeleteForm($hebergementUnifie);
        return $this->render('@MondofuteHebergement/hebergementunifie/show.html.twig', array(
            'hebergementUnifie' => $hebergementUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a HebergementUnifie entity.
     *
     * @param HebergementUnifie $hebergementUnifie The HebergementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(HebergementUnifie $hebergementUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hebergement_hebergement_delete',
                array('id' => $hebergementUnifie->getId())))
            ->add('delete', SubmitType::class, array('label' => 'supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing HebergementUnifie entity.
     *
     */
    public function editAction(Request $request, HebergementUnifie $hebergementUnifie)
    {

        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                array_push($sitesAEnregistrer, $hebergement->getSite()->getId());
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalHebergements = new ArrayCollection();
        $originalHebergementVisuels = new ArrayCollection();
        $originalVisuels = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        /** @var Hebergement $hebergement */
        foreach ($hebergementUnifie->getHebergements() as $hebergement) {
            $originalHebergements->add($hebergement);
            // si l'hebergement est celui du CRM
            if ($hebergement->getSite()->getCrm() == 1) {
                // on parcourt les hebergementVisuel pour les comparer ensuite
                /** @var HebergementVisuel $hebergementVisuel */
                foreach ($hebergement->getVisuels() as $hebergementVisuel) {
                    // on ajoute les visuel dans la collection de sauvegarde
                    $originalHebergementVisuels->add($hebergementVisuel);
                    $originalVisuels->add($hebergementVisuel->getVisuel());
                }
            }
        }

        $this->ajouterHebergementsDansForm($hebergementUnifie);
//        $this->dispacherDonneesCommune($departementUnifie);
        $this->hebergementsSortByAffichage($hebergementUnifie);
        $deleteForm = $this->createDeleteForm($hebergementUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType',
            $hebergementUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'mettre.a.jour',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));

        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // ************* suppression visuels *************
            // ** CAS OU L'ON SUPPRIME UN "HEBERGEMENT VISUEL" **
            // on récupère les HebergementVisuel de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Hebergement $hebergementCrm */
            $hebergementCrm = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $hebergementSites = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newHebergementVisuels = new ArrayCollection();
            foreach ($hebergementCrm->getVisuels() as $hebergementVisuel) {
                $newHebergementVisuels->add($hebergementVisuel);
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
                    /** @var Hebergement $hebergementSite */
                    foreach ($hebergementSites as $hebergementSite) {
                        $hebergementVisuelSite = $em->getRepository(HebergementVisuel::class)->findOneBy(
                            array(
                                'hebergement' => $hebergementSite,
                                'visuel' => $originalHebergementVisuel->getVisuel()
                            ));
                        if (!empty($hebergementVisuelSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($hebergementVisuelSite->getHebergement()->getSite()->getLibelle());
                            $hebergementSite = $emSite->getRepository(Hebergement::class)->findOneBy(
                                array(
                                    'hebergementUnifie' => $hebergementVisuelSite->getHebergement()->getHebergementUnifie()
                                ));
                            $hebergementVisuelSiteSites = new ArrayCollection($emSite->getRepository(HebergementVisuel::class)->findBy(
                                array(
                                    'hebergement' => $hebergementSite
                                ))
                            );
                            $hebergementVisuelSiteSite = $hebergementVisuelSiteSites->filter(function (HebergementVisuel $element)
                            use ($hebergementVisuelSite) {
//                            return $element->getVisuel()->getProviderReference() == $hebergementVisuelSite->getVisuel()->getProviderReference();
                                return $element->getVisuel()->getMetadataValue('crm_ref_id') == $hebergementVisuelSite->getVisuel()->getId();
                            })->first();
                            if (!empty($hebergementVisuelSiteSite)) {
                                $emSite->remove($hebergementVisuelSiteSite->getVisuel());
                                $this->deleteFile($hebergementVisuelSiteSite->getVisuel());
                                $hebergementVisuelSiteSite->setHebergement(null);
                                $emSite->remove($hebergementVisuelSiteSite);
                                $emSite->flush();
                            }
                            $hebergementVisuelSite->setHebergement(null);
                            $em->remove($hebergementVisuelSite->getVisuel());
                            $this->deleteFile($hebergementVisuelSite->getVisuel());
                            $em->remove($hebergementVisuelSite);
                        }
                    }
                }
            }
            // ************* fin suppression visuels *************

            /** @var Hebergement $hebergement */
            foreach ($hebergementUnifie->getHebergements() as $keyHebergement => $hebergement) {
                foreach ($hebergement->getEmplacements() as $keyEmplacement => $emplacement) {
                    if (empty($request->request->get('hebergement_unifie')['hebergements'][$keyHebergement]['emplacements'][$keyEmplacement]['checkbox'])) {
                        $hebergement->removeEmplacement($emplacement);
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
            }
            $this->supprimerHebergements($hebergementUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre l'hébergement et hebergementUnifie
            foreach ($originalHebergements as $hebergement) {
                /** @var Hebergement $hebergement */
                /** @var Hebergement $hebergementSite */
                if (!$hebergementUnifie->getHebergements()->contains($hebergement)) {
                    //  suppression de l'hébergement sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($hebergement->getSite()->getLibelle());
                    $entitySite = $emSite->find(HebergementUnifie::class, $hebergementUnifie->getId());
                    foreach ($entitySite->getFournisseurs() as $fournisseurSite) {
                        $entitySite->removeFournisseur($fournisseurSite);
                        $emSite->remove($fournisseurSite);
                    }
                    if (!empty($entitySite)) {
                        if (!empty($entitySite->getHebergements())) {
                            $hebergementSite = $entitySite->getHebergements()->first();
                            if (!empty($hebergementSite)) {
//                                permet de gérer les contraintes en base de données
                                if (!empty($hebergementSite->getMoyenComs())) {
                                    foreach ($hebergementSite->getMoyenComs() as $moyenComSite) {
                                        $hebergementSite->removeMoyenCom($moyenComSite);
                                        $emSite->remove($moyenComSite);
                                    }
                                    $emSite->flush();
                                }
                                /** @var HebergementVisuel $hebergementVisuelSite */
                                if (!empty($hebergementSite->getVisuels())) {
                                    foreach ($hebergementSite->getVisuels() as $hebergementVisuelSite) {
                                        $hebergementSite->removeVisuel($hebergementVisuelSite);
//                                        $hebergementVisuelSite->setHebergement(null);
//                                        $hebergementVisuelSite->setVisuel(null);
                                        $emSite->remove($hebergementVisuelSite);
                                        $emSite->remove($hebergementVisuelSite->getVisuel());
                                        $this->deleteFile($hebergementVisuelSite->getVisuel());
                                    }
                                    $emSite->flush();
                                }
                                $emSite->remove($hebergementSite);
                                $emSite->flush();
                            }
                        }
                    }
                    if (!empty($hebergement->getMoyenComs())) {
                        foreach ($hebergement->getMoyenComs() as $moyenCom) {
                            $hebergement->removeMoyenCom($moyenCom);
                            $em->remove($moyenCom);
                        }
                    }
//                    permet de gérer les moyens de com sans une erreur d'intégrité
                    $em->persist($hebergement);
                    $em->flush();

                    $hebergement->setHebergementUnifie(null);

                    // *** suppression des hebergementVisuels de l'hebergement à supprimer ***
                    /** @var HebergementVisuel $hebergementVisuel */
                    $hebergementVisuelSites = $em->getRepository(HebergementVisuel::class)->findBy(array('hebergement' => $hebergement));
                    if (!empty($hebergementVisuelSites)) {
                        foreach ($hebergementVisuelSites as $hebergementVisuel) {
                            $hebergementVisuel->setVisuel(null);
                            $hebergementVisuel->setHebergement(null);
                            $em->remove($hebergementVisuel);
                        }
                        $em->flush();
                    }
                    // *** fin suppression des hebergementVisuels de l'hebergement à supprimer ***

                    $em->remove($hebergement);
                }
            }
            /** @var FournisseurHebergement $fournisseur */
            foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
                    //  supprime le fournisseurHebergement car plus présent
                    $hebergementUnifie->removeFournisseur($fournisseur);
                    $em->remove($fournisseur);
                } else {
                    $fournisseur->setHebergement($hebergementUnifie);
                }
            }

            // ***** Gestion des Medias *****
            // CAS D'UN NOUVEAU 'HEBERGEMENT VISUEL' OU DE MODIFICATION D'UN "HEBERGEMENT VISUEL"
            /** @var HebergementVisuel $hebergementVisuel */
            // tableau pour la suppression des anciens visuels
            $visuelToRemoveCollection = new ArrayCollection();
            $keyCrm = $hebergementUnifie->getHebergements()->indexOf($hebergementCrm);
            // on parcourt les hebergementVisuels de l'hebergement crm
            foreach ($hebergementCrm->getVisuels() as $key => $hebergementVisuel) {
                // on active le nouveau hebergementVisuel (CRM) => il doit être toujours actif
                $hebergementVisuel->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementVisuel pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Hebergement $hebergementSite */
                        $hebergementSite = $hebergementUnifie->getHebergements()->filter(function (Hebergement $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($hebergementSite)) {
                            // on réinitialise la variable
                            unset($hebergementVisuelSite);
                            // s'il ne s'agit pas d'un nouveau hebergementVisuel
                            if (!empty($hebergementVisuel->getId())) {
                                // on récupère l'hebergementVisuel pour le modifier
                                $hebergementVisuelSite = $em->getRepository(HebergementVisuel::class)->findOneBy(array('hebergement' => $hebergementSite, 'visuel' => $originalVisuels->get($key)));
                            }
                            // si l'hebergementVisuel est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($hebergementVisuel->getId()) || empty($hebergementVisuelSite)) {
                                // on récupère la classe correspondant au visuel (photo ou video)
                                $typeVisuel = (new ReflectionClass($hebergementVisuel))->getName();
                                // on créé un nouveau HebergementVisuel on fonction du type
                                /** @var HebergementVisuel $hebergementVisuelSite */
                                $hebergementVisuelSite = new $typeVisuel();
                                $hebergementVisuelSite->setHebergement($hebergementSite);
                            }
                            // si l'hébergemenent visuel existe déjà pour le site
                            if (!empty($hebergementVisuelSite)) {
                                if ($hebergementVisuelSite->getVisuel() != $hebergementVisuel->getVisuel()) {
//                                    // si l'hébergementVisuelSite avait déjà un visuel
//                                    if (!empty($hebergementVisuelSite->getVisuel()) && !$visuelToRemoveCollection->contains($hebergementVisuelSite->getVisuel()))
//                                    {
//                                        // on met l'ancien visuel dans un tableau afin de le supprimer plus tard
//                                        $visuelToRemoveCollection->add($hebergementVisuelSite->getVisuel());
//                                    }
                                    // on met le nouveau visuel
                                    $hebergementVisuelSite->setVisuel($hebergementVisuel->getVisuel());
                                }
                                $hebergementSite->addVisuel($hebergementVisuelSite);

                                /** @var HebergementVisuelTraduction $traduction */
                                foreach ($hebergementVisuel->getTraductions() as $traduction) {
                                    /** @var HebergementVisuelTraduction $traductionSite */
                                    $traductionSites = $hebergementVisuelSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (HebergementVisuelTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new HebergementVisuelTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $hebergementVisuelSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementVisuel doit être actif sur le site ou non
                                if (!empty($request->get('hebergement_unifie')['hebergements'][$keyCrm]['visuels'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('hebergement_unifie')['hebergements'][$keyCrm]['visuels'][$key]['sites'])
                                ) {
                                    $hebergementVisuelSite->setActif(true);
                                }
                            }
                        }
                    }
                    // on est dans l'hebergementVisuel CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($hebergementVisuel->getVisuel()->getId()) && !empty($originalVisuels->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $visuelToRemoveCollection->add($originalVisuels->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($hebergementUnifie);
            $em->flush();
            $this->copieVersSites($hebergementUnifie, $originalHebergementVisuels);

            // on parcourt les médias à supprimer
            dump($visuelToRemoveCollection);
            if (!empty($visuelToRemoveCollection)) {
                foreach ($visuelToRemoveCollection as $item) {
                    if (!empty($item)) {
                        $this->deleteFile($item);
                        $em->remove($item);
                    }
                }
                $em->flush();
            }

            $this->addFlash('success', 'L\'hébergement a bien été modifié');
            return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $hebergementUnifie->getId()));
        }

        return $this->render('@MondofuteHebergement/hebergementunifie/edit.html.twig', array(
            'entity' => $hebergementUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

//    /**
//     * @param $entity
//     */
//    private function deleteMoyenComs($entity)
//    {
//        $moyenComs = $entity->getMoyenComs();
//        if (!empty($moyenComs)) {
//            foreach ($moyenComs as $moyenCom) {
//                $entity->removeMoyenCom($moyenCom);
//            }
//        }
//    }

    /**
     * Deletes a HebergementUnifie entity.
     *
     */
    public function deleteAction(Request $request, HebergementUnifie $hebergementUnifie)
    {
        /** @var HebergementUnifie $hebergementUnifieSite */
        try {
            $form = $this->createDeleteForm($hebergementUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $hebergementUnifieSite = $emSite->find(HebergementUnifie::class, $hebergementUnifie->getId());
                    if (!empty($hebergementUnifieSite)) {
                        if (!empty($hebergementUnifieSite->getHebergements())) {
                            /** @var Hebergement $hebergementSite */
                            foreach ($hebergementUnifieSite->getHebergements() as $hebergementSite) {
//                                $hebergementSite->setClassement(null);
                                if (!empty($hebergementSite->getMoyenComs())) {
                                    foreach ($hebergementSite->getMoyenComs() as $moyenComSite) {
                                        $hebergementSite->removeMoyenCom($moyenComSite);
                                        $emSite->remove($moyenComSite);
                                    }
                                }

                                // si il y a des visuels pour l'entité, les supprimer
                                if (!empty($hebergementSite->getVisuels())) {
                                    /** @var HebergementVisuel $hebergementVisuelSite */
                                    foreach ($hebergementSite->getVisuels() as $hebergementVisuelSite) {
                                        $visuelSite = $hebergementVisuelSite->getVisuel();
                                        $hebergementVisuelSite->setVisuel(null);
                                        if (!empty($visuelSite)) {
                                            $emSite->remove($visuelSite);
                                            $this->deleteFile($visuelSite);
                                        }
                                    }
                                }
                            }
                            $emSite->flush();
                        }
                        $emSite->remove($hebergementUnifieSite);
                        $emSite->flush();
                    }
                }
                if (!empty($hebergementUnifie)) {
                    if (!empty($hebergementUnifie->getHebergements())) {
                        /** @var Hebergement $hebergement */
                        foreach ($hebergementUnifie->getHebergements() as $hebergement) {
//                            $hebergement->setClassement(null);
                            if (!empty($hebergement->getMoyenComs())) {
                                foreach ($hebergement->getMoyenComs() as $moyenCom) {
                                    $hebergement->removeMoyenCom($moyenCom);
                                    $em->remove($moyenCom);
                                }
                            }

                            // si il y a des visuels pour l'entité, les supprimer
                            if (!empty($hebergement->getVisuels())) {
                                /** @var HebergementVisuel $hebergementVisuel */
                                foreach ($hebergement->getVisuels() as $hebergementVisuel) {
                                    $visuel = $hebergementVisuel->getVisuel();
                                    $hebergementVisuel->setVisuel(null);
                                    $em->remove($visuel);
                                    $this->deleteFile($visuel);
                                }
                            }
                        }
                        $em->flush();
                    }
//                    $emSite->remove($hebergementUnifieSite);
//                    $emSite->flush();
                }
//                $em = $this->getDoctrine()->getManager();
                $em->remove($hebergementUnifie);
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $except) {
            /** @var ForeignKeyConstraintViolationException $except */
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'Impossible de supprimer l\'hébergement, il est utilisé par une autre entité');
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
}
