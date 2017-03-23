<?php

namespace Mondofute\Bundle\CommandeBundle\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\ClientBundle\Controller\ClientController;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\ClientBundle\Entity\ClientUser;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\CommandeBundle\Entity\Commande;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLigne;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLignePrestationAnnexe;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLigneSejour;
use Mondofute\Bundle\CommandeBundle\Entity\RemiseCodePromo;
use Mondofute\Bundle\CommandeBundle\Entity\RemiseDecote;
use Mondofute\Bundle\CommandeBundle\Entity\RemisePromotion;
use Mondofute\Bundle\CommandeBundle\Entity\SejourNuite;
use Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseurPrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteLogement;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteLogementPeriode;
use Mondofute\Bundle\DecoteBundle\Entity\Type;
use Mondofute\Bundle\DecoteBundle\Entity\TypePeriodeSejour as DecoteTypePeriodeSejour;
use Mondofute\Bundle\DecoteBundle\Entity\TypePeriodeValidite as DecoteTypePeriodeValidite;
use Mondofute\Bundle\DecoteBundle\Entity\TypeRemise;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLitigeDossier;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeStatutDossier;
use Mondofute\Bundle\CommandeBundle\Entity\LitigeDossier;
use Mondofute\Bundle\CommandeBundle\Entity\StatutDossier;
use Mondofute\Bundle\CommandeBundle\Form\CommandeType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogement;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogementPeriode;
use Mondofute\Bundle\PromotionBundle\Entity\TypePeriodeSejour as PromotionTypePeriodeSejour;
use Mondofute\Bundle\PromotionBundle\Entity\TypePeriodeValidite as PromotionTypePeriodeValidite;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Nucleus\ContactBundle\Entity\Civilite;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\Email;
use Nucleus\MoyenComBundle\Entity\TelFixe;
use Nucleus\MoyenComBundle\Entity\TelMobile;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commande controller.
 *
 */
class CommandeController extends Controller
{
    /**
     * Lists all Commande entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteCommandeBundle:Commande')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'commande_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteCommandeBundle:Commande')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteCommande/commande/index.html.twig', array(
            'commandes' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new commande entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $commande = new Commande();
        $form = $this->createForm(new CommandeType(null, null), $commande);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);
//        gestion du premier formulaire client formulaire
        /** @var Client $client */
        if (empty($client = $commande->getClients()->first())) {

//        dump($client);
//        die;
            $client = new Client();
            $client->addMoyenCom(new Adresse())
                ->addMoyenCom(new TelFixe())
                ->addMoyenCom(new TelMobile())
                ->addMoyenCom(new Email())
                ->addMoyenCom(new Email());
            $clientUser = new ClientUser();
            $clientUser->setClient($client);
            $client->setClientUser($clientUser);
        } else {
            $clientUser = $client->getClientUser();
        }
        $controllerClient = new ClientController();
        $controllerClient->setContainer($this->container);
        $formClient = $controllerClient->createForm('Mondofute\Bundle\ClientBundle\Form\ClientClientUserType', $client);
        $formClient->handleRequest($request);
//        fin gestion du formulaire client
        if ($form->isSubmitted() && $form->isValid() && $formClient->isSubmitted() && $formClient->isValid()) {
            $this->gestionNumeroCommande($commande);

            $em = $this->getDoctrine()->getManager();
//            if($originalStatutDossier->getStatutDossier()->getId() != $request->request->get('mondofute_bundle_commandebundle_commande')['statutDossier']){
            $commandeStatutDossier = new CommandeStatutDossier();
            $commandeStatutDossier->setCommande($commande);
            $commandeStatutDossier->setDateHeure(new \DateTime());
            $commandeStatutDossier->setStatutDossier($em->getRepository(StatutDossier::class)->find($request->request->get('mondofute_bundle_commandebundle_commande')['statutDossier']));
            $commande->addCommandeStatutDossier($commandeStatutDossier);
            if ($request->request->get('mondofute_bundle_commandebundle_commande')['litigeDossier'] != '') {
                $commandeLitigeDossier = new CommandeLitigeDossier();
                $commandeLitigeDossier->setCommande($commande);
                $commandeLitigeDossier->setLitigeDossier($em->getRepository(LitigeDossier::class)->find($request->request->get('mondofute_bundle_commandebundle_commande')['litigeDossier']));
                $commandeLitigeDossier->setDateHeure(new \DateTime());
                $commande->addCommandeLitigeDossier($commandeLitigeDossier);
            }
//            }
//            gestion du client
            $clientUser->setEnabled(true);
            foreach ($client->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateCreation();
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $clientUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }
            $client->setDateCreation();
            if (!$controllerClient->loginExist($clientUser)) {
//                gestion du client
                if (!empty($client->getId())) {
                    $controllerClient->majSites($clientUser);
                } else {
                    $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                    $controllerClient->newSites($clientUser, $client, $sites);
                    $controllerClient->dupliquerMoyenComs($client, $em);
                }
                $em->persist($client);
//            fin de la gestion du client
                if (count($commande->getClients()) <= 0) {
                    $commande->addClient($client);
                }
//                dump($commande->getClients()->first());die;
                $em->persist($commande);
                $em->flush();
                /*
                 * TODO: gérer la création de nouveau client dans TOUTES LES BASES
                 */
                $this->copieVersSites($commande);

                $this->addFlash('success', 'Commande créé avec succès.');
                return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
            }
        }

