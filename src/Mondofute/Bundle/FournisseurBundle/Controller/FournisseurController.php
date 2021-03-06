<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use HiDev\Bundle\AuteurBundle\Entity\UtilisateurAuteur;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFamillePrestationAnnexe;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseurPrestationAnnexe;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\FournisseurBundle\Entity\ConditionAnnulation;
use Mondofute\Bundle\FournisseurBundle\Entity\ConditionAnnulationDescription;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurCommentaire;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurUser;
use Mondofute\Bundle\FournisseurBundle\Entity\Priorite;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\ModeAffectation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseurUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStationUnifie;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParamTraduction;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexePeriodeIndisponible;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeTraduction;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\Type;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\PasserelleBundle\Entity\CodePasserelle;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFamillePrestationAnnexe;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction;
use Mondofute\Bundle\SaisonBundle\Entity\Saison;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonFournisseur;
use Mondofute\Bundle\ServiceBundle\Entity\CategorieService;
use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Mondofute\Bundle\ServiceBundle\Entity\Service;
use Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService;
use Mondofute\Bundle\ServiceBundle\Entity\TarifService;
use Mondofute\Bundle\ServiceBundle\Entity\TypeService;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Entity\StationUnifie;
use Mondofute\Bundle\TrancheHoraireBundle\Entity\TrancheHoraire;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Email;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Nucleus\MoyenComBundle\Entity\Pays;
use Nucleus\MoyenComBundle\Entity\TelFixe;
use Nucleus\MoyenComBundle\Entity\TelMobile;
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
 * Fournisseur controller.
 *
 */
class FournisseurController extends Controller
{
    /**
     * Lists all FournisseurUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $priorites = Priorite::$libelles;

        $count = $em
            ->getRepository('MondofuteFournisseurBundle:Fournisseur')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'fournisseur_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'entity.enseigne' => 'ASC'
        );

        $entities = $this->getDoctrine()->getRepository('MondofuteFournisseurBundle:Fournisseur')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteFournisseur/fournisseur/index.html.twig', array(
            'fournisseurs' => $entities,
            'pagination' => $pagination,
            'priorites' => $priorites,
            'utilisateurs' => $em->getRepository(Utilisateur::class)->findAll()
        ));
    }


    public function rechercherTypeHebergementAction(Request $request)
    {
        $enseigne = $request->get('enseigne');
        $em = $this->getDoctrine()->getManager();
        $fournisseurs = $em->getRepository('MondofuteFournisseurBundle:Fournisseur')->rechercherTypeHebergement($enseigne)->getQuery()->getArrayResult();
        if ($request->isXmlHttpRequest()) {
            $response = new Response();

            $data = json_encode($fournisseurs); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
        return new Response();
    }

    public function setAgentMaJSaisieSaisonEnCoursAction($id, $val)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $fournisseur = $emSite->find(Fournisseur::class, $id);
            if (empty($fournisseur->getSaisonFournisseurEnCours())) {
                $saisonEnCours = $emSite->getRepository(Saison::class)->findOneBy(['enCours' => true]);
                $saisonFournisseur = new SaisonFournisseur();
                $fournisseur->addSaisonFournisseur($saisonFournisseur);
                $saisonFournisseur->setSaison($saisonEnCours);
            }
            $fournisseur->setAgentMaJSaisieSaisonEnCours($emSite->find(Utilisateur::class, $val));
            $emSite->persist($fournisseur);
            $emSite->flush();
        }
        return new Response();
    }

    public function setPrioriteAction($id, $priorite)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $fournisseur = $emSite->find(Fournisseur::class, $id);
            $fournisseur->setPriorite($priorite);
            $emSite->persist($fournisseur);
            $emSite->flush();
        }
        return new Response();
    }

    public function setAgentMaJProdSaisonEnCoursAction($id, $val)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $fournisseur = $emSite->find(Fournisseur::class, $id);
            if (empty($fournisseur->getSaisonFournisseurEnCours())) {
                $saisonEnCours = $emSite->getRepository(Saison::class)->findOneBy(['enCours' => true]);
                $saisonFournisseur = new SaisonFournisseur();
                $fournisseur->addSaisonFournisseur($saisonFournisseur);
                $saisonFournisseur->setSaison($saisonEnCours);
            }
            $fournisseur->setAgentMaJProdSaisonEnCours($emSite->find(Utilisateur::class, $val));
            $emSite->persist($fournisseur);
            $emSite->flush();
        }
        return new Response();
    }

    /**
     * Creates a new Fournisseur entity.
     *
     */
    public function newAction(Request $request)
    {
        /** @var FournisseurInterlocuteur $interlocuteur */
        /** @var FournisseurInterlocuteur $interlocuteur */
        /** @var Site $site */
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $locale = $request->getLocale();
        $fournisseur = new Fournisseur();

        // Ajouter une nouvelle adresse au Moyen de communication du fournisseur
        $adresse = new Adresse();
        $adresse->setCoordonneeGps(new CoordonneesGPS());
        $fournisseur->addMoyenCom($adresse);

        // *** gestion saisons ***
        $this->gestionSaisons($fournisseur);
        // *** fin gestion saisons ***

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array('locale' => $locale));

        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));

        $form->handleRequest($request);

        $errorType = false;

        $errorInterlocuteur = false;
        $interlocuteurController = new InterlocuteurController();
        $interlocuteurController->setContainer($this->container);
//        if (!empty($fournisseur->getInterlocuteurs()) && empty($fournisseur->getFournisseurParent())) {
        if (!empty($fournisseur->getInterlocuteurs())) {
            $interlocuteurController->newInterlocuteurUsers($fournisseur->getInterlocuteurs());
            if ($interlocuteurController->testInterlocuteursLoginExist($fournisseur->getInterlocuteurs())) {
                $errorInterlocuteur = true;
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$errorType && !$errorInterlocuteur) {

            /** @var ListeService $listeService */
            foreach ($fournisseur->getListeServices() as $listeService) {
                $listeService->setFournisseur($fournisseur);
                /** @var Service $service */
                foreach ($listeService->getServices() as $service) {
                    $service->setListeService($listeService);
                    /** @var TarifService $tarifService */
                    foreach ($service->getTarifs() as $tarifService) {
                        $tarifService->setService($service);
                    }
                }
            }

            // ***** GESTION DES INTERLOCUTEURS *****
            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                $interlocuteur->setFournisseur($fournisseur);
            }

            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                foreach ($interlocuteur->getInterlocuteur()->getMoyenComs() as $moyenCom) {

                    $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                    switch ($typeComm) {
                        case "Adresse":
                            /** @var Adresse $moyenComSite */
                            $moyenCom->setPays($em->find(Pays::class, $moyenCom->getPays()));
                            break;
                        default:
                            break;
                    }
                }
            }
            // ***** FIN GESTION DES INTERLOCUTEURS *****

            foreach ($fournisseur->getMoyenComs() as $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                switch ($typeComm) {
                    case "Adresse":
                        /** @var Adresse $moyenComSite */
                        $moyenCom->setPays($em->find(Pays::class, $moyenCom->getPays()));
                        break;
                    default:
                        break;
                }
            }


            $this->gestionInformationRM($fournisseur);

            $this->gestionConditionAnnulationDescription($fournisseur);

            $em->persist($fournisseur);
            $em->flush();

            $this->copieVersSites($fournisseur);

            $this->gestionPromotionFournisseur($fournisseur);
