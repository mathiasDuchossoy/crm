<?php

namespace Mondofute\Bundle\CodePromoBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use HiDev\Bundle\CodePromoBundle\Entity\ClientAffectation;
use HiDev\Bundle\CodePromoBundle\Entity\CodePromoPeriodeValidite;
use HiDev\Bundle\CodePromoBundle\Entity\Usage;
use JMS\JobQueueBundle\Entity\Job;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFamillePrestationAnnexe;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseurPrestationAnnexe;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoHebergement;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoLogement;
use Mondofute\Bundle\CodePromoBundle\Entity\Application;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoApplication;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoClient;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoPeriodeSejour;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie;
use Mondofute\Bundle\CodePromoBundle\Form\CodePromoType;
use Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * CodePromoUnifie controller.
 *
 */
class CodePromoUnifieController extends Controller
{
    const CodePromoPeriodeValidite = "HiDev\\Bundle\\CodePromoBundle\\Entity\\CodePromoPeriodeValidite";

    /**
     * Lists all CodePromoUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(CodePromoUnifie::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofuteCodePromoBundle:CodePromoUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'codepromo_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteCodePromoBundle:CodePromoUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteCodePromo/codepromounifie/index.html.twig', array(
            'codePromoUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new CodePromoUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $applications = Application::$libelles;

        $sitesAEnregistrer = $request->get('sites');

        // *** gestion code promo client ***
        $clientsBySites = $this->getClientsBySites($sitesAEnregistrer, $sites, $request);
        // *** fin gestion code promo client ***

        $codePromoUnifie = new CodePromoUnifie();

        $this->ajouterCodePromosDansForm($codePromoUnifie);
        $this->codePromosSortByAffichage($codePromoUnifie);

        $form = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType', $codePromoUnifie);
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $form->handleRequest($request);

        $codeExists = $this->testCodeExists($codePromoUnifie);
        if ($codeExists) {
            $this->addFlash('error', "Ce code existe déjà. ");
        }

        if ($form->isSubmitted() && $form->isValid() && !$codeExists) {

            /** @var CodePromo $entity */
            $this->addCodePromoPeriode($codePromoUnifie, 'Validite', CodePromoPeriodeValidite::class);
            $this->addCodePromoPeriode($codePromoUnifie, 'Sejour', CodePromoPeriodeSejour::class);
            // *** gestion code promo client ***
            $this->gestionCodePromoClient($codePromoUnifie, $clientsBySites);
            // *** fin gestion code promo client ***

            // *** gestion code promo application ***
            $this->gestionCodePromoApplication($codePromoUnifie);
            // *** fin gestion code promo application ***

            /** @var CodePromo $codePromo */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                if (false === in_array($codePromo->getSite()->getId(), $sitesAEnregistrer)) {
                    $codePromo->setActifSite(false);
                }
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($codePromoUnifie);