        return $this->render('@MondofuteCommande/commande/new.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
            'langues' => $langues,
            'formClient' => $formClient->createView()
        ));
    }

    /**
     * @param Commande $commande
     */
    public function gestionNumeroCommande($commande)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new DateTime();
        $dateNumeroCommande = $date->format('Ymd');
        $countCommande = $em->getRepository(Commande::class)->countCommandeForDay($dateNumeroCommande);
        $commande->setNumCommande($dateNumeroCommande . (intval($countCommande) + 1));
    }

    /**
     * @param Commande $commande
     */
    function copieVersSites($commande)
    {
        /** @var EntityManager $emSite */
        $emSite = $this->getDoctrine()->getManager($commande->getSite()->getLibelle());
        $commandeSite = $emSite->find(Commande::class, $commande->getId());
        if (empty($commandeSite)) {
            $commandeSite = new Commande();
            $commandeSite->setId($commande->getId());
            $metadata = $emSite->getClassMetadata(get_class($commandeSite));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        }
        $commandeSite
            ->setSite($emSite->find(Site::class, $commande->getSite()))
            ->setDateCommande($commande->getDateCommande())
            ->setNumCommande($commande->getNumCommande());
//        gestion litiges
        $this->gestionLitigeSite($commande, $commandeSite, $emSite);
//        gestion statut
        $this->gestionStatutSite($commande, $commandeSite, $emSite);
        // /* *** gestion clients ***
        $this->gestionClientSite($commande, $commandeSite, $emSite);
        // *** fin gestion clients *** */

        // /* *** gestion commande ligne ***
        $this->gestionCommandeLigneSite($commande, $commandeSite, $emSite);
        // *** fin gestion commande ligne *** */

        // /* *** gestion commande ligne prestation annexe sejour ***
        $this->gestionCommandeLignePrestationAnnexeSejourSite($commande, $commandeSite, $emSite);
        // *** gestion commande ligne prestation annexe sejour *** */

        $emSite->persist($commandeSite);
        $emSite->flush();
    }

    public function gestionLitigeSite($commande, $commandeSite, $emSite)
    {
        /** @var EntityManager $emSite */
        /** @var Commande $commandeSite */
        /** @var Commande $commande */
        /** @var CommandeLitigeDossier $commandeLitige */
//        gestion de la suppression distante des commande litige
        foreach ($commandeSite->getCommandeLitigeDossiers() as $commandeLitigeSite) {
            $commandeLitige = $commande->getCommandeLitigeDossiers()->filter(function (CommandeLitigeDossier $element
            ) use ($commandeLitigeSite) {
                return $element->getId() == $commandeLitigeSite->getId();
            })->first();
            if (false === $commandeLitige) {
                $commandeSite->removeCommandeLitigeDossier($commandeLitigeSite);
            }
        }
        // ajout commande litige avec forcage de l'id
        foreach ($commande->getCommandeLitigeDossiers() as $commandeLitige) {
            $commandeLitigeSite = $commandeSite->getCommandeLitigeDossiers()->filter(function (
                CommandeLitigeDossier $element
            ) use ($commandeLitige) {
                return $element->getId() == $commandeLitige->getId();
            })->first();
            if (false === $commandeLitigeSite) {
                $commandeLitigeSite = new CommandeLitigeDossier();
                $commandeLitigeSite->setDateHeure($commandeLitige->getDateHeure())->setLitigeDossier($emSite->find(LitigeDossier::class,
                    $commandeLitige->getLitigeDossier()))->setCommande($commandeSite)->setId($commandeLitige->getId());

                $metadata = $emSite->getClassMetadata(get_class($commandeLitigeSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $commandeSite->addCommandeLitigeDossier($commandeLitigeSite);
            }
        }
    }

    public function gestionStatutSite($commande, $commandeSite, $emSite)
    {
        /** @var EntityManager $emSite */
        /** @var Commande $commandeSite */
        /** @var Commande $commande */
        /** @var CommandeStatutDossier $commandeStatut */
//        gestion de la suppression distante des commande litige
        foreach ($commandeSite->getCommandeStatutDossiers() as $commandeStatutSite) {
            $commandeStatut = $commande->getCommandeStatutDossiers()->filter(function (CommandeStatutDossier $element
            ) use ($commandeStatutSite) {
                return $element->getId() == $commandeStatutSite->getId();
            })->first();
            if (false === $commandeStatut) {
                $commandeSite->removeCommandeStatutDossier($commandeStatutSite);
            }
        }
        // ajout commande litige avec forcage de l'id
        foreach ($commande->getCommandeStatutDossiers() as $commandeStatut) {
            $commandeStatutSite = $commandeSite->getCommandeStatutDossiers()->filter(function (
                CommandeStatutDossier $element
            ) use ($commandeStatut) {
                return $element->getId() == $commandeStatut->getId();
            })->first();
            if (false === $commandeStatutSite) {
                $commandeStatutSite = new CommandeStatutDossier();
                $commandeStatutSite->setDateHeure($commandeStatut->getDateHeure())->setStatutDossier($emSite->find(StatutDossier::class,
                    $commandeStatut->getStatutDossier()))->setCommande($commandeSite)->setId($commandeStatut->getId());

                $metadata = $emSite->getClassMetadata(get_class($commandeStatutSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $commandeSite->addCommandeStatutDossier($commandeStatutSite);
            }
        }
    }

    /**
     * @param Commande $commande
     * @param Commande $commandeSite
     * @param EntityManager $emSite
     */
    private function gestionClientSite($commande, $commandeSite, $emSite)
    {
        /** @var Client $clientSite */
        /** @var Client $client */
        // suppression clients
        foreach ($commandeSite->getClients() as $clientSite) {
            $client = $commande->getClients()->filter(function (Client $element) use ($clientSite) {
                return $element->getId() == $clientSite->getId();
            })->first();
            if (false === $client) {
                $commandeSite->removeClient($clientSite);
            }
        }
        // ajout clients
        foreach ($commande->getClients() as $client) {
            $clientSite = $commandeSite->getClients()->filter(function (Client $element) use ($client) {
                return $element->getId() == $client->getId();
            })->first();
            if (false === $clientSite) {
                $commandeSite->addClient($emSite->find(Client::class, $client));
            }
        }
    }

    /**
     * @param Commande $commande
     * @param Commande $commandeSite
     * @param EntityManager $emSite
     */
    private function gestionCommandeLigneSite($commande, $commandeSite, $emSite)
    {
        /** @var CommandeLigne $commandeLigneSite */
        /** @var CommandeLigne $commandeLigne */
        // suppression commandeLignes
        foreach ($commandeSite->getCommandeLignes() as $commandeLigneSite) {
            $commandeLigne = $commande->getCommandeLignes()->filter(function (CommandeLigne $element) use (
                $commandeLigneSite
            ) {
                return $element->getId() == $commandeLigneSite->getId();
            })->first();
            if (false === $commandeLigne) {
                $commandeSite->removeCommandeLigne($commandeLigneSite);
                $emSite->remove($commandeLigneSite);
            }
        }
        // ajout commandeLignes
        foreach ($commande->getCommandeLignes() as $commandeLigne) {
            $commandeLigneSite = $commandeSite->getCommandeLignes()->filter(function (CommandeLigne $element) use (
                $commandeLigne
            ) {
                return $element->getId() == $commandeLigne->getId();
            })->first();
            if (false === $commandeLigneSite) {
                $oReflectionClass = new ReflectionClass($commandeLigne);
                $ClassParent = $oReflectionClass->getParentClass()->getName();
                if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour'
                    || $oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneRemise'
                ) {
                    $oReflectionClassParent = new ReflectionClass($ClassParent);
                    $metadata = $emSite->getClassMetadata($oReflectionClassParent->getParentClass()->getName());
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }
                $Class = get_class($commandeLigne);
                $commandeLigneSite = new $Class();
                $commandeLigneSite->setId($commandeLigne->getId());
                $metadata = $emSite->getClassMetadata($ClassParent);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata = $emSite->getClassMetadata($Class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $commandeSite->addCommandeLigne($commandeLigneSite);
            }
//            $commandeLigneSite->setMontant($commandeLigne->getMontant());
            $oReflectionClass = new ReflectionClass($commandeLigneSite);
            $commandeLigneSite
                ->setPrixCatalogue($commandeLigne->getPrixCatalogue())
                ->setPrixVente($commandeLigne->getPrixVente())
                ->setPrixAchat($commandeLigne->getPrixAchat())
                ->setQuantite($commandeLigne->getQuantite())
                ->setDateAchat($commandeLigne->getDateAchat())
                ->setDatePaiement($commandeLigne->getDatePaiement());
            switch ($oReflectionClass->getShortName()) {
                case 'SejourPeriode':
                    /** @var SejourPeriode $commandeLigneSite */
                    /** @var SejourPeriode $commandeLigne */
                    $commandeLigneSite
                        ->setPeriode($emSite->find(Periode::class, $commandeLigne->getPeriode()))
                        ->setNbParticipants($commandeLigne->getNbParticipants())
                        ->setLogement($emSite->getRepository(Logement::class)->findOneBy(['logementUnifie' => $commandeLigne->getLogement()->getLogementUnifie()]));
                    break;
                case 'SejourNuite':
                    /** @var SejourNuite $commandeLigneSite */
                    /** @var SejourNuite $commandeLigne */
                    $commandeLigneSite
                        ->setLogement($emSite->getRepository(Logement::class)->findOneBy(['logementUnifie' => $commandeLigne->getLogement()->getLogementUnifie()]));
                    break;
                case 'CommandeLignePrestationAnnexe':
                    /** @var CommandeLignePrestationAnnexe $commandeLigneSite */
                    /** @var CommandeLignePrestationAnnexe $commandeLigne */
                    $commandeLigneSite
                        ->setStation($emSite->getRepository(Station::class)->findOneBy(['stationUnifie' => $commandeLigne->getStation()->getStationUnifie()]))
                        ->setDateDebut($commandeLigne->getDateDebut())
                        ->setDateFin($commandeLigne->getDateFin())
                        ->setFournisseurPrestationAnnexeParam($emSite->find(FournisseurPrestationAnnexeParam::class, $commandeLigne->getFournisseurPrestationAnnexeParam()));
                    break;
                case 'RemiseCodePromo':
                    /** @var RemiseCodePromo $commandeLigneSite */
                    /** @var RemiseCodePromo $commandeLigne */
                    $commandeLigneSite
                        ->setCodePromo($emSite->getRepository(CodePromo::class)->findOneBy(['codePromoUnifie' => $commandeLigne->getCodePromo()->getCodePromoUnifie()]));
                    break;
                case 'RemiseDecote':
                    /** @var RemiseDecote $commandeLigneSite */
                    /** @var RemiseDecote $commandeLigne */
                    $commandeLigneSite
                        ->setDecote($emSite->getRepository(Decote::class)->findOneBy(['decoteUnifie' => $commandeLigne->getDecote()->getDecoteUnifie()]));
                    break;
                case 'RemisePromotion':
                    /** @var RemisePromotion $commandeLigneSite */
                    /** @var RemisePromotion $commandeLigne */
                    $commandeLigneSite
                        ->setPromotion($emSite->getRepository(Promotion::class)->findOneBy(['promotionUnifie' => $commandeLigne->getPromotion()->getPromotionUnifie()]));
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param Commande $commande
     * @param Commande $commandeSite
     * @param EntityManager $emSite
     */
    private function gestionCommandeLignePrestationAnnexeSejourSite($commande, $commandeSite, $emSite)
    {
        /** @var CommandeLigne $commandeLigneSite */
        /** @var CommandeLigne $commandeLigne */
        $commandeLignePrestationAnnexeSejourSites = new ArrayCollection();
        $commandeLignePrestationAnnexeSejours = new ArrayCollection();
        foreach ($commandeSite->getCommandeLignes() as $commandeLigneSite) {
            $oReflectionClass = new ReflectionClass($commandeLigneSite);
            if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                /** @var CommandeLigneSejour $commandeLigneSite */
                foreach ($commandeLigneSite->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                    $commandeLignePrestationAnnexeSejourSites->add($commandeLignePrestationAnnex);
                }
            }
        }
        foreach ($commande->getCommandeLignes() as $commandeLigne) {
            $oReflectionClass = new ReflectionClass($commandeLigne);
            if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                /** @var CommandeLigneSejour $commandeLigne */
                foreach ($commandeLigne->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                    $commandeLignePrestationAnnexeSejours->add($commandeLignePrestationAnnex);
                }
            }
        }

        /** @var CommandeLignePrestationAnnexe $commandeLigne */
        /** @var CommandeLignePrestationAnnexe $commandeLigneSite */
        // suppression commandeLignePrestationAnnexeSejours
        foreach ($commandeLignePrestationAnnexeSejourSites as $commandeLigneSite) {
            $commandeLigne = $commandeLignePrestationAnnexeSejours->filter(function (
                CommandeLignePrestationAnnexe $element
            ) use ($commandeLigneSite) {
                return $element->getId() == $commandeLigneSite->getId();
            })->first();
            if (false === $commandeLigne) {
                $commandeLigneSite->getCommandeLigneSejour()->removeCommandeLignePrestationAnnex($commandeLigneSite);
                $emSite->remove($commandeLigneSite);
            }
        }
        // ajout commandeLignePrestationAnnexeSejours
        foreach ($commandeLignePrestationAnnexeSejours as $commandeLigne) {
            $commandeLigneSite = $commandeLignePrestationAnnexeSejourSites->filter(function (
                CommandeLignePrestationAnnexe $element
            ) use ($commandeLigne) {
                return $element->getId() == $commandeLigne->getId();
            })->first();
            if (false === $commandeLigneSite) {
                /** @var CommandeLigneSejour $commandeLigneSejourSite */
                $commandeLigneSejourSite = $commandeSite->getCommandeLignes()->filter(function (CommandeLigne $element
                ) use ($commandeLigne) {
                    return $element->getId() == $commandeLigne->getCommandeLigneSejour()->getId();
                })->first();
                $oReflectionClass = new ReflectionClass($commandeLigne);
                $ClassParent = $oReflectionClass->getParentClass()->getName();
                $Class = get_class($commandeLigne);
                $commandeLigneSite = new $Class();
                $commandeLigneSite->setId($commandeLigne->getId());
                $metadata = $emSite->getClassMetadata($ClassParent);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata = $emSite->getClassMetadata($Class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $commandeLigneSejourSite->addCommandeLignePrestationAnnex($commandeLigneSite);
                $commandeLigneSite
                    ->setDateDebut($commandeLigne->getDateDebut())
                    ->setDateFin($commandeLigne->getDateFin());
            }
        }
    }

    public function ajoutClientAction(Request $request)
    {
//        récupère l'indice en preparation pour le multi-client
        $indice = intval($request->query->get('indice'), 10);
        $em = $this->getDoctrine()->getManager();
        if (empty($request->query->get('id'))) {
            $client = new Client();
            $client->addMoyenCom(new Adresse())
                ->addMoyenCom(new TelFixe())
                ->addMoyenCom(new TelMobile())
                ->addMoyenCom(new Email())
                ->addMoyenCom(new Email());
            $clientUser = new ClientUser();
            $clientUser->setClient($client);
            $client->setClientUser($clientUser);
        } else {
            $client = $em->getRepository(Client::class)->find($request->query->get('id'));
        }
//        création du formType de la commande
        $form = $this->createForm('Mondofute\Bundle\ClientBundle\Form\ClientClientUserType', $client);
        return $this->render('@MondofuteCommande/commande/fiche-client.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    public function getPeriodesByLogementAction($id)
    {
        $today = new DateTime(date('Y-m-d'));

        /** @var LogementPeriodeLocatif $logementPeriodeLocatif */
        $em = $this->getDoctrine()->getManager();
        $logement = $em->find(Logement::class, $id);
        $logementPeriodeLocatifs = $logement->getLogementPeriodeLocatifs()->filter(function (LogementPeriodeLocatif $element) use ($today) {
            return $element->getPrixPublic() > 0 and $element->getPeriode()->getDebut() >= $today;
        });
        $periodes = new ArrayCollection();
        foreach ($logementPeriodeLocatifs as $logementPeriodeLocatif) {
            $periodes->add($logementPeriodeLocatif->getPeriode());
        }

        return $this->render('@MondofuteCommande/commande/options_logement_periodes.html.twig', array(
            'periodes' => $periodes,
            'logementPeriodeLocatifsStockNotEmpty' => $logement->getLogementPeriodeLocatifsStockNotEmpty()
        ));
    }

    public function getPrestationAnnexeExterneAction($dateDebut, $dateFin, $fournisseurId, $typeId, $stationId)
    {
        $em = $this->getDoctrine()->getManager();
        $prestationAnnexeExternes = $em->getRepository(FournisseurPrestationAnnexeParam::class)->findPrestationAnnexeExterne($dateDebut, $dateFin, $fournisseurId, $typeId, $stationId);

        $tarifs = new ArrayCollection();
        /** @var FournisseurPrestationAnnexeParam $prestationAnnexeExterne */
        foreach ($prestationAnnexeExternes as $prestationAnnexeExterne) {
            /** @var PrestationAnnexeTarif $tarif */
            foreach ($prestationAnnexeExterne->getTarifs() as $tarif) {
                $periodeValidite = $tarif->getPeriodeValidites()->filter(function (PeriodeValidite $element) use ($dateDebut, $dateFin) {
                    return $element->getDateDebut() <= new DateTime($dateDebut) && $element->getDateFin() >= new DateTime($dateFin);
                })->first();
                if (!empty($periodeValidite) or $tarif->getPeriodeValidites()->isEmpty()) {
                    $tarifs->set($prestationAnnexeExterne->getId(), $tarif->getPrixPublic());
                }
            }
        }

        return $this->render('@MondofuteCommande/commande/options_prestation_annexe_externe.html.twig', array(
            'prestationAnnexeExternes' => $prestationAnnexeExternes,
            'tarifs' => $tarifs
        ));
    }

    public function getPrestationAnnexeSejourAction($logementId)
    {
        $em = $this->getDoctrine()->getManager();
        $logement = $em->find(Logement::class, $logementId);
        $prestationAnnexeLogements = $logement->getPrestationAnnexeLogements();
        $params = new ArrayCollection();
        /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
        foreach ($prestationAnnexeLogements as $prestationAnnexeLogement) {
            if ($prestationAnnexeLogement->getActif() && $logement->getFournisseur() == $prestationAnnexeLogement->getParam()->getFournisseurPrestationAnnexe()->getFournisseur()) {
                $params->add($prestationAnnexeLogement->getParam());
            }
        }

        return $this->render('@MondofuteCommande/commande/options_commande_ligne_prestation_annexe_sejour.html.twig', array(
            'params' => $params
        ));
    }

    public function getFournisseurPrestationAnnexeExterneAction($dateDebut, $dateFin, $stationId, $typeId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurForPrestationAnnexeExternes = $em->getRepository(Fournisseur::class)->findFournisseurForPrestationAnnexeExterne($dateDebut, $dateFin, $stationId, $typeId);

        return $this->render('@MondofuteCommande/commande/options_fournisseur_prestation_annexe.html.twig', array(
            'fournisseurForPrestationAnnexeExternes' => $fournisseurForPrestationAnnexeExternes
        ));
    }

    public function ajoutClientAction(Request $request)
    {
//        récupère l'indice en preparation pour le multi-client
        $indice = intval($request->query->get('indice'), 10);
        $em = $this->getDoctrine()->getManager();
        if (empty($request->query->get('id'))) {
            $client = new Client();
            $client->addMoyenCom(new Adresse())
                ->addMoyenCom(new TelFixe())
                ->addMoyenCom(new TelMobile())
                ->addMoyenCom(new Email())
                ->addMoyenCom(new Email());
            $clientUser = new ClientUser();
            $clientUser->setClient($client);
            $client->setClientUser($clientUser);
        } else {
            $client = $em->getRepository(Client::class)->find($request->query->get('id'));
        }
//        création du formType de la commande
        $form = $this->createForm('Mondofute\Bundle\ClientBundle\Form\ClientClientUserType', $client);
        return $this->render('@MondofuteCommande/commande/fiche-client.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a commande entity.
     *
     */
    public function showAction(Commande $commande)
    {
        $deleteForm = $this->createDeleteForm($commande);

        return $this->render('@MondofuteCommande/commande/show.html.twig', array(
            'commande' => $commande,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a commande entity.
     *
     * @param Commande $commande The commande entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commande $commande)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commande_delete', array('id' => $commande->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     */
    public function editAction(Request $request, Commande $commande)
    {
//        gère le classement des commandeStatutDossier afin d'avoir le statut en cours
        $criteresStatut = Criteria::create();
        $criteresStatut->orderBy(array('dateHeure' => 'DESC'));
        /** @var CommandeStatutDossier $originalStatutDossier */
        /** @var CommandeLitigeDossier $originalLitigeDossier */
        $originalStatutDossier = $commande->getCommandeStatutDossiers()->matching($criteresStatut)->first();
//        récupération du litige en cours (null si pas de litige) et de la collection de litiges
        $originalLitigeDossier = $commande->getCommandeLitigeDossiers()->matching($criteresStatut)->first();
        if ($originalLitigeDossier == false) {
            $originalLitigeDossier = null;
        } else {
            $originalLitigeDossier = $originalLitigeDossier->getLitigeDossier();
        }
        $originalLitigeDossiers = $commande->getCommandeLitigeDossiers();
//        fin de la gestion des litiges
        $deleteForm = $this->createDeleteForm($commande);
        $form = $this->createForm(new CommandeType($originalStatutDossier->getStatutDossier(), $originalLitigeDossier),
            $commande, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));
        $originalCommandeLignes = new ArrayCollection();
        $originalCommandeLignePrestationAnnexeSejours = new ArrayCollection();
        /** @var CommandeLigne $commandeLigne */
        foreach ($commande->getCommandeLignes() as $commandeLigne) {
            $originalCommandeLignes->add($commandeLigne);
            $oReflectionClass = new ReflectionClass($commandeLigne);
            if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                /** @var CommandeLigneSejour $commandeLigne */
                /** @var CommandeLignePrestationAnnexe $commandeLignePrestationAnnex */
                foreach ($commandeLigne->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                    if (empty($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId()))) {
                        $originalCommandeLignePrestationAnnexeSejours->set($commandeLigne->getId(),
                            new ArrayCollection());
                    }
                    $originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId())->add($commandeLignePrestationAnnex);
                }
            }
        }

        $promotionSejourPeriodes = $this->getPromotionSejourPeriodes($commande);

        $decoteMasqueeSejourPeriodes = $this->getDecoteSejourPeriodes($commande, Type::masquee);

        $decoteVisibleSejourPeriodes = $this->getDecoteSejourPeriodes($commande, Type::visible);

        $promotionPrestationAnnexeSejourPeriodes = $this->getPromotionPrestationAnnexeSejourPeriodes($commande);

        $decoteMasqueePrestationAnnexeSejourPeriodes = $this->getDecotePrestationAnnexeSejourPeriodes($commande, Type::masquee);

        $decoteVisiblePrestationAnnexeSejourPeriodes = $this->getDecotePrestationAnnexeSejourPeriodes($commande, Type::visible);


        $form->handleRequest($request);
//        gestion du premier formulaire client formulaire
        /** @var Client $client */
        if (empty($client = $commande->getClients()->first())) {
            $client = new Client();
            $client->addMoyenCom(new Adresse())
                ->addMoyenCom(new TelFixe())
                ->addMoyenCom(new TelMobile())
                ->addMoyenCom(new Email())
                ->addMoyenCom(new Email());
            $clientUser = new ClientUser();
            $clientUser->setClient($client);
            $client->setClientUser($clientUser);
        } else {
            $clientUser = $client->getClientUser();
        }
        $controllerClient = new ClientController();
        $controllerClient->setContainer($this->container);
        $formClient = $controllerClient->createForm('Mondofute\Bundle\ClientBundle\Form\ClientClientUserType', $client);
        $formClient->handleRequest($request);
//        fin gestion du formulaire client
        if ($form->isSubmitted() && $form->isValid() && $formClient->isSubmitted() && $formClient->isValid()) {
            $em = $this->getDoctrine()->getManager();
//            ajoute le nouveau statut si le statut en cours est différent du statut choisi
            if ($originalStatutDossier->getStatutDossier()->getId() != $request->request->get('mondofute_bundle_commandebundle_commande')['statutDossier']) {
                $commandeStatutDossier = new CommandeStatutDossier();
                $commandeStatutDossier->setCommande($commande);
                $commandeStatutDossier->setDateHeure(new \DateTime());
                $commandeStatutDossier->setStatutDossier($em->getRepository(StatutDossier::class)->find($request->request->get('mondofute_bundle_commandebundle_commande')['statutDossier']));
                $commande->addCommandeStatutDossier($commandeStatutDossier);
            }
//            gestion des litiges
//            si litige est vide on supprime l'historique des litiges
            if ($request->request->get('mondofute_bundle_commandebundle_commande')['litigeDossier'] == '') {
                foreach ($commande->getCommandeLitigeDossiers() as $each) {
                    $commande->removeCommandeLitigeDossier($each);
                    $em->remove($each);
                }
            } else {
//                vérifie si au moins un litige est présent
                if ($originalLitigeDossier != null) {
//                    si un litige est présent on vérifie si l'état du litige a changé, si oui on ajoute le nouvel état du litige
                    if ($originalLitigeDossier->getId() != $request->request->get('mondofute_bundle_commandebundle_commande')['litigeDossier']) {
                        $commandeLitigeDossier = new CommandeLitigeDossier();
                        $commandeLitigeDossier->setCommande($commande);
                        $commandeLitigeDossier->setLitigeDossier($em->getRepository(LitigeDossier::class)->find($request->request->get('mondofute_bundle_commandebundle_commande')['litigeDossier']));
                        $commandeLitigeDossier->setDateHeure(new \DateTime());
                        $commande->addCommandeLitigeDossier($commandeLitigeDossier);
                    }
                } else {
//                    si aucun litige est déjà présent on ajoute le nouvel état
                    $commandeLitigeDossier = new CommandeLitigeDossier();
                    $commandeLitigeDossier->setCommande($commande);
                    $commandeLitigeDossier->setLitigeDossier($em->getRepository(LitigeDossier::class)->find($request->request->get('mondofute_bundle_commandebundle_commande')['litigeDossier']));
                    $commandeLitigeDossier->setDateHeure(new \DateTime());
                    $commande->addCommandeLitigeDossier($commandeLitigeDossier);
                }
            }
//            fin de la gestion des litiges
//            gestion du client
            $clientUser->setEnabled(true);
            foreach ($client->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateCreation();
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $clientUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }
            $client->setDateCreation();
            if (!$controllerClient->loginExist($clientUser)) {
//                gestion du client
                if (!empty($client->getId())) {
                    $controllerClient->majSites($clientUser);
                } else {
                    $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                    $controllerClient->newSites($clientUser, $client, $sites);
                    $controllerClient->dupliquerMoyenComs($client, $em);
                }
                $em->persist($client);
//            fin de la gestion du client
                if (count($commande->getClients()) <= 0) {
                    $commande->addClient($client);
                }
                foreach ($originalCommandeLignes as $originalCommandeLigne) {
                    if (false === $commande->getCommandeLignes()->contains($originalCommandeLigne)) {
                        $em->remove($originalCommandeLigne);
                    } else {
                        foreach ($commande->getCommandeLignes() as $commandeLigne) {
                            if (!empty($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId()))) {
                                foreach ($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId()) as $originalCommandeLignePrestationAnnexeSejour) {
                                    if (false === $commandeLigne->getCommandeLignePrestationAnnexes()->contains($originalCommandeLignePrestationAnnexeSejour)) {
                                        $em->remove($originalCommandeLignePrestationAnnexeSejour);
                                    }
                                }
                            }
                        }
                    }
                }
//            /** @var Client $client */
//            foreach ($commande->getClients() as $client){
//                dump($client);
//                if(!empty($client->getId())){
//                    dump($client);
//                    $tmp = $em->getRepository(Client::class)->find($client->getId());
//                    $tmp->setClientUser($client->getClientUser())->setDateNaissance($client->getDateNaissance())->setNom($client->getNom())->setPrenom($client->getPrenom());
//                    $commande->removeClient($client);
//                    $commande->addClient($tmp);
//
//                }
//                die;
//            }
                $em->flush();

                $this->copieVersSites($commande);

                $this->addFlash('success', 'Commande modifiée avec succès.');

                return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
            }
        }
        $stations = $em->getRepository(Station::class)->getTraductionsByLocale($this->getParameter('locale'), null, $commande->getSite()->getId())->getQuery()->getResult();
        $stationTraductions = new ArrayCollection();
        foreach ($stations as $station) {
            $stationTraductions->add([
                'id' => $station->getId(),
                'libelle' => $station->getTraductions()->first()->getLibelle()
            ]);
        }
        $fournisseurs = $em->getRepository(Fournisseur::class)->findAll();

        return $this->render('@MondofuteCommande/commande/edit.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'formClient' => $formClient->createView(),
            'stations' => $stationTraductions,
            'fournisseurs' => $fournisseurs,
            'promotionSejourPeriodes' => $promotionSejourPeriodes,
            'decoteMasqueeSejourPeriodes' => $decoteMasqueeSejourPeriodes,
            'decoteVisibleSejourPeriodes' => $decoteVisibleSejourPeriodes,
            'promotionPrestationAnnexeSejourPeriodes' => $promotionPrestationAnnexeSejourPeriodes,
            'decoteMasqueePrestationAnnexeSejourPeriodes' => $decoteMasqueePrestationAnnexeSejourPeriodes,
            'decoteVisiblePrestationAnnexeSejourPeriodes' => $decoteVisiblePrestationAnnexeSejourPeriodes,
        ));
    }

    private function getPromotionSejourPeriodes(Commande $commande)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PromotionLogementPeriode $promotionLogementPeriode */
        /** @var RemisePromotion $commandeLigneRemisePromotion */
        /** @var SejourPeriode $sejourPeriode */
        /** @var PromotionLogement $promotionLogement */
        /** @var Promotion $promotion */
        $promotionSejourPeriodes = new ArrayCollection();
        foreach ($commande->getSejourPeriodes() as $sejourPeriode) {
            $promotionSejourPeriodes->set($sejourPeriode->getId(), new ArrayCollection());
            foreach ($commande->getCommandeLigneRemisePromotions() as $commandeLigneRemisePromotion) {
                $promotions = new ArrayCollection();

                // on récupère toute les pormotions concernant le logement
                $promotionLogements = $em->getRepository(PromotionLogement::class)->findBy(['logement' => $sejourPeriode->getLogement(), 'promotion' => $commandeLigneRemisePromotion->getPromotion()]);
                foreach ($promotionLogements as $promotionLogement) {
                    if (!$promotions->contains($promotionLogement->getPromotion())) {
                        $promotions->add($promotionLogement->getPromotion());
                    }
                }
                $promotionLogementPeriodes = $em->getRepository(PromotionLogementPeriode::class)->findBy(['logement' => $sejourPeriode->getLogement(), 'promotion' => $commandeLigneRemisePromotion->getPromotion()]);
                foreach ($promotionLogementPeriodes as $promotionLogementPeriode) {
                    if (!$promotions->contains($promotionLogementPeriode->getPromotion())) {
                        $promotions->add($promotionLogementPeriode->getPromotion());
                    }
                }

                foreach ($promotions as $promotion) {
                    // 1er test: TypePeriodeValidite
                    if (
                        $promotion->getTypePeriodeValidite() == PromotionTypePeriodeValidite::permanent
                        || $promotion->getTypePeriodeValidite() == PromotionTypePeriodeValidite::dateADate
                        && $promotion->getPromotionPeriodeValiditeDate()->getDateDebut() <= $commande->getDateCommande()
                        && $commande->getDateCommande() <= $promotion->getPromotionPeriodeValiditeDate()->getDateFin()
                        || $promotion->getTypePeriodeValidite() == PromotionTypePeriodeValidite::periode
                        && $sejourPeriode->getDateAchat() >= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . $promotion->getPromotionPeriodeValiditeJour()->getJourDebut() . 'D'))
                        && $sejourPeriode->getDateAchat() <= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . intval($promotion->getPromotionPeriodeValiditeJour()->getJourFin()) . 'D'))
                    ) {
                        // 2eme test: TypePeriodeSejour
                        if (
                            $promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::permanent
                            || $promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::dateADate
                            && $promotion->getPromotionPeriodeSejourDate()->getDateDebut() <= $sejourPeriode->getPeriode()->getDebut()
                            && $sejourPeriode->getPeriode()->getFin() <= $promotion->getPromotionPeriodeSejourDate()->getDateFin()
                            || $promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::periode

                        ) {
                            if ($promotion->getTypePeriodeSejour() == DecoteTypePeriodeSejour::periode) {
                                /** @var PeriodeValidite $periodeValidite */
                                $ok = false;
                                /** @var PromotionLogementPeriode $logementPeriode */
                                foreach ($promotion->getLogementPeriodes() as $logementPeriode) {
                                    if (!$ok && $logementPeriode->getPeriode() == $sejourPeriode->getPeriode()) {
                                        $promotionSejourPeriodes->get($sejourPeriode->getId())->add($promotion);
                                        $ok = true;
                                    }
                                }
                            } else {
                                $promotionSejourPeriodes->get($sejourPeriode->getId())->add($promotion);
                            }
                        }
                    }
                }
            }
        }

        return $promotionSejourPeriodes;
    }

    private function getDecoteSejourPeriodes(Commande $commande, $type)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var DecoteLogementPeriode $decoteLogementPeriode */
        /** @var RemiseDecote $commandeLigneRemiseDecote */
        /** @var SejourPeriode $sejourPeriode */
        /** @var DecoteLogement $decoteLogement */
        /** @var Decote $decote */
        $decoteSejourPeriodes = new ArrayCollection();
        foreach ($commande->getSejourPeriodes() as $sejourPeriode) {
            $decoteSejourPeriodes->set($sejourPeriode->getId(), new ArrayCollection());
            foreach ($commande->getCommandeLigneRemiseDecotes($type) as $commandeLigneRemiseDecote) {
                $decotes = new ArrayCollection();

                // on récupère toute les promotions concernant le logement
                $decoteLogements = $em->getRepository(DecoteLogement::class)->findBy(['logement' => $sejourPeriode->getLogement(), 'decote' => $commandeLigneRemiseDecote->getDecote()]);
                foreach ($decoteLogements as $decoteLogement) {
                    if (!$decotes->contains($decoteLogement->getDecote())) {
                        $decotes->add($decoteLogement->getDecote());
                    }
                }
                $decoteLogementPeriodes = $em->getRepository(DecoteLogementPeriode::class)->findBy(['logement' => $sejourPeriode->getLogement(), 'decote' => $commandeLigneRemiseDecote->getDecote()]);
                foreach ($decoteLogementPeriodes as $decoteLogementPeriode) {
                    if (!$decotes->contains($decoteLogementPeriode->getDecote())) {
                        $decotes->add($decoteLogementPeriode->getDecote());
                    }
                }

                foreach ($decotes as $decote) {
                    // 1er test: TypePeriodeValidite
                    if (
                        $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::permanent
                        || $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::dateADate
                        && $decote->getDecotePeriodeValiditeDate()->getDateDebut() <= $commande->getDateCommande()
                        && $commande->getDateCommande() <= $decote->getDecotePeriodeValiditeDate()->getDateFin()
                        || $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::periode
                        && $sejourPeriode->getDateAchat() >= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . $decote->getDecotePeriodeValiditeJour()->getJourDebut() . 'D'))
                        && $sejourPeriode->getDateAchat() <= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . intval($decote->getDecotePeriodeValiditeJour()->getJourFin()) . 'D'))
                        || $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::weekend && $sejourPeriode->getPeriode()->getType()->getId() == 1
                    ) {
                        // 2eme test: TypePeriodeSejour
                        if (
                            $decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::permanent
                            || $decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::dateADate
                            && $decote->getDecotePeriodeSejourDate()->getDateDebut() <= $sejourPeriode->getPeriode()->getDebut()
                            && $sejourPeriode->getPeriode()->getFin() <= $decote->getDecotePeriodeSejourDate()->getDateFin()
                            || $decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::periode
                        ) {
                            if ($decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::periode) {
                                /** @var PeriodeValidite $periodeValidite */
                                $ok = false;
                                /** @var DecoteLogementPeriode $logementPeriode */
                                foreach ($decote->getLogementPeriodes() as $logementPeriode) {
                                    if (!$ok && $logementPeriode->getPeriode() == $sejourPeriode->getPeriode()) {
                                        $decoteSejourPeriodes->get($sejourPeriode->getId())->add($decote);
                                        $ok = true;
                                    }
                                }
                            } else {
                                $decoteSejourPeriodes->get($sejourPeriode->getId())->add($decote);
                            }
                        }
                    }
                }
            }

            $decoteTmp = new Decote();
            foreach ($decoteSejourPeriodes->get($sejourPeriode->getId()) as $decote) {
                // traitement pour savoir si le type est un pourcentage
                if ($decote->getTypeRemise() == TypeRemise::poucentage) {
                    $val = ($sejourPeriode->getPrixVente() * $decote->getValeurRemise()) / 100;
                } else {
                    $val = $decote->getValeurRemise();
                }
                if ($decoteTmp->getTypeRemise() == TypeRemise::poucentage) {
                    $valTmp = ($sejourPeriode->getPrixVente() * $decoteTmp->getValeurRemise()) / 100;
                } else {
                    $valTmp = $decoteTmp->getValeurRemise();
                }
                if ($val > $valTmp) {
                    $decoteSejourPeriodes->get($sejourPeriode->getId())->removeElement($decoteTmp);
                } else {
                    $decoteSejourPeriodes->get($sejourPeriode->getId())->removeElement($decote);
                }
                $decoteTmp = $decote;
            }
        }
        return $decoteSejourPeriodes;
    }

    private function getPromotionPrestationAnnexeSejourPeriodes(Commande $commande)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PromotionLogementPeriode $promotionLogementPeriode */
        /** @var RemisePromotion $commandeLigneRemisePromotion */
        /** @var SejourPeriode $sejourPeriode */
        /** @var PromotionLogement $promotionLogement */
        /** @var Promotion $promotion */
        $promotionSejourPeriodes = new ArrayCollection();
        foreach ($commande->getSejourPeriodes() as $sejourPeriode) {
            $promotionSejourPeriodes->set($sejourPeriode->getId(), new ArrayCollection());
            foreach ($sejourPeriode->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                $promotionSejourPeriodes->get($sejourPeriode->getId())->set($commandeLignePrestationAnnex->getId(), new ArrayCollection());
                foreach ($commande->getCommandeLigneRemisePromotions() as $commandeLigneRemisePromotion) {
                    $promotions = new ArrayCollection();

                    // on récupère toute les pormotions concernant le logement
                    /** @var CommandeLignePrestationAnnexe $commandeLignePrestationAnnex */
                    $promotionLogements = $em->getRepository(PromotionFournisseurPrestationAnnexe::class)->findBy(['fournisseurPrestationAnnexe' => $commandeLignePrestationAnnex->getFournisseurPrestationAnnexeParam()->getFournisseurPrestationAnnexe(), 'promotion' => $commandeLigneRemisePromotion->getPromotion()]);
                    foreach ($promotionLogements as $promotionLogement) {
                        if (!$promotions->contains($promotionLogement->getPromotion())) {
                            $promotions->add($promotionLogement->getPromotion());
                        }
                    }

                    foreach ($promotions as $promotion) {
                        // 1er test: TypePeriodeValidite
                        if (
                            $promotion->getTypePeriodeValidite() == PromotionTypePeriodeValidite::permanent
                            || $promotion->getTypePeriodeValidite() == PromotionTypePeriodeValidite::dateADate
                            && $promotion->getPromotionPeriodeValiditeDate()->getDateDebut() <= $commande->getDateCommande()
                            && $commande->getDateCommande() <= $promotion->getPromotionPeriodeValiditeDate()->getDateFin()
                            || $promotion->getTypePeriodeValidite() == PromotionTypePeriodeValidite::periode
                            && $sejourPeriode->getDateAchat() >= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . $promotion->getPromotionPeriodeValiditeJour()->getJourDebut() . 'D'))
                            && $sejourPeriode->getDateAchat() <= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . intval($promotion->getPromotionPeriodeValiditeJour()->getJourFin()) . 'D'))
                        ) {
                            // 2eme test: TypePeriodeSejour
                            if (
                                $promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::permanent
                                || $promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::dateADate
                                && $promotion->getPromotionPeriodeSejourDate()->getDateDebut() <= $sejourPeriode->getPeriode()->getDebut()
                                && $sejourPeriode->getPeriode()->getFin() <= $promotion->getPromotionPeriodeSejourDate()->getDateFin()
                                || $promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::periode
                            ) {
                                if ($promotion->getTypePeriodeSejour() == PromotionTypePeriodeSejour::periode) {
                                    /** @var PeriodeValidite $periodeValidite */
                                    $ok = false;
                                    foreach ($promotion->getPeriodeValidites() as $periodeValidite) {
                                        if (!$ok && $periodeValidite->getDateDebut() <= $sejourPeriode->getPeriode()->getDebut()
                                            && $sejourPeriode->getPeriode()->getFin() <= $periodeValidite->getDateFin()
                                        ) {
                                            $promotionSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId())->add($promotion);
                                            $ok = true;
                                        }
                                    }
                                } else {
                                    $promotionSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId())->add($promotion);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $promotionSejourPeriodes;
    }

    private function getDecotePrestationAnnexeSejourPeriodes(Commande $commande, $type)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var DecoteLogementPeriode $decoteLogementPeriode */
        /** @var RemiseDecote $commandeLigneRemiseDecote */
        /** @var SejourPeriode $sejourPeriode */
        /** @var DecoteLogement $decoteLogement */
        /** @var Decote $decote */
        $decoteSejourPeriodes = new ArrayCollection();
        foreach ($commande->getSejourPeriodes() as $sejourPeriode) {
            $decoteSejourPeriodes->set($sejourPeriode->getId(), new ArrayCollection());
            foreach ($sejourPeriode->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                $decoteSejourPeriodes->get($sejourPeriode->getId())->set($commandeLignePrestationAnnex->getId(), new ArrayCollection());
                foreach ($commande->getCommandeLigneRemiseDecotes($type) as $commandeLigneRemiseDecote) {
                    $decotes = new ArrayCollection();

                    // on récupère toute les pormotions concernant le logement
                    $decoteLogements = $em->getRepository(DecoteFournisseurPrestationAnnexe::class)->findBy(['fournisseurPrestationAnnexe' => $commandeLignePrestationAnnex->getFournisseurPrestationAnnexeParam()->getFournisseurPrestationAnnexe(), 'decote' => $commandeLigneRemiseDecote->getDecote()]);
                    foreach ($decoteLogements as $decoteLogement) {
                        if (!$decotes->contains($decoteLogement->getDecote())) {
                            $decotes->add($decoteLogement->getDecote());
                        }
                    }

                    foreach ($decotes as $decote) {
                        // 1er test: TypePeriodeValidite
                        if (
                            $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::permanent
                            || $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::dateADate
                            && $decote->getDecotePeriodeValiditeDate()->getDateDebut() <= $sejourPeriode->getDateAchat()
                            && $sejourPeriode->getDateAchat() <= $decote->getDecotePeriodeValiditeDate()->getDateFin()
                            || $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::periode
                            && $sejourPeriode->getDateAchat() >= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . $decote->getDecotePeriodeValiditeJour()->getJourDebut() . 'D'))
                            && $sejourPeriode->getDateAchat() <= date_sub(new DateTime(date($sejourPeriode->getPeriode()->getDebut()->format('y-m-d'))), new \DateInterval('P' . intval($decote->getDecotePeriodeValiditeJour()->getJourFin()) . 'D'))
                            || $decote->getTypePeriodeValidite() == DecoteTypePeriodeValidite::weekend && $sejourPeriode->getPeriode()->getType()->getId() == 1
                        ) {
                            // 2eme test: TypePeriodeSejour
                            if (
                                $decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::permanent
                                || $decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::dateADate
                                && $decote->getDecotePeriodeSejourDate()->getDateDebut() <= $sejourPeriode->getPeriode()->getDebut()
                                && $sejourPeriode->getPeriode()->getFin() <= $decote->getDecotePeriodeSejourDate()->getDateFin()
                                || $decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::periode
                            ) {
                                if ($decote->getTypePeriodeSejour() == DecoteTypePeriodeSejour::periode) {
                                    /** @var PeriodeValidite $periodeValidite */
                                    $ok = false;
                                    foreach ($decote->getPeriodeValidites() as $periodeValidite) {
                                        if (!$ok && $periodeValidite->getDateDebut() <= $sejourPeriode->getPeriode()->getDebut()
                                            && $sejourPeriode->getPeriode()->getFin() <= $periodeValidite->getDateFin()
                                        ) {
                                            $decoteSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId())->add($decote);
                                            $ok = true;
                                        }
                                    }
                                } else {
                                    $decoteSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId())->add($decote);
                                }
                            }
                        }
                    }
                }
                $decoteTmp = new Decote();
                foreach ($decoteSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId()) as $decote) {
                    // traitement pour savoir si le type est un pourcentage
                    if ($decote->getTypeRemise() == TypeRemise::poucentage) {
                        $val = ($sejourPeriode->getPrixVente() * $decote->getValeurRemise()) / 100;
                    } else {
                        $val = $decote->getValeurRemise();
                    }
                    if ($decoteTmp->getTypeRemise() == TypeRemise::poucentage) {
                        $valTmp = ($sejourPeriode->getPrixVente() * $decoteTmp->getValeurRemise()) / 100;
                    } else {
                        $valTmp = $decoteTmp->getValeurRemise();
                    }
                    if ($val > $valTmp) {
                        $decoteSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId())->removeElement($decoteTmp);
                    } else {
                        $decoteSejourPeriodes->get($sejourPeriode->getId())->get($commandeLignePrestationAnnex->getId())->removeElement($decote);
                    }
                    $decoteTmp = $decote;
                }
            }
        }

        return $decoteSejourPeriodes;
    }

    /**
     * Deletes a commande entity.
     *
     */
    public function deleteAction(Request $request, Commande $commande)
    {
        /** @var Site $site */
        $form = $this->createDeleteForm($commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $commandeSite = $emSite->find(Commande::class, $commande->getId());
                    if (!empty($commandeSite)) {
                        $emSite->remove($commandeSite);
                        $emSite->flush();
                    }
                }

                $em->remove($commande);
                $em->flush();

                $this->addFlash('success', 'Commande supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'la commande est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('commande_index');
    }

    public function addCommandeLignePeriodeSejourAction($logementId, $periodeId, $index)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = new Commande();
        $sejourPeriode = new SejourPeriode();
        $sejourPeriode
            ->setPeriode($em->find(Periode::class, $periodeId))
            ->setLogement($em->find(Logement::class, $logementId))
            ->setDateAchat(new DateTime());

        $commande->getCommandeLignes()->set($index, $sejourPeriode);

        $form = $this->createForm('Mondofute\Bundle\CommandeBundle\Form\CommandeType', $commande, ['addSejourPeriode' => true])->get('commandeLignes')->get($index);

        return $this->render('@MondofuteCommande/commande/commande_ligne_sejour_periode_widget.html.twig', array(
            'form' => $form->createView(),
            'name' => $index,
            'ajax' => true
        ));
    }
}