//            $this->gestionPromotionFamillePrestationAnnexe($fournisseur);

            // add flash messages
            $this->addFlash(
                'success',
                'Le fournisseur a bien été créé.'
            );

            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));

        }

        return $this->render('@MondofuteFournisseur/fournisseur/new.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $form->createView(),
            'langues' => $langues,
//            'famillePrestationAnnexes'  => $famillePrestationAnnexes
        ));
    }

    private function gestionSaisons(Fournisseur $fournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        $saisons = $em->getRepository(Saison::class)->findAll();
        foreach ($saisons as $saison) {
            $saisonFournisseur = $fournisseur->getSaisonFournisseurs()->filter(function (SaisonFournisseur $element) use ($saison) {
                return $element->getSaison() == $saison;
            })->first();
            if (false === $saisonFournisseur) {
                $saisonFournisseur = new SaisonFournisseur();
                $saisonFournisseur
                    ->setSaison($saison);
                if (false !== $fournisseur->getSaisonFournisseurs()->first()) {
                    $saisonFournisseur
                        ->setFlux($fournisseur->getSaisonFournisseurs()->first()->getFlux())
                        ->setAgentMaJProd($fournisseur->getSaisonFournisseurs()->first()->getAgentMaJProd())
                        ->setAgentMaJSaisie($fournisseur->getSaisonFournisseurs()->first()->getAgentMaJSaisie());
                }
                $fournisseur->addSaisonFournisseur($saisonFournisseur);
            }
        }
    }

    /**
     * @param Fournisseur $fournisseur
     */
    private function gestionInformationRM($fournisseur)
    {
        $type = $fournisseur->getTypes()->filter(function (FamillePrestationAnnexe $element) {
            return $element->getId() == 1;
        })->first();
        if (false === $type) {
            $fournisseur
                ->setLieuRetraitForfaitSki(null)
                ->setCommissionForfaitFamille(null)
                ->setCommissionForfaitPeriode(null)
                ->setCommissionSupportMainLibre(null);
        }
    }

    private function gestionConditionAnnulationDescription(Fournisseur $fournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        switch ($fournisseur->getConditionAnnulation()) {
            case ConditionAnnulation::standard:
                $standard = $em->find(ConditionAnnulationDescription::class, 1);
                $em->refresh($standard);
                if (!empty($fournisseur->getConditionAnnulationDescription()) && $fournisseur->getConditionAnnulationDescription()->getId() != 1) {
                    $em->remove($fournisseur->getConditionAnnulationDescription());
                }
                $fournisseur->setConditionAnnulationDescription($standard);
                break;
            case ConditionAnnulation::personnalisee:
                if (empty($fournisseur->getConditionAnnulationDescription()->getId()) or $fournisseur->getConditionAnnulationDescription()->getId() == 1) {
                    $perso = new ConditionAnnulationDescription();
                    $perso->setDescription($fournisseur->getConditionAnnulationDescription()->getDescription());
                    $fournisseur->setConditionAnnulationDescription($perso);
                }
                $standard = $em->find(ConditionAnnulationDescription::class, 1);
                $em->refresh($standard);
                break;
            default:
                if (!empty($fournisseur->getConditionAnnulationDescription()) && $fournisseur->getConditionAnnulationDescription()->getId() != 1) {
                    $em->remove($fournisseur->getConditionAnnulationDescription());
                }
                $fournisseur->setConditionAnnulationDescription(null);
                break;
        }
    }

    private function copieVersSites(Fournisseur $fournisseur)
    {
        /** @var EntityManager $emSite */
        /** @var MoyenCommunication $moyenComSite */
        /** @var Site $site */
        /** @var FournisseurInterlocuteur $interlocuteur */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            $fournisseurSite = $emSite->find(Fournisseur::class, $fournisseur);
            if (empty($fournisseurSite)) {
                $fournisseurSite = new Fournisseur();
                $fournisseurSite->setId($fournisseur->getId());
                $metadata = $emSite->getClassMetadata(get_class($fournisseurSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $fournisseurSite
                    ->setContient($fournisseur->getContient())
                    ->setPhototheque($fournisseur->getPhototheque())
                    ->setBlocageVente($fournisseur->getBlocageVente())
                    ->setPriorite($fournisseur->getPriorite())
                    ->setEnseigne($fournisseur->getEnseigne())
                    ->setRaisonSociale($fournisseur->getRaisonSociale());

                foreach ($fournisseur->getTypes() as $typeFournisseur) {
                    $typeFournisseurSite = $emSite->find(FamillePrestationAnnexe::class, $typeFournisseur);
                    $fournisseurSite->addType($typeFournisseurSite);
                }

                if (!empty($fournisseurSite->getFournisseurParent())) {
                    $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur',
                        $fournisseurSite->getFournisseurParent()->getId()));
                }

                $this->dupliquerListeServicesSite($fournisseurSite, $fournisseur->getListeServices(), $emSite);

                $moyenComsSite = $fournisseurSite->getMoyenComs();
                if (!empty($moyenComsSite)) {
                    foreach ($moyenComsSite as $key => $moyenComSite) {
                        $moyenComsSite[$key] = clone $moyenComSite;

                        $typeComm = (new ReflectionClass($moyenComSite))->getShortName();
                        switch ($typeComm) {
                            case "Adresse":
                                /** @var Adresse $moyenComSite */
                                $moyenComSite->setPays($emSite->find(Pays::class, $moyenComSite->getPays()));
                                $moyenComSite->setCoordonneeGps(new CoordonneesGPS());
                                $moyenComsSite[$key]->setPays($emSite->find(Pays::class, $moyenComSite->getPays()));
                                break;
                            default:
                                break;
                        }
                    }
                }

                // ***** GESTION DES INTERLOCUTEURS *****
                // on parcourt les fournisseurInterlocuteurs du fournisseur de la base crm
                /** @var FournisseurInterlocuteur $fournisseurInterlocuteur */
                /** @var FournisseurInterlocuteur $fournisseurInterlocuteurSite */
                /** @var Interlocuteur $interlocuteur */
                /** @var Interlocuteur $interlocuteurSite */
                /** @var InterlocuteurUser $interlocuteurUser */
                /** @var InterlocuteurUser $interlocuteurUserSite */

                foreach ($fournisseur->getInterlocuteurs() as $fournisseurInterlocuteur) {
                    $interlocuteur = $fournisseurInterlocuteur->getInterlocuteur();

                    $fournisseurInterlocuteurSite = new FournisseurInterlocuteur();

                    /** @var Interlocuteur $interlocuteurSite */
                    $interlocuteurSite = new Interlocuteur();

                    $interlocuteurSite->setPrenom($interlocuteur->getPrenom());
                    $interlocuteurSite->setNom($interlocuteur->getNom());

                    if (!empty($interlocuteur->getFonction())) {
                        $interlocuteurSite->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                            $interlocuteur->getFonction()->getId()));
                    }
                    if (!empty($interlocuteur->getService())) {
                        $interlocuteurSite->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                            $interlocuteur->getService()->getId()));
                    }

                    $fournisseurInterlocuteurSite->setFournisseur($fournisseurSite);
                    $fournisseurInterlocuteurSite->setInterlocuteur($interlocuteurSite);

                    foreach ($interlocuteur->getMoyenComs() as $moyenCom) {
                        $moyenComClone = clone $moyenCom;
                        $interlocuteurSite->addMoyenCom($moyenComClone);

                        $typeComm = (new ReflectionClass($moyenComClone))->getShortName();
                        switch ($typeComm) {
                            case "Adresse":
                                /** @var Adresse $moyenComClone */
                                $moyenComClone->setPays($emSite->find(Pays::class, $moyenComClone->getPays()));
                                break;
                            default:
                                break;
                        }
                    }
                    // ***** gestion creation interlocuteur_user *****
                    $interlocuteurUserSite = clone $interlocuteur->getUser();
                    $interlocuteurSite->setUser($interlocuteurUserSite);
                    // ***** fin creation gestion interlocuteur_user *****

                    $fournisseurSite->addInterlocuteur($fournisseurInterlocuteurSite);
                }
                // ***** FIN GESTION DES INTERLOCUTEURS *****

                if (!empty($fournisseur->getRemiseClefs())) {
                    $this->dupliquerRemiseClefsSite($fournisseur->getRemiseClefs(), $fournisseurSite, $emSite);
                }

                if (!empty($fournisseur->getReceptions())) {
                    $this->dupliquerReceptionsSite($fournisseur->getReceptions(), $fournisseurSite);
                }

                // ***** gestion logo *****
                if (!empty($fournisseur->getLogo())) {
                    $logo = $fournisseur->getLogo();
                    if (empty($fournisseurSite->getLogo()) || $logo->getId() != $fournisseurSite->getLogo()->getId()) {
                        $cloneVisuel = clone $logo;
                        $metadata = $emSite->getClassMetadata(get_class($cloneVisuel));
                        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        $cloneVisuel->setContext('fournisseur_logo_' . $site->getLibelle());
                        if (!empty($fournisseurSite->getLogo())) {
                            $emSite->remove($fournisseurSite->getLogo());
                        }
                        $fournisseurSite->setLogo($cloneVisuel);
                    }
                } else {
                    if (!empty($fournisseurSite->getLogo())) {
                        $emSite->remove($fournisseurSite->getLogo());
                        $fournisseurSite->setLogo(null);
                    }
                }
                // ***** fin gestion logo *****

                // ***** gestion clause contractuelle *****
                $this->gestionClauseContractuelleSite($fournisseur, $fournisseurSite);
                // ***** fin gestion clause contractuelle *****

                // ***** gestion remontée RM *****
                $this->gestionInformationRMSite($fournisseur, $fournisseurSite);
                // ***** fin remontée RM *****

                $this->gestionConditionAnnulationDescriptionSite($fournisseur, $fournisseurSite, $emSite);

                // ** gestion saison **
                $this->gestionSaisonsSite($fournisseur, $fournisseurSite, $emSite);
                // ** fin gestion saison **

                $this->gestionCodePasserelleSite($fournisseurSite, $fournisseur, $emSite);

                $emSite->persist($fournisseurSite);

                $emSite->flush();
            }
        }
    }

    /**
     * @param Fournisseur $fournisseurSite
     * @param $listeServices
     * @param EntityManager $emSite
     * @throws \Doctrine\ORM\ORMException
     */
    public function dupliquerListeServicesSite(
        Fournisseur $fournisseurSite,
        $listeServices,
        EntityManager $emSite
    )
    {
        /** @var ListeService $listeService */
        foreach ($listeServices as $listeService) {
            if (empty($listeService->getId()) || empty($listeServiceSite = $emSite->getRepository(ListeService::class)->find($listeService->getId()))) {
                $listeServiceSite = new ListeService();
                $fournisseurSite->addListeService($listeServiceSite);
            }
            $listeServiceSite
                ->setLibelle($listeService->getLibelle())
                ->setFournisseur($fournisseurSite);
            /** @var Service $service */
            foreach ($listeService->getServices() as $service) {
                if (empty($service->getId()) || empty($serviceSite = $emSite->getRepository(Service::class)->find($service->getId()))) {
                    $serviceSite = new Service();
                    $listeServiceSite->addService($serviceSite);
                }
                $serviceSite->setListeService($listeServiceSite)
                    ->setDefaut($service->getDefaut())
                    ->setCategorieService($emSite->getRepository(CategorieService::class)->find($service->getCategorieService()->getId()))
                    ->setSousCategorieService($emSite->getRepository(SousCategorieService::class)->find($service->getSousCategorieService()->getId()))
                    ->setType($emSite->getRepository(TypeService::class)->find($service->getType()->getId()));
                /** @var TarifService $tarifService */
                foreach ($service->getTarifs() as $tarifService) {
                    if (empty($tarifService->getId()) || empty($tarifServiceSite = $emSite->getRepository(TarifService::class)->find($tarifService->getId()))) {
                        $tarifServiceSite = new TarifService();
                        $serviceSite->addTarif($tarifServiceSite);
                    }
                    if (empty($tarifServiceSite->getTarif())) {
                        $tarifSite = new Tarif();
                        $tarifServiceSite->setTarif($tarifSite);
//                        $emSite->persist($tarifSite);
                    }
                    $tarifServiceSite->getTarif()
                        ->setUnite($emSite->getRepository(UniteTarif::class)->find($tarifService->getTarif()->getUnite()->getId()))
                        ->setValeur($tarifService->getTarif()->getValeur());
                    $tarifServiceSite->setService($serviceSite)
                        ->setTypePeriode($emSite->getRepository(TypePeriode::class)->find($tarifService->getTypePeriode()->getId()));
//                    $emSite->persist($tarifService);
                }
//                $emSite->persist($serviceSite);
            }
//            $fournisseurSite->addListeService($listeServiceSite);
//            $emSite->persist($listeServiceSite);
        }
        $emSite->persist($fournisseurSite);
    }

    public function dupliquerRemiseClefsSite($remiseClefs, Fournisseur $fournisseurSite, EntityManager $emSite)
    {
        /** @var RemiseClef $remiseClef */
        foreach ($remiseClefs as $remiseClef) {

            $remiseClefSite = new RemiseClef();
            $fournisseurSite->addRemiseClef($remiseClefSite);
            $remiseClefSite
                ->setLibelle($remiseClef->getLibelle())
                ->setHeureRemiseClefLongSejour($remiseClef->getHeureRemiseClefLongSejour())
                ->setHeureRemiseClefCourtSejour($remiseClef->getHeureRemiseClefCourtSejour())
                ->setHeureDepartLongSejour($remiseClef->getHeureDepartLongSejour())
                ->setHeureDepartCourtSejour($remiseClef->getHeureDepartCourtSejour())
                ->setHeureTardiveLongSejour($remiseClef->getHeureTardiveLongSejour())
                ->setHeureTardiveCourtSejour($remiseClef->getHeureRemiseClefCourtSejour())
                ->setStandard($remiseClef->getStandard());

            /** @var RemiseClefTraduction $traduction */
            foreach ($remiseClef->getTraductions() as $traduction) {
                $traductionSite = new RemiseClefTraduction();
                $remiseClefSite->addTraduction($traductionSite);
                $traductionSite->setLieuxRemiseClef($traduction->getLieuxRemiseClef());
                $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
            }
        }
    }

    public function dupliquerReceptionsSite($receptions, Fournisseur $fournisseurSite)
    {
        /** @var Reception $reception */
        foreach ($receptions as $reception) {
            $receptionSite = new Reception();
            $fournisseurSite->addReception($receptionSite);
            $tranche1 = new TrancheHoraire();
            $tranche1->setDebut($reception->getTranche1()->getDebut());
            $tranche1->setFin($reception->getTranche1()->getFin());
            $receptionSite->setTranche1($tranche1);
            $tranche2 = new TrancheHoraire();
            $tranche2->setDebut($reception->getTranche2()->getDebut());
            $tranche2->setFin($reception->getTranche2()->getFin());
            $receptionSite->setTranche2($tranche2);
            $receptionSite->setJour($reception->getJour());
        }
    }

    /**
     * @param Fournisseur $fournisseur
     * @param Fournisseur $fournisseurSite
     */
    private function gestionClauseContractuelleSite($fournisseur, $fournisseurSite)
    {

        $fournisseurSite
            ->setSpecificiteCommission($fournisseur->getSpecificiteCommission())
            ->setRetrocommissionMFFinSaison($fournisseur->getRetrocommissionMFFinSaison())
            ->setConditionAnnulation($fournisseur->getConditionAnnulation())
            ->setRelocationAnnulation($fournisseur->getRelocationAnnulation())
            ->setDelaiPaiementFacture($fournisseur->getDelaiPaiementFacture());
    }

    /**
     * @param Fournisseur $fournisseur
     * @param Fournisseur $fournisseurSite
     */
    private function gestionInformationRMSite($fournisseur, $fournisseurSite)
    {

        $fournisseurSite
            ->setLieuRetraitForfaitSki($fournisseur->getLieuRetraitForfaitSki())
            ->setCommissionForfaitFamille($fournisseur->getCommissionForfaitFamille())
            ->setCommissionForfaitPeriode($fournisseur->getCommissionForfaitPeriode())
            ->setCommissionSupportMainLibre($fournisseur->getCommissionSupportMainLibre());
    }

    private function gestionConditionAnnulationDescriptionSite(
        Fournisseur $fournisseur,
        Fournisseur $fournisseurSite,
        EntityManager $em
    )
    {
        switch ($fournisseurSite->getConditionAnnulation()) {
            case ConditionAnnulation::standard:
                $standard = $em->find(ConditionAnnulationDescription::class, 1);
                $em->refresh($standard);
                if (!empty($fournisseurSite->getConditionAnnulationDescription()) && $fournisseurSite->getConditionAnnulationDescription()->getId() != 1) {
                    $em->remove($fournisseurSite->getConditionAnnulationDescription());
                }
                $fournisseurSite->setConditionAnnulationDescription($standard);
                break;
            case ConditionAnnulation::personnalisee:
                if (empty($fournisseurSite->getConditionAnnulationDescription()->getId()) or $fournisseurSite->getConditionAnnulationDescription()->getId() == 1) {
                    $perso = new ConditionAnnulationDescription();
                    $fournisseurSite->setConditionAnnulationDescription($perso);
                }
                $fournisseurSite->getConditionAnnulationDescription()->setDescription($fournisseur->getConditionAnnulationDescription()->getDescription());
                $standard = $em->find(ConditionAnnulationDescription::class, 1);
                $em->refresh($standard);
                break;
            default:
                if (!empty($fournisseurSite->getConditionAnnulationDescription()) && $fournisseurSite->getConditionAnnulationDescription()->getId() != 1) {
                    $em->remove($fournisseurSite->getConditionAnnulationDescription());
                }
                $fournisseurSite->setConditionAnnulationDescription(null);
                break;
        }
    }

    /**
     * @param Fournisseur $fournisseur
     * @param Fournisseur $fournisseurSite
     * @param EntityManager $emSite
     */
    private function gestionSaisonsSite($fournisseur, $fournisseurSite, $emSite)
    {
        /** @var SaisonFournisseur $saisonFournisseur */
        foreach ($fournisseur->getSaisonFournisseurs() as $saisonFournisseur) {
            $saisonFournisseurSite = $fournisseurSite->getSaisonFournisseurs()->filter(function (SaisonFournisseur $element) use ($saisonFournisseur) {
                return $element->getId() == $saisonFournisseur->getId();
            })->first();
            if (false === $saisonFournisseurSite) {
                $saisonFournisseurSite = new SaisonFournisseur();
                $fournisseurSite->addSaisonFournisseur($saisonFournisseurSite);
                $saisonFournisseurSite->setId($saisonFournisseur->getId());
                $metadata = $emSite->getClassMetadata(get_class($saisonFournisseurSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $saisonFournisseurSite->setSaison($emSite->find(Saison::class, $saisonFournisseur->getSaison()));
            }
            $saisonFournisseurSite
                ->setContrat($saisonFournisseur->getContrat())
                ->setStock($saisonFournisseur->getStock())
                ->setFlux($saisonFournisseur->getFlux())
                ->setValideOptions($saisonFournisseur->getValideOptions())
                ->setEarlybooking($saisonFournisseur->getEarlybooking())
                ->setConditionEarlybooking($saisonFournisseur->getConditionEarlybooking())
                ->setFicheTechniques($saisonFournisseur->getFicheTechniques())
                ->setTarifTechniques($saisonFournisseur->getTarifTechniques())
                ->setPhotosTechniques($saisonFournisseur->getPhotosTechniques());

            if (!empty($saisonFournisseur->getAgentMajSaisie())) {
                $saisonFournisseurSite->setAgentMaJSaisie($emSite->find(Utilisateur::class, $saisonFournisseur->getAgentMajSaisie()));
            }
            if (!empty($saisonFournisseur->getAgentMaJProd())) {
                $saisonFournisseurSite->setAgentMaJProd($emSite->find(Utilisateur::class, $saisonFournisseur->getAgentMaJProd()));
            }

        }
    }

    private function gestionPromotionFournisseur(Fournisseur $fournisseur)
    {
        $kernel = $this->get('kernel');

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_promotion:promotion_fournisseur_command',
            'fournisseurId' => $fournisseur->getId(),
        ));
        $output = new NullOutput();
        $application->run($input, $output);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function newCommentaireAction(Request $request)
    {
        $reponse = new \stdClass();
        $reponse->valid = false;
        if ($request->isXmlHttpRequest()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $sites = $em->getRepository(Site::class)->findAll();
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                if (is_null($auteur = $this->get('security.token_storage')->getToken()->getUser()->getUtilisateur()->getAuteur())) {
                    $utilisateur = $emSite->getRepository(Utilisateur::class)->find($this->get('security.token_storage')->getToken()->getUser()->getUtilisateur()->getId());
                    $auteur = new UtilisateurAuteur();
                    $auteur->setUtilisateur($utilisateur);
                    $emSite->persist($auteur);
                    $emSite->flush();
                } else {
                    $auteur = $emSite->getRepository(UtilisateurAuteur::class)->find($this->get('security.token_storage')->getToken()->getUser()->getUtilisateur()->getAuteur()->getId());
                }
                if (is_array($request->request->get('fournisseur'))) {
                    foreach ($request->request->get('fournisseur')['commentaires'] as $commentairePost) {
                        $commentaire = new FournisseurCommentaire();
                        if (isset($commentairePost['commentaireParent'])) {
                            $commentaire->setCommentaireParent($emSite->getRepository(FournisseurCommentaire::class)->find($commentairePost['commentaireParent']));
                        } else {
                            $commentaire->setFournisseur($emSite->getRepository(Fournisseur::class)->find($commentairePost['fournisseur']));
                        }
                        $commentaire->setAuteur($auteur);
                        $commentaire->setContenu($commentairePost['contenu']);
                        $emSite->persist($commentaire);
                    }
                } else {
                    $commentaire = new FournisseurCommentaire();
                    if (!empty($request->request->get('commentaireParent'))) {
                        $commentaire->setCommentaireParent($emSite->getRepository(FournisseurCommentaire::class)->find($request->request->get('commentaireParent')));
                    } else {
                        $commentaire->setFournisseur($emSite->getRepository(Fournisseur::class)->find($request->request->get('reponse_fournisseur')));
                    }
                    $commentaire->setAuteur($auteur);
                    $commentaire->setContenu($request->request->get('contenu'));
                    $emSite->persist($commentaire);
                }
                $emSite->flush();
            }
            $reponse->valid = true;
            $reponse->contenu = $commentaire->getContenu();
            $reponse->dateHeureCreation = $commentaire->getDateHeureCreation()->format('d/m/Y H:i');
            $reponse->dateHeureModification = $commentaire->getDateHeureModification()->format('d/m/Y H:i');
            $reponse->id = $commentaire->getId();
            if (!empty($commentaire->getCommentaireParent())) {
                $reponse->commentaireParent = new \stdClass();
                $reponse->commentaireParent->id = $commentaire->getCommentaireParent()->getId();
            }
        }
        return new JsonResponse($reponse);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editCommentaireAction(Request $request, FournisseurCommentaire $fournisseurCommentaire)
    {
        $reponse = new \stdClass();
        $reponse->valid = false;
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $sites = $em->getRepository(Site::class)->findAll();
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                /** @var FournisseurCommentaire $fournisseurCommentaireSite */
//                echo $request->request->get('fournisseur');
//                die;
                $fournisseurCommentaireSite = $emSite->getRepository(FournisseurCommentaire::class)->find($fournisseurCommentaire->getId());
                if (is_array($request->request->get('fournisseur'))) {
                    $reponse->commentaire = true;
                    foreach ($request->request->get('fournisseur')['commentaires'] as $commentairePost) {
                        $fournisseurCommentaireSite->setContenu($commentairePost['contenu']);
                    }
                } else {
                    $reponse->commentaire = false;
                    $fournisseurCommentaireSite->setContenu($request->request->get('contenu'));
                }
                $emSite->persist($fournisseurCommentaireSite);
                $emSite->flush();
                $reponse->valid = true;
            }
        }
        return new JsonResponse($reponse);
    }

    /**
     * Finds and displays a Fournisseur entity.
     *
     */
    public function showAction(Fournisseur $fournisseur)
    {
        $deleteForm = $this->createDeleteForm($fournisseur);

        return $this->render('@MondofuteFournisseur/fournisseur/show.html.twig', array(
            'fournisseur' => $fournisseur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Fournisseur entity.
     *
     * @param Fournisseur $fournisseur The Fournisseur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Fournisseur $fournisseur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fournisseur_delete', array('id' => $fournisseur->getId())))
            ->add('Supprimer', SubmitType::class, array('label' => 'Supprimer', 'translation_domain' => 'messages'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Fournisseur entity.
     *
     */
    public function editAction(Request $request, Fournisseur $fournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $sites = $em->getRepository(Site::class)->findBy(array(), array('id' => 'ASC'));

        $fournisseurId = null;
        /** @var FamillePrestationAnnexe $type */
        foreach ($fournisseur->getTypes() as $type) {
            if ($type->getId() == 9) {
                $fournisseurId = $fournisseur->getId();
            }
        }

        $fournisseurProduits = $em->getRepository(Fournisseur::class)->findFournisseurByContient(FournisseurContient::PRODUIT,
            $fournisseurId);

        $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'),
            null, null, $fournisseurId);

        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        $hebergements = new ArrayCollection();
        foreach ($fournisseur->getPrestationAnnexes() as $fournisseurPrestationAnnexe) {
            foreach ($fournisseurPrestationAnnexe->getParams() as $keyParam => $param) {
                $prestationAnnexeHebergements = $param->getPrestationAnnexeHebergements();
                /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
                foreach ($prestationAnnexeHebergements as $prestationAnnexeHebergement) {
                    if ($prestationAnnexeHebergement->getActif()) {
                        $a = new ArrayCollection($em->getRepository(HebergementUnifie::class)->findByFournisseur($prestationAnnexeHebergement->getFournisseur()->getId(),
                            $this->container->getParameter('locale'),
                            $prestationAnnexeHebergement->getSite()->getId()));
                        $hebergements->set($fournisseurPrestationAnnexe->getPrestationAnnexe()->getId() . '_' . $keyParam . '_' . $prestationAnnexeHebergement->getSite()->getId() . '_' . $prestationAnnexeHebergement->getFournisseur()->getId(),
                            $a);
                    }
                }
            }
        }

        // *** gestion prestation annexe ***
        $originalPrestationAnnexes = new ArrayCollection();
        $originalTarifs = new ArrayCollection();
        $originalPeriodeValidites = new ArrayCollection();
        $originalPrestationsAnnexeFournisseurs = new ArrayCollection();
        $originalPrestationsAnnexeHebergements = new ArrayCollection();
        $originalPrestationsAnnexeStations = new ArrayCollection();
        $originalPrestationAnnexeParams = new ArrayCollection();

        /** @var FournisseurPrestationAnnexe $prestationAnnex */
        foreach ($fournisseur->getPrestationAnnexes() as $prestationAnnex) {
            $originalPrestationAnnexes->add($prestationAnnex);
            foreach ($langues as $langue) {
                $traduction = $prestationAnnex->getTraductions()->filter(function (
                    FournisseurPrestationAnnexeTraduction $element
                ) use ($langue) {
                    return $element->getLangue() === $langue;
                })->first();
                if (false === $traduction) {
                    $traduction = new FournisseurPrestationAnnexeTraduction();
                    $prestationAnnex->addTraduction($traduction);
                    $traduction->setLangue($langue);
                }
            }
            // on stocke les prestationannexeparams originals
            $originalPrestationAnnexeParams->set($prestationAnnex->getId(), new ArrayCollection());
            foreach ($prestationAnnex->getParams() as $param) {
                $originalPrestationAnnexeParams->get($prestationAnnex->getId())->add($param);
                /** @var PrestationAnnexeTarif $tarif */
                foreach ($param->getTarifs() as $tarif) {
                    $originalTarifs->add($tarif);
                    foreach ($tarif->getPeriodeValidites() as $periodeValidite) {
                        $originalPeriodeValidites->add($periodeValidite);
                    }
                }
                /** @var PrestationAnnexeFournisseur $entity */
                foreach ($param->getPrestationAnnexeFournisseurs() as $entity) {
                    $originalPrestationsAnnexeFournisseurs->add($entity);
                }
                /** @var PrestationAnnexeHebergement $entity */
                foreach ($param->getPrestationAnnexeHebergements() as $entity) {
                    $originalPrestationsAnnexeHebergements->add($entity);
                }
                /** @var PrestationAnnexeStation $entity */
                foreach ($param->getPrestationAnnexeStations() as $entity) {
                    $originalPrestationsAnnexeStations->add($entity);
                }
            }
        }

        $newPrestationAnnexes = new ArrayCollection();
        /** @var FournisseurPrestationAnnexe $prestationAnnex */
        foreach ($fournisseur->getPrestationAnnexes() as $prestationAnnex) {
            $newPrestationAnnexes->set($prestationAnnex->getPrestationAnnexe()->getId(), $prestationAnnex);
        }

        $fournisseur->getPrestationAnnexes()->clear();

        foreach ($newPrestationAnnexes as $key => $prestationAnnex) {
            $fournisseur->getPrestationAnnexes()->set($key, $prestationAnnex);
        }
        // *** fin gestion prestation annexe ***

        /** @var FournisseurInterlocuteur $interlocuteur */
        $originalInterlocuteurs = new ArrayCollection();
        $originalRemiseClefs = new ArrayCollection();
        $originalReceptions = new ArrayCollection();
        $originalTypeFournisseurs = new ArrayCollection();
        $originalListeServices = new ArrayCollection();
        $originalServices = new ArrayCollection();
        $originalTarifsService = new ArrayCollection();
        $originalFamillePrestationAnnexes = new ArrayCollection();

        foreach ($fournisseur->getTypes() as $type) {
            $originalFamillePrestationAnnexes->add($type);
        }

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
            $originalInterlocuteurs->add($interlocuteur);
        }

        foreach ($fournisseur->getRemiseClefs() as $remiseClef) {
            $originalRemiseClefs->add($remiseClef);
        }
        foreach ($fournisseur->getReceptions() as $reception) {
            $originalReceptions->add($reception);
        }
        foreach ($fournisseur->getTypes() as $typeFournisseur) {
            $originalTypeFournisseurs->add($typeFournisseur);
        }

        $originalLogo = $fournisseur->getLogo();

        foreach ($fournisseur->getListeServices() as $listeService) {
            /** @var Service $service */
            foreach ($listeService->getServices() as $service) {
                $originalServices->add($service);
                foreach ($service->getTarifs() as $tarif) {
                    $originalTarifsService->add($tarif);
                }
            }
            $originalListeServices->add($listeService);
        }

        $locale = $request->getLocale();
        $famillePrestationAnnexes = $em
            ->getRepository('MondofutePrestationAnnexeBundle:FamillePrestationAnnexe')->getTraductionsByLocale($locale)
            ->getQuery()
            ->getResult();

        // *** gestion saisons ***
        $this->gestionSaisons($fournisseur);
        // *** fin gestion saisons ***

        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $deleteForm = $this->createDeleteForm($fournisseur);
        $fournisseur->triReceptions();
        $fournisseur->triRemiseClefs();

        $saisons = $em->getRepository(Saison::class)->findAll();
        $this->addSaisonCodePasserelle($fournisseur, $saisons);

        $editForm = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array('locale' => $locale))
            ->add('submit', SubmitType::class, array('label' => 'mettre.a.jour'));
        $editForm->handleRequest($request);

        $disabledOptionHebergement = false;
        if (!$fournisseur->getHebergements()->isEmpty()) {
            $disabledOptionHebergement = true;
            $fournisseurTypesHebergement = $em->find(FamillePrestationAnnexe::class, 9);
            if (!$fournisseur->getTypes()->contains($fournisseurTypesHebergement)) {
                $fournisseur->addType($fournisseurTypesHebergement);
            }
        }

        $errorType = false;

        $errorInterlocuteur = false;
        $interlocuteurController = new InterlocuteurController();
        $interlocuteurController->setContainer($this->container);
//        if (!empty($fournisseur->getInterlocuteurs()) && empty($fournisseur->getFournisseurParent())) {
        if (!empty($fournisseur->getInterlocuteurs())) {
            $interlocuteurController->newInterlocuteurUsers($fournisseur->getInterlocuteurs());
            if ($interlocuteurController->testInterlocuteursLoginExist($fournisseur->getInterlocuteurs())) {
                $errorInterlocuteur = true;
            }
        }

        $errorRemiseClef = false;
        /** @var RemiseClef $originalRemiseClef */
        foreach ($originalRemiseClefs as $originalRemiseClef) {
            if (false === $fournisseur->getRemiseClefs()->contains($originalRemiseClef) && !$errorRemiseClef) {
                if (!empty($originalRemiseClef->getFournisseurHebergements()) and !$originalRemiseClef->getFournisseurHebergements()->isEmpty()) {
                    $this->addFlash('error',
                        'Une remise clef ne peut pas être supprimé car elle est lié à un hébergement.');
                    $errorRemiseClef = true;
                }
            }
        }

        // **********************************************
        // ********** VALIDATION DU FORMULAIRE **********
        // **********************************************
        if ($editForm->isSubmitted() && $editForm->isValid() && !$errorType && !$errorInterlocuteur && !$errorRemiseClef) {
            // *** gestion suppression prestations annexe et ses collections ***
            /** @var FournisseurPrestationAnnexe $originalPrestationAnnex */
            foreach ($fournisseur->getPrestationAnnexes() as $fournisseurPrestationAnnexe) {
                if (false === $fournisseur->getTypes()->contains($fournisseurPrestationAnnexe->getPrestationAnnexe()->getFamillePrestationAnnexe())) {
                    $fournisseur->removePrestationAnnex($fournisseurPrestationAnnexe);
                    $em->remove($fournisseurPrestationAnnexe);
                    $this->deletePrestationsAnnexeUnifies($fournisseurPrestationAnnexe, $em);
                }
            }

            foreach ($originalPrestationAnnexes as $originalPrestationAnnex) {
                if (false === $fournisseur->getPrestationAnnexes()->contains($originalPrestationAnnex)) {
                    $codePromoPrestationAnnexes = $em->getRepository(CodePromoFournisseurPrestationAnnexe::class)->findBy(array('fournisseurPrestationAnnexe' => $originalPrestationAnnex->getId()));
                    foreach ($codePromoPrestationAnnexes as $codePromoPrestationAnnexe) {
                        $em->remove($codePromoPrestationAnnexe);
                    }
                    $em->remove($originalPrestationAnnex);
                    $this->deletePrestationsAnnexeUnifies($originalPrestationAnnex, $em);
                }
            }
            // *** gestion suppression prestations annexe et ses collections ***

            foreach ($fournisseur->getListeServices() as $listeService) {
                $listeService->setFournisseur($fournisseur);
                foreach ($listeService->getServices() as $service) {
                    $service->setListeService($listeService);
                    /** @var TarifService $tarifService */
                    foreach ($service->getTarifs() as $tarifService) {
                        $tarifService->setService($service);
                    }
                }
            }

            // ***** GESTION SUPPRESSION DES INTERLOCUTEURS *****
            $interlocuteurController = new InterlocuteurController();
            $interlocuteurController->setContainer($this->container);

            // Si le fournisseur a un parent, on efface la liste des interlocuteurs
            foreach ($originalInterlocuteurs as $interlocuteur) {
                if (false === $fournisseur->getInterlocuteurs()->contains($interlocuteur)) {

                    // if it was a many-to-one relationship, remove the relationship like this
                    $this->deleteInterlocuteurSites($interlocuteur);
                    $this->deleteMoyenComs($interlocuteur->getInterlocuteur(), $em);

                    $em->flush();
                    $interlocuteur->setFournisseur(null);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($interlocuteur);
                }
            }

            $interlocuteurController->newInterlocuteurUsers($fournisseur->getInterlocuteurs());
            // ***** FIN SUPPRESSION DES INTERLOCUTEURS *****

            /** @var ListeService $listeService */
            foreach ($originalListeServices as $listeService) {
                if (false === $fournisseur->getListeServices()->contains($listeService)) {
                    foreach ($listeService->getHebergements() as $hebergementUnifie) {
                        $listeService->removeHebergement($hebergementUnifie);
                        $em->persist($listeService);
                    }
                    $this->deleteListeServiceSites($listeService);
                    $em->remove($listeService);
                }
            }
            foreach ($originalServices as $service) {
                $trouve = false;
                foreach ($fournisseur->getListeServices() as $listeService) {
                    if ($listeService->getServices()->contains($service) === true) {
                        $trouve = true;
                    }
                }
                if ($trouve === false) {
                    $this->deleteServiceSites($service);
                    $em->remove($service);
                }
            }
            foreach ($originalTarifsService as $tarifService) {
                $trouve = false;
                foreach ($fournisseur->getListeServices() as $listeService) {
                    foreach ($listeService->getServices() as $service) {

                        /** @var TarifService $tarifService */
                        if ($service->getTarifs()->contains($tarifService) === true) {
                            $trouve = true;
                        }
                    }
                }
                if ($trouve === false) {
                    $this->deleteTarifServiceSites($tarifService);
                    $em->remove($tarifService);
                }
            }
            foreach ($originalRemiseClefs as $remiseClef) {
                if (false === $fournisseur->getRemiseClefs()->contains($remiseClef)) {
                    $fournisseur->getRemiseClefs()->removeElement($remiseClef);
                    $this->deleteRemiseClefSites($remiseClef);
                    $em->remove($remiseClef);
                }
            }
            foreach ($originalReceptions as $reception) {
                if (false === $fournisseur->getReceptions()->contains($reception)) {
                    $fournisseur->getReceptions()->removeElement($reception);
                    $this->deleteReceptionSites($reception);
                    $em->remove($reception);
                }
            }


            // ***** MISE A JOURS DES INTERLOCUTEURS *****
            /** @var FournisseurInterlocuteur $fournisseurInterlocuteur */
            /** @var Interlocuteur $interlocuteur */
            foreach ($fournisseur->getInterlocuteurs() as $fournisseurInterlocuteur) {
                $interlocuteur = $fournisseurInterlocuteur->getInterlocuteur();
                $interlocuteurUser = $interlocuteur->getUser();
                $interlocuteurUser->setEnabled(true);

                $userManager = $this->get('fos_user.user_manager');
                $userManager->updatePassword($interlocuteurUser);

                foreach ($interlocuteur->getMoyenComs() as $moyenCom) {
                    $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                    if ($typeComm == 'Email' && empty($login)) {
                        /** @var Email $moyenCom */
                        $login = $moyenCom->getAdresse();
                        $interlocuteurUser
                            ->setUsername($login)
                            ->setEmail($login);
                        unset($login);
                    }
                }

                // Mis à jours du mot de passe du user
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updatePassword($interlocuteurUser);
                $fournisseurInterlocuteur->setFournisseur($fournisseur);
            }
            // ***** FIN MISE A JOURS DES INTERLOCUTEURS *****

            /** @var ListeService $listeService */
            foreach ($fournisseur->getListeServices() as $listeService) {
                $listeService->setFournisseur($fournisseur);
            }

            // ***** GESTION CODE PROMO *****
            $this->gestionCodePromoFournisseurPrestationAnnexe($fournisseur);
            // ***** FIN GESTION CODE PROMO *****


            if (!empty($fournisseur->getLogo()) && empty($request->request->get('fournisseur')['logo'])) {
                $em->remove($fournisseur->getLogo());
                $fournisseur->setLogo(null);
            }

            $this->gestionInformationRM($fournisseur);

            $this->gestionConditionAnnulationDescription($fournisseur);

            $em->persist($fournisseur);
            $em->flush();

            $this->mAJSites($fournisseur);

            // *** GESTION PROMOTIONS ***
            $this->gestionPromotionFournisseur($fournisseur);
            $this->gestionPromotionFamillePrestationAnnexe($fournisseur, $originalFamillePrestationAnnexes);
            // *** FIN GESTION PROMOTIONS ***

            if (empty($fournisseur->getLogo()) && !empty($originalLogo) || !empty($originalLogo) && $originalLogo != $fournisseur->getLogo()) {
                $em->remove($originalLogo);
                $em->flush();
            }

            // add flash messages
            $this->addFlash(
                'success',
                'Le fournisseur a bien été modifié.'
            );

            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
        }

        return $this->render('@MondofuteFournisseur/fournisseur/edit.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $editForm->createView(),
            'langues' => $langues,
            'delete_form' => $deleteForm->createView(),
            'famillePrestationAnnexes' => $famillePrestationAnnexes,
            'edit' => true,
            'fournisseurProduits' => $fournisseurProduits,
            'sites' => $sites,
            'hebergements' => $hebergements,
            'stationsWithHebergement' => $stationsWithHebergement,
            'disabledOptionHebergement' => $disabledOptionHebergement
        ));
    }

    /**
     * @param Fournisseur $fournisseur
     * @param ArrayCollection $saisons
     */
    private function addSaisonCodePasserelle($fournisseur, $saisons)
    {
        /** @var FournisseurPrestationAnnexe $prestationAnnex */
        foreach ($fournisseur->getPrestationAnnexes() as $prestationAnnex) {
            foreach ($saisons as $saison) {
                $saisonCodePasserelle = $prestationAnnex->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saison) {
                    return $element->getSaison() == $saison;
                })->first();
                if (false === $saisonCodePasserelle) {
                    $saisonCodePasserelle = new SaisonCodePasserelle();
                    $saisonCodePasserelle->setSaison($saison);
                    $prestationAnnex->addSaisonCodePasserelle($saisonCodePasserelle);
                }
            }
        }
    }

    private function deletePrestationsAnnexeUnifies(
        FournisseurPrestationAnnexe $originalPrestationAnnex,
        EntityManager $em
    )
    {
        $prestationAnnexeUnifies = new ArrayCollection();
        foreach ($originalPrestationAnnex->getParams() as $param) {
            /** @var PrestationAnnexeStation $item */
            foreach ($param->getPrestationAnnexeStations() as $item) {
                if (!$prestationAnnexeUnifies->contains($item->getPrestationAnnexeStationUnifie())) {
                    $prestationAnnexeUnifies->add($item->getPrestationAnnexeStationUnifie());
                }
            }
            /** @var PrestationAnnexeFournisseur $item */
            foreach ($param->getPrestationAnnexeFournisseurs() as $item) {
                if (!$prestationAnnexeUnifies->contains($item->getPrestationAnnexeFournisseurUnifie())) {
                    $prestationAnnexeUnifies->add($item->getPrestationAnnexeFournisseurUnifie());
                }
            }
            /** @var PrestationAnnexeHebergement $item */
            foreach ($param->getPrestationAnnexeHebergements() as $item) {
                if (!$prestationAnnexeUnifies->contains($item->getPrestationAnnexeHebergementUnifie())) {
                    $prestationAnnexeUnifies->add($item->getPrestationAnnexeHebergementUnifie());
                }
            }
            /** @var PrestationAnnexeLogement $item */
            foreach ($param->getPrestationAnnexeLogements() as $item) {
                if (!$prestationAnnexeUnifies->contains($item->getPrestationAnnexeLogementUnifie())) {
                    $prestationAnnexeUnifies->add($item->getPrestationAnnexeLogementUnifie());
                }
            }
        }
        foreach ($prestationAnnexeUnifies as $annexeUnifie) {
            $em->remove($annexeUnifie);
        }
    }

    private function deleteInterlocuteurSites(FournisseurInterlocuteur $interlocuteur)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            $interlocuteurSite = $emSite->find('MondofuteFournisseurBundle:FournisseurInterlocuteur',
                $interlocuteur->getId());

            if (!empty($interlocuteurSite)) {
                $this->deleteMoyenComs($interlocuteurSite->getInterlocuteur(), $emSite);

                $interlocuteurSite->setFournisseur(null);

                $emSite->remove($interlocuteurSite);

                $emSite->flush();

            }
        }
    }

    /**
     * @param $entity
     * @param EntityManager $em
     */
    private function deleteMoyenComs($entity, EntityManager $em)
    {
        $moyenComs = $entity->getMoyenComs();
        if (!empty($moyenComs)) {
            foreach ($moyenComs as $moyenCom) {
                $entity->removeMoyenCom($moyenCom);
                $em->remove($moyenCom);
            }
        }
    }

    /**
     * @param ListeService $listeService
     */
    private function deleteListeServiceSites(ListeService $listeService)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $listeServiceSite = $emSite->find(ListeService::class,
                $listeService->getId());
            if (!empty($listeServiceSite)) {
                /** @var Service $serviceSite */
                foreach ($listeServiceSite->getServices() as $serviceSite) {
                    $serviceSite->setListeService(null);
                    $emSite->remove($serviceSite);
                }
                foreach ($listeServiceSite->getHebergements() as $hebergementUnifieSite) {
                    $listeServiceSite->removeHebergement($hebergementUnifieSite);
                    $emSite->persist($listeServiceSite);
                }
                $listeServiceSite->setFournisseur(null);
                $emSite->remove($listeServiceSite);
            }

        }
    }

    /**
     * @param Service $service
     */
    private function deleteServiceSites(Service $service)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $serviceSite = $emSite->find(Service::class,
                $service->getId());
            if (!empty($serviceSite)) {
                $serviceSite->setListeService(null);
                $emSite->remove($serviceSite);
            }

        }
    }

    /**
     * @param TarifService $tarifService
     */
    private function deleteTarifServiceSites(TarifService $tarifService)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $tarifServiceSite = $emSite->find(TarifService::class,
                $tarifService->getId());
            if (!empty($tarifServiceSite)) {
                $tarifServiceSite->setService(null);
                $emSite->remove($tarifServiceSite);
            }

        }
    }

    private function deleteRemiseClefSites(RemiseClef $remiseClef)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            $remiseClefSite = $emSite->find('MondofuteRemiseClefBundle:RemiseClef', $remiseClef->getId());
            if (!empty($remiseClefSite)) {
                $remiseClefSite->setFournisseur(null);

                $emSite->remove($remiseClefSite);
            }
        }
    }

    private function deleteReceptionSites(Reception $reception)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            $receptionSite = $emSite->find(Reception::class, $reception->getId());
            if (!empty($receptionSite)) {
                $receptionSite->setFournisseur(null);
                if ($receptionSite->getTranche1() !== null) {
                    $emSite->remove($receptionSite->getTranche1());
                }
                if ($receptionSite->getTranche2() !== null) {
                    $emSite->remove($receptionSite->getTranche2());
                }
                $receptionSite->setTranche1(null);
                $receptionSite->setTranche2(null);
                $emSite->remove($receptionSite);
            }
        }
    }

    /**
     * @param Fournisseur $fournisseur
     * attribution des codes promo fournisseur
     */
    private function gestionCodePromoFournisseurPrestationAnnexe($fournisseur)
    {
        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        $em = $this->getDoctrine()->getManager();
        $codePromoFournisseurs = new ArrayCollection($em->getRepository(CodePromoFournisseur::class)->findBy(array(
            'fournisseur' => $fournisseur->getId(),
            'type' => 2
        )));
        foreach ($fournisseur->getPrestationAnnexes() as $fournisseurPrestationAnnexe) {
            if (empty($fournisseurPrestationAnnexe->getId())) {
                foreach ($codePromoFournisseurs as $codePromoFournisseur) {
                    $codePromoFournisseurPrestationAnnexe = new CodePromoFournisseurPrestationAnnexe();
                    $em->persist($codePromoFournisseurPrestationAnnexe);
                    $codePromoFournisseurPrestationAnnexe
                        ->setCodePromo($codePromoFournisseur->getCodePromo())
                        ->setFournisseur($codePromoFournisseur->getFournisseur())
                        ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe);
                }
            }
        }


        $codePromoFamillePrestationAnnexes = new ArrayCollection($em->getRepository(CodePromoFamillePrestationAnnexe::class)->findBy(array('fournisseur' => $fournisseur->getId())));

        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        /** @var CodePromoFamillePrestationAnnexe $codePromoFamillePrestationAnnexe */
        foreach ($fournisseur->getPrestationAnnexes() as $fournisseurPrestationAnnexe) {
            if (empty($fournisseurPrestationAnnexe->getId())) {
                foreach ($codePromoFamillePrestationAnnexes as $codePromoFamillePrestationAnnexe) {
                    $codePromoFournisseur = $codePromoFournisseurs->filter(function (CodePromoFournisseur $element) use (
                        $codePromoFamillePrestationAnnexe
                    ) {
                        return $element->getCodePromo() === $codePromoFamillePrestationAnnexe->getCodePromo();
                    })->first();
                    if (false === $codePromoFournisseur) {
                        $codePromoFournisseurPrestationAnnexe = new CodePromoFournisseurPrestationAnnexe();
                        $em->persist($codePromoFournisseurPrestationAnnexe);
                        $codePromoFournisseurPrestationAnnexe
                            ->setCodePromo($codePromoFamillePrestationAnnexe->getCodePromo())
                            ->setFournisseur($codePromoFamillePrestationAnnexe->getFournisseur())
                            ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe);
                    }
                }
            }
        }
    }

    private function mAJSites(Fournisseur $fournisseur)
    {
        /** @var EntityManager $emSite */
        /** @var FournisseurInterlocuteur $interlocuteurSite */
        /** @var Site $site */
        /** @var FournisseurInterlocuteur $interlocuteur */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            $fournisseurSite = $emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur);
            if (!empty($fournisseurSite)) {
                $this->dupliquerListeServicesSite($fournisseurSite, $fournisseur->getListeServices(), $emSite);
                $fournisseurSite
                    ->setContient($fournisseur->getContient())
                    ->setPhototheque($fournisseur->getPhototheque())
                    ->setBlocageVente($fournisseur->getBlocageVente())
                    ->setPriorite($fournisseur->getPriorite())
                    ->setEnseigne($fournisseur->getEnseigne())
                    ->setRaisonSociale($fournisseur->getRaisonSociale());

                // remove the relationship between the sousFamillePrestationAnnexeSite and the famillePrestationAnnexeSite
                foreach ($fournisseurSite->getTypes() as $typeSite) {
                    $type = $fournisseur->getTypes()->filter(function (FamillePrestationAnnexe $element) use ($typeSite
                    ) {
                        return $element->getId() == $typeSite->getId();
                    })->first();
                    if (false === $type) {
                        // On doit le supprimer de l'entité parent
                        $fournisseurSite->removeType($typeSite);
                    }
                }

                foreach ($fournisseur->getTypes() as $type) {
                    $typeSite = $fournisseurSite->getTypes()->filter(function (FamillePrestationAnnexe $element) use (
                        $type
                    ) {
                        return $element->getId() == $type->getId();
                    })->first();
                    if (false === $typeSite) {
                        $typeSite = $emSite->find(FamillePrestationAnnexe::class, $type);
                        $fournisseurSite->addType($typeSite);
                    }
                }


                // *** GESTION PRESTATION ANNEXE ***
                /** @var FournisseurPrestationAnnexe $prestationAnnexeSite */
                /** @var PeriodeValidite $periodeValiditeSite */
                /** @var FournisseurPrestationAnnexe $prestationAnnexe */
                foreach ($fournisseurSite->getPrestationAnnexes() as $prestationAnnexeSite) {
                    $prestationAnnexe = $fournisseur->getPrestationAnnexes()->filter(function (
                        FournisseurPrestationAnnexe $element
                    ) use ($prestationAnnexeSite) {
                        return $element->getId() == $prestationAnnexeSite->getId();
                    })->first();
                    if (false === $prestationAnnexe) {
                        // On doit le supprimer de l'entité parent
                        $fournisseurSite->removePrestationAnnex($prestationAnnexeSite);
                        $emSite->remove($prestationAnnexeSite);
                        $this->deletePrestationsAnnexeUnifies($prestationAnnexeSite, $emSite);
                    } else {
                        foreach ($prestationAnnexeSite->getParams() as $paramSite) {
                            $param = $prestationAnnexe->getParams()->filter(function (
                                FournisseurPrestationAnnexeParam $element
                            ) use ($paramSite) {
                                return $element->getId() == $paramSite->getId();
                            })->first();
                            if (false === $param) {
                                $prestationAnnexeSite->removeParam($paramSite);
                                $emSite->remove($paramSite);
                            } else {
                                // *** suprression des tarif
                                /** @var PrestationAnnexeTarif $tarifSite */
                                /** @var PrestationAnnexeTarif $tarif */
                                foreach ($paramSite->getTarifs() as $tarifSite) {
                                    $tarif = $param->getTarifs()->filter(function (PrestationAnnexeTarif $element) use (
                                        $tarifSite
                                    ) {
                                        return $element->getId() == $tarifSite->getId();
                                    })->first();
                                    if (false === $tarif) {
                                        // On doit le supprimer de l'entité parent
                                        $paramSite->removeTarif($tarifSite);
                                        $emSite->remove($tarifSite);
                                    } else {
                                        foreach ($tarifSite->getPeriodeValidites() as $periodeValiditeSite) {
                                            $periodeValidite = $tarif->getPeriodeValidites()->filter(function (
                                                PeriodeValidite $element
                                            ) use ($periodeValiditeSite) {
                                                return $element->getId() == $periodeValiditeSite->getId();
                                            })->first();
                                            if (false === $periodeValidite) {
                                                // On doit le supprimer de l'entité parent
                                                $tarifSite->removePeriodeValidite($periodeValiditeSite);
                                                $emSite->remove($periodeValiditeSite);
                                            }
                                        }
                                    }
                                }

                                // *** suppression prestation annexe station ***
                                /** @var PrestationAnnexeStation $prestationAnnexeStation */
                                $prestationAnnexeStationUnifies = new ArrayCollection();
                                $prestationAnnexeStationUnifiesSite = new ArrayCollection();
                                foreach ($param->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                                    if (false === $prestationAnnexeStationUnifies->contains($prestationAnnexeStation->getPrestationAnnexeStationUnifie())) {
                                        $prestationAnnexeStationUnifies->add($prestationAnnexeStation->getPrestationAnnexeStationUnifie());
                                    }
                                }
                                foreach ($paramSite->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                                    if (false === $prestationAnnexeStationUnifiesSite->contains($prestationAnnexeStation->getPrestationAnnexeStationUnifie())) {
                                        $prestationAnnexeStationUnifiesSite->add($prestationAnnexeStation->getPrestationAnnexeStationUnifie());
                                    }
                                }

                                /** @var PrestationAnnexeStationUnifie $prestationAnnexeAffectationUnifieSite */
                                foreach ($prestationAnnexeStationUnifiesSite as $prestationAnnexeAffectationUnifieSite) {
                                    $prestationAnnexeAffectationUnifie = $prestationAnnexeStationUnifies->filter(function (
                                        PrestationAnnexeStationUnifie $element
                                    ) use ($prestationAnnexeAffectationUnifieSite) {
                                        return $element->getId() == $prestationAnnexeAffectationUnifieSite->getId();
                                    })->first();
                                    if (false === $prestationAnnexeAffectationUnifie) {
                                        /** @var PrestationAnnexeStation $prestationAnnexeStation */
                                        foreach ($prestationAnnexeAffectationUnifieSite->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                                            $prestationAnnexeAffectationUnifieSite->getPrestationAnnexeStations()->removeElement($prestationAnnexeStation);
                                            $paramSite->getPrestationAnnexeStations()->removeElement($prestationAnnexeStation);
                                            $emSite->remove($prestationAnnexeStation);
                                        }
                                        $emSite->remove($prestationAnnexeAffectationUnifieSite);
                                    }
                                }
                                // *** fin suppression prestation annexe station ***


                                // *** suppression prestation annexe fournisseur ***
                                /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
                                $prestationAnnexeFournisseurUnifies = new ArrayCollection();
                                $prestationAnnexeFournisseurUnifiesSite = new ArrayCollection();
                                foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                                    if (false === $prestationAnnexeFournisseurUnifies->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                        $prestationAnnexeFournisseurUnifies->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                                    }
                                }
                                foreach ($paramSite->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                                    if (false === $prestationAnnexeFournisseurUnifiesSite->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                        $prestationAnnexeFournisseurUnifiesSite->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                                    }
                                }

                                /** @var PrestationAnnexeFournisseurUnifie $prestationAnnexeAffectationUnifieSite */
                                foreach ($prestationAnnexeFournisseurUnifiesSite as $prestationAnnexeAffectationUnifieSite) {
                                    $prestationAnnexeAffectationUnifie = $prestationAnnexeFournisseurUnifies->filter(function (
                                        PrestationAnnexeFournisseurUnifie $element
                                    ) use ($prestationAnnexeAffectationUnifieSite) {
                                        return $element->getId() == $prestationAnnexeAffectationUnifieSite->getId();
                                    })->first();
                                    if (false === $prestationAnnexeAffectationUnifie) {
                                        /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
                                        foreach ($prestationAnnexeAffectationUnifieSite->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                                            $prestationAnnexeAffectationUnifieSite->getPrestationAnnexeFournisseurs()->removeElement($prestationAnnexeFournisseur);
                                            $paramSite->getPrestationAnnexeFournisseurs()->removeElement($prestationAnnexeFournisseur);
                                            $emSite->remove($prestationAnnexeFournisseur);
                                        }
                                        $emSite->remove($prestationAnnexeAffectationUnifieSite);
                                    }
                                }
                                // *** fin suppression prestation annexe fournisseur ***


                                // *** suppression prestation annexe hebergement ***
                                /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
                                $prestationAnnexeHebergementUnifies = new ArrayCollection();
                                $prestationAnnexeHebergementUnifiesSite = new ArrayCollection();
                                foreach ($param->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                                    if (false === $prestationAnnexeHebergementUnifies->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                                        $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
                                    }
                                }
                                foreach ($paramSite->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                                    if (false === $prestationAnnexeHebergementUnifiesSite->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                                        $prestationAnnexeHebergementUnifiesSite->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
                                    }
                                }

                                /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeAffectationUnifieSite */
                                foreach ($prestationAnnexeHebergementUnifiesSite as $prestationAnnexeAffectationUnifieSite) {
                                    $prestationAnnexeAffectationUnifie = $prestationAnnexeHebergementUnifies->filter(function (
                                        PrestationAnnexeHebergementUnifie $element
                                    ) use ($prestationAnnexeAffectationUnifieSite) {
                                        return $element->getId() == $prestationAnnexeAffectationUnifieSite->getId();
                                    })->first();
                                    if (false === $prestationAnnexeAffectationUnifie) {
                                        foreach ($prestationAnnexeAffectationUnifieSite->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                                            $prestationAnnexeAffectationUnifieSite->getPrestationAnnexeHebergements()->removeElement($prestationAnnexeHebergement);
                                            $paramSite->getPrestationAnnexeHebergements()->removeElement($prestationAnnexeHebergement);
                                            $emSite->remove($prestationAnnexeHebergement);
                                        }
                                        $emSite->remove($prestationAnnexeAffectationUnifieSite);
                                    }
                                }
                                // *** fin suppression prestation annexe hebergement ***


                                // *** suppression prestation annexe logement ***
                                /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
                                $prestationAnnexeLogementUnifies = new ArrayCollection();
                                $prestationAnnexeLogementUnifiesSite = new ArrayCollection();
                                foreach ($param->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                                    if (false === $prestationAnnexeLogementUnifies->contains($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie())) {
                                        $prestationAnnexeLogementUnifies->add($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie());
                                    }
                                }
                                foreach ($paramSite->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                                    if (false === $prestationAnnexeLogementUnifiesSite->contains($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie())) {
                                        $prestationAnnexeLogementUnifiesSite->add($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie());
                                    }
                                }

                                /** @var PrestationAnnexeLogementUnifie $prestationAnnexeAffectationUnifieSite */
                                foreach ($prestationAnnexeLogementUnifiesSite as $prestationAnnexeAffectationUnifieSite) {
                                    $prestationAnnexeAffectationUnifie = $prestationAnnexeLogementUnifies->filter(function (
                                        PrestationAnnexeLogementUnifie $element
                                    ) use ($prestationAnnexeAffectationUnifieSite) {
                                        return $element->getId() == $prestationAnnexeAffectationUnifieSite->getId();
                                    })->first();
                                    if (false === $prestationAnnexeAffectationUnifie) {
                                        foreach ($prestationAnnexeAffectationUnifieSite->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                                            $prestationAnnexeAffectationUnifieSite->getPrestationAnnexeLogements()->removeElement($prestationAnnexeLogement);
                                            $paramSite->getPrestationAnnexeLogements()->removeElement($prestationAnnexeLogement);
                                            $emSite->remove($prestationAnnexeLogement);
                                        }
                                        $emSite->remove($prestationAnnexeAffectationUnifieSite);
                                    }
                                }
                                // *** fin suppression prestation annexe logement ***

                            }
                        }


                    }
                }

                /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
                foreach ($fournisseur->getPrestationAnnexes() as $fournisseurPrestationAnnexe) {
                    $fournisseurPrestationAnnexeSite = $fournisseurSite->getPrestationAnnexes()->filter(function (
                        FournisseurPrestationAnnexe $element
                    ) use ($fournisseurPrestationAnnexe) {
                        return $element->getId() == $fournisseurPrestationAnnexe->getId();
                    })->first();
                    if (false === $fournisseurPrestationAnnexeSite) {
                        $fournisseurPrestationAnnexeSite = new FournisseurPrestationAnnexe();
                        $fournisseurPrestationAnnexeSite->setId($fournisseurPrestationAnnexe->getId());
                        $metadata = $emSite->getClassMetadata(get_class($fournisseurPrestationAnnexeSite));
                        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        $fournisseurSite->addPrestationAnnex($fournisseurPrestationAnnexeSite);
                    }
                    $fournisseurPrestationAnnexeSite->setPrestationAnnexe($emSite->find(PrestationAnnexe::class,
                        $fournisseurPrestationAnnexe->getPrestationAnnexe()));

                    $fournisseurPrestationAnnexeSite->setFreeSale($fournisseurPrestationAnnexe->getFreeSale());

                    // /* gestion fournisseur prestation annexe periodeIndisponible
                    /** @var FournisseurPrestationAnnexePeriodeIndisponible $periodeIndisponible */
                    /** @var FournisseurPrestationAnnexePeriodeIndisponible $periodeIndisponibleSite */
                    foreach ($fournisseurPrestationAnnexe->getPeriodeIndisponibles() as $periodeIndisponible) {
                        $periodeIndisponibleSite = $fournisseurPrestationAnnexeSite->getPeriodeIndisponibles()->filter(function (
                            FournisseurPrestationAnnexePeriodeIndisponible $element
                        ) use ($periodeIndisponible) {
                            return $element->getId() == $periodeIndisponible->getId();
                        })->first();
                        if (false == $periodeIndisponibleSite) {
                            $periodeIndisponibleSite = new FournisseurPrestationAnnexePeriodeIndisponible();
                            $fournisseurPrestationAnnexeSite->addPeriodeIndisponible($periodeIndisponibleSite);
                            $periodeIndisponibleSite->setId($periodeIndisponible->getId());
                            $metadata = $emSite->getClassMetadata(get_class($fournisseurPrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $periodeIndisponibleSite->setDateDebut($periodeIndisponible->getDateDebut())->setDateFin($periodeIndisponible->getDateFin());
                    }
                    foreach ($fournisseurPrestationAnnexeSite->getPeriodeIndisponibles() as $periodeIndisponibleSite) {
                        $periodeIndisponible = $fournisseurPrestationAnnexe->getPeriodeIndisponibles()->filter(function (
                            FournisseurPrestationAnnexePeriodeIndisponible $element
                        ) use ($periodeIndisponibleSite) {
                            return $element->getId() == $periodeIndisponibleSite->getId();
                        })->first();
                        if (false === $periodeIndisponible) {
                            $fournisseurPrestationAnnexeSite->removePeriodeIndisponible($periodeIndisponibleSite);
                            $emSite->remove($periodeIndisponibleSite);
                        }
                    }
                    // fin gestion fournisseur prestation annexe periodeIndisponible */

                    // *** traductions ***
                    /** @var FournisseurPrestationAnnexeTraduction $traduction */
                    foreach ($fournisseurPrestationAnnexe->getTraductions() as $traduction) {
                        $traductionSite = $fournisseurPrestationAnnexeSite->getTraductions()->filter(function (
                            FournisseurPrestationAnnexeTraduction $element
                        ) use ($traduction) {
                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                        })->first();
                        if (false === $traductionSite) {
                            $traductionSite = new FournisseurPrestationAnnexeTraduction();
                            $fournisseurPrestationAnnexeSite->addTraduction($traductionSite);
                            $traductionSite
                                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                        }
                        $traductionSite->setLibelle($traduction->getLibelle());
                    }
                    // *** fin traductions ***

                    // *** param ***
                    /** @var FournisseurPrestationAnnexeParam $param */
                    foreach ($fournisseurPrestationAnnexe->getParams() as $param) {
                        $paramSite = $fournisseurPrestationAnnexeSite->getParams()->filter(function (
                            FournisseurPrestationAnnexeParam $element
                        ) use ($param) {
                            return $element->getId() == $param->getId();
                        })->first();
                        if (false === $paramSite) {
                            $paramSite = new FournisseurPrestationAnnexeParam();
                            $paramSite->setId($param->getId());
                            $metadata = $emSite->getClassMetadata(get_class($paramSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $fournisseurPrestationAnnexeSite->addParam($paramSite);
                        }
                        $paramSite
                            ->setType($param->getType())
                            ->setModeAffectation($param->getModeAffectation());

                        // *** capacite ***
                        $capaciteSite = null;
                        if (!empty($capacite = $param->getCapacite())) {
                            if (empty($capaciteSite = $paramSite->getCapacite())) {
                                $capaciteSite = new FournisseurPrestationAnnexeCapacite();
                            }
                            $capaciteSite
                                ->setMin($capacite->getMin())
                                ->setMax($capacite->getMax());
                        } elseif (!empty($paramSite->getCapacite())) {
                            $emSite->remove($paramSite->getCapacite());
                        }
                        $paramSite->setCapacite($capaciteSite);
                        // *** fin capacite ***

                        // *** duree sejour ***
                        $dureeSejourSite = null;
                        if (!empty($dureeSejour = $param->getDureeSejour())) {
                            if (empty($dureeSejourSite = $paramSite->getDureeSejour())) {
                                $dureeSejourSite = new FournisseurPrestationAnnexeDureeSejour();
                            }
                            $dureeSejourSite
                                ->setMin($dureeSejour->getMin())
                                ->setMax($dureeSejour->getMax());
                        } elseif (!empty($paramSite->getDureeSejour())) {
                            $emSite->remove($paramSite->getDureeSejour());
                        }
                        $paramSite->setDureeSejour($dureeSejourSite);
                        // *** fin duree sejour ***

                        // *** traductions ***
                        /** @var FournisseurPrestationAnnexeParamTraduction $traduction */
                        foreach ($param->getTraductions() as $traduction) {
                            $traductionSite = $paramSite->getTraductions()->filter(function (
                                FournisseurPrestationAnnexeParamTraduction $element
                            ) use ($traduction) {
                                return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new FournisseurPrestationAnnexeParamTraduction();
                                $paramSite->addTraduction($traductionSite);
                                $traductionSite
                                    ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                            }
                            $traductionSite
                                ->setLibelleParam($traduction->getLibelleParam())
                                ->setLibelleFournisseurPrestationAnnexeParam($traduction->getLibelleFournisseurPrestationAnnexeParam());
                        }
                        // *** fin traductions ***

                        // *** tarifs ***
                        /** @var PrestationAnnexeTarif $tarif */
                        foreach ($param->getTarifs() as $tarif) {
                            $tarifSite = $paramSite->getTarifs()->filter(function (PrestationAnnexeTarif $element) use (
                                $tarif
                            ) {
                                return $element->getId() == $tarif->getId();
                            })->first();
                            if (false === $tarifSite) {
                                $tarifSite = new PrestationAnnexeTarif();
                                $tarifSite->setId($tarif->getId());
                                $metadata = $emSite->getClassMetadata(get_class($tarifSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                                $paramSite->addTarif($tarifSite);
                            }
                            $tarifSite
                                ->setPrixCatalogue($tarif->getPrixCatalogue())
                                ->setPrixPublic($tarif->getPrixPublic())
                                ->setComMondofute($tarif->getComMondofute())
                                ->setPrixAchat($tarif->getPrixAchat());
                            // *** periode validite ***
                            /** @var PeriodeValidite $periodeValidite */
                            foreach ($tarif->getPeriodeValidites() as $periodeValidite) {
                                $periodeValiditeSite = $tarifSite->getPeriodeValidites()->filter(function (
                                    PeriodeValidite $element
                                ) use ($periodeValidite) {
                                    return $element->getId() == $periodeValidite->getId();
                                })->first();
                                if (false === $periodeValiditeSite) {
                                    $periodeValiditeSite = new PeriodeValidite();
                                    $periodeValiditeSite->setId($periodeValidite->getId());
                                    $metadata = $emSite->getClassMetadata(get_class($periodeValiditeSite));
                                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                                    $tarifSite->addPeriodeValidite($periodeValiditeSite);
                                }
                                $periodeValiditeSite
                                    ->setDateDebut($periodeValidite->getDateDebut())
                                    ->setDateFin($periodeValidite->getDateFin());
                            }
                            // *** fin periode validite ***
                        }
                        // *** fin tarifs ***

                        // *** prestationAnnexeFournisseurs ***
                        /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
                        $prestationAnnexeFournisseurUnifies = new ArrayCollection();
                        $prestationAnnexeFournisseurUnifiesSite = new ArrayCollection();
                        foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                            if (false === $prestationAnnexeFournisseurUnifies->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                $prestationAnnexeFournisseurUnifies->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                            }
                        }
                        foreach ($paramSite->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                            if (false === $prestationAnnexeFournisseurUnifiesSite->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                $prestationAnnexeFournisseurUnifiesSite->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                            }
                        }
                        /** @var PrestationAnnexeFournisseurUnifie $prestationAnnexeFournisseurUnifie */
                        foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
                            $prestationAnnexeFournisseurUnifieSite = $prestationAnnexeFournisseurUnifiesSite->filter(function (
                                PrestationAnnexeFournisseurUnifie $element
                            ) use ($prestationAnnexeFournisseurUnifie) {
                                return $element->getId() == $prestationAnnexeFournisseurUnifie->getId();
                            })->first();

                            $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (
                                PrestationAnnexeFournisseur $element
                            ) use ($site) {
                                return $element->getSite() == $site;
                            })->first();

                            if (false === $prestationAnnexeFournisseurUnifieSite) {
                                $prestationAnnexeFournisseurUnifieSite = new PrestationAnnexeFournisseurUnifie();
                                $emSite->persist($prestationAnnexeFournisseurUnifieSite);
                                $prestationAnnexeFournisseurUnifieSite->setId($prestationAnnexeFournisseurUnifie->getId());
                                $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeFournisseurUnifieSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);


                                $prestationAnnexeFournisseurSite = new PrestationAnnexeFournisseur();
                                $prestationAnnexeFournisseurUnifieSite->addPrestationAnnexeFournisseur($prestationAnnexeFournisseurSite);

                                $paramSite->addPrestationAnnexeFournisseur($prestationAnnexeFournisseurSite);
                                $prestationAnnexeFournisseurSite
                                    ->setFournisseur($emSite->find(Fournisseur::class,
                                        $prestationAnnexeFournisseur->getFournisseur()))
                                    ->setSite($emSite->find(Site::class, $prestationAnnexeFournisseur->getSite()));
                            }

                            $stationSite = null;
                            if (!empty($prestationAnnexeFournisseur->getStation())) {
                                $stationUnifieSite = $emSite->find(StationUnifie::class,
                                    $prestationAnnexeFournisseur->getStation()->getStationUnifie());
                                $stationSite = $stationUnifieSite->getStations()->first();
                            }

                            $prestationAnnexeFournisseurSite = $prestationAnnexeFournisseurUnifieSite->getPrestationAnnexeFournisseurs()->first();
                            $prestationAnnexeFournisseurSite
                                ->setActif($prestationAnnexeFournisseur->getActif())
                                ->setStation($stationSite);
                        }
                        // *** fin prestationAnnexeFournisseurs ***


                        // *** prestationAnnexeStations ***
                        /** @var PrestationAnnexeStation $prestationAnnexeStation */
                        $prestationAnnexeStationUnifies = new ArrayCollection();
                        $prestationAnnexeStationUnifiesSite = new ArrayCollection();
                        foreach ($param->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                            if (false === $prestationAnnexeStationUnifies->contains($prestationAnnexeStation->getPrestationAnnexeStationUnifie())) {
                                $prestationAnnexeStationUnifies->add($prestationAnnexeStation->getPrestationAnnexeStationUnifie());
                            }
                        }
                        foreach ($paramSite->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                            if (false === $prestationAnnexeStationUnifiesSite->contains($prestationAnnexeStation->getPrestationAnnexeStationUnifie())) {
                                $prestationAnnexeStationUnifiesSite->add($prestationAnnexeStation->getPrestationAnnexeStationUnifie());
                            }
                        }
                        /** @var PrestationAnnexeStationUnifie $prestationAnnexeStationUnifie */
                        foreach ($prestationAnnexeStationUnifies as $prestationAnnexeStationUnifie) {
                            $prestationAnnexeStationUnifieSite = $prestationAnnexeStationUnifiesSite->filter(function (
                                PrestationAnnexeStationUnifie $element
                            ) use ($prestationAnnexeStationUnifie) {
                                return $element->getId() == $prestationAnnexeStationUnifie->getId();
                            })->first();

                            $prestationAnnexeStation = $prestationAnnexeStationUnifie->getPrestationAnnexeStations()->filter(function (
                                PrestationAnnexeStation $element
                            ) use ($site) {
                                return $element->getSite() == $site;
                            })->first();

                            if (false === $prestationAnnexeStationUnifieSite) {
                                $prestationAnnexeStationUnifieSite = new PrestationAnnexeStationUnifie();
                                $emSite->persist($prestationAnnexeStationUnifieSite);
                                $prestationAnnexeStationUnifieSite->setId($prestationAnnexeStationUnifie->getId());
                                $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeStationUnifieSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);


                                $prestationAnnexeStationSite = new PrestationAnnexeStation();
                                $prestationAnnexeStationUnifieSite->addPrestationAnnexeStation($prestationAnnexeStationSite);

                                $stationUnifieSite = $emSite->find(StationUnifie::class,
                                    $prestationAnnexeStation->getStation()->getStationUnifie());
                                $stationSite = $stationUnifieSite->getStations()->first();
                                $paramSite->addPrestationAnnexeStation($prestationAnnexeStationSite);
                                $prestationAnnexeStationSite
                                    ->setStation($stationSite)
                                    ->setSite($emSite->find(Site::class, $prestationAnnexeStation->getSite()));
                            }

                            $prestationAnnexeStationSite = $prestationAnnexeStationUnifieSite->getPrestationAnnexeStations()->first();
                            $prestationAnnexeStationSite
                                ->setActif($prestationAnnexeStation->getActif());
                        }
                        // *** fin prestationAnnexeStations ***


                        // *** prestationAnnexeHebergements ***
                        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
                        $prestationAnnexeHebergementUnifies = new ArrayCollection();
                        $prestationAnnexeHebergementUnifiesSite = new ArrayCollection();
                        foreach ($param->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                            if (false === $prestationAnnexeHebergementUnifies->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
                            }
                        }
                        foreach ($paramSite->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                            if (false === $prestationAnnexeHebergementUnifiesSite->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                                $prestationAnnexeHebergementUnifiesSite->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
                            }
                        }
//                        dump($prestationAnnexeHebergementUnifies);
//                        dump($prestationAnnexeHebergementUnifiesSite);
//                        die;
                        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
                        foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                            $prestationAnnexeHebergementUnifieSite = $prestationAnnexeHebergementUnifiesSite->filter(function (
                                PrestationAnnexeHebergementUnifie $element
                            ) use ($prestationAnnexeHebergementUnifie) {
                                return $element->getId() == $prestationAnnexeHebergementUnifie->getId();
                            })->first();

                            $prestationAnnexeHebergement = $prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements()->filter(function (
                                PrestationAnnexeHebergement $element
                            ) use ($site) {
                                return $element->getSite() == $site;
                            })->first();

                            if (false === $prestationAnnexeHebergementUnifieSite) {
                                $prestationAnnexeHebergementUnifieSite = new PrestationAnnexeHebergementUnifie();
                                $emSite->persist($prestationAnnexeHebergementUnifieSite);
                                $prestationAnnexeHebergementUnifieSite->setId($prestationAnnexeHebergementUnifie->getId());
                                $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeHebergementUnifieSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                                $prestationAnnexeHebergementSite = new PrestationAnnexeHebergement();
                                $prestationAnnexeHebergementUnifieSite->addPrestationAnnexeHebergement($prestationAnnexeHebergementSite);

                                $paramSite->addPrestationAnnexeHebergement($prestationAnnexeHebergementSite);
                                $hebergementUnifieSite = $emSite->find(HebergementUnifie::class,
                                    $prestationAnnexeHebergement->getHebergement()->getHebergementUnifie());
                                $hebergementSite = $hebergementUnifieSite->getHebergements()->first();
                                $prestationAnnexeHebergementSite
                                    ->setFournisseur($emSite->find(Fournisseur::class,
                                        $prestationAnnexeHebergement->getFournisseur()))
                                    ->setHebergement($hebergementSite)
                                    ->setSite($emSite->find(Site::class, $prestationAnnexeHebergement->getSite()));
                            }

                            $prestationAnnexeHebergementSite = $prestationAnnexeHebergementUnifieSite->getPrestationAnnexeHebergements()->first();
                            $prestationAnnexeHebergementSite
                                ->setActif($prestationAnnexeHebergement->getActif());
                        }
                        // *** fin prestationAnnexeHebergements ***


                        // *** prestationAnnexeLogements ***
                        /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
                        $prestationAnnexeLogementUnifies = new ArrayCollection();
                        $prestationAnnexeLogementUnifiesSite = new ArrayCollection();
                        foreach ($param->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                            if (false === $prestationAnnexeLogementUnifies->contains($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie())) {
                                $prestationAnnexeLogementUnifies->add($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie());
                            }
                        }
                        foreach ($paramSite->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                            if (false === $prestationAnnexeLogementUnifiesSite->contains($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie())) {
                                $prestationAnnexeLogementUnifiesSite->add($prestationAnnexeLogement->getPrestationAnnexeLogementUnifie());
                            }
                        }
                        /** @var PrestationAnnexeLogementUnifie $prestationAnnexeLogementUnifie */
                        foreach ($prestationAnnexeLogementUnifies as $prestationAnnexeLogementUnifie) {
                            $prestationAnnexeLogementUnifieSite = $prestationAnnexeLogementUnifiesSite->filter(function (
                                PrestationAnnexeLogementUnifie $element
                            ) use ($prestationAnnexeLogementUnifie) {
                                return $element->getId() == $prestationAnnexeLogementUnifie->getId();
                            })->first();

                            $prestationAnnexeLogement = $prestationAnnexeLogementUnifie->getPrestationAnnexeLogements()->filter(function (
                                PrestationAnnexeLogement $element
                            ) use ($site) {
                                return $element->getSite() == $site;
                            })->first();

                            if (false === $prestationAnnexeLogementUnifieSite) {
                                $prestationAnnexeLogementUnifieSite = new PrestationAnnexeLogementUnifie();
                                $emSite->persist($prestationAnnexeLogementUnifieSite);
                                $prestationAnnexeLogementUnifieSite->setId($prestationAnnexeLogementUnifie->getId());
                                $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeLogementUnifieSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                                $prestationAnnexeLogementSite = new PrestationAnnexeLogement();
                                $prestationAnnexeLogementUnifieSite->addPrestationAnnexeLogement($prestationAnnexeLogementSite);

                                $paramSite->addPrestationAnnexeLogement($prestationAnnexeLogementSite);
                                $logementUnifieSite = $emSite->find(LogementUnifie::class,
                                    $prestationAnnexeLogement->getLogement()->getLogementUnifie());
                                $logementSite = $logementUnifieSite->getLogements()->first();
                                $prestationAnnexeLogementSite
                                    ->setLogement($logementSite)
                                    ->setSite($emSite->find(Site::class, $prestationAnnexeLogement->getSite()));
                            }

                            $prestationAnnexeLogementSite = $prestationAnnexeLogementUnifieSite->getPrestationAnnexeLogements()->first();
                            $prestationAnnexeLogementSite
                                ->setActif($prestationAnnexeLogement->getActif());
                        }
                        // *** fin prestationAnnexeLogements ***


                    }


                }
                // *** FIN GESTION PRESTATION ANNEXE ***

                $fournisseurSite->setContient($fournisseur->getContient());

                foreach ($fournisseur->getMoyenComs() as $key => $moyenCom) {
                    $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                    switch ($typeComm) {
                        case "Adresse":
                            $adresse = $fournisseurSite->getMoyenComs()->get($key);
                            if (!empty($adresse)) {
                                $adresse->setCodePostal($moyenCom->getCodePostal());
                                $adresse->setAdresse1($moyenCom->getAdresse1());
                                $adresse->setAdresse2($moyenCom->getAdresse2());
                                $adresse->setAdresse3($moyenCom->getAdresse3());
                                $adresse->setVille($moyenCom->getVille());
                                $adresse->setPays($emSite->find(Pays::class, $moyenCom->getPays()));
                            }
//                        $adresse->setDateModification(new DateTime());
                            break;
                        default:
                            break;
                    }
                }

                if (!empty($fournisseur->getFournisseurParent())) {
                    $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur',
                        $fournisseur->getFournisseurParent()->getId()));
                } else {
                    $fournisseurSite->setFournisseurParent(null);
                }

                // ***** GESTION CREATION & EDITION DES INTERLOCUTEURS *****
                // on parcourt les fournisseurInterlocuteurs du fournisseur de la base crm
                /** @var FournisseurInterlocuteur $fournisseurInterlocuteur */
                /** @var FournisseurInterlocuteur $fournisseurInterlocuteurSite */
                /** @var Interlocuteur $interlocuteur */
                /** @var Interlocuteur $interlocuteurSite */
                /** @var InterlocuteurUser $interlocuteurUser */
                /** @var InterlocuteurUser $interlocuteurUserSite */

                foreach ($fournisseur->getInterlocuteurs() as $fournisseurInterlocuteur) {
                    $interlocuteur = $fournisseurInterlocuteur->getInterlocuteur();
                    $interlocuteurUser = $interlocuteur->getUser();

                    // on récupère le fournisseurInterlocuteur correspondant à celui de la base distante
                    $fournisseurInterlocuteurSite = $fournisseurSite->getInterlocuteurs()->filter(function (
                        FournisseurInterlocuteur $element
                    ) use ($fournisseurInterlocuteur) {
                        return $element->getId() == $fournisseurInterlocuteur->getId();
                    })->first();
                    // si il existe pas
                    if (!empty($fournisseurInterlocuteurSite)) {
                        $interlocuteurSite = $fournisseurInterlocuteurSite->getInterlocuteur();
                        $interlocuteurUserSite = $interlocuteurSite->getUser();

                        $interlocuteurSite->setPrenom($interlocuteur->getPrenom());
                        $interlocuteurSite->setNom($interlocuteur->getNom());
                        // on met à jours
                        if (!empty($interlocuteur->getFonction())) {
                            $interlocuteurSite->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                                $interlocuteur->getFonction()->getId()));
                        } else {
                            $interlocuteurSite->setFonction(null);
                        }
                        if (!empty($interlocuteur->getService())) {
                            $interlocuteurSite->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                                $interlocuteur->getService()->getId()));
                        } else {
                            $interlocuteurSite->setService(null);
                        }

                        $moyenComsSite = $interlocuteurSite->getMoyenComs();
                        if (!empty($moyenComsSite)) {
                            foreach ($moyenComsSite as $key => $moyenComSite) {
                                $typeComm = (new ReflectionClass($moyenComSite))->getShortName();
                                $firstFixe = true;
                                switch ($typeComm) {
                                    case 'Adresse':
                                        $moyenComCrm = $interlocuteur->getMoyenComs()->filter(function (
                                            $element
                                        ) {
                                            return (new ReflectionClass($element))->getShortName() == 'Adresse';
                                        })->first();
                                        if ($moyenComCrm) {
                                            /** @var Adresse $moyenComSite */
                                            /** @var Adresse $moyenComCrm */
                                            $moyenComSite->setCodePostal($moyenComCrm->getCodePostal());
                                            $moyenComSite->setAdresse1($moyenComCrm->getAdresse1());
                                            $moyenComSite->setAdresse2($moyenComCrm->getAdresse2());
                                            $moyenComSite->setAdresse3($moyenComCrm->getAdresse3());
                                            $moyenComSite->setVille($moyenComCrm->getVille());
                                            $moyenComSite->setPays($emSite->find(Pays::class, $moyenComCrm->getPays()));
                                            $moyenComSite->getCoordonneeGps()->setLatitude($moyenComCrm->getCoordonneeGps()->getLatitude());
                                            $moyenComSite->getCoordonneeGps()->setLongitude($moyenComCrm->getCoordonneeGps()->getLongitude());
                                            $moyenComSite->getCoordonneeGps()->setPrecis($moyenComCrm->getCoordonneeGps()->getPrecis());
                                        }
                                        break;
                                    case 'Email':
                                        $moyenComCrm = $interlocuteur->getMoyenComs()->filter(function (
                                            $element
                                        ) {
                                            return (new ReflectionClass($element))->getShortName() == 'Email';
                                        })->first();
                                        if ($moyenComCrm) {
                                            /** @var Email $moyenComSite */
                                            /** @var Email $moyenComCrm */
                                            $moyenComSite->setAdresse($moyenComCrm->getAdresse());
                                            $interlocuteurUserSite
                                                ->setUsername($moyenComCrm->getAdresse())
                                                ->setEmail($moyenComCrm->getAdresse());
                                        }
                                        break;
                                    case 'Mobile':
                                        $moyenComCrm = $interlocuteur->getMoyenComs()->filter(function (
                                            $element
                                        ) {
                                            return (new ReflectionClass($element))->getShortName() == 'Mobile';
                                        })->first();
                                        if ($moyenComCrm) {
                                            /** @var TelMobile $moyenComSite */
                                            /** @var TelMobile $moyenComCrm */
                                            $moyenComSite->setNumero($moyenComCrm->getNumero());
                                        }
                                        break;
                                    case 'Fixe':
                                        $moyenComCrm = $interlocuteur->getMoyenComs()->filter(function (
                                            $element
                                        ) {
                                            return (new ReflectionClass($element))->getShortName() == 'Fixe';
                                        });
                                        if ($moyenComCrm) {
                                            /** @var TelFixe $moyenComSite */
                                            /** @var ArrayCollection $moyenComCrm */
                                            if ($firstFixe) {
                                                $moyenComSite->setNumero($moyenComCrm->first()->getNumero());
                                                $firstFixe = false;
                                            } else {
                                                $moyenComSite->setNumero($moyenComCrm->last()->getNumero());
                                            }
                                        }
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                        // Mis à jours du mot de passe du user
                        $interlocuteurUserSite->setPassword($interlocuteurUser->getPassword());
                    } else {
                        $fournisseurInterlocuteurSite = new FournisseurInterlocuteur();

                        /** @var Interlocuteur $interlocuteurSite */
                        $interlocuteurSite = new Interlocuteur();

                        $interlocuteurSite->setPrenom($interlocuteur->getPrenom());
                        $interlocuteurSite->setNom($interlocuteur->getNom());

                        if (!empty($interlocuteur->getFonction())) {
                            $interlocuteurSite->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                                $interlocuteur->getFonction()->getId()));
                        }
                        if (!empty($interlocuteur->getService())) {
                            $interlocuteurSite->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                                $interlocuteur->getService()->getId()));
                        }

                        $fournisseurInterlocuteurSite->setFournisseur($fournisseurSite);
                        $fournisseurInterlocuteurSite->setInterlocuteur($interlocuteurSite);

                        foreach ($interlocuteur->getMoyenComs() as $moyenCom) {
                            $moyenComClone = clone $moyenCom;
                            $interlocuteurSite->addMoyenCom($moyenComClone);

                            $typeComm = (new ReflectionClass($moyenComClone))->getShortName();
                            switch ($typeComm) {
                                case "Adresse":
                                    /** @var Adresse $moyenComClone */
                                    $moyenComClone->setPays($emSite->find(Pays::class, $moyenComClone->getPays()));
                                    break;
                                default:
                                    break;
                            }
                        }
                        // ***** gestion creation interlocuteur_user *****
                        $interlocuteurUserSite = clone $interlocuteur->getUser();
                        $interlocuteurSite->setUser($interlocuteurUserSite);
                        // ***** fin creation gestion interlocuteur_user *****

                        $fournisseurSite->addInterlocuteur($fournisseurInterlocuteurSite);
                    }
                }
                // ***** FIN GESTION CREATION & EDITION DES INTERLOCUTEURS *****


                // *** gestion des remiseclefs ***
                /** @var RemiseClef $remiseClef */
                foreach ($fournisseur->getRemiseClefs() as $remiseClef) {
                    if (!empty($remiseClef->getId())) {
                        $remiseClefSite = $fournisseurSite->getRemiseClefs()->filter(function (RemiseClef $element) use (
                            $remiseClef
                        ) {
                            return $element->getId() == $remiseClef->getId();
                        })->first();
                    } else {
                        $remiseClefSite = null;
                    }
                    if (empty($remiseClefSite)) {
                        $remiseClefSite = new RemiseClef();
                    }
                    $remiseClefSite->setLibelle($remiseClef->getLibelle());
                    if (!empty($remiseClef->getHeureDepartCourtSejour())) {
                        $remiseClefSite->setHeureDepartCourtSejour($remiseClef->getHeureDepartCourtSejour());
                    } else {
                        $remiseClefSite->setHeureDepartCourtSejour(null);
                    }
                    if (!empty($remiseClef->getHeureTardiveCourtSejour())) {
                        $remiseClefSite->setHeureTardiveCourtSejour($remiseClef->getHeureTardiveCourtSejour());
                    } else {
                        $remiseClefSite->setHeureTardiveCourtSejour(null);
                    }
                    if (!empty($remiseClef->getFournisseur())) {
                        $remiseClefSite->setFournisseur($emSite->find(Fournisseur::class,
                            $remiseClef->getFournisseur()->getId()));
                    } else {
                        $remiseClefSite->setFournisseur(null);
                    }
                    if (!empty($remiseClef->getHeureDepartLongSejour())) {
                        $remiseClefSite->setHeureDepartLongSejour($remiseClef->getHeureDepartLongSejour());
                    } else {
                        $remiseClefSite->setHeureDepartLongSejour(null);
                    }
                    if (!empty($remiseClef->getHeureRemiseClefCourtSejour())) {
                        $remiseClefSite->setHeureRemiseClefCourtSejour($remiseClef->getHeureRemiseClefCourtSejour());
                    } else {
                        $remiseClefSite->setHeureRemiseClefCourtSejour(null);
                    }
                    if (!empty($remiseClef->getHeureRemiseClefLongSejour())) {
                        $remiseClefSite->setHeureRemiseClefLongSejour($remiseClef->getHeureRemiseClefLongSejour());
                    } else {
                        $remiseClefSite->setHeureRemiseClefLongSejour(null);
                    }
                    if (!empty($remiseClef->getHeureTardiveLongSejour())) {
                        $remiseClefSite->setHeureTardiveLongSejour($remiseClef->getHeureTardiveLongSejour());
                    } else {
                        $remiseClefSite->setHeureTardiveLongSejour(null);
                    }
                    if (!empty($remiseClef->getStandard())) {
                        $remiseClefSite->setStandard($remiseClef->getStandard());
                    } else {
                        $remiseClefSite->setStandard(false);
                    }
                    if (!empty($remiseClef->getHeureTardiveLongSejour())) {
                        $remiseClefSite->setHeureTardiveLongSejour($remiseClef->getHeureTardiveLongSejour());
                    } else {
                        $remiseClefSite->setHeureTardiveLongSejour(null);
                    }
                    /** @var RemiseClefTraduction $remiseClefTraduction */
                    foreach ($remiseClef->getTraductions() as $remiseClefTraduction) {
                        if (!empty($remiseClefTraduction->getId())) {
                            $remiseClefTraductionSite = $remiseClefSite->getTraductions()->filter(function (
                                RemiseClefTraduction $element
                            ) use (
                                $remiseClefTraduction
                            ) {
                                return ($element->getLangue()->getId() == $remiseClefTraduction->getLangue()->getId()) && ($element->getRemiseClef()->getId() == $remiseClefTraduction->getRemiseClef()->getId());
                            })->first();
                        } else {
                            $remiseClefTraductionSite = null;
                        }
                        if (empty($remiseClefTraductionSite)) {
                            $remiseClefTraductionSite = new RemiseClefTraduction();
                        }
                        if (!empty($remiseClefTraduction->getLangue())) {
                            $remiseClefTraductionSite->setLangue($emSite->find(Langue::class,
                                $remiseClefTraduction->getLangue()->getId()));
                        }
                        if (!empty($remiseClefTraduction->getLieuxRemiseClef())) {
                            $remiseClefTraductionSite->setLieuxRemiseClef($remiseClefTraduction->getLieuxRemiseClef());
                        } else {
                            $remiseClefTraductionSite->setLieuxRemiseClef('');
                        }
                        $remiseClefSite->addTraduction($remiseClefTraductionSite);
//                    if(!empty($remiseClefTraduction->getRemiseClef())){
//                        $remiseClefTraductionSite->setRemiseClef($emSite->find(RemiseClef::class,$remiseClefTraduction->getRemiseClef()->getId()));
//                    }else{
//                        $remiseClefTraductionSite->setRemiseClef(null);
//                    }
                    }
                    $fournisseurSite->addRemiseClef($remiseClefSite);
                }
                // *** fin gestion des remiseclefs ***

                // *** gestion reception ***
                /** @var Reception $reception */
                foreach ($fournisseur->getReceptions() as $reception) {
                    if (!empty($reception->getId())) {
                        $receptionSite = $fournisseurSite->getReceptions()->filter(function (Reception $element) use (
                            $reception
                        ) {
                            return $element->getId() == $reception->getId();
                        })->first();
                    } else {
                        $receptionSite = null;
                    }
//                if(empty($receptionSite = $emSite->getRepository(Reception::class)->find($reception->getId()))){
//
////                }
                    if (empty($receptionSite)) {
                        $receptionSite = new Reception();
                        $fournisseurSite->addReception($receptionSite);
                    }
                    if (!empty($reception->getTranche1())) {
//                    if(empty($tranche1Site = $emSite->getRepository(TrancheHoraire::class)->find($receptionSite->getTranche1()))){
                        if (empty($receptionSite->getTranche1())) {
                            $tranche1Site = new TrancheHoraire();
                        } else {
                            $tranche1Site = $receptionSite->getTranche1();
                        }
                        $tranche1Site->setDebut($reception->getTranche1()->getDebut())
                            ->setFin($reception->getTranche1()->getFin());
                        $receptionSite->setTranche1($tranche1Site);
                    }
                    if (!empty($reception->getTranche2())) {
//                    if(empty($tranche2Site = $emSite->getRepository(TrancheHoraire::class)->find($receptionSite->getTranche2()))){
                        if (empty($receptionSite->getTranche2())) {
                            $tranche2Site = new TrancheHoraire();
                        } else {
                            $tranche2Site = $receptionSite->getTranche2();
                        }
                        $tranche2Site->setDebut($reception->getTranche2()->getDebut())
                            ->setFin($reception->getTranche2()->getFin());
                        $receptionSite->setTranche2($tranche2Site);
                    }
//                if (!empty($reception->getTranche2())) {
//                    $receptionSite->setTranche2($reception->getTranche2());
//                }
                    if (!empty($reception->getJour())) {
                        $receptionSite->setJour($reception->getJour());
                    }
//                $fournisseurSite->addReception($receptionSite);
                }
                // *** gestion reception ***

                // ***** gestion logo *****
                if (!empty($fournisseur->getLogo())) {
                    $logo = $fournisseur->getLogo();
                    if (empty($fournisseurSite->getLogo()) || $logo->getId() != $fournisseurSite->getLogo()->getId()) {
                        $cloneVisuel = clone $logo;
                        $metadata = $emSite->getClassMetadata(get_class($cloneVisuel));
                        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        $cloneVisuel->setContext('fournisseur_logo_' . $site->getLibelle());
                        if (!empty($fournisseurSite->getLogo())) {
                            $emSite->remove($fournisseurSite->getLogo());
                        }
                        $fournisseurSite->setLogo($cloneVisuel);
                    }
                } else {
                    if (!empty($fournisseurSite->getLogo())) {
                        $emSite->remove($fournisseurSite->getLogo());
                        $fournisseurSite->setLogo(null);
                    }
                }
                // ***** fin gestion logo *****


                // *** gestion code promo fournisseurPrestationAnnexe ***
                $codePromoFournisseurPrestationAnnexes = new ArrayCollection($em->getRepository(CodePromoFournisseurPrestationAnnexe::class)->findBySite($fournisseur->getId(),
                    $site->getId()));
                $codePromoFournisseurPrestationAnnexeSites = new ArrayCollection($emSite->getRepository(CodePromoFournisseurPrestationAnnexe::class)->findBySite($fournisseur->getId(),
                    $site->getId()));
                if (!empty($codePromoFournisseurPrestationAnnexes) && !$codePromoFournisseurPrestationAnnexes->isEmpty()) {
                    /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnexe */
                    foreach ($codePromoFournisseurPrestationAnnexes as $codePromoFournisseurPrestationAnnexe) {
                        $codePromoFournisseurPrestationAnnexeSite = $codePromoFournisseurPrestationAnnexeSites->filter(function (
                            CodePromoFournisseurPrestationAnnexe $element
                        ) use ($codePromoFournisseurPrestationAnnexe) {
                            return $element->getId() == $codePromoFournisseurPrestationAnnexe->getId();
                        })->first();
                        if (false === $codePromoFournisseurPrestationAnnexeSite) {
                            $codePromoFournisseurPrestationAnnexeSite = new CodePromoFournisseurPrestationAnnexe();
//                            $entitySite->addCodePromoFournisseurPrestationAnnex($codePromoFournisseurPrestationAnnexeSite);
                            $emSite->persist($codePromoFournisseurPrestationAnnexeSite);
                            $codePromoFournisseurPrestationAnnexeSite
                                ->setId($codePromoFournisseurPrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoFournisseurPrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $codePromo = $emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $codePromoFournisseurPrestationAnnexe->getCodePromo()->getCodePromoUnifie()));

                        $codePromoFournisseurPrestationAnnexeSite
                            ->setCodePromo($codePromo)
                            ->setFournisseur($emSite->find(Fournisseur::class,
                                $codePromoFournisseurPrestationAnnexe->getFournisseur()))
                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class,
                                $codePromoFournisseurPrestationAnnexe->getFournisseurPrestationAnnexe()));
                    }
                }

                if (!empty($codePromoFournisseurPrestationAnnexeSites) && !$codePromoFournisseurPrestationAnnexeSites->isEmpty()) {
                    /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnexe */
                    foreach ($codePromoFournisseurPrestationAnnexeSites as $codePromoFournisseurPrestationAnnexeSite) {
                        $codePromoFournisseurPrestationAnnexe = $codePromoFournisseurPrestationAnnexes->filter(function (
                            CodePromoFournisseurPrestationAnnexe $element
                        ) use ($codePromoFournisseurPrestationAnnexeSite) {
                            return $element->getId() == $codePromoFournisseurPrestationAnnexeSite->getId();
                        })->first();
                        if (false === $codePromoFournisseurPrestationAnnexe) {
//                            $entitySite->removeCodePromoFournisseurPrestationAnnexe($codePromoFournisseurPrestationAnnexeSite);
                            $emSite->remove($codePromoFournisseurPrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion code promo fournisseurPrestationAnnexe ***

                // ***** gestion clause contractuelle *****
                $this->gestionClauseContractuelleSite($fournisseur, $fournisseurSite);
                // ***** fin gestion clause contractuelle *****

                // ***** gestion remontée RM *****
                $this->gestionInformationRMSite($fournisseur, $fournisseurSite);
                // ***** fin remontée RM *****

                $this->gestionSaisonsSite($fournisseur, $fournisseurSite, $emSite);

                $this->gestionConditionAnnulationDescriptionSite($fournisseur, $fournisseurSite, $emSite);

                $this->gestionCodePasserelleSite($fournisseurSite, $fournisseur, $emSite);

                $emSite->persist($fournisseurSite);
                $emSite->flush();
            } else {
                $this->copieVersSites($fournisseur);
            }
        }
    }

    private function gestionPromotionFamillePrestationAnnexe(
        Fournisseur $fournisseur,
        $originalFamillePrestationAnnexes
    )
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($originalFamillePrestationAnnexes as $originalFamillePrestationAnnex) {
            if (!$fournisseur->getTypes()->contains($originalFamillePrestationAnnex)) {
                $promotions = $em->getRepository(PromotionFamillePrestationAnnexe::class)->findBy([
                    'fournisseur' => $fournisseur,
                    'famillePrestationAnnexe' => $originalFamillePrestationAnnex
                ]);
                foreach ($promotions as $promotion) {
                    $em->remove($promotion);
                }
            }
        }
        $em->flush();

        $kernel = $this->get('kernel');

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_promotion:promotion_famille_prestation_command',
            'fournisseurId' => $fournisseur->getId(),
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }

    public function getConditionAnnulationStandardAction()
    {
        $em = $this->getDoctrine()->getManager();
        $conditionAnnulationStandard = $em->find(ConditionAnnulationDescription::class, 1);

        return new Response($conditionAnnulationStandard->getDescription());
    }

    /**
     * Deletes a Fournisseur entity.
     *
     */
    public function deleteAction(Request $request, Fournisseur $fournisseur)
    {
        /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
        /** @var FournisseurPrestationAnnexe $prestationAnnex */
        /** @var FournisseurPrestationAnnexeParam $param */
        /** @var EntityManager $em */
        /** @var Site $site */
        $form = $this->createDeleteForm($fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $fournisseurSite = $emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur->getId());

                    // ***** suppression des moyen de communications *****
                    if (!empty($fournisseurSite)) {
                        $this->deleteMoyenComs($fournisseurSite, $emSite);
                        $emSite->flush();

                        $fournisseurInterlocuteurs = $fournisseurSite->getInterlocuteurs();
                        if (!empty($fournisseurInterlocuteurs)) {
                            foreach ($fournisseurInterlocuteurs as $fournisseurInterlocuteur) {
                                $this->deleteMoyenComs($fournisseurInterlocuteur->getInterlocuteur(), $emSite);

                                $emSite->flush();
                                $emSite->remove($fournisseurInterlocuteur);
                            }
                        }

                        $prestationAnnexeAffectationUnifies = new ArrayCollection();
                        foreach ($fournisseurSite->getPrestationAnnexes() as $prestationAnnex) {
                            foreach ($prestationAnnex->getParams() as $param) {
                                foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeAffectation) {
                                    if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                        $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeFournisseurUnifie());
                                    }
                                }
                                foreach ($param->getPrestationAnnexeHebergements() as $prestationAnnexeAffectation) {
                                    if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                        $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeHebergementUnifie());
                                    }
                                }
                                foreach ($param->getPrestationAnnexeLogements() as $prestationAnnexeAffectation) {
                                    if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                        $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeLogementUnifie());
                                    }
                                }
                                foreach ($param->getPrestationAnnexeStations() as $prestationAnnexeAffectation) {
                                    if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                        $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeStationUnifie());
                                    }
                                }
                            }
                        }
                        foreach ($prestationAnnexeAffectationUnifies as $unify) {
                            $emSite->remove($unify);
                        }
                        $emSite->flush();
                        // ***** fin suppression des moyen de communications *****

                        $codePromoFournisseurs = $emSite->getRepository(CodePromoFournisseur::class)->findBy(array(
                            'fournisseur' => $fournisseurSite->getId(),
                            'type' => 2
                        ));
                        foreach ($codePromoFournisseurs as $codePromoFournisseur) {
                            $emSite->remove($codePromoFournisseur);
                        }

                        $emSite->remove($fournisseurSite);
                        $emSite->flush();
                    }
                }
                // ***** suppression des moyen de communications *****

                $this->deleteMoyenComs($fournisseur, $em);
                $em->flush();

                $fournisseurInterlocuteurs = $fournisseur->getInterlocuteurs();
                if (!empty($fournisseurInterlocuteurs)) {
                    foreach ($fournisseurInterlocuteurs as $fournisseurInterlocuteur) {
                        $this->deleteMoyenComs($fournisseurInterlocuteur->getInterlocuteur(), $em);
                        $em->flush();
                        $em->remove($fournisseurInterlocuteur);
                    }
                }

                $prestationAnnexeAffectationUnifies = new ArrayCollection();
                foreach ($fournisseur->getPrestationAnnexes() as $prestationAnnex) {
                    foreach ($prestationAnnex->getParams() as $param) {
                        foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeAffectation) {
                            if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeFournisseurUnifie());
                            }
                        }
                        foreach ($param->getPrestationAnnexeHebergements() as $prestationAnnexeAffectation) {
                            if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeHebergementUnifie());
                            }
                        }
                        foreach ($param->getPrestationAnnexeLogements() as $prestationAnnexeAffectation) {
                            if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeLogementUnifie());
                            }
                        }
                        foreach ($param->getPrestationAnnexeStations() as $prestationAnnexeAffectation) {
                            if (!$prestationAnnexeAffectationUnifies->contains($prestationAnnexeAffectation)) {
                                $prestationAnnexeAffectationUnifies->add($prestationAnnexeAffectation->getPrestationAnnexeStationUnifie());
                            }
                        }
                    }
                }

                foreach ($prestationAnnexeAffectationUnifies as $unify) {
                    $em->remove($unify);
                }

                $em->flush();
                // ***** fin suppression des moyen de communications *****

                $codePromoFournisseurs = $em->getRepository(CodePromoFournisseur::class)->findBy(array(
                    'fournisseur' => $fournisseur->getId(),
                    'type' => 2
                ));
                foreach ($codePromoFournisseurs as $codePromoFournisseur) {
                    $em->remove($codePromoFournisseur);
                }

                $em->remove($fournisseur);
                $em->flush();

            } catch (ForeignKeyConstraintViolationException $except) {

                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'Impossible de supprimer le fournisseur, il est utilisé par une autre entité');