            try {
                $em->flush();

                $this->copieVersSites($codePromoUnifie);

                $this->addFlash('success', 'Le code promo a bien été créé.');

                return $this->redirectToRoute('codepromo_edit', array('id' => $codePromoUnifie->getId()));
            } catch (Exception $e) {
//                switch ($e->getCode()){
//                    case 0:
//                        $this->addFlash('error', "Le code " . $codePromoUnifie->getCode() . " existe déjà.");
//                        break;
//                    default:
//                        $this->addFlash('error', "Add not done: " . $e->getMessage());
//                        break;
//                }

                $this->addFlash('error', "Add not done: " . $e->getMessage());
            }

        }

        return $this->render('@MondofuteCodePromo/codepromounifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $codePromoUnifie,
            'form' => $form->createView(),
            'applications' => $applications,
        ));
    }

    /**
     * @param $sitesAEnregistrer
     * @param $sites
     * @param Request $request
     * @return array
     */
    private function getClientsBySites($sitesAEnregistrer, $sites, $request)
    {
        $clientsBySites = array();
        foreach ($sites as $site) {
            $clientsBySites[intval($site->getId())] = array();
        }
        if (!empty($sitesAEnregistrer)) {
            foreach ($sitesAEnregistrer as $item) {
//                if(empty($clientsBySites[intval($item)])){
//                    $clientsBySites[intval($item)]   = array();
//                }
                $clients = $request->get('client_' . $item);
                if (!empty($clients)) {
                    foreach ($clients as $client) {
                        array_push($clientsBySites[intval($item)], $client);
                    }
                }
            }
        }
        /** @var Site $site */
        $siteCrm = '';
        foreach ($sites as $site) {
            if ($site->getCrm()) {
                $siteCrm = $site;
            }
        }
        foreach ($clientsBySites as $key => $item) {
            if ($key != $siteCrm->getId()) {
                foreach ($clientsBySites[$siteCrm->getId()] as $value) {
                    if (!in_array($value, $item)) {
                        array_push($clientsBySites[$key], $value);
                    }
                }
            }
        }
        return $clientsBySites;
    }

    /**
     * Ajouter les codePromos qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param CodePromoUnifie $entityUnifie
     */
    private function ajouterCodePromosDansForm(CodePromoUnifie $entityUnifie)
    {
        /** @var CodePromo $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $entity = $entityUnifie->getCodePromos()->filter(function (CodePromo $element) use ($site) {
                return $element->getSite() == $site;
            })->first();
            if (false === $entity) {
                $entity = new CodePromo();
                $entityUnifie->addCodePromo($entity);
                $entity->setSite($site);
            }
        }
    }

    /**
     * Classe les codePromos par classementAffichage
     * @param CodePromoUnifie $entity
     */
    private function codePromosSortByAffichage(CodePromoUnifie $entity)
    {
        // Trier les codePromos en fonction de leurs ordre d'affichage
        $codePromos = $entity->getCodePromos(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $codePromos->getIterator();
        unset($codePromos);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (CodePromo $a, CodePromo $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $codePromos = new ArrayCollection(iterator_to_array($iterator));

        // remplacé les codePromos par ce nouveau tableau (une fonction 'set' a été créé dans CodePromo unifié)
        $entity->setCodePromos($codePromos);
    }

    private function testCodeExists($codePromoUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $codePromoUnifieByCode = $em->getRepository(CodePromoUnifie::class)->getCodePromoUnifieByCode($codePromoUnifie);
        if (!$codePromoUnifieByCode) {
            return false;
        }
        return true;
    }

    private function addCodePromoPeriode(CodePromoUnifie $entityUnifie, $spec, $CodePromoPeriode)
    {
//    private function addCodePromoPeriode(CodePromoUnifie $entityUnifie , $spec,  ){
        $getCodePromoPeriodes = "getCodePromoPeriode" . $spec . "s";
        $addCodePromoPeriode = "addCodePromoPeriode" . $spec;
        $entityCrm = $entityUnifie->getCodePromos()->filter(function (CodePromo $element) {
            return $element->getSite()->getCrm() == true;
        })->first();

        if (
            !empty($entityCrm->$getCodePromoPeriodes()) &&
            !$entityCrm->$getCodePromoPeriodes()->isEmpty()
        ) {
            foreach ($entityUnifie->getCodePromos() as $codePromo) {
                if ($codePromo->getSite()->getCrm() == false &&
                    (empty($codePromo->$getCodePromoPeriodes()) ||
                        $codePromo->$getCodePromoPeriodes()->isEmpty())
                ) {
//                    /** @var CodePromoPeriode $codePromoPeriodeValidite */
                    foreach ($entityCrm->$getCodePromoPeriodes() as $codePromoPeriode) {
                        $codePromoPeriodeSite = new $CodePromoPeriode();
                        $codePromo->$addCodePromoPeriode($codePromoPeriodeSite);
                        $codePromoPeriodeSite
                            ->setDateDebut($codePromoPeriode->getDateDebut())
                            ->setDateFin($codePromoPeriode->getDateFin());
                    }
                }
            }
        }
    }

    /**
     * @param CodePromoUnifie $codePromoUnifie
     * @param $clientsBySites
     */
    private function gestionCodePromoClient($codePromoUnifie, $clientsBySites)
    {
        /** @var CodePromoClient $codePromoClient */
        $em = $this->getDoctrine()->getManager();
        /** @var CodePromo $entity */
        // parcourir les codePromos
        foreach ($codePromoUnifie->getCodePromos() as $entity) {
            // si
            if ($entity->getClientAffectation() == ClientAffectation::existants) {
                $clients = $clientsBySites[$entity->getSite()->getId()];
                if ($entity->getUsageCodePromo() == Usage::uniqueParPeriode && !$entity->getCodePromoPeriodeValidites()->isEmpty()) {
                    $codePromoClients = $entity->getCodePromoClients()->filter(function (CodePromoClient $element) {
                        return empty($element->getCodePromoPeriodeValidite());
                    });
                    foreach ($codePromoClients as $codePromoClient) {
                        $entity->getCodePromoClients()->removeElement($codePromoClient);
                        $em->remove($codePromoClient);
                    }
                    foreach ($entity->getCodePromoPeriodeValidites() as $codePromoPeriodeValidite) {
                        foreach ($clients as $client) {
                            $codePromoClient = $entity->getCodePromoClients()->filter(function (CodePromoClient $element) use ($codePromoPeriodeValidite, $client) {
                                return ($element->getClient()->getId() == $client && $element->getCodePromoPeriodeValidite() == $codePromoPeriodeValidite);
                            })->first();
                            if (false === $codePromoClient) {
                                $codePromoClient = new CodePromoClient();
                                $entity->addCodePromoClient($codePromoClient);
                                $codePromoClient
                                    ->setClient($em->find(Client::class, $client));
                            }
                            $codePromoClient
                                ->setCodePromoPeriodeValidite($codePromoPeriodeValidite);
                        }
                    }
                } else {
                    $codePromoClients = $entity->getCodePromoClients()->filter(function (CodePromoClient $element) {
                        return !empty($element->getCodePromoPeriodeValidite());
                    });
                    foreach ($codePromoClients as $codePromoClient) {
                        $entity->getCodePromoClients()->removeElement($codePromoClient);
                        $em->remove($codePromoClient);
                    }
                    foreach ($clients as $client) {
                        $codePromoClient = $entity->getCodePromoClients()->filter(function (CodePromoClient $element) use ($client) {
                            return $element->getClient()->getId() == $client;
                        })->first();
                        if (false === $codePromoClient) {
                            $codePromoClient = new CodePromoClient();
                            $entity->addCodePromoClient($codePromoClient);
                            $codePromoClient->setClient($em->find(Client::class, $client));
                        }
                        $codePromoClient
                            ->setCodePromoPeriodeValidite(null);
                    }
                }
            }
        }
    }

    /**
     * @param CodePromoUnifie $codePromoUnifie
     */
    private function gestionCodePromoApplication($codePromoUnifie)
    {
        /** @var CodePromoApplication $codePromoApplicationCrm */
        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            foreach ($codePromo->getCodePromoApplications() as $application) {
                $application->setCodePromo($codePromo);
            }
        }
        $codePromoApplicationCrms = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getCodePromoApplications();
        $applications = new ArrayCollection();
        foreach ($codePromoApplicationCrms as $codePromoApplicationCrm) {
            $applications->add($codePromoApplicationCrm->getApplication());
        }
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            if ($codePromo->getSite()->getCrm() == 0) {
                $applicationSites = new ArrayCollection();
                foreach ($codePromo->getCodePromoApplications() as $codePromoApplicationSite) {
                    $applicationSites->add($codePromoApplicationSite->getApplication());
                }
                foreach ($applications as $application) {
                    if (false === $applicationSites->contains($application)) {
                        $newApplication = new CodePromoApplication();
                        $codePromo->addCodePromoApplication($newApplication);
                        $newApplication->setApplication($application);
                    }
                }
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité codePromo
     * @param CodePromoUnifie $entityUnifie
     */
    private function copieVersSites(CodePromoUnifie $entityUnifie)
    {
        /** @var EntityManager $emSite */
        /** @var CodePromo $entity */
        /** @var CodePromo $entityCrm */
//        Boucle sur les codePromos afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entityUnifie->getCodePromos() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());
                $site = $emSite->find(Site::class, $entity->getSite());

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (empty($entityUnifieSite = $emSite->find(CodePromoUnifie::class, $entityUnifie))) {
                    $entityUnifieSite = new CodePromoUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                }

                //  Récupération de la codePromo sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty($entitySite = $emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $entityUnifieSite)))) {
                    $entitySite = new CodePromo();
                    $entitySite
                        ->setSite($site)
                        ->setCodePromoUnifie($entityUnifieSite);

                    $entityUnifieSite->addCodePromo($entitySite);
                }

                // *** gestion code promo client ***
                if (!empty($entity->getCodePromoClients()) && !$entity->getCodePromoClients()->isEmpty()) {
                    /** @var CodePromoClient $codePromoClient */
                    foreach ($entity->getCodePromoClients() as $codePromoClient) {
                        $codePromoClientSite = $entitySite->getCodePromoClients()->filter(function (CodePromoClient $element) use ($codePromoClient) {
                            return $element->getId() == $codePromoClient->getId();
                        })->first();
                        if (false === $codePromoClientSite) {

                            $codePromoClientSite = new CodePromoClient();
                            $entitySite->addCodePromoClient($codePromoClientSite);
                            $codePromoClientSite
                                ->setId($codePromoClient->getId());
                            $metadata = $emSite->getClassMetadata(get_class($codePromoClientSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $codePromoPeriodeValidite = null;
                        if (!empty($codePromoClient->getCodePromoPeriodeValidite())) {
                            $codePromoPeriodeValidite = $emSite->find(CodePromoPeriodeValidite::class, $codePromoClient->getCodePromoPeriodeValidite());
                        }
                        $codePromoClientSite
                            ->setCodePromoPeriodeValidite($codePromoPeriodeValidite)
                            ->setClient($emSite->find(Client::class, $codePromoClient->getClient()))
                            ->setUtilise($codePromoClient->getUtilise());
                    }
                }

                if (!empty($entitySite->getCodePromoClients()) && !$entitySite->getCodePromoClients()->isEmpty()) {
                    /** @var CodePromoClient $codePromoClient */
                    /** @var CodePromoClient $codePromoClientSite */
                    foreach ($entitySite->getCodePromoClients() as $codePromoClientSite) {
                        $codePromoClient = $entity->getCodePromoClients()->filter(function (CodePromoClient $element) use ($codePromoClientSite) {
                            return $element->getId() == $codePromoClientSite->getId();
                        })->first();
                        if (false === $codePromoClient) {
//                            $entitySite->removeCodePromoClient($codePromoClientSite);
                            $emSite->remove($codePromoClientSite);
                        }
                    }
                }
                // *** fin gestion code promo client ***

//                 *** gestion code promo periode validité ***
                if (!empty($entity->getCodePromoPeriodeValidites()) && !$entity->getCodePromoPeriodeValidites()->isEmpty()) {
                    /** @var CodePromoPeriodeValidite $codePromoPeriodeValidite */
                    foreach ($entity->getCodePromoPeriodeValidites() as $codePromoPeriodeValidite) {
                        $codePromoPeriodeValiditeSite = $entitySite->getCodePromoPeriodeValidites()->filter(function (CodePromoPeriodeValidite $element) use ($codePromoPeriodeValidite) {
                            return $element->getId() == $codePromoPeriodeValidite->getId();
                        })->first();
                        if (false === $codePromoPeriodeValiditeSite) {
                            $codePromoPeriodeValiditeSite = new CodePromoPeriodeValidite();
                            $entitySite->addCodePromoPeriodeValidite($codePromoPeriodeValiditeSite);
                            $codePromoPeriodeValiditeSite
                                ->setId($codePromoPeriodeValidite->getId());
                            $metadata = $emSite->getClassMetadata(get_class($codePromoPeriodeValiditeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $codePromoPeriodeValiditeSite
                            ->setDateDebut($codePromoPeriodeValidite->getDateDebut())
                            ->setDateFin($codePromoPeriodeValidite->getDateFin());
                    }
                }

                if (!empty($entitySite->getCodePromoPeriodeValidites()) && !$entitySite->getCodePromoPeriodeValidites()->isEmpty()) {
                    /** @var CodePromoPeriodeValidite $codePromoPeriodeValidite */
                    /** @var CodePromoPeriodeValidite $codePromoPeriodeValiditeSite */
                    foreach ($entitySite->getCodePromoPeriodeValidites() as $codePromoPeriodeValiditeSite) {
                        $codePromoPeriodeValidite = $entity->getCodePromoPeriodeValidites()->filter(function (CodePromoPeriodeValidite $element) use ($codePromoPeriodeValiditeSite) {
                            return $element->getId() == $codePromoPeriodeValiditeSite->getId();
                        })->first();
                        if (false === $codePromoPeriodeValidite) {
                            $codePromoClientSite = $entitySite->getCodePromoClients()->filter(function (CodePromoClient $element) use ($codePromoPeriodeValiditeSite) {
                                return $element->getCodePromoPeriodeValidite() == $codePromoPeriodeValiditeSite;
                            })->first();
                            if (!empty($codePromoClientSite)) {
                                $codePromoClientSite->setCodePromoPeriodeValidite(null);
                                $emSite->remove($codePromoClientSite);
                            }
//                            $entitySite->removeCodePromoPeriodeValidite($codePromoPeriodeValiditeSite);
                            $emSite->remove($codePromoPeriodeValiditeSite);
                        }
                    }
                }
//                 *** fin code promo periode validité ***

                // *** gestion code promo periode séjour ***
                if (!empty($entity->getCodePromoPeriodeSejours()) && !$entity->getCodePromoPeriodeSejours()->isEmpty()) {
                    /** @var CodePromoPeriodeSejour $codePromoPeriodeSejour */
                    foreach ($entity->getCodePromoPeriodeSejours() as $codePromoPeriodeSejour) {
                        $codePromoPeriodeSejourSite = $entitySite->getCodePromoPeriodeSejours()->filter(function (CodePromoPeriodeSejour $element) use ($codePromoPeriodeSejour) {
                            return $element->getId() == $codePromoPeriodeSejour->getId();
                        })->first();
                        if (false === $codePromoPeriodeSejourSite) {
                            $codePromoPeriodeSejourSite = new CodePromoPeriodeSejour();
                            $entitySite->addCodePromoPeriodeSejour($codePromoPeriodeSejourSite);
                            $codePromoPeriodeSejourSite
                                ->setId($codePromoPeriodeSejour->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoPeriodeSejourSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $codePromoPeriodeSejourSite
                            ->setDateDebut($codePromoPeriodeSejour->getDateDebut())
                            ->setDateFin($codePromoPeriodeSejour->getDateFin());
                    }
                }

                if (!empty($entitySite->getCodePromoPeriodeSejours()) && !$entitySite->getCodePromoPeriodeSejours()->isEmpty()) {
                    /** @var CodePromoPeriodeSejour $codePromoPeriodeSejour */
                    foreach ($entitySite->getCodePromoPeriodeSejours() as $codePromoPeriodeSejourSite) {
                        $codePromoPeriodeSejour = $entity->getCodePromoPeriodeSejours()->filter(function (CodePromoPeriodeSejour $element) use ($codePromoPeriodeSejourSite) {
                            return $element->getId() == $codePromoPeriodeSejourSite->getId();
                        })->first();
                        if (false === $codePromoPeriodeSejour) {
//                            $entitySite->removeCodePromoPeriodeSejour($codePromoPeriodeSejourSite);
                            $emSite->remove($codePromoPeriodeSejourSite);
                        }
                    }
                }
                // *** fin gestion code promo periode séjour ***

                // *** gestion code promo application ***
                if (!empty($entity->getCodePromoApplications()) && !$entity->getCodePromoApplications()->isEmpty()) {
                    /** @var CodePromoApplication $codePromoApplication */
                    foreach ($entity->getCodePromoApplications() as $codePromoApplication) {
                        $codePromoApplicationSite = $entitySite->getCodePromoApplications()->filter(function (CodePromoApplication $element) use ($codePromoApplication) {
                            return $element->getId() == $codePromoApplication->getId();
                        })->first();
                        if (false === $codePromoApplicationSite) {
                            $codePromoApplicationSite = new CodePromoApplication();
                            $entitySite->addCodePromoApplication($codePromoApplicationSite);
                            $codePromoApplicationSite
                                ->setId($codePromoApplication->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoApplicationSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $codePromoApplicationSite
                            ->setApplication($codePromoApplication->getApplication());
                    }
                }

                if (!empty($entitySite->getCodePromoApplications()) && !$entitySite->getCodePromoApplications()->isEmpty()) {
                    /** @var CodePromoApplication $codePromoApplication */
                    foreach ($entitySite->getCodePromoApplications() as $codePromoApplicationSite) {
                        $codePromoApplication = $entity->getCodePromoApplications()->filter(function (CodePromoApplication $element) use ($codePromoApplicationSite) {
                            return $element->getId() == $codePromoApplicationSite->getId();
                        })->first();
                        if (false === $codePromoApplication) {
//                            $entitySite->removeCodePromoApplication($codePromoApplicationSite);
                            $emSite->remove($codePromoApplicationSite);
                        }
                    }
                }
                // *** fin gestion code promo application ***

                // *** gestion code promo fournisseur ***
                if (!empty($entity->getCodePromoFournisseurs()) && !$entity->getCodePromoFournisseurs()->isEmpty()) {
                    /** @var CodePromoFournisseur $codePromoFournisseur */
                    foreach ($entity->getCodePromoFournisseurs() as $codePromoFournisseur) {
                        $codePromoFournisseurSite = $entitySite->getCodePromoFournisseurs()->filter(function (CodePromoFournisseur $element) use ($codePromoFournisseur) {
                            return $element->getId() == $codePromoFournisseur->getId();
                        })->first();
                        if (false === $codePromoFournisseurSite) {
                            $codePromoFournisseurSite = new CodePromoFournisseur();
                            $entitySite->addCodePromoFournisseur($codePromoFournisseurSite);
                            $codePromoFournisseurSite
                                ->setId($codePromoFournisseur->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoFournisseurSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $codePromoFournisseurSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $codePromoFournisseur->getFournisseur()))
                            ->setType($codePromoFournisseur->getType())
                        ;
                    }
                }

                if (!empty($entitySite->getCodePromoFournisseurs()) && !$entitySite->getCodePromoFournisseurs()->isEmpty()) {
                    /** @var CodePromoFournisseur $codePromoFournisseur */
                    foreach ($entitySite->getCodePromoFournisseurs() as $codePromoFournisseurSite) {
                        $codePromoFournisseur = $entity->getCodePromoFournisseurs()->filter(function (CodePromoFournisseur $element) use ($codePromoFournisseurSite) {
                            return $element->getId() == $codePromoFournisseurSite->getId();
                        })->first();
                        if (false === $codePromoFournisseur) {
//                            $entitySite->removeCodePromoFournisseur($codePromoFournisseurSite);
                            $emSite->remove($codePromoFournisseurSite);
                        }
                    }
                }
                // *** fin gestion code promo fournisseur ***

                // *** gestion code promo hebergement ***
                if (!empty($entity->getCodePromoHebergements()) && !$entity->getCodePromoHebergements()->isEmpty()) {
                    /** @var CodePromoHebergement $codePromoHebergement */
                    foreach ($entity->getCodePromoHebergements() as $codePromoHebergement) {
                        $codePromoHebergementSite = $entitySite->getCodePromoHebergements()->filter(function (CodePromoHebergement $element) use ($codePromoHebergement) {
                            return $element->getId() == $codePromoHebergement->getId();
                        })->first();
                        if (false === $codePromoHebergementSite) {
                            $codePromoHebergementSite = new CodePromoHebergement();
                            $entitySite->addCodePromoHebergement($codePromoHebergementSite);
                            $codePromoHebergementSite
                                ->setId($codePromoHebergement->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoHebergementSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $codePromoHebergementSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $codePromoHebergement->getFournisseur()))
                            ->setHebergement($emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $codePromoHebergement->getHebergement()->getHebergementUnifie())));
                    }
                }

                if (!empty($entitySite->getCodePromoHebergements()) && !$entitySite->getCodePromoHebergements()->isEmpty()) {
                    /** @var CodePromoHebergement $codePromoHebergement */
                    foreach ($entitySite->getCodePromoHebergements() as $codePromoHebergementSite) {
                        $codePromoHebergement = $entity->getCodePromoHebergements()->filter(function (CodePromoHebergement $element) use ($codePromoHebergementSite) {
                            return $element->getId() == $codePromoHebergementSite->getId();
                        })->first();
                        if (false === $codePromoHebergement) {
//                            $entitySite->removeCodePromoHebergement($codePromoHebergementSite);
                            $emSite->remove($codePromoHebergementSite);
                        }
                    }
                }
                // *** fin gestion code promo hebergement ***

                // *** gestion code promo fournisseurPrestationAnnexe ***
                if (!empty($entity->getCodePromoFournisseurPrestationAnnexes()) && !$entity->getCodePromoFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnexe */
                    foreach ($entity->getCodePromoFournisseurPrestationAnnexes() as $codePromoFournisseurPrestationAnnexe) {
                        $codePromoFournisseurPrestationAnnexeSite = $entitySite->getCodePromoFournisseurPrestationAnnexes()->filter(function (CodePromoFournisseurPrestationAnnexe $element) use ($codePromoFournisseurPrestationAnnexe) {
                            return $element->getId() == $codePromoFournisseurPrestationAnnexe->getId();
                        })->first();
                        if (false === $codePromoFournisseurPrestationAnnexeSite) {
                            $codePromoFournisseurPrestationAnnexeSite = new CodePromoFournisseurPrestationAnnexe();
                            $entitySite->addCodePromoFournisseurPrestationAnnex($codePromoFournisseurPrestationAnnexeSite);
                            $codePromoFournisseurPrestationAnnexeSite
                                ->setId($codePromoFournisseurPrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoFournisseurPrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $codePromoFournisseurPrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $codePromoFournisseurPrestationAnnexe->getFournisseur()))
                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class , $codePromoFournisseurPrestationAnnexe->getFournisseurPrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getCodePromoFournisseurPrestationAnnexes()) && !$entitySite->getCodePromoFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnexe */
                    foreach ($entitySite->getCodePromoFournisseurPrestationAnnexes() as $codePromoFournisseurPrestationAnnexeSite) {
                        $codePromoFournisseurPrestationAnnexe = $entity->getCodePromoFournisseurPrestationAnnexes()->filter(function (CodePromoFournisseurPrestationAnnexe $element) use ($codePromoFournisseurPrestationAnnexeSite) {
                            return $element->getId() == $codePromoFournisseurPrestationAnnexeSite->getId();
                        })->first();
                        if (false === $codePromoFournisseurPrestationAnnexe) {
//                            $entitySite->removeCodePromoFournisseurPrestationAnnexe($codePromoFournisseurPrestationAnnexeSite);
                            $emSite->remove($codePromoFournisseurPrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion code promo fournisseurPrestationAnnexe ***

                // *** gestion code promo famillePrestationAnnexe ***
                if (!empty($entity->getCodePromoFamillePrestationAnnexes()) && !$entity->getCodePromoFamillePrestationAnnexes()->isEmpty()) {
                    /** @var CodePromoFamillePrestationAnnexe $codePromoFamillePrestationAnnexe */
                    foreach ($entity->getCodePromoFamillePrestationAnnexes() as $codePromoFamillePrestationAnnexe) {
                        $codePromoFamillePrestationAnnexeSite = $entitySite->getCodePromoFamillePrestationAnnexes()->filter(function (CodePromoFamillePrestationAnnexe $element) use ($codePromoFamillePrestationAnnexe) {
                            return $element->getId() == $codePromoFamillePrestationAnnexe->getId();
                        })->first();
                        if (false === $codePromoFamillePrestationAnnexeSite) {
                            $codePromoFamillePrestationAnnexeSite = new CodePromoFamillePrestationAnnexe();
                            $entitySite->addCodePromoFamillePrestationAnnex($codePromoFamillePrestationAnnexeSite);
                            $codePromoFamillePrestationAnnexeSite
                                ->setId($codePromoFamillePrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($codePromoFamillePrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $codePromoFamillePrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $codePromoFamillePrestationAnnexe->getFournisseur()))
                            ->setFamillePrestationAnnexe($emSite->find(FamillePrestationAnnexe::class , $codePromoFamillePrestationAnnexe->getFamillePrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getCodePromoFamillePrestationAnnexes()) && !$entitySite->getCodePromoFamillePrestationAnnexes()->isEmpty()) {
                    /** @var CodePromoFamillePrestationAnnexe $codePromoFamillePrestationAnnexe */
                    foreach ($entitySite->getCodePromoFamillePrestationAnnexes() as $codePromoFamillePrestationAnnexeSite) {
                        $codePromoFamillePrestationAnnexe = $entity->getCodePromoFamillePrestationAnnexes()->filter(function (CodePromoFamillePrestationAnnexe $element) use ($codePromoFamillePrestationAnnexeSite) {
                            return $element->getId() == $codePromoFamillePrestationAnnexeSite->getId();
                        })->first();
                        if (false === $codePromoFamillePrestationAnnexe) {
//                            $entitySite->removeCodePromoFamillePrestationAnnexe($codePromoFamillePrestationAnnexeSite);
                            $emSite->remove($codePromoFamillePrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion code promo famillePrestationAnnexe ***

                // *** gestion code promo logement ***
                if (!empty($entitySite->getCodePromoLogements()) && !$entitySite->getCodePromoLogements()->isEmpty()) {
                    /** @var CodePromoLogement $codePromoLogement */
                    foreach ($entitySite->getCodePromoLogements() as $codePromoLogementSite) {
                        $codePromoLogement = $entity->getCodePromoLogements()->filter(function (CodePromoLogement $element) use ($codePromoLogementSite) {
                            return $element->getId() == $codePromoLogementSite->getId();
                        })->first();
                        if (false === $codePromoLogement) {
//                            $entitySite->removeCodePromoLogement($codePromoLogementSite);
                            $emSite->remove($codePromoLogementSite);
                        }
                    }
                }
                // *** fin gestion code promo logement ***

                $entityUnifieSite
                    ->setCode($entityUnifie->getCode());
                //  copie des données codePromo
                $entitySite
                    ->setActifSite($entity->getActifSite())
                    ->setLibelle($entity->getLibelle())
                    ->setValeurRemise($entity->getValeurRemise())
                    ->setPrixMini($entity->getPrixMini())
                    ->setActif($entity->getActif())
                    ->setClientAffectation($entity->getClientAffectation())
                    ->setTypeRemise($entity->getTypeRemise())
                    ->setUsageCodePromo($entity->getUsageCodePromo());

                $emSite->persist($entityUnifieSite);

                $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a CodePromoUnifie entity.
     *
     */
    public function showAction(CodePromoUnifie $codePromoUnifie)
    {
        $deleteForm = $this->createDeleteForm($codePromoUnifie);

        return $this->render('@MondofuteCodePromo/codepromounifie/show.html.twig', array(
            'codePromoUnifie' => $codePromoUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a CodePromoUnifie entity.
     *
     * @param CodePromoUnifie $codePromoUnifie The CodePromoUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CodePromoUnifie $codePromoUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('codepromo_delete', array('id' => $codePromoUnifie->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing CodePromoUnifie entity.
     *
     */
    public function editAction(Request $request, CodePromoUnifie $codePromoUnifie)
    {
        /** @var CodePromoClient $codePromoClient */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));

        // *** gestion code promo application ***
        $applications = Application::$libelles;

        $originalCodePromoApplications = new ArrayCollection();
        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            $originalCodePromoApplications->set($codePromo->getSite()->getId(), new ArrayCollection());
            foreach ($codePromo->getCodePromoApplications() as $codePromoApplication) {
                $originalCodePromoApplications->get($codePromo->getSite()->getId())->add($codePromoApplication);
            }
        }
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        // *** fin gestion code promo application ***

        // *** gestion code promo fournisseur ***
        $originalCodePromoFournisseurs = new ArrayCollection();

        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            $originalCodePromoFournisseurs->set($codePromo->getSite()->getId(), new ArrayCollection());
            foreach ($codePromo->getCodePromoFournisseurs() as $codePromoFournisseur) {
                $originalCodePromoFournisseurs->get($codePromo->getSite()->getId())->add($codePromoFournisseur);
            }
        }
        // *** fin gestion code promo fournisseur ***

        // *** gestion code promo hebergement ***
        $originalCodePromoHebergements = new ArrayCollection();
//        $fournisseurHebergements = new ArrayCollection();
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
//            $fournisseurHebergements->set($codePromo->getId(), new ArrayCollection());
            $originalCodePromoHebergements->set($codePromo->getSite()->getId(), new ArrayCollection());
            /** @var CodePromoHebergement $codePromoHebergement */
            foreach ($codePromo->getCodePromoHebergements() as $codePromoHebergement) {
                $originalCodePromoHebergements->get($codePromo->getSite()->getId())->add($codePromoHebergement);
//                if (empty($fournisseurHebergements->get($codePromo->getId())->get($codePromoHebergement->getFournisseur()->getId()))) {
//                    $fournisseurHebergements->get($codePromo->getId())->set($codePromoHebergement->getFournisseur()->getId(), new ArrayCollection());
//                }
            }
        }
//        foreach ($fournisseurHebergements as $codePomoId => $fournisseurHebergement) {
//            foreach ($fournisseurHebergement as $fournisseurId => $item) {
//                $siteId = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) use ($codePomoId) {
//                    return $element->getId() == $codePomoId;
//                })->first()->getSite()->getId();
//                $hebergements = $em->getRepository(HebergementUnifie::class)->getFournisseurHebergements($fournisseurId, $this->container->getParameter('locale'), $siteId);
//                foreach ($hebergements as $hebergement) {
//                    $fournisseurHebergement->get($fournisseurId)->add($hebergement);
//                }
//            }
//        }
        // *** fin gestion code promo hebergement ***

        // *** gestion code promo fournisseurPrestationAnnexe ***
        $originalCodePromoFournisseurPrestationAnnexes = new ArrayCollection();
//        $fournisseurFournisseurPrestationAnnexes = new ArrayCollection();
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
//            $fournisseurFournisseurPrestationAnnexes->set($codePromo->getId(), new ArrayCollection());
            $originalCodePromoFournisseurPrestationAnnexes->set($codePromo->getSite()->getId(), new ArrayCollection());
            /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnexe */
            foreach ($codePromo->getCodePromoFournisseurPrestationAnnexes() as $codePromoFournisseurPrestationAnnexe) {
                $originalCodePromoFournisseurPrestationAnnexes->get($codePromo->getSite()->getId())->add($codePromoFournisseurPrestationAnnexe);
//                if (empty($fournisseurFournisseurPrestationAnnexes->get($codePromo->getId())->get($codePromoFournisseurPrestationAnnexe->getFournisseur()->getId()))) {
//                    $fournisseurFournisseurPrestationAnnexes->get($codePromo->getId())->set($codePromoFournisseurPrestationAnnexe->getFournisseur()->getId(), new ArrayCollection());
//                }
            }
        }
//        foreach ($fournisseurFournisseurPrestationAnnexes as $codePomoId => $fournisseurFournisseurPrestationAnnexe) {
//            foreach ($fournisseurFournisseurPrestationAnnexe as $fournisseurId => $item) {
//                $fournisseurPrestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->getFournisseurPrestationAnnexes($fournisseurId, $this->container->getParameter('locale'));
//                foreach ($fournisseurPrestationAnnexes as $fournisseurPrestationAnnexe) {
//                    $fournisseurFournisseurPrestationAnnexe->get($fournisseurId)->add($fournisseurPrestationAnnexe);
//                }
//            }
//        }
//        dump($fournisseurFournisseurPrestationAnnexes);die;
        // *** fin gestion code promo fournisseurPrestationAnnexe ***


        // *** gestion code promo famillePrestationAnnexe ***
        $originalCodePromoFamillePrestationAnnexes = new ArrayCollection();
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            $originalCodePromoFamillePrestationAnnexes->set($codePromo->getSite()->getId(), new ArrayCollection());
            /** @var CodePromoFamillePrestationAnnexe $codePromoFamillePrestationAnnex */
            foreach ($codePromo->getCodePromoFamillePrestationAnnexes() as $codePromoFamillePrestationAnnex) {
                $originalCodePromoFamillePrestationAnnexes->get($codePromo->getSite()->getId())->add($codePromoFamillePrestationAnnex);
            }
        }
        // *** fin gestion code promo fournisseurPrestationAnnexe ***

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var CodePromo $codePromo */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                if ($codePromo->getActifSite()) {
                    array_push($sitesAEnregistrer, $codePromo->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        // *** gestion code promo client ***
        $clientsBySites = $this->getClientsBySites($sitesAEnregistrer, $sites, $request);
        // *** fin gestion code promo client ***

        $this->ajouterCodePromosDansForm($codePromoUnifie);

        $this->codePromosSortByAffichage($codePromoUnifie);
        $deleteForm = $this->createDeleteForm($codePromoUnifie);

        $originalCodePromoClients = new ArrayCollection();
        $originalCodePromoPeriodeValidites = array();

        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            foreach ($codePromo->getCodePromoClients() as $codePromoClient) {
                $client = $originalCodePromoClients->filter(function (CodePromoClient $element) use ($codePromoClient) {
                    return ($element->getClient() == $codePromoClient->getClient() && $element->getCodePromo()->getSite() == $codePromoClient->getCodePromo()->getSite());
                })->first();
                if (false === $client) {
                    $originalCodePromoClients->add($codePromoClient);
                }
            }
            foreach ($codePromo->getCodePromoPeriodeValidites() as $codePromoPeriodeValidite) {
                if (empty($originalCodePromoPeriodeValidites[$codePromo->getSite()->getId()])) {
                    $originalCodePromoPeriodeValidites[$codePromo->getSite()->getId()] = new ArrayCollection();
                }
                $originalCodePromoPeriodeValidites[$codePromo->getSite()->getId()]->add($codePromoPeriodeValidite);
            }
        }

        $editForm = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType',
            $codePromoUnifie, array('clients' => $originalCodePromoClients->getValues()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        $codeExists = $this->testCodeExists($codePromoUnifie);
        if ($codeExists) {
            $this->addFlash('error', "Ce code existe déjà. ");
        }

        if ($editForm->isSubmitted() && $editForm->isValid() && !$codeExists) {
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                if (false === in_array($codePromo->getSite()->getId(), $sitesAEnregistrer)) {
                    $codePromo->setActifSite(false);
                } else {
                    $codePromo->setActifSite(true);
                }

                // remove the relationship between the codePromoClient and the codePromo
                /** @var CodePromoClient $codePromoClient */
                foreach ($codePromo->getCodePromoClients() as $codePromoClient) {
                    $exist = in_array($codePromoClient->getClient()->getId(), $clientsBySites[$codePromo->getSite()->getId()]);
                    if (!$exist || $codePromo->getClientAffectation() != ClientAffectation::existants) {
                        // remove the Task from the Tag
                        $codePromo->getCodePromoClients()->removeElement($codePromoClient);
                        $em->remove($codePromoClient);
                    }
                }

                if (!empty($originalCodePromoPeriodeValidites[$codePromo->getSite()->getId()])) {
                    foreach ($originalCodePromoPeriodeValidites[$codePromo->getSite()->getId()] as $codePromoPeriodeValidite) {
                        $codePromoClient = $codePromo->getCodePromoClients()->filter(function (CodePromoClient $element) use ($codePromoPeriodeValidite) {
                            return $element->getCodePromoPeriodeValidite() == $codePromoPeriodeValidite;
                        })->first();
                        if (false === $codePromo->getCodePromoPeriodeValidites()->contains($codePromoPeriodeValidite)) {
                            if (!empty($codePromoClient)) {
                                $codePromoClient->setCodePromoPeriodeValidite(null);
                            }
                            $em->remove($codePromoPeriodeValidite);
                        }
                    }
                }
            }

            // *** gestion de code promo periode validite ***
            $this->addCodePromoPeriode($codePromoUnifie, 'Validite', CodePromoPeriodeValidite::class);
            // *** fin gestion de code promo periode validite ***

            // *** gestion de code promo periode sejour ***
            $this->addCodePromoPeriode($codePromoUnifie, 'Sejour', CodePromoPeriodeSejour::class);
            // *** fin gestion de code promo periode sejour ***

            // *** gestion code promo client ***
            $this->gestionCodePromoClient($codePromoUnifie, $clientsBySites);
            // *** fin gestion code promo client ***

            // *** gestion code promo application ***
            $this->gestionCodePromoApplication($codePromoUnifie);

            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                $originalCodePromoApplicationSites = $originalCodePromoApplications->get($codePromo->getSite()->getId());
                foreach ($originalCodePromoApplicationSites as $originalCodePromoApplication) {
                    if (false === $codePromo->getCodePromoApplications()->contains($originalCodePromoApplication)) {
                        $em->remove($originalCodePromoApplication);
                    }
                }
            }
            // *** fin gestion code promo application ***

            // *** gestion code promo fournisseur ***
            $this->gestionCodePromoFournisseur($codePromoUnifie);

            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                $originalCodePromoFournisseurSites = $originalCodePromoFournisseurs->get($codePromo->getSite()->getId());
                foreach ($codePromo->getCodePromoFournisseurs() as $codePromoFournisseur)
                {
                    /** @var ArrayCollection $originalCodePromoFournisseurSites */
                    /** @var CodePromoFournisseur $codePromoFournisseur */
                    $originalCodePromoFournisseur = $originalCodePromoFournisseurSites->filter(function (CodePromoFournisseur $element) use ($codePromoFournisseur){
                        return($element->getFournisseur() == $codePromoFournisseur->getFournisseur()
                            and $element->getType() == $codePromoFournisseur->getType()
                            and $element->getCodePromo() == $codePromoFournisseur->getCodePromo());
                    })->first();
                    if(!empty($originalCodePromoFournisseur)){
                        $codePromo->getCodePromoFournisseurs()->removeElement($codePromoFournisseur);
                        $codePromo->addCodePromoFournisseur($originalCodePromoFournisseur);
                    }
                }
                foreach ($originalCodePromoFournisseurSites as $originalCodePromoFournisseur) {
                    if (false === $codePromo->getCodePromoFournisseurs()->contains($originalCodePromoFournisseur)) {
                        $em->remove($originalCodePromoFournisseur);
                    }
                }
            }
            // *** fin gestion code promo fournisseur ***

            // *** gestion code promo hebergement ***
            $this->gestionCodePromoHebergement($codePromoUnifie);

            /** @var CodePromoHebergement $originalCodePromoHebergement */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                $originalCodePromoHebergementSites = $originalCodePromoHebergements->get($codePromo->getSite()->getId());
                foreach ($codePromo->getCodePromoHebergements() as $codePromoHebergement)
                {
                    /** @var ArrayCollection $originalCodePromoHebergementSites */
                    /** @var CodePromoHebergement $codePromoHebergement */
                    $originalCodePromoHebergement = $originalCodePromoHebergementSites->filter(function (CodePromoHebergement $element) use ($codePromoHebergement){
                        return($element->getHebergement() == $codePromoHebergement->getHebergement()
                            and $element->getFournisseur() == $codePromoHebergement->getFournisseur()
                            and $element->getCodePromo() == $codePromoHebergement->getCodePromo());
                    })->first();
                    if(!empty($originalCodePromoHebergement)){
                        $codePromo->getCodePromoHebergements()->removeElement($codePromoHebergement);
                        $codePromo->addCodePromoHebergement($originalCodePromoHebergement);
                    }
                }
                foreach ($originalCodePromoHebergementSites as $originalCodePromoHebergement) {
                    if (false === $codePromo->getCodePromoHebergements()->contains($originalCodePromoHebergement)) {
//                        $em->detach($originalCodePromoHebergement);
//                        $originalCodePromoHebergement = $em->find(CodePromoHebergement::class, $originalCodePromoHebergement);
//                        $hebergementUnifieId = $originalCodePromoHebergement->getHebergement()->getHebergementUnifie()->getId();
//                        $em->merge($originalCodePromoHebergement);
//                        $logements = new ArrayCollection($em->getRepository(Logement::class)->findByFournisseurHebergement($originalCodePromoHebergement->getFournisseur()->getId(), $hebergementUnifieId, $codePromo->getSite()->getId()));
//                        /** @var CodePromoLogement $codePromoLogement */
//                        foreach ($codePromo->getCodePromoLogements() as $codePromoLogement) {
//                            if ($logements->contains($codePromoLogement->getLogement())) {
//                                $codePromo->getCodePromoLogements()->removeElement($codePromoLogement);
//                                $em->remove($codePromoLogement);
//                            }
//                        }

                        $codePromoLogements = $codePromo->getCodePromoLogements()->filter(function (CodePromoLogement $element) use ($originalCodePromoHebergement){
                            return ($element->getLogement()->getFournisseurHebergement()->getHebergement() == $originalCodePromoHebergement->getHebergement()->getHebergementUnifie()
//                            and $element->getLogement()->getFournisseurHebergement()->getFournisseur() == $originalCodePromoHebergement->getHebergement()->getHebergementUnifie()->get
                            );
                        });
                        foreach ($codePromoLogements as $codePromoLogement)
                        {
                            $codePromo->getCodePromoLogements()->removeElement($codePromoLogement);
                            $em->remove($codePromoLogement);
                        }
                        $codePromo->getCodePromoHebergements()->removeElement($originalCodePromoHebergement);
                        $em->remove($originalCodePromoHebergement);
                    }
                }
            }
            // *** fin gestion code promo hebergement ***

            // *** gestion code promo fournisseurPrestationAnnexe ***
            $this->gestionCodePromoFournisseurPrestationAnnexe($codePromoUnifie);

            /** @var CodePromoFournisseurPrestationAnnexe $originalCodePromoFournisseurPrestationAnnexe */
            /** @var CodePromoFournisseurPrestationAnnexe $originalCodePromoFournisseurPrestationAnnexe */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                $originalCodePromoFournisseurPrestationAnnexeSites = $originalCodePromoFournisseurPrestationAnnexes->get($codePromo->getSite()->getId());
                foreach ($codePromo->getCodePromoFournisseurPrestationAnnexes() as $codePromoFournisseurPrestationAnnex)
                {
                    /** @var ArrayCollection $originalCodePromoFournisseurPrestationAnnexeSites */
                    /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnex */
                    $originalCodePromoFournisseurPrestationAnnexe = $originalCodePromoFournisseurPrestationAnnexeSites->filter(function (CodePromoFournisseurPrestationAnnexe $element) use ($codePromoFournisseurPrestationAnnex){
                        return($element->getFournisseurPrestationAnnexe() == $codePromoFournisseurPrestationAnnex->getFournisseurPrestationAnnexe()
                            and $element->getFournisseur() == $codePromoFournisseurPrestationAnnex->getFournisseur()
                            and $element->getCodePromo() == $codePromoFournisseurPrestationAnnex->getCodePromo());
                    })->first();
                    if(!empty($originalCodePromoFournisseurPrestationAnnexe)){
                        $codePromo->getCodePromoFournisseurPrestationAnnexes()->removeElement($codePromoFournisseurPrestationAnnex);
                        $codePromo->addCodePromoFournisseurPrestationAnnex($originalCodePromoFournisseurPrestationAnnexe);
                    }
                }
                foreach ($originalCodePromoFournisseurPrestationAnnexeSites as $originalCodePromoFournisseurPrestationAnnexe) {
                    if (false === $codePromo->getCodePromoFournisseurPrestationAnnexes()->contains($originalCodePromoFournisseurPrestationAnnexe)) {
                        $codePromo->getCodePromoFournisseurPrestationAnnexes()->removeElement($originalCodePromoFournisseurPrestationAnnexe);
                        $em->remove($originalCodePromoFournisseurPrestationAnnexe);
                    }
                }
            }
            // *** fin gestion code promo fournisseurPrestationAnnexe ***

            // *** gestion code promo famillePrestationAnnexe ***
            $this->gestionCodePromoFamillePrestationAnnexe($codePromoUnifie);

            /** @var CodePromoFamillePrestationAnnexe $originalCodePromoFamillePrestationAnnexe */
            /** @var CodePromoFamillePrestationAnnexe $originalCodePromoFamillePrestationAnnexe */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                $originalCodePromoFamillePrestationAnnexeSites = $originalCodePromoFamillePrestationAnnexes->get($codePromo->getSite()->getId());
                foreach ($codePromo->getCodePromoFamillePrestationAnnexes() as $codePromoFamillePrestationAnnex)
                {
                    /** @var ArrayCollection $originalCodePromoFamillePrestationAnnexeSites */
                    /** @var CodePromoFamillePrestationAnnexe $codePromoFamillePrestationAnnex */
                    $originalCodePromoFamillePrestationAnnexe = $originalCodePromoFamillePrestationAnnexeSites->filter(function (CodePromoFamillePrestationAnnexe $element) use ($codePromoFamillePrestationAnnex){
                        return($element->getFamillePrestationAnnexe() == $codePromoFamillePrestationAnnex->getFamillePrestationAnnexe()
                            and $element->getFournisseur() == $codePromoFamillePrestationAnnex->getFournisseur()
                            and $element->getCodePromo() == $codePromoFamillePrestationAnnex->getCodePromo());
                    })->first();
                    if(!empty($originalCodePromoFamillePrestationAnnexe)){
                        $codePromo->getCodePromoFamillePrestationAnnexes()->removeElement($codePromoFamillePrestationAnnex);
                        $codePromo->addCodePromoFamillePrestationAnnex($originalCodePromoFamillePrestationAnnexe);
                    }
                }
                foreach ($originalCodePromoFamillePrestationAnnexeSites as $originalCodePromoFamillePrestationAnnexe) {
                    if (false === $codePromo->getCodePromoFamillePrestationAnnexes()->contains($originalCodePromoFamillePrestationAnnexe)) {
                        $codePromo->getCodePromoFamillePrestationAnnexes()->removeElement($originalCodePromoFamillePrestationAnnexe);
                        $em->remove($originalCodePromoFamillePrestationAnnexe);
                    }
                }
            }
            // *** fin gestion code promo famillePrestationAnnexe ***

            $em->persist($codePromoUnifie);
            $em->flush();

            $this->copieVersSites($codePromoUnifie);

            // *** gestion code promo logement ***
            $this->gestionCodePromoLogement($codePromoUnifie);
            // *** fin gestion code promo logement ***

            // add flash messages
            /** @var Session $session */
            $this->addFlash('success', 'La code promo a bien été modifié.');

            return $this->redirectToRoute('codepromo_edit', array('id' => $codePromoUnifie->getId()));
        }

        return $this->render('@MondofuteCodePromo/codepromounifie/edit.html.twig', array(
            'entity' => $codePromoUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'codePromoClients' => $originalCodePromoClients,
            'applications' => $applications,
            'panelCodePromo' => true,
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
//            'fournisseurHebergements' => $fournisseurHebergements,
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
//            'fournisseurFournisseurPrestationAnnexes' => $fournisseurFournisseurPrestationAnnexes,
        ));
    }

    /**
     * @param CodePromoUnifie $codePromoUnifie
     */
    private function gestionCodePromoFournisseur($codePromoUnifie)
    {
        /** @var CodePromoFournisseur $codePromoFournisseurSite */
        /** @var CodePromoFournisseur $codePromoFournisseurCrm */
        /** @var CodePromoFournisseur $fournisseur */
        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            foreach ($codePromo->getCodePromoFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
                    $codePromo->getCodePromoFournisseurs()->removeElement($fournisseur);
                } else {
                    $fournisseur->setCodePromo($codePromo);
                }
            }
        }
        $codePromoFournisseurCrms = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getCodePromoFournisseurs();
        $fournisseurs = new ArrayCollection();
        foreach ($codePromoFournisseurCrms as $codePromoFournisseurCrm) {
            $fournisseurs->add($codePromoFournisseurCrm);
        }
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            if ($codePromo->getSite()->getCrm() == 0) {
                foreach ($codePromoFournisseurCrms as $key => $fournisseur) {
                    $fournisseurSite = $codePromo->getCodePromoFournisseurs()->filter(function (CodePromoFournisseur $element) use ($fournisseur)
                    {
                        return ($element->getFournisseur() == $fournisseur->getFournisseur() and $element->getType() == $fournisseur->getType());
                    })->first();
                    if (false === $fournisseurSite) {
                        $newFournisseur = new CodePromoFournisseur();
                        $codePromo->addCodePromoFournisseur($newFournisseur);
                        $newFournisseur
                            ->setFournisseur($fournisseur->getFournisseur())
                            ->setType($fournisseur->getType())
                        ;
                    }
                }
            }
        }
    }

    /**
     * @param CodePromoUnifie $codePromoUnifie
     */
    private function gestionCodePromoHebergement($codePromoUnifie)
    {
        /** @var Hebergement $hebergement */
        /** @var CodePromoHebergement $codePromoHebergementCrm */
        /** @var CodePromoHebergement $codePromoHebergement */
        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            foreach ($codePromo->getCodePromoHebergements() as $codePromoHebergement) {
                if (empty($codePromoHebergement->getHebergement())) {
                    $codePromo->getCodePromoHebergements()->removeElement($codePromoHebergement);
                } else {
                    $codePromoHebergement->setCodePromo($codePromo);
                }
            }
        }
        $codePromoHebergementCrms = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getCodePromoHebergements();
        $hebergements = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($codePromoHebergementCrms as $codePromoHebergementCrm) {
            $hebergements->add($codePromoHebergementCrm->getHebergement());
            $fournisseurs->add($codePromoHebergementCrm->getFournisseur());
        }
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            if ($codePromo->getSite()->getCrm() == 0) {
                $hebergementSites = new ArrayCollection();
                foreach ($codePromo->getCodePromoHebergements() as $codePromoHebergementSite) {
                    $hebergementSites->add($codePromoHebergementSite->getHebergement());
                }

                foreach ($hebergements as $key => $hebergement) {
                    $hebergementSite = $hebergement->getHebergementUnifie()->getHebergements()->filter(function (Hebergement $element) use ($codePromo)
                    {
                        return  $element->getSite() == $codePromo->getSite();
                    })->first();
                    if (false === $hebergementSites->contains($hebergementSite)) {
                        $newHebergement = new CodePromoHebergement();
                        $codePromo->addCodePromoHebergement($newHebergement);
                        $newHebergement
                            ->setHebergement($hebergementSite)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param CodePromoUnifie $codePromoUnifie
     */
    private function gestionCodePromoFournisseurPrestationAnnexe($codePromoUnifie)
    {
        /** @var CodePromoFournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        /** @var CodePromoFournisseurPrestationAnnexe $codePromoFournisseurPrestationAnnexeCrm */
        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            foreach ($codePromo->getCodePromoFournisseurPrestationAnnexes() as $fournisseurPrestationAnnexe) {
                if (empty($fournisseurPrestationAnnexe->getFournisseurPrestationAnnexe())) {
                    $codePromo->getCodePromoFournisseurPrestationAnnexes()->removeElement($fournisseurPrestationAnnexe);
                } else {
                    $fournisseurPrestationAnnexe->setCodePromo($codePromo);
                }
            }
        }
        $codePromoFournisseurPrestationAnnexeCrms = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getCodePromoFournisseurPrestationAnnexes();
        $fournisseurPrestationAnnexes = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($codePromoFournisseurPrestationAnnexeCrms as $codePromoFournisseurPrestationAnnexeCrm) {
            $fournisseurPrestationAnnexes->add($codePromoFournisseurPrestationAnnexeCrm->getFournisseurPrestationAnnexe());
            $fournisseurs->add($codePromoFournisseurPrestationAnnexeCrm->getFournisseur());
        }
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            if ($codePromo->getSite()->getCrm() == 0) {
                $fournisseurPrestationAnnexeSites = new ArrayCollection();
                foreach ($codePromo->getCodePromoFournisseurPrestationAnnexes() as $codePromoFournisseurPrestationAnnexeSite) {
                    $fournisseurPrestationAnnexeSites->add($codePromoFournisseurPrestationAnnexeSite->getFournisseurPrestationAnnexe());
                }
                foreach ($fournisseurPrestationAnnexes as $key => $fournisseurPrestationAnnexe) {
                    if (false === $fournisseurPrestationAnnexeSites->contains($fournisseurPrestationAnnexe)) {
                        $newFournisseurPrestationAnnexe = new CodePromoFournisseurPrestationAnnexe();
                        $codePromo->addCodePromoFournisseurPrestationAnnex($newFournisseurPrestationAnnexe);
                        $newFournisseurPrestationAnnexe
                            ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }
    /**
     * @param CodePromoUnifie $codePromoUnifie
     */
    private function gestionCodePromoFamillePrestationAnnexe($codePromoUnifie)
    {
        /** @var CodePromoFamillePrestationAnnexe $famillePrestationAnnexe */
        /** @var CodePromoFamillePrestationAnnexe $codePromoFamillePrestationAnnexeCrm */
        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            foreach ($codePromo->getCodePromoFamillePrestationAnnexes() as $famillePrestationAnnexe) {
                if (empty($famillePrestationAnnexe->getFamillePrestationAnnexe())) {
                    $codePromo->getCodePromoFamillePrestationAnnexes()->removeElement($famillePrestationAnnexe);
                } else {
                    $famillePrestationAnnexe->setCodePromo($codePromo);
                }
            }
        }
        $codePromoFamillePrestationAnnexeCrms = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getCodePromoFamillePrestationAnnexes();
        $famillePrestationAnnexes = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($codePromoFamillePrestationAnnexeCrms as $codePromoFamillePrestationAnnexeCrm) {
            $famillePrestationAnnexes->add($codePromoFamillePrestationAnnexeCrm->getFamillePrestationAnnexe());
            $fournisseurs->add($codePromoFamillePrestationAnnexeCrm->getFournisseur());
        }
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            if ($codePromo->getSite()->getCrm() == 0) {
                $famillePrestationAnnexeSites = new ArrayCollection();
                foreach ($codePromo->getCodePromoFamillePrestationAnnexes() as $codePromoFamillePrestationAnnexeSite) {
                    $famillePrestationAnnexeSites->add($codePromoFamillePrestationAnnexeSite->getFamillePrestationAnnexe());
                }
                foreach ($famillePrestationAnnexes as $key => $famillePrestationAnnexe) {
                    if (false === $famillePrestationAnnexeSites->contains($famillePrestationAnnexe)) {
                        $newFamillePrestationAnnexe = new CodePromoFamillePrestationAnnexe();
                        $codePromo->addCodePromoFamillePrestationAnnex($newFamillePrestationAnnexe);
                        $newFamillePrestationAnnexe
                            ->setFamillePrestationAnnexe($famillePrestationAnnexe)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param CodePromoUnifie $codePromoUnifie
     */
    private function gestionCodePromoLogement($codePromoUnifie)
    {
        $em = $this->getDoctrine()->getManager();

        $job = new Job('creer:codePromoLogementByCodePromoUnifie',
            array(
                'codePromoUnifieId' => $codePromoUnifie->getId()
            ), true, 'codePromoLogementByCodePromoUnifie');
        $em->persist($job);
        $em->flush();
    }

    public function getFournisseurHebergementsAction($codePromoId, $fournisseurId, $siteId)
    {
        $em = $this->getDoctrine()->getManager();
        $hebergements = $em->getRepository(HebergementUnifie::class)->getFournisseurHebergements($fournisseurId, $this->container->getParameter('locale'), $siteId);

        $codePromoHebergements = $em->getRepository(CodePromoHebergement::class )->findBy(array('codePromo' => $codePromoId , 'fournisseur' => $fournisseurId ));

        $codePromoUnifie = new CodePromoUnifie();
        $codePromo = new CodePromo();
        $codePromoUnifie->addCodePromo($codePromo);
        foreach ($codePromoHebergements as $codePromoHebergement)
        {
            $codePromo->addCodePromoHebergement($codePromoHebergement);
        }

        $form = $this->createForm(CodePromoUnifieType::class,  $codePromoUnifie)->createView();

//        dump($form->children['codePromos'][0]);die;

        return $this->render('@MondofuteCodePromo/codepromounifie/get-code-promo-fournisseur-hebergements.html.twig', array(
            'hebergements' => $hebergements,
            'codePromoId' => $codePromoId,
            'fournisseurId' => $fournisseurId,
            'keyCodePromo' => '__keyCodePromo__',
            'codePromo' => $form->children['codePromos'][0],
        ));
    }

    public function getFournisseurPrestationAnnexesAction($codePromoId, $fournisseurId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurPrestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->getFournisseurPrestationAnnexes($fournisseurId , $this->container->getParameter('locale'));
//        $prestationAnnexes = new ArrayCollection();

        $codePromoFournisseurPrestationAnnexes = $em->getRepository(CodePromoFournisseurPrestationAnnexe::class )->findBy(array('codePromo' => $codePromoId , 'fournisseur' => $fournisseurId ));
        $codePromoFamillePrestationAnnexes = $em->getRepository(CodePromoFamillePrestationAnnexe::class )->findBy(array('codePromo' => $codePromoId , 'fournisseur' => $fournisseurId ));
        $codePromoUnifie = new CodePromoUnifie();
        $codePromo = new CodePromo();
        $codePromoUnifie->addCodePromo($codePromo);
        foreach ($codePromoFournisseurPrestationAnnexes as $codePromoFournisseurPrestationAnnex)
        {
            $codePromo->addCodePromoFournisseurPrestationAnnex($codePromoFournisseurPrestationAnnex);
        }
        foreach ($codePromoFamillePrestationAnnexes as $codePromoFamillePrestationAnnex)
        {
            $codePromo->addCodePromoFamillePrestationAnnex($codePromoFamillePrestationAnnex);
        }

        $form = $this->createForm(CodePromoUnifieType::class,  $codePromoUnifie)->createView();

        return $this->render('@MondofuteCodePromo/codepromounifie/get-code-promo-fournisseur-prestation-annexes.html.twig', array(
            'fournisseurPrestationAnnexes' => $fournisseurPrestationAnnexes,
            'codePromoId' => $codePromoId,
            'fournisseurId' => $fournisseurId,
            'keyCodePromo' => '__keyCodePromo__',
            'codePromo' => $form->children['codePromos'][0],
        ));
    }

    /**
     * Deletes a CodePromoUnifie entity.
     *
     */
    public function deleteAction(Request $request, CodePromoUnifie $codePromoUnifie)
    {
        /** @var CodePromo $codePromoSite */
        /** @var CodePromo $codePromo */
        $form = $this->createDeleteForm($codePromoUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $codePromoUnifieSite = $emSite->find(CodePromoUnifie::class, $codePromoUnifie);
                if (!empty($codePromoUnifieSite)) {
                    $emSite->remove($codePromoUnifieSite);
                    $emSite->flush();
                }
            }

            $em->remove($codePromoUnifie);
            $em->flush();

            $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('codepromo_index');
    }

    public function getClientsAction($clientName, $codePromoId, $siteId)
    {
        $em = $this->getDoctrine()->getManager();
        $entityUnifie = new CodePromoUnifie();

        $this->ajouterCodePromosDansForm($entityUnifie);
        $this->codePromosSortByAffichage($entityUnifie);

        $codePromoClients = new ArrayCollection();
        if (!empty($codePromoId)) {
            $codePromo = $em->find(CodePromo::class, $codePromoId);
            $codePromoClients = $codePromo->getCodePromoClients();
        }

//        /** @var CodePromo $codePromo */
        $clients = $em->getRepository(Client::class)->getClients($clientName);
        $clientForm = new ArrayCollection();
        foreach ($clients as $client) {

            $clientExist = $codePromoClients->filter(function (CodePromoClient $element) use ($client) {
                return $element->getClient()->getId() == $client->getId();
            })->first();
            if (false === $clientExist) {
                $clientForm->add($client);
            }
        }

//        $famillePrestationAnnexe    = $em->find(FamillePrestationAnnexe::class, $clientName);
        $form = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType', $entityUnifie,
            array(
                'clients' => $clients
            )
        );

        return $this->render('@MondofuteCodePromo/codepromounifie/client.html.twig', array(
            'form' => $form->createView(),
            'siteId' => $siteId,
            'clients' => $clientForm
        ));
    }

}