//                        $except->getMessage());
                        break;
                    default:
                        $this->addFlash('error', 'une erreure inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }


            // add flash messages
            $this->addFlash('success', 'Le fournisseur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('fournisseur_index');
    }

    public function getPrestationAnnexesAction($famillePrestationAnnexeId, $fournisseurId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array(), array('id' => 'ASC'));

        if (!empty($fournisseurId)) {
            $fournisseur = $em->find(Fournisseur::class, $fournisseurId);
//            $fournisseur->getPrestationAnnexes()->clear();
        } else {
            $fournisseur = new Fournisseur();
        }

        $famillePrestationAnnexe = $em->find(FamillePrestationAnnexe::class, $famillePrestationAnnexeId);
        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array(
                'locale' => 'fr_FR',
                'famillePrestationAnnexeId' => $famillePrestationAnnexeId
            ));

        return $this->render('@MondofuteFournisseur/fournisseur/prestation-annexe.html.twig', array(
            'form' => $form->createView(),
//            'prestationAnnexes'         => $prestationAnnexes,
            'famillePrestationAnnexe' => $famillePrestationAnnexe,
            'formAjax' => true,
            'sites' => $sites
        ));
    }

    public function getFournisseurPrestationAnnexeFormAction(
        $fournisseurId,
        $prestationAnnexeId,
        $fournisseurHebergementType
    )
    {
        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        /** @var FournisseurPrestationAnnexeParam $param */
        $em = $this->getDoctrine()->getManager();

        $sites = $em->getRepository(Site::class)->findBy(array(), array('id' => 'ASC'));
        if ($fournisseurHebergementType == "true") {
            $fournisseurProduits = $em->getRepository(Fournisseur::class)->findFournisseurByContient(FournisseurContient::PRODUIT,
                $fournisseurId);
        } else {
            $fournisseurProduits = $em->getRepository(Fournisseur::class)->findFournisseurByContient(FournisseurContient::PRODUIT);
        }

        $fournisseur = new Fournisseur();
        $fournisseurPrestationAnnexe = $em->getRepository(FournisseurPrestationAnnexe::class)->findOneBy(array(
            'fournisseur' => $fournisseurId,
            'prestationAnnexe' => $prestationAnnexeId
        ));
        if (empty($fournisseurPrestationAnnexe)) {
            // *** gestion prestations annexe ***
            $fournisseurPrestationAnnexe = new FournisseurPrestationAnnexe();
            $fournisseurPrestationAnnexe->setPrestationAnnexe($em->find(PrestationAnnexe::class, $prestationAnnexeId));
        }
        $fournisseur->getPrestationAnnexes()->set($prestationAnnexeId, $fournisseurPrestationAnnexe);
        foreach ($fournisseurPrestationAnnexe->getPrestationAnnexe()->getTraductions() as $traduction) {
            $traductionFPA = $fournisseurPrestationAnnexe->getTraductions()->filter(function (
                FournisseurPrestationAnnexeTraduction $element
            ) use ($traduction) {
                return $element->getLangue() == $traduction->getLangue();
            })->first();
            if (false === $traductionFPA) {
                $traductionFPA = new FournisseurPrestationAnnexeTraduction();
                $fournisseurPrestationAnnexe->addTraduction($traductionFPA);
                $traductionFPA->setLangue($traduction->getLangue());
                $traductionFPA->setLibelle($traduction->getLibelle());
            }
        }

        // traductions
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        // *** fin gestion prestations annexe ***

        $saisons = $em->getRepository(Saison::class)->findAll();
        $this->addSaisonCodePasserelle($fournisseur, $saisons);

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array(
                'locale' => $this->container->getParameter('locale'),
            ));


        if ($fournisseurHebergementType == "true") {
            $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'),
                null, null, $fournisseurId);
        } else {
            $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'));
        }

        $hebergements = new ArrayCollection();
        foreach ($fournisseur->getPrestationAnnexes() as $fournisseurPrestationAnnexe) {
            foreach ($fournisseurPrestationAnnexe->getParams() as $keyParam => $param) {
                $prestationAnnexeHebergements = $param->getPrestationAnnexeHebergements();
                foreach ($prestationAnnexeHebergements as $prestationAnnexeHebergement) {
                    if ($prestationAnnexeHebergement->getActif()) {
                        $a = new ArrayCollection($em->getRepository(HebergementUnifie::class)->findByFournisseur($prestationAnnexeHebergement->getFournisseur()->getId(),
                            $this->container->getParameter('locale'),
                            $prestationAnnexeHebergement->getSite()->getId()));
                        $hebergements->set($fournisseurPrestationAnnexe->getPrestationAnnexe()->getId() . '_' . $keyParam . '_' . $prestationAnnexeHebergement->getSite()->getId() . '_' . $prestationAnnexeHebergement->getFournisseur()->getId(),
                            $a);
                    }
                }
            }
        }

        return $this->render('@MondofuteFournisseur/fournisseur/fournisseur-prestation-annexe.html.twig', array(
            'form' => $form->createView(),
            'formAjax' => true,
            'langues' => $langues,
            'sites' => $sites,
            'fournisseurProduits' => $fournisseurProduits,
            'stationsWithHebergement' => $stationsWithHebergement,
            'hebergements' => $hebergements
        ));
    }

    public function getFournisseurPrestationAnnexeAffectationAction(
        $affectation,
        $prestationAnnexeId,
        $fournisseurId,
        $paramIndex,
        $fournisseurHebergementType
    )
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array(), array('id' => 'ASC'));

        $fournisseur = $em->find(Fournisseur::class, $fournisseurId);

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array(
                'locale' => $this->container->getParameter('locale'),
            ));

        $fournisseurForm = $form->createView();
        $fournisseurPrestationAnnexesForm = $fournisseurForm['prestationAnnexes']->children;

        $prestationAnnexeFournisseurs = null;
        $prestationAnnexeHebergements = null;
        $prestationAnnexeStations = null;
        $hebergements = new ArrayCollection();
        foreach ($fournisseurPrestationAnnexesForm as $fournisseurPrestationAnnexeForm) {
            if ($fournisseurPrestationAnnexeForm->vars['value']->getPrestationAnnexe()->getId() == $prestationAnnexeId) {
                if (!empty($fournisseurPrestationAnnexeForm->children['params'][$paramIndex])) {
                    $prestationAnnexeFournisseurs = $fournisseurPrestationAnnexeForm->children['params'][$paramIndex]->children['prestationAnnexeFournisseurs'];
                    $prestationAnnexeHebergements = $fournisseurPrestationAnnexeForm->children['params'][$paramIndex]->children['prestationAnnexeHebergements'];
                    $prestationAnnexeStations = $fournisseurPrestationAnnexeForm->children['params'][$paramIndex]->children['prestationAnnexeStations'];

                    foreach ($prestationAnnexeHebergements->vars['value'] as $prestationAnnexeHebergement) {
                        if ($prestationAnnexeHebergement->getActif()) {
                            $a = new ArrayCollection($em->getRepository(HebergementUnifie::class)->findByFournisseur($prestationAnnexeHebergement->getFournisseur()->getId(),
                                $this->container->getParameter('locale'),
                                $prestationAnnexeHebergement->getSite()->getId()));
                            $hebergements->set($prestationAnnexeId . '_' . $paramIndex . '_' . $prestationAnnexeHebergement->getSite()->getId() . '_' . $prestationAnnexeHebergement->getFournisseur()->getId(),
                                $a);
                        }
                    }
                }
            }
        }

        switch ($affectation) {
            case 'station':
                // Affectation par station (station uniquement qui sont composées de produit)
//                $stations       = $em->getRepository(Station::class )->findBy(array('stationMere' => null));
                $fournisseurs = $em->getRepository(Fournisseur::class)->findFournisseurByContient(FournisseurContient::PRODUIT);
//                $prestationAnnexeFournisseurs = new ArrayCollection();
//                foreach ($fournisseurs as $fournisseur) {
//                    $prestationAnnexeFournisseur = new PrestationAnnexeFournisseur();
//                    $prestationAnnexeFournisseur->setFournisseur($em->find(Fournisseur::class,$fournisseur['id']));
//                    $prestationAnnexeFournisseurs->add($prestationAnnexeFournisseur);
//                }
                if ($fournisseurHebergementType == "true") {
                    $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'),
                        null, null, $fournisseurId);
                } else {
                    $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'));
                }

                return $this->render('@MondofuteFournisseur/fournisseur/template-fournisseur-prestation-annexe-affectation-station.html.twig',
                    array(
                        'fournisseurProduits' => $stationsWithHebergement,
                        'prestationAnnexeId' => $prestationAnnexeId,
                        'sites' => $sites,
//                    'prestationAnnexeFournisseurs' => $prestationAnnexeFournisseurs,
                        'form' => $form->createView(),
                        'prestationAnnexeFournisseurs' => $prestationAnnexeFournisseurs,
                        'prestationAnnexeHebergements' => $prestationAnnexeHebergements,
                        'prestationAnnexeStations' => $prestationAnnexeStations,
                        'hebergements' => $hebergements,
//                    'stationsWithHebergement'   => $stationsWithHebergement
                        'keyParam' => $paramIndex
                    ));
                break;
            case 'fournisseur':

                if ($fournisseurHebergementType == "true") {
                    $fournisseurs = $em->getRepository(Fournisseur::class)->findFournisseurByContient(FournisseurContient::PRODUIT,
                        $fournisseurId);
                } else {
                    $fournisseurs = $em->getRepository(Fournisseur::class)->findFournisseurByContient(FournisseurContient::PRODUIT);
                }
                return $this->render('@MondofuteFournisseur/fournisseur/template-fournisseur-prestation-annexe-affectation-fournisseur.html.twig',
                    array(
                        'fournisseurProduits' => $fournisseurs,
                        'prestationAnnexeId' => $prestationAnnexeId,
                        'sites' => $sites,
//                    'prestationAnnexeFournisseurs' => $prestationAnnexeFournisseurs,
                        'form' => $form->createView(),
                        'prestationAnnexeFournisseurs' => $prestationAnnexeFournisseurs,
                        'prestationAnnexeHebergements' => $prestationAnnexeHebergements,
                        'hebergements' => $hebergements,
                        'keyParam' => $paramIndex
                    ));
                break;
            default:
                break;
        }

        return false;
    }

    public function getFournisseurPrestationAnnexeAffectationHebergementAction(
        $prestationAnnexeId,
        $siteId,
        $fournisseurId,
        $stationId,
        $fournisseurCurrentId,
        $paramIndex
    )
    {
        $em = $this->getDoctrine()->getManager();

        $site = $em->find(Site::class, $siteId);
        $fournisseur = $em->find(Fournisseur::class, $fournisseurCurrentId);

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array(
                'locale' => $this->container->getParameter('locale'),
            ));

        $fournisseurForm = $form->createView();
        $fournisseurPrestationAnnexesForm = $fournisseurForm['prestationAnnexes']->children;

        $prestationAnnexeHebergements = null;

        foreach ($fournisseurPrestationAnnexesForm as $fournisseurPrestationAnnexeForm) {
            if ($fournisseurPrestationAnnexeForm->vars['value']->getPrestationAnnexe()->getId() == $prestationAnnexeId) {
                if (!empty($fournisseurPrestationAnnexeForm->children['params'][$paramIndex])) {
                    $prestationAnnexeHebergements = $fournisseurPrestationAnnexeForm->children['params'][$paramIndex]->children['prestationAnnexeHebergements'];

                }
            }
        }

        $hebergements = $em->getRepository(HebergementUnifie::class)->findByFournisseur($fournisseurId,
            $this->container->getParameter('locale'), $siteId, $stationId);

        return $this->render('@MondofuteFournisseur/fournisseur/template-fournisseur-prestation-annexe-affectation-hebergement.html.twig',
            array(
                'hebergements' => $hebergements,
                'prestationAnnexeId' => $prestationAnnexeId,
                'siteId' => $siteId,
                'prestationAnnexeHebergements' => $prestationAnnexeHebergements,
                'site' => $site,
                'keyParam' => $paramIndex
            ));
    }

    public function getFournisseurPrestationAnnexeAffectationStationFournisseurAction(
        $prestationAnnexeId,
        $stationId,
        $siteId,
        $fournisseurId,
        $paramIndex,
        $fournisseurHebergementType
    )
    {
        $em = $this->getDoctrine()->getManager();


        $fournisseur = $em->find(Fournisseur::class, $fournisseurId);
//        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
//        $fournisseurPrestationAnnexe = $fournisseur->getPrestationAnnexes()->filter(function (FournisseurPrestationAnnexe $element) use ($prestationAnnexeId){
//           return $element->getPrestationAnnexe()->getId() == $prestationAnnexeId;
//        })->first();
//        $prestationAnnexeFournisseurs = $fournisseurPrestationAnnexe->getPrestationAnnexeFournisseurs();
//        $prestationAnnexeHebergements = $fournisseurPrestationAnnexe->getPrestationAnnexeHebergements();

        $site = $em->find(Site::class, $siteId);
        $stationUnifie = $em->find(StationUnifie::class, $stationId);
        $station = $stationUnifie->getStations()->filter(function (Station $element) use ($siteId) {
            return $element->getSite()->getId() == $siteId;
        })->first();

//        $fournisseur = new Fournisseur();

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array(
                'locale' => $this->container->getParameter('locale'),
            ));

//        dump($form->createView());die;
        $fournisseurForm = $form->createView();
        $fournisseurPrestationAnnexesForm = $fournisseurForm['prestationAnnexes']->children;

        $prestationAnnexeFournisseurs = null;
        $prestationAnnexeHebergements = null;
        $hebergements = new ArrayCollection();
        foreach ($fournisseurPrestationAnnexesForm as $fournisseurPrestationAnnexeForm) {
            if ($fournisseurPrestationAnnexeForm->vars['value']->getPrestationAnnexe()->getId() == $prestationAnnexeId) {
                if (!empty($fournisseurPrestationAnnexeForm->children['params'][$paramIndex])) {
                    $prestationAnnexeFournisseurs = $fournisseurPrestationAnnexeForm->children['params'][$paramIndex]->children['prestationAnnexeFournisseurs'];
                    $prestationAnnexeHebergements = $fournisseurPrestationAnnexeForm->children['params'][$paramIndex]->children['prestationAnnexeHebergements'];
                }

                if (!empty($prestationAnnexeHebergements)) {
                    foreach ($prestationAnnexeHebergements->vars['value'] as $prestationAnnexeHebergement) {
                        if ($prestationAnnexeHebergement->getActif()) {
                            $a = new ArrayCollection($em->getRepository(HebergementUnifie::class)->findByFournisseur($prestationAnnexeHebergement->getFournisseur()->getId(),
                                $this->container->getParameter('locale'),
                                $prestationAnnexeHebergement->getSite()->getId()));
                            $hebergements->set($prestationAnnexeId . '_' . $paramIndex . '_' . $prestationAnnexeHebergement->getSite()->getId() . '_' . $prestationAnnexeHebergement->getFournisseur()->getId(),
                                $a);
                        }
                    }
                }
            }
        }

        if ($fournisseurHebergementType == "true") {
            $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'),
                $station->getId(), $siteId, $fournisseurId);
        } else {
            $stationsWithHebergement = $em->getRepository(Hebergement::class)->findStationsWithHebergement($this->container->getParameter('locale'),
                $station->getId(), $siteId);
        }

        return $this->render('@MondofuteFournisseur/fournisseur/get-fournisseur-prestation-annexe-affectation-station-fournisseur-action.html.twig',
            array(
                'fournisseurProduits' => $stationsWithHebergement,
                'prestationAnnexeId' => $prestationAnnexeId,
                'site' => $site,
                'form' => $form->createView(),
                'prestationAnnexeFournisseurs' => $prestationAnnexeFournisseurs,
                'prestationAnnexeHebergements' => $prestationAnnexeHebergements,
                'hebergements' => $hebergements,
                'keyParam' => $paramIndex
            ));
    }

    /**
     * enregistrer fournisseur prestation annexe
     *
     * @param Request $request
     *
     * @return Response
     */
    public function enregisterFournisseurPrestationAnnexeAction(
        Request $request,
        Fournisseur $fournisseur,
        $prestationAnnexeId
    )
    {
        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->get('data'));

        $fournisseurPrestationAnnexe = $fournisseur->getPrestationAnnexes()->filter(function (
            FournisseurPrestationAnnexe $element
        ) use ($prestationAnnexeId) {
            return $element->getPrestationAnnexe()->getId() == $prestationAnnexeId;
        })->first();

        $new = false;
        if (false === $fournisseurPrestationAnnexe) {
            $new = true;
            $fournisseurPrestationAnnexe = new FournisseurPrestationAnnexe();
            $fournisseur->addPrestationAnnex($fournisseurPrestationAnnexe);
            $fournisseurPrestationAnnexe->setPrestationAnnexe($em->find(PrestationAnnexe::class, $prestationAnnexeId));
        }

        $fournisseurPrestationAnnexePost = $data->fournisseur->prestationAnnexes[$prestationAnnexeId];

        // gestion traductions
        $langues = $em->getRepository(Langue::class)->findAll();
        /** @var Langue $langue */
        foreach ($langues as $langue) {
            $traduction = $fournisseurPrestationAnnexe->getTraductions()->filter(function (
                FournisseurPrestationAnnexeTraduction $element
            ) use ($langue) {
                return $element->getLangue() === $langue;
            })->first();
            if (false === $traduction) {
                $traduction = new FournisseurPrestationAnnexeTraduction();
                $fournisseurPrestationAnnexe->addTraduction($traduction);
                $traduction->setLangue($langue);
            }
            $traductionPosts = new ArrayCollection($fournisseurPrestationAnnexePost->traductions);
            $traductionPost = $traductionPosts->filter(function ($element) use ($langue) {
                return $element->langue == $langue->getId();
            })->first();
            $traduction->setLibelle($traductionPost->libelle);
        }

        // gestion free sale
        if (!empty($fournisseurPrestationAnnexePost->freeSale)) {
            $fournisseurPrestationAnnexe->setFreeSale($fournisseurPrestationAnnexePost->freeSale);
        } else {
            $fournisseurPrestationAnnexe->setFreeSale(false);
            unset($fournisseurPrestationAnnexePost->periodes);
        }

        // /* gestion des code passerelles
        /** @var SaisonCodePasserelle $saisonCodePasserelle */
        if (!empty($fournisseurPrestationAnnexePost->saisonCodePasserelles)) {
            foreach ($fournisseurPrestationAnnexePost->saisonCodePasserelles as $saisonCodePasserellePost) {
                $saisonCodePasserelle = $fournisseurPrestationAnnexe->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saisonCodePasserellePost) {
                    return $element->getSaison()->getId() == $saisonCodePasserellePost->saison;
                })->first();
                if (false === $saisonCodePasserelle) {
                    $saisonCodePasserelle = new SaisonCodePasserelle();
                    $fournisseurPrestationAnnexe->addSaisonCodePasserelle($saisonCodePasserelle);
                    $saisonCodePasserelle->setSaison($em->find(Saison::class, $saisonCodePasserellePost->saison));
                }
                if (!empty($saisonCodePasserellePost->codePasserelles)) {
                    foreach ($saisonCodePasserelle->getCodePasserelles() as $keyCodePasserelle => $codePasserelle) {
                        if (!empty($saisonCodePasserellePost->codePasserelles[$keyCodePasserelle])) {
                            $codePasserellePost = $saisonCodePasserellePost->codePasserelles[$keyCodePasserelle];
                            if (!empty($codePasserellePost->libelle)) {
                                $codePasserelle->setLibelle($codePasserellePost->libelle);
                            } else {
                                $saisonCodePasserelle->removeCodePasserelle($codePasserelle);
                                $em->remove($codePasserelle);
                            }
                        } else {
                            $saisonCodePasserelle->removeCodePasserelle($codePasserelle);
                            $em->remove($codePasserelle);
                        }
                    }
                    foreach ($saisonCodePasserellePost->codePasserelles as $keyCodePasserellePost => $codePasserellePost) {
                        if (!empty($codePasserelle = $saisonCodePasserelle->getCodePasserelles()->get($keyCodePasserellePost))) {
//                            // on modifie le libelle
//                            if (!empty($codePasserellePost->libelle)) {
//                                $codePasserelle->setLibelle($codePasserellePost->libelle);
//                                dump($keyCodePasserellePost);
//                            } else {
//                                dump($keyCodePasserellePost);
//                                $saisonCodePasserelle->removeCodePasserelle($codePasserelle);
//                            }
                        } else {
                            // on cré le code passerelle
                            if (!empty($codePasserellePost->libelle)) {
                                $codePasserelle = new CodePasserelle();
                                $saisonCodePasserelle->addCodePasserelle($codePasserelle);
                                $codePasserelle->setLibelle($codePasserellePost->libelle);
                            }
                        }
                    }
                } else {
                    // supprimer tout
                    foreach ($saisonCodePasserelle->getCodePasserelles() as $codePasserelle) {
                        $saisonCodePasserelle->removeCodePasserelle($codePasserelle);
                        $em->remove($codePasserelle);
                    }
                }
            }
        } else {
            // supprimer tout
            foreach ($fournisseurPrestationAnnexe->getSaisonCodePasserelles() as $saisonCodePasserelle) {
                $fournisseurPrestationAnnexe->removeSaisonCodePasserelle($saisonCodePasserelle);
            }
        }
        // fin gestion des code passerelles */

        // /* gestion fournisseur prestation annexe periode
        if (!empty($fournisseurPrestationAnnexePost->periodeIndisponibles)) {
            foreach ($fournisseurPrestationAnnexePost->periodeIndisponibles as $keyPeriodeIndisponiblePost => $periodeIndisponiblePost) {
                if (!empty($periodeIndisponiblePost)) {
                    if (empty($periodeIndisponible = $fournisseurPrestationAnnexe->getPeriodeIndisponibles()->get($keyPeriodeIndisponiblePost))) {
                        $periodeIndisponible = new FournisseurPrestationAnnexePeriodeIndisponible();
                        $fournisseurPrestationAnnexe->addPeriodeIndisponible($periodeIndisponible);
                    }
                    $periodeIndisponiblePost->dateDebut = str_replace('/', '-', $periodeIndisponiblePost->dateDebut);
                    $periodeIndisponiblePost->dateFin = str_replace('/', '-', $periodeIndisponiblePost->dateFin);
                    $periodeIndisponible->setDateDebut(new \DateTime($periodeIndisponiblePost->dateDebut))->setDateFin(new \DateTime($periodeIndisponiblePost->dateFin));
                }
            }
        }
        foreach ($fournisseurPrestationAnnexe->getPeriodeIndisponibles() as $keyPeriodeIndisponible => $periodeIndisponible) {
            if (!empty($fournisseurPrestationAnnexePost->periodeIndisponibles)) {
                $periodeIndisponiblePosts = new ArrayCollection($fournisseurPrestationAnnexePost->periodeIndisponibles);
                if (empty($periodeIndisponiblePosts->get($keyPeriodeIndisponible))) {
                    $fournisseurPrestationAnnexe->removePeriodeIndisponible($periodeIndisponible);
                    $em->remove($periodeIndisponible);
                }
            } else {
                $fournisseurPrestationAnnexe->removePeriodeIndisponible($periodeIndisponible);
                $em->remove($periodeIndisponible);
            }
        }
        // fin gestion fournisseur prestation annexe periodeIndisponible */

        // gestion des params
        if (!empty($fournisseurPrestationAnnexePost->params)) {
            foreach ($fournisseurPrestationAnnexePost->params as $keyParamPost => $paramPost) {
                if (!empty($paramPost)) {
                    if (empty($param = $fournisseurPrestationAnnexe->getParams()->get($keyParamPost))) {
                        $param = new FournisseurPrestationAnnexeParam();
                        $fournisseurPrestationAnnexe->addParam($param);
                    }

                    // gestion type
                    $param->setType($paramPost->type);

                    // /* gestion forfait quantite type
                    if ($param->getType() != Type::Forfait) {
                        $param->setForfaitQuantiteType();
                    } else {
                        $param->setForfaitQuantiteType($paramPost->forfaitQuantiteType);
                    }
                    // fin gestion forfait quantite type */

                    // gestion capacités
                    if (empty($paramPost->capacite->min) && empty($paramPost->capacite->max)) {
                        $param->setCapacite(null);
                    } else {
                        $capacite = $param->getCapacite();
                        if (empty($capacite)) {
                            $capacite = new FournisseurPrestationAnnexeCapacite();
                            $param->setCapacite($capacite);
                        }

                        $capacite->setMin($paramPost->capacite->min);
                        $capacite->setMax($paramPost->capacite->max);
                    }

                    // gestion duree séjour
                    if (empty($paramPost->dureeSejour->min) && empty($paramPost->dureeSejour->max)) {
                        $param->setDureeSejour(null);
                    } else {
                        $dureeSejour = $param->getDureeSejour();
                        if (empty($dureeSejour)) {
                            $dureeSejour = new FournisseurPrestationAnnexeDureeSejour();
                            $param->setDureeSejour($dureeSejour);
                        }

                        $dureeSejour->setMin($paramPost->dureeSejour->min);
                        $dureeSejour->setMax($paramPost->dureeSejour->max);
                    }

                    // gestion des traductions
                    foreach ($langues as $langue) {
                        $traduction = $param->getTraductions()->filter(function (
                            FournisseurPrestationAnnexeParamTraduction $element
                        ) use ($langue) {
                            return $element->getLangue() === $langue;
                        })->first();
                        if (false === $traduction) {
                            $traduction = new FournisseurPrestationAnnexeParamTraduction();
                            $param->addTraduction($traduction);
                            $traduction->setLangue($langue);
                        }
                        $traductionPosts = new ArrayCollection($paramPost->traductions);
                        $traductionPost = $traductionPosts->filter(function ($element) use ($langue) {
                            return $element->langue == $langue->getId();
                        })->first();
                        $traduction
                            ->setLibelleParam($traductionPost->libelleParam)
                            ->setLibelleFournisseurPrestationAnnexeParam($traductionPost->libelleFournisseurPrestationAnnexeParam);
                    }

                    // gestion tarifs
                    if (!empty($paramPost->tarifs)) {
                        $tarifPosts = $paramPost->tarifs;
                        /** @var PrestationAnnexeTarif $tarif */
                        foreach ($param->getTarifs() as $key => $tarif) {
                            if (empty($tarifPosts[$key])) {
                                $param->getTarifs()->removeElement($tarif);
                                $em->remove($tarif);
                            } else {
                                foreach ($tarif->getPeriodeValidites() as $keyPV => $periodeValidite) {
                                    if (empty($tarifPosts[$key]->periodeValidites[$keyPV])) {
                                        $param->getTarifs()->get($key)->getPeriodeValidites()->removeElement($periodeValidite);
                                        $em->remove($periodeValidite);
                                    }
                                }
                            }
                        }

                        foreach ($tarifPosts as $key => $tarifPost) {
                            if (!empty($tarifPost)) {
                                $tarif = $param->getTarifs()->get($key);
                                if (empty($tarif)) {
                                    $tarif = new PrestationAnnexeTarif();
                                    $param->addTarif($tarif);
                                }

                                $tarif
                                    ->setPrixCatalogue($tarifPost->prixCatalogue)
                                    ->setPrixPublic($tarifPost->prixPublic)
                                    ->setComMondofute($tarifPost->comMondofute)
                                    ->setPrixAchat($tarifPost->prixAchat);

                                if (!empty($tarifPost->periodeValidites)) {
                                    $periodeValiditePosts = $tarifPost->periodeValidites;
                                    foreach ($periodeValiditePosts as $keyPV => $periodeValiditePost) {
                                        if (!empty($periodeValiditePost)) {
                                            $periodeValidite = $tarif->getPeriodeValidites()->get($keyPV);
                                            if (empty($periodeValidite)) {
                                                $periodeValidite = new PeriodeValidite();
                                                $tarif->addPeriodeValidite($periodeValidite);
                                            }
                                            $periodeValidite
                                                ->setDateDebut(date_create_from_format('d/m/Y - H:i',
                                                    date($periodeValiditePost->dateDebut)))
                                                ->setDateFin(date_create_from_format('d/m/Y - H:i',
                                                    date($periodeValiditePost->dateFin)));
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($param->getTarifs() as $tarif) {
                            $param->getTarifs()->removeElement($tarif);
                            $em->remove($tarif);
                        }
                    }

                    // gestion mode affectation
                    $param->setModeAffectation($paramPost->modeAffectation);
                }
            }
        }

        foreach ($fournisseurPrestationAnnexe->getParams() as $keyParam => $param) {
            if (!empty($fournisseurPrestationAnnexePost->params)) {
                $paramPosts = new ArrayCollection($fournisseurPrestationAnnexePost->params);
                if (empty($paramPosts->get($keyParam))) {
                    $fournisseurPrestationAnnexe->getParams()->removeElement($param);
                    $em->remove($param);
                }
            } else {
                $fournisseurPrestationAnnexe->getParams()->removeElement($param);
                $em->remove($param);
            }
        }
        // fin gestion des params

        $affectationUnifieRemoves = new ArrayCollection();
//        foreach ($fournisseurPrestationAnnexe->getPrestationAnnexeFournisseurs() as $item)
//        {
//            if(!in_array($item->getSite()->getId() ,$sitePosts ))
//            {
//                $item->setActif(false);
//            }
//        }
//        foreach ($sitePosts as $sitePost)
//        {
//            $fournisseurPrestationAnnexeFournisseur
//        }
        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($fournisseurPrestationAnnexe->getParams() as $keyParam => $param) {

            // gestion FournisseurPrestationAnnexeAffectation
            /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
            $sitePosts = null;
            if (!empty($data->{'sites_' . $prestationAnnexeId . '_' . $keyParam})) {
                $sitePosts = $data->{'sites_' . $prestationAnnexeId . '_' . $keyParam};
            }

            switch ($param->getModeAffectation()) {
                case ModeAffectation::Fournisseur :
                    $prestationAnnexeFournisseurStations = $param->getPrestationAnnexeFournisseurs()->filter(function (
                        PrestationAnnexeFournisseur $element
                    ) {
                        return !empty($element->getStation());
                    });
                    /** @var PrestationAnnexeFournisseur $item */
                    foreach ($prestationAnnexeFournisseurStations as $item) {
                        $param->getPrestationAnnexeFournisseurs()->removeElement($item);
                        if (!$affectationUnifieRemoves->contains($item->getPrestationAnnexeFournisseurUnifie())) {
                            $affectationUnifieRemoves->add($item->getPrestationAnnexeFournisseurUnifie());
                        }
                    }
                    /** @var PrestationAnnexeStation $item */
                    foreach ($param->getPrestationAnnexeStations() as $item) {
                        $param->getPrestationAnnexeStations()->removeElement($item);
                        if (!$affectationUnifieRemoves->contains($item->getPrestationAnnexeStationUnifie())) {
                            $affectationUnifieRemoves->add($item->getPrestationAnnexeStationUnifie());
                        }
                    }

                    if (!empty($data->prestation_annexe_affectation_fournisseur)
                        and !empty($data->prestation_annexe_affectation_fournisseur[$prestationAnnexeId])
                    ) {
                        if (!empty($data->prestation_annexe_affectation_fournisseur[$prestationAnnexeId][$keyParam])) {
                            $fournisseurIds = $data->prestation_annexe_affectation_fournisseur[$prestationAnnexeId][$keyParam];
                            foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                                if (empty($fournisseurIds[$prestationAnnexeFournisseur->getFournisseur()->getId()]) or !$fournisseurIds[$prestationAnnexeFournisseur->getFournisseur()->getId()] != null) {
                                    $param->getPrestationAnnexeFournisseurs()->removeElement($prestationAnnexeFournisseur);
                                    if (!$affectationUnifieRemoves->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                        $affectationUnifieRemoves->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                                    }
                                }
                            }

                            foreach ($fournisseurIds as $fournisseurId => $siteIds) {
                                if ($siteIds != null) {
                                    if ($siteIds[1] != null) {
                                        foreach ($sites as $site) {
                                            if ($site->getCrm() != 1) {
                                                $fournisseurIds[$fournisseurId][$site->getId()] = "on";
                                            }
                                        }
                                    }
                                }
                            }

                            foreach ($fournisseurIds as $fournisseurId => $siteIds) {
                                if ($siteIds != null) {
                                    $prestationAnnexeFournisseur = $param->getPrestationAnnexeFournisseurs()->filter(function (
                                        PrestationAnnexeFournisseur $element
                                    ) use ($fournisseurId) {
                                        return ($element->getFournisseur()->getId() == $fournisseurId);
                                    })->first();
                                    if (false === $prestationAnnexeFournisseur) {
                                        $prestationAnnexeFournisseurUnifie = new PrestationAnnexeFournisseurUnifie();
                                        $em->persist($prestationAnnexeFournisseurUnifie);
                                        foreach ($sites as $site) {
                                            $prestationAnnexeFournisseur = new PrestationAnnexeFournisseur();
                                            $prestationAnnexeFournisseurUnifie->addPrestationAnnexeFournisseur($prestationAnnexeFournisseur);
                                            $param->addPrestationAnnexeFournisseur($prestationAnnexeFournisseur);
                                            $prestationAnnexeFournisseur
                                                ->setFournisseur($em->find(Fournisseur::class, $fournisseurId))
                                                ->setSite($site);
                                        }
                                    }
                                    foreach ($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie()->getPrestationAnnexeFournisseurs() as $prestationAnnexe) {
                                        $actif = true;
                                        if ((empty($siteIds[$prestationAnnexe->getSite()->getId()]) or
                                                !empty($siteIds[$prestationAnnexe->getSite()->getId()]) and
                                                $siteIds[$prestationAnnexe->getSite()->getId()] == null)
                                            or !in_array($prestationAnnexe->getSite()->getId(), $sitePosts)
                                        ) {
                                            $actif = false;
                                        }
                                        $prestationAnnexe->setActif($actif);
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                            $param->getPrestationAnnexeFournisseurs()->removeElement($prestationAnnexeFournisseur);
                            if (!$affectationUnifieRemoves->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                $affectationUnifieRemoves->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                            }
                        }
                    }
                    break;
                case ModeAffectation::Station :
                    $prestationAnnexeFournisseurs = $param->getPrestationAnnexeFournisseurs()->filter(function (
                        PrestationAnnexeFournisseur $element
                    ) {
                        return empty($element->getStation());
                    });
                    /** @var PrestationAnnexeFournisseur $item */
                    foreach ($prestationAnnexeFournisseurs as $item) {
                        $param->getPrestationAnnexeFournisseurs()->removeElement($item);
                        if (!$affectationUnifieRemoves->contains($item->getPrestationAnnexeFournisseurUnifie())) {
                            $affectationUnifieRemoves->add($item->getPrestationAnnexeFournisseurUnifie());
                        }
                    }

                    // fournisseurs
                    if (!empty($data->prestation_annexe_affectation_station_fournisseur)
                        and !empty($data->prestation_annexe_affectation_station_fournisseur[$prestationAnnexeId])
                    ) {
                        if (!empty($data->prestation_annexe_affectation_station_fournisseur[$prestationAnnexeId][$keyParam])) {
                            $stationUnifieIds = $data->prestation_annexe_affectation_station_fournisseur[$prestationAnnexeId][$keyParam];
                            foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                                if (empty($stationUnifieIds[$prestationAnnexeFournisseur->getStation()->getStationUnifie()->getId()][$prestationAnnexeFournisseur->getFournisseur()->getId()]) or $stationUnifieIds[$prestationAnnexeFournisseur->getStation()->getStationUnifie()->getId()][$prestationAnnexeFournisseur->getFournisseur()->getId()] == null) {
                                    $param->getPrestationAnnexeFournisseurs()->removeElement($prestationAnnexeFournisseur);
                                    if (!$affectationUnifieRemoves->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                        $affectationUnifieRemoves->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                                    }
                                }
                            }

                            if ($stationUnifieIds != null) {
                                foreach ($stationUnifieIds as $stationUnifieId => $fournisseurIds) {
                                    if ($fournisseurIds != null) {
                                        foreach ($fournisseurIds as $fournisseurId => $siteIds) {
                                            if ($siteIds != null) {
                                                if ($siteIds[1] != null) {
                                                    foreach ($sites as $site) {
                                                        if ($site->getCrm() != 1) {
                                                            $stationUnifieIds[$stationUnifieId][$fournisseurId][$site->getId()] = "on";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($stationUnifieIds != null) {
                                foreach ($stationUnifieIds as $stationUnifieId => $fournisseurIds) {
                                    if ($fournisseurIds != null) {
                                        foreach ($fournisseurIds as $fournisseurId => $siteIds) {
                                            if (!empty($siteIds)) {
                                                $prestationAnnexeFournisseur = $param->getPrestationAnnexeFournisseurs()->filter(function (
                                                    PrestationAnnexeFournisseur $element
                                                ) use ($fournisseurId, $stationUnifieId) {
                                                    return ($element->getFournisseur()->getId() == $fournisseurId and
                                                        $element->getStation()->getStationUnifie()->getId() == $stationUnifieId);
                                                })->first();
                                                if (false === $prestationAnnexeFournisseur) {
                                                    $prestationAnnexeFournisseurUnifie = new PrestationAnnexeFournisseurUnifie();
                                                    $em->persist($prestationAnnexeFournisseurUnifie);
                                                    foreach ($sites as $site) {
                                                        $prestationAnnexeFournisseur = new PrestationAnnexeFournisseur();
                                                        $prestationAnnexeFournisseurUnifie->addPrestationAnnexeFournisseur($prestationAnnexeFournisseur);
                                                        $param->addPrestationAnnexeFournisseur($prestationAnnexeFournisseur);
                                                        $prestationAnnexeFournisseur
                                                            ->setFournisseur($em->find(Fournisseur::class,
                                                                $fournisseurId))
                                                            ->setStation($em->getRepository(Station::class)->findOneBy(array(
                                                                'stationUnifie' => $stationUnifieId,
                                                                'site' => $site
                                                            )))
                                                            ->setSite($site);
                                                    }
                                                }
                                                foreach ($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie()->getPrestationAnnexeFournisseurs() as $prestationAnnexe) {
                                                    $actif = true;
                                                    if ((empty($siteIds[$prestationAnnexe->getSite()->getId()]) or
                                                            !empty($siteIds[$prestationAnnexe->getSite()->getId()]) and
                                                            $siteIds[$prestationAnnexe->getSite()->getId()] == null)
                                                        or !in_array($prestationAnnexe->getSite()->getId(), $sitePosts)
                                                    ) {
                                                        $actif = false;
                                                    }
                                                    $prestationAnnexe->setActif($actif);
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    } else {
                        foreach ($param->getPrestationAnnexeFournisseurs() as $prestationAnnexeFournisseur) {
                            $param->getPrestationAnnexeFournisseurs()->removeElement($prestationAnnexeFournisseur);
                            if (!$affectationUnifieRemoves->contains($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie())) {
                                $affectationUnifieRemoves->add($prestationAnnexeFournisseur->getPrestationAnnexeFournisseurUnifie());
                            }
                        }
                    }
                    // fin fournisseurs

                    // station
                    if (!empty($data->prestation_annexe_affectation_station)
                        and !empty($data->prestation_annexe_affectation_station[$prestationAnnexeId])
                    ) {
                        if (!empty($data->prestation_annexe_affectation_station[$prestationAnnexeId][$keyParam])) {
                            $stationUnifieIds = $data->prestation_annexe_affectation_station[$prestationAnnexeId][$keyParam];
                            /** @var PrestationAnnexeStation $prestationAnnexeStation */
                            foreach ($param->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                                if (empty($stationUnifieIds[$prestationAnnexeStation->getStation()->getStationUnifie()->getId()]) or !$stationUnifieIds[$prestationAnnexeStation->getStation()->getStationUnifie()->getId()] == null) {
                                    $param->getPrestationAnnexeStations()->removeElement($prestationAnnexeStation);
                                    if (!$affectationUnifieRemoves->contains($prestationAnnexeStation->getPrestationAnnexeStationUnifie())) {
                                        $affectationUnifieRemoves->add($prestationAnnexeStation->getPrestationAnnexeStationUnifie());
                                    }
                                }
                            }

                            foreach ($stationUnifieIds as $stationUnifieId => $siteIds) {
                                if ($siteIds != null) {
                                    if ($siteIds[1] != null) {
                                        foreach ($sites as $site) {
                                            if ($site->getCrm() != 1) {
                                                /** @var Site $site */
                                                $stationUnifieIds[$stationUnifieId][$site->getId()] = "on";
                                            }
                                        }
                                    }
                                }
                            }

                            if (!empty($stationUnifieIds)) {
                                foreach ($stationUnifieIds as $stationUnifieId => $siteIds) {
                                    if (!empty($siteIds)) {
                                        $prestationAnnexeStation = $param->getPrestationAnnexeStations()->filter(function (
                                            PrestationAnnexeStation $element
                                        ) use ($stationUnifieId) {
                                            return ($element->getStation()->getStationUnifie()->getId() == $stationUnifieId);
                                        })->first();
                                        if (false === $prestationAnnexeStation) {
                                            $prestationAnnexeStationUnifie = new PrestationAnnexeStationUnifie();
                                            $em->persist($prestationAnnexeStationUnifie);
                                            foreach ($sites as $site) {
                                                $prestationAnnexeStation = new PrestationAnnexeStation();
                                                $prestationAnnexeStationUnifie->addPrestationAnnexeStation($prestationAnnexeStation);
                                                $param->addPrestationAnnexeStation($prestationAnnexeStation);
                                                $prestationAnnexeStation
                                                    ->setStation($em->getRepository(Station::class)->findOneBy(array(
                                                        'stationUnifie' => $stationUnifieId,
                                                        'site' => $site
                                                    )))
                                                    ->setSite($site);
                                            }
                                        }
                                        foreach ($prestationAnnexeStation->getPrestationAnnexeStationUnifie()->getPrestationAnnexeStations() as $prestationAnnexe) {
                                            $actif = true;
                                            if ((empty($siteIds[$prestationAnnexe->getSite()->getId()]) or
                                                    !empty($siteIds[$prestationAnnexe->getSite()->getId()]) and
                                                    $siteIds[$prestationAnnexe->getSite()->getId()] == null)
                                                or !in_array($prestationAnnexe->getSite()->getId(), $sitePosts)
                                            ) {
                                                $actif = false;
                                            }
                                            $prestationAnnexe->setActif($actif);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        /** @var PrestationAnnexeStation $prestationAnnexeStation */
                        foreach ($param->getPrestationAnnexeStations() as $prestationAnnexeStation) {
                            $param->getPrestationAnnexeStations()->removeElement($prestationAnnexeStation);
                            if (!$affectationUnifieRemoves->contains($prestationAnnexeStation->getPrestationAnnexeStationUnifie())) {
                                $affectationUnifieRemoves->add($prestationAnnexeStation->getPrestationAnnexeStationUnifie());
                            }
                        }
                    }
                    // fin station

                    break;
                default:
                    break;
            }


            // *** hebergement ***
            if (!empty($data->prestation_annexe_affectation_hebergement)
                and !empty($data->prestation_annexe_affectation_hebergement[$prestationAnnexeId]
                    and !empty($data->prestation_annexe_affectation_hebergement[$prestationAnnexeId][$keyParam])
                )
            ) {
//                if (!empty($data->prestation_annexe_affectation_hebergement[$prestationAnnexeId][$keyParam])) {
                    $fournisseurIds = $data->prestation_annexe_affectation_hebergement[$prestationAnnexeId][$keyParam];
                    /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
                    foreach ($param->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                        if (empty($fournisseurIds[$prestationAnnexeHebergement->getFournisseur()->getId()])
                            and empty($fournisseurIds[$prestationAnnexeHebergement->getFournisseur()->getId()][$prestationAnnexeHebergement->getHebergement()->getHebergementUnifie()->getId()])
                        ) {
                            $param->getPrestationAnnexeHebergements()->removeElement($prestationAnnexeHebergement);
                            if (!$affectationUnifieRemoves->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                                $affectationUnifieRemoves->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
                            }
                        }
                    }

                    foreach ($fournisseurIds as $fournisseurId => $hebergementUnfieIds) {
                        if (!empty($hebergementUnfieIds)) {
                            foreach ($hebergementUnfieIds as $hebergementUnfieId => $siteIds) {
                                if ($siteIds != null) {
                                    if ($siteIds[1] != null) {
                                        foreach ($sites as $site) {
                                            if ($site->getCrm() != 1) {
                                                $fournisseurIds[$fournisseurId][$hebergementUnfieId][$site->getId()] = "on";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach ($fournisseurIds as $fournisseurId => $hebergementUnfieIds) {
                        if (!empty($hebergementUnfieIds)) {
                            foreach ($hebergementUnfieIds as $hebergementUnfieId => $siteIds) {
                                if (!empty($siteIds)) {
//                                dump($param->getPrestationAnnexeHebergements());die;
                                    $prestationAnnexeHebergement = $param->getPrestationAnnexeHebergements()->filter(function (
                                        PrestationAnnexeHebergement $element
                                    ) use ($fournisseurId, $hebergementUnfieId) {
                                        return ($element->getFournisseur()->getId()
                                            == $fournisseurId and
                                            $element->getHebergement()->getHebergementUnifie()->getId() ==
                                            $hebergementUnfieId);
                                    })->first();
                                    if (false === $prestationAnnexeHebergement) {
                                        $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                                        $em->persist($prestationAnnexeHebergementUnifie);
                                        foreach ($sites as $site) {
                                            $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                                            $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                                            $param->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                                            $prestationAnnexeHebergement
                                                ->setFournisseur($em->find(Fournisseur::class, $fournisseurId))
                                                ->setHebergement($em->getRepository(Hebergement::class)->findOneBy(array(
                                                    'hebergementUnifie' => $hebergementUnfieId,
                                                    'site' => $site
                                                )))
                                                ->setSite($site);
                                        }
                                    }

                                    foreach ($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie()->getPrestationAnnexeHebergements() as $prestationAnnexe) {
                                        $actif = true;
                                        if ((empty($siteIds[$prestationAnnexe->getSite()->getId()]) or
                                                !empty($siteIds[$prestationAnnexe->getSite()->getId()]) and
                                                $siteIds[$prestationAnnexe->getSite()->getId()] == null)
                                            or !in_array($prestationAnnexe->getSite()->getId(), $sitePosts)
                                        ) {
                                            $actif = false;
                                        }
                                        $prestationAnnexe->setActif($actif);
                                    }

                                    $this->gestionPrestationAnnexeLogement($prestationAnnexeHebergement, $param,
                                        $fournisseurId, $sitePosts, $fournisseurIds);

                                }
                            }
                        }
                    }
//                }

            } else {
                foreach ($param->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                    $param->getPrestationAnnexeHebergements()->removeElement($prestationAnnexeHebergement);
                    if (!$affectationUnifieRemoves->contains($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                        $affectationUnifieRemoves->add($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());

                        $logementUnifies = $em->getRepository(LogementUnifie::class)->findByFournisseurHebergement($prestationAnnexeHebergement->getFournisseur()->getId(),
                            $prestationAnnexeHebergement->getHebergement()->getHebergementUnifie()->getId());
                        foreach ($logementUnifies as $logementUnifie) {
                            $prestationAnnexeLogementUnifie = $em->getRepository(PrestationAnnexeLogementUnifie::class)->findByCriteria($param->getId(),
                                $logementUnifie->getId());
                            if (!empty($prestationAnnexeLogementUnifie)) {
                                $em->remove($prestationAnnexeLogementUnifie);
                                $affectationUnifieRemoves->add($prestationAnnexeLogementUnifie);
                            }
                        }
                    }
                }
            }
            // *** fin hebergement
        }

        foreach ($affectationUnifieRemoves as $affectationUnifieRemove) {
            $em->remove($affectationUnifieRemove);
        }
//        die;

        $em->persist($fournisseurPrestationAnnexe);
        $em->flush();

        $this->mAJSites($fournisseur);

        if ($new) {
            $this->gestionPromotionFournisseurPrestationAnnexe($fournisseur, $fournisseurPrestationAnnexe);
        }

        if (empty($data)) {
            return new Response(0);
        }
        return new Response($fournisseurPrestationAnnexe->getId());
    }

    private function gestionPrestationAnnexeLogement(
        PrestationAnnexeHebergement $prestationAnnexeHebergement,
//                                                     FournisseurPrestationAnnexe $prestationAnnex,
        FournisseurPrestationAnnexeParam $param,
        $prestationAnnexeHebergementFournisseurId,
        $postSitesAEnregistrer,
        $prestationAnnexeHebergementsPosts
    )
    {
        $em = $this->getDoctrine()->getManager();

        $logementUnifies = $em->getRepository(LogementUnifie::class)->findByFournisseurHebergement($prestationAnnexeHebergement->getFournisseur()->getId(),
            $prestationAnnexeHebergement->getHebergement()->getHebergementUnifie()->getId());

        /** @var Logement $logement */
        /** @var LogementUnifie $logementUnifie */
        foreach ($logementUnifies as $logementUnifie) {
            $prestationAnnexeLogementUnifie = $em->getRepository(PrestationAnnexeLogementUnifie::class)->findByCriteria($param->getId(),
                $logementUnifie->getId());
            if (empty($prestationAnnexeLogementUnifie)) {
                $prestationAnnexeLogementUnifie = new PrestationAnnexeLogementUnifie();
                $em->persist($prestationAnnexeLogementUnifie);
                /** @var LogementUnifie $logementUnifie */
                /** @var Logement $logement */
                foreach ($logementUnifie->getLogements() as $logementPrestationAnnexeLogement) {
                    $prestationAnnexeLogement = new PrestationAnnexeLogement();
                    $prestationAnnexeLogementUnifie->addPrestationAnnexeLogement($prestationAnnexeLogement);
                    $prestationAnnexeLogement
                        ->setLogement($logementPrestationAnnexeLogement)
                        ->setSite($logementPrestationAnnexeLogement->getSite());
                }
            }

            foreach ($prestationAnnexeLogementUnifie->getPrestationAnnexeLogements() as $prestationAnnexeLogement) {
                $param
                    ->addPrestationAnnexeLogement($prestationAnnexeLogement);
            }

            /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
            foreach ($prestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie()->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {

                $actif = false;

                $prestationAnnexeLogement = $prestationAnnexeLogementUnifie->getPrestationAnnexeLogements()->filter(function (
                    PrestationAnnexeLogement $element
                ) use ($prestationAnnexeHebergement) {
                    return $element->getSite() === $prestationAnnexeHebergement->getSite();
                })->first();

                $capacite = $prestationAnnexeHebergement->getParam()->getCapacite();
                /** @var PrestationAnnexeLogement $prestationAnnexeLogement */

                if (!empty($prestationAnnexeHebergementsPosts[$prestationAnnexeHebergementFournisseurId][$prestationAnnexeHebergement->getHebergement()->getHebergementUnifie()->getId()][$prestationAnnexeHebergement->getSite()->getId()])
                    and
                    in_array($prestationAnnexeHebergement->getSite()->getId(), $postSitesAEnregistrer)
                    and
                    (empty($capacite) or (!empty($capacite) and $capacite->getMin() <= $prestationAnnexeLogement->getLogement()->getCapacite() and $prestationAnnexeLogement->getLogement()->getCapacite() <= $capacite->getMax()))
                ) {
                    $actif = true;
                }

                $prestationAnnexeLogement->setActif($actif);
                $em->persist($prestationAnnexeHebergement);
            }
        }
    }

    private function gestionPromotionFournisseurPrestationAnnexe(
        Fournisseur $fournisseur,
        FournisseurPrestationAnnexe $fournisseurPrestationAnnexe
    )
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mondofute_promotion:promotion_fournisseur_prestation_annexe_command',
            'fournisseurId' => $fournisseur->getId(),
            'fournisseurPrestationAnnexeId' => $fournisseurPrestationAnnexe->getId(),
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * @param Fournisseur $fournisseurSite
     * @param Fournisseur $fournisseur
     * @param EntityManager $emSite
     */
    private function gestionCodePasserelleSite($fournisseurSite, $fournisseur, $emSite)
    {
        /** @var CodePasserelle $codePasserelle */
        /** @var CodePasserelle $codePasserelleSite */
        /** @var SaisonCodePasserelle $saisonCodePasserelleSite */
        /** @var SaisonCodePasserelle $saisonCodePasserelle */
        /** @var FournisseurPrestationAnnexe $prestationAnnex */
        /** @var FournisseurPrestationAnnexe $prestationAnnexSite */
        foreach ($fournisseurSite->getPrestationAnnexes() as $keyPrestationAnnexe => $prestationAnnexSite) {
            foreach ($prestationAnnexSite->getSaisonCodePasserelles() as $saisonCodePasserelleSite) {
                $saisonCodePasserelle = $fournisseur->getPrestationAnnexes()->get($keyPrestationAnnexe)->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saisonCodePasserelleSite) {
                    return $element->getId() == $saisonCodePasserelleSite->getId();
                })->first();
                if (false === $saisonCodePasserelle) {
                    $prestationAnnexSite->removeSaisonCodePasserelle($saisonCodePasserelleSite);
                    $emSite->remove($saisonCodePasserelleSite);
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
        }
        foreach ($fournisseur->getPrestationAnnexes() as $keyPrestationAnnexe => $prestationAnnex) {
            foreach ($prestationAnnex->getSaisonCodePasserelles() as $saisonCodePasserelle) {
                $saisonCodePasserelleSite = $fournisseurSite->getPrestationAnnexes()->get($keyPrestationAnnexe)->getSaisonCodePasserelles()->filter(function (SaisonCodePasserelle $element) use ($saisonCodePasserelle) {
                    return $element->getId() == $saisonCodePasserelle->getId();
                })->first();
                if (false === $saisonCodePasserelleSite) {
                    $saisonCodePasserelleSite = new SaisonCodePasserelle();
                    $saisonCodePasserelleSite->setId($saisonCodePasserelle->getId());
                    $metadata = $emSite->getClassMetadata(get_class($saisonCodePasserelleSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    $fournisseurSite->getPrestationAnnexes()->get($keyPrestationAnnexe)->addSaisonCodePasserelle($saisonCodePasserelleSite);
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
    }

}
