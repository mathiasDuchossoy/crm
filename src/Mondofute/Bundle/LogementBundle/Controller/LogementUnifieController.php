<?php

namespace Mondofute\Bundle\LogementBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\JobQueueBundle\Entity\Job;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementPhoto;
use Mondofute\Bundle\LogementBundle\Entity\LogementPhotoTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementBundle\Form\LogementUnifieType;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LogementUnifie controller.
 *
 */
class LogementUnifieController extends Controller
{
    /**
     * Lists all LogementUnifie entities.
     *
     */
    public function indexPopupAction($idFournisseurHebergement, $page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $fournisseurHebergement = $em->getRepository(FournisseurHebergement::class)->find($idFournisseurHebergement);

        $locale = $this->container->getParameter('locale');

        $count = $em
            ->getRepository('MondofuteLogementBundle:LogementUnifie')
            ->countTotalToFournisseur($idFournisseurHebergement);

        $pagination = array(
            'page' => $page,
            'route' => 'fournisseur_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.nom' => 'ASC'
        );

        $entities = $this->getDoctrine()->getRepository('MondofuteLogementBundle:LogementUnifie')
            ->getListToFournisseur($page, $maxPerPage, $locale, $sortbyArray, $idFournisseurHebergement);

        return $this->render('@MondofuteLogement/logementunifie/index_popup.html.twig', array(
            'logementUnifies' => $entities,
            'pagination' => $pagination,
            'fournisseurHebergement' => $fournisseurHebergement
        ));
    }

    /**
     * Lists all LogementUnifie entities.
     *
     */
    public function listAction($idFournisseurHebergement, $page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $fournisseurHebergement = $em->getRepository(FournisseurHebergement::class)->find($idFournisseurHebergement);

        $locale = $this->container->getParameter('locale');

        $count = $em
            ->getRepository('MondofuteLogementBundle:LogementUnifie')
            ->countTotalToFournisseur($idFournisseurHebergement);
        $pagination = array(
            'page' => $page,
            'route' => 'fournisseur_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.nom' => 'ASC'
        );

        $entities = $this->getDoctrine()->getRepository('MondofuteLogementBundle:LogementUnifie')
            ->getListToFournisseur($page, $maxPerPage, $locale, $sortbyArray, $idFournisseurHebergement);

        return $this->render('@MondofuteLogement/logementunifie/logement.html.twig', array(
            'logementUnifies' => $entities,
            'pagination' => $pagination,
            'fournisseurHebergement' => $fournisseurHebergement
        ));
    }

    /**
     * Lists all LogementUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $logementUnifies = $em->getRepository('MondofuteLogementBundle:LogementUnifie')->findAll();

        return $this->render('@MondofuteLogement/logementunifie/index.html.twig', array(
            'logementUnifies' => $logementUnifies,
        ));
    }

//    /**
//     * Lists all LogementUnifie entities.
//     *
//     */
//    public function indexPopupAction($idFournisseurHebergement)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $fournisseurHebergement = $em->getRepository(FournisseurHebergement::class)->find($idFournisseurHebergement);
//        $logementUnifies = $em->getRepository('MondofuteLogementBundle:LogementUnifie')->rechercherParFournisseurHebergement($fournisseurHebergement);
//
//        return $this->render('@MondofuteLogement/logementunifie/index_popup.html.twig', array(
//            'logementUnifies' => $logementUnifies,
//            'fournisseurHebergement' => $fournisseurHebergement,
//        ));
//    }

    /**
     * Creates a new LogementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $logementUnifie = new LogementUnifie();

        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);

        $form = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($logementUnifie);
            $em->flush();
            /** @var Logement $logement */
//            foreach ($logementUnifie->getLogements() as $logement){
//                $job = new Job('mondofute_logement:associer_periodes_command',
//                    array(
//                        'id-logement' => $logement->getId(),
//                        'id-site' => ,
//                        'type-periode' => $periode->getType()->getId(),
//                        'nb-jour' => $periode->getNbJour(),
//                    ), true, 'periode');
//                $em->persist($job);
//                $em->flush();
//            }
            return $this->redirectToRoute('logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'logementUnifie' => $logementUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les logements qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param LogementUnifie $entity
     */
    private function ajouterLogementsDansForm(LogementUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getLogements() as $logement) {
                if ($logement->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($logement->getTraductions()->filter(function (LogementTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new LogementTraduction();
                            $traduction->setLangue($langue);
                            $logement->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $logement = new Logement();
                $logement->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new LogementTraduction();
                    $traduction->setLangue($langue);
                    $logement->addTraduction($traduction);
                }
                $entity->addLogement($logement);
            }
        }
    }

    /**
     * Classe les departements par classementAffichage
     * @param LogementUnifie $entity
     */
    private function logementsSortByAffichage(LogementUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les stations en fonction de leurs ordre d'affichage
        $logements = $entity->getLogements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $logements->getIterator();
        unset($departements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Logement $a, Logement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $logements = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($logements);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setLogements($logements);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $logements
     */
    private function traductionsSortByLangue($logements)
    {
        /** @var ArrayIterator $iterator */
        /** @var Logement $logement */
        foreach ($logements as $logement) {
            $traductions = $logement->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (LogementTraduction $a, LogementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $logement->setTraductions($traductions);
        }
    }

    /**
     * Creates a new LogementUnifie entity.
     *
     */
    public function newPopupAction(Request $request, $idFournisseurHebergement)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();

        $fournisseurHebergement = $em->getRepository(FournisseurHebergement::class)->find($idFournisseurHebergement);
//        $L = $em->getRepository(LogementUnifie::class)->rechercherParFournisseurHebergement($fournisseurHebergement);

        $sitesAEnregistrer = $request->get('sites');

        $logementUnifie = new LogementUnifie();

        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);

        /** @var Logement $logement */
        foreach ($logementUnifie->getLogements() as $logement) {
            $logement->setFournisseurHebergement($fournisseurHebergement);
        }

        $form = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Logement $entity */
            foreach ($logementUnifie->getLogements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            $em = $this->getDoctrine()->getManager();

            // ***** Gestion des Medias *****
            foreach ($request->get('logement_unifie')['logements'] as $key => $logement) {
                if (!empty($logementUnifie->getLogements()->get($key)) && $logementUnifie->getLogements()->get($key)->getSite()->getCrm() == 1) {
                    $logementCrm = $logementUnifie->getLogements()->get($key);
                    if (!empty($logement['photos'])) {
                        foreach ($logement['photos'] as $keyPhoto => $photo) {
                            /** @var LogementPhoto $photoCrm */
                            $photoCrm = $logementCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setLogement($logementCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Logement $logementSite */
                                    $logementSite = $logementUnifie->getLogements()->filter(function (Logement $element
                                    ) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($logementSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var LogementPhoto $logementPhoto */
                                        $logementPhoto = new $typePhoto();
                                        $logementPhoto->setLogement($logementSite);
                                        $logementPhoto->setPhoto($photoCrm->getPhoto());
                                        $logementSite->addPhoto($logementPhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new LogementPhotoTraduction();
                                            /** @var LogementPhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $logementPhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $logementPhoto->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($logementUnifie);
            $em->flush();
            foreach ($sites as $site) {
                if ($site->getCrm() == 1) {
                    foreach ($logementUnifie->getLogements() as $logement) {
                        $job = new Job('mondofute_logement:associer_periodes_command',
                            array(
                                'id-logement' => $logement->getId(),
                                'id-site' => $site->getId()
                            ), true, 'periode');
                        $em->persist($job);
                        $em->flush();
                    }
                }
            }

            $this->copieVersSites($logementUnifie);

            return $this->redirectToRoute('popup_logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/new_popup.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'logementUnifie' => $logementUnifie,
            'form' => $form->createView(),
            'fournisseurHebergement' => $fournisseurHebergement,
        ));
    }

    /**
     * Copie dans la base de données site l'entité hébergement
     * @param LogementUnifie $entity
     */
    private function copieVersSites(LogementUnifie $entity, $originalLogementPhotos = null)
    {
        /** @var HebergementTraduction $hebergementTraduc */
//        Boucle sur les hébergements afin de savoir sur quel site nous devons l'enregistrer
        /** @var Logement $logement */
        foreach ($entity->getLogements() as $logement) {
            if ($logement->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($logement->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $logement->getSite()->getId()));
                if (empty($entity->getId()) || is_null(($entitySite = $emSite->getRepository(LogementUnifie::class)->find($entity->getId())))) {
                    $entitySite = new LogementUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }
                $fournisseurHebergementSite = $emSite->getRepository(FournisseurHebergement::class)->findOneBy(array(
                    'fournisseur' => $logement->getFournisseurHebergement()->getFournisseur(),
                    'hebergement' => $logement->getFournisseurHebergement()->getHebergement()
                ));
                $edit = true;
                if (empty($entity->getId()) || is_null($logementSite = $emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $entity->getId())))) {
                    $logementSite = new Logement();
                    $entitySite->addLogement($logementSite);
                    $edit = false;
                }
                $logementSite->setActif($logement->getActif())
                    ->setAccesPMR($logement->getAccesPMR())
                    ->setCapacite($logement->getCapacite())
                    ->setNbChambre($logement->getNbChambre())
                    ->setSite($site)
                    ->setSuperficieMax($logement->getSuperficieMax())
                    ->setSuperficieMin($logement->getSuperficieMin())
                    ->setLogementUnifie($entitySite)
                    ->setFournisseurHebergement($fournisseurHebergementSite);

                /** @var LogementTraduction $traduction */
                foreach ($logement->getTraductions() as $traduction) {
                    $langue = $emSite->getRepository(Langue::class)->find($traduction->getLangue());
                    if (empty($traduction->getId()) || empty($logementSite) || empty($traductionSite = $emSite->getRepository(LogementTraduction::class)->findOneBy(array(
                            'logement' => $logementSite,
                            'langue' => $traduction->getLangue()
                        )))
                    ) {
                        $traductionSite = new LogementTraduction();
                        $logementSite->addTraduction($traductionSite);
                    }
                    $traductionSite->setDescriptif($traduction->getDescriptif())
                        ->setLangue($langue)
                        ->setLogement($logementSite)
                        ->setNom($traduction->getNom());
                }


                // ********** GESTION DES MEDIAS **********
                $logementPhotos = $logement->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'logement de référence
                if (!empty($logementPhotos) && !$logementPhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($logementSite->getPhotos()) && !$logementSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $logementPhotoSites = new ArrayCollection();
                        foreach ($logementSite->getPhotos() as $logementphotoSite) {
                            $logementPhotoSites->add($logementphotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var LogementPhoto $logementPhoto */
                        foreach ($logementPhotos as $logementPhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'logementPhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalLogementPhotos */
                            $originalLogementPhoto = $originalLogementPhotos->filter(function (LogementPhoto $element
                            ) use ($logementPhoto) {
                                return $element->getPhoto() == $logementPhoto->getPhoto();
                            })->first();
                            unset($logementPhotoSite);
                            if ($originalLogementPhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalLogementPhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalLogementPhoto);

                                $logementPhotoSite = $logementPhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'logementPhoto existe sur la bdd distante, on va le modifier
                            /** @var LogementPhoto $logementPhotoSite */
                            if (!empty($logementPhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'logementPhoto du crm)
                                if ($logementPhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $logementPhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $logementPhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $logementPhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('logement_photo_' . $logement->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($logementPhotoSite->getPhoto());

                                    $logementPhotoSite->setPhoto($clonePhoto);
                                }

                                $logementPhotoSite->setActif($logementPhoto->getActif());

                                // on parcourt les traductions
                                /** @var LogementPhotoTraduction $traduction */
                                foreach ($logementPhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var LogementPhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $logementPhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (
                                            LogementPhotoTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new LogementPhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class,
                                                $traduction->getLangue()->getId()));
                                        $logementPhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createLogementPhoto($logementPhoto, $logementSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'logement de référence
                        /** @var LogementPhoto $logementPhoto */
                        foreach ($logementPhotos as $logementPhoto) {
                            $this->createLogementPhoto($logementPhoto, $logementSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($logementPhotoSites)) {
                        /** @var LogementPhoto $logementPhotoSite */
                        foreach ($logementPhotoSites as $logementPhotoSite) {
                            $logementPhotoSite->setLogement(null);
                            $emSite->remove($logementPhotoSite->getPhoto());
                            $emSite->remove($logementPhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********

                $emSite->persist($entitySite);
                $emSite->flush();
                if (!$edit) {
                    $em = $this->getDoctrine()->getManager();
                    foreach ($entitySite->getLogements() as $logement) {
                        $job = new Job('mondofute_logement:associer_periodes_command',
                            array(
                                'id-logement' => $logement->getId(),
                                'id-site' => $site->getId()
                            ), true, 'periode');
                        $em->persist($job);
                        $em->flush();
                    }
                }
//                $em->persist($job);
//                $em->flush();
            }
        }
    }

    /**
     * Création d'un nouveau logementPhoto
     * @param LogementPhoto $logementPhoto
     * @param Logement $logementSite
     * @param EntityManager $emSite
     */
    private function createLogementPhoto(LogementPhoto $logementPhoto, Logement $logementSite, EntityManager $emSite)
    {
        /** @var LogementPhoto $logementPhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($logementPhoto))->getName();
        // on cré un nouveau LogementPhoto on fonction du type
        $logementPhotoSite = new $typePhoto();
        $logementPhotoSite->setLogement($logementSite);
        $logementPhotoSite->setActif($logementPhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $logementPhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($logementPhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($logementPhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('logement_photo_' . $logementSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $logementPhoto->getPhoto()->getId());

        $logementPhotoSite->setPhoto($clonePhoto);

        $logementSite->addPhoto($logementPhotoSite);
        // on ajoute les traductions correspondante
        foreach ($logementPhoto->getTraductions() as $traduction) {
            $traductionSite = new LogementPhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $logementPhotoSite->addTraduction($traductionSite);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function chargerLocatifAction(Request $request)
    {
        ini_set('memory_limit','128M');
        //        récupère la valeur numérique de memory_limit
        $memory = intval(ini_get('memory_limit'), 10);
//        récupère l'unite de memory_limit, le trim permet de supprimer un éventuel espace
        $unite = trim(substr(ini_get('memory_limit'), strlen($memory)));
//        conversion du memory_limit en octets
        switch (strtolower($unite)) {
            case "g":
                $memoryLimit = $memory * 1024 * 1024 * 1024;
                break;
            case "m":
                $memoryLimit = $memory * 1024 * 1024;
                break;
            case "k":
                $memoryLimit = $memory * 1024;
                break;
            default:
                $memoryLimit = $memory;
                break;
        }
        $memoryLimitPourcentage=80;
        $logementsRef = $request->get('logements');
//        dump($request->get('logements'));
//        die;
        $em = $this->getDoctrine()->getManager();
        $reponse = array();
        $logements = array();
        foreach ($logementsRef as $indiceLogement => $idLogement){
            if (memory_get_usage() >= (($memoryLimit * $memoryLimitPourcentage) / 100)){
                $reponse['suivant'] = $idLogement;
                $reponse['logements'] = $logements;
                return new JsonResponse($reponse);
            }
            $logementRef = $em->getRepository(Logement::class)->chargerPourStocks($idLogement);
//            echo memory_get_usage().PHP_EOL;
            $logement['id'] = $logementRef->getId();
            $logement['logementUnifie']['id'] = $logementRef->getLogementUnifie()->getId();
            foreach ($logementRef->getTraductions() as $traduction) {
                if ($traduction->getLangue()->getCode() == $request->getLocale()) {
                    $logement['nom'] = $traduction->getNom();
                }
            }
            /** @var LogementPeriode $logementPeriodeRef */
            foreach ($logementRef->getPeriodes() as $logementPeriodeRef){
//                $em->getRepository(LogementPeriode::class)->chargerLocatif($logementPeriodeRef);
                $logementPeriode['id']=$logementPeriodeRef->getPeriode()->getId();
                $logementPeriode['type']['id']=$logementPeriodeRef->getPeriode()->getType()->getId();
//            $logementPeriode['debut']=$logementPeriodeRef->getPeriode()->getDebut();
//            $logementPeriode['fin']=$logementPeriodeRef->getPeriode()->getFin();
                $logementPeriode['stock']=$logementPeriodeRef->getLocatif()->getStock();
                $logement['periodes'][]= $logementPeriode;
            }
            array_push($logements,$logement);
        }
        $reponse['logements']=$logements;
        $reponse['suivant'] = null;
//        echo memory_get_peak_usage();
        return new JsonResponse($reponse);
    }

    /**
     * Duplique a new LogementUnifie entity.
     *
     */
    public function dupliquePopupAction(Request $request, LogementUnifie $logementUnifieRef)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();

        $fournisseurHebergement = $logementUnifieRef->getLogements()->first()->getFournisseurHebergement();
//        $L = $em->getRepository(LogementUnifie::class)->rechercherParFournisseurHebergement($fournisseurHebergement);

        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($logementUnifieRef->getLogements() as $logementRef) {
                array_push($sitesAEnregistrer, $logementRef->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }
        $logementUnifie = new LogementUnifie();

        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);
        $this->duplique($logementUnifieRef, $logementUnifie);

        /** @var Logement $logementRef */

        $form = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array(
                'locale' => $request->getLocale(),
                'action' => $this->generateUrl('popup_logement_logement_new',
                    array('idFournisseurHebergement' => $fournisseurHebergement->getId()))
            ));
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->copieVersSites($logementUnifie);
            $em->persist($logementUnifie);
            $em->flush();

            return $this->redirectToRoute('popup_logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/new_popup.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'logementUnifie' => $logementUnifie,
            'form' => $form->createView(),
            'fournisseurHebergement' => $fournisseurHebergement,
        ));
    }

    /**
     * @param LogementUnifie $logementUnifieRef
     * @param LogementUnifie $logementUnifie
     */
    public function duplique($logementUnifieRef, $logementUnifie)
    {
        /** @var Logement $logementRef */
        foreach ($logementUnifieRef->getLogements() as $logementRef) {
//            if(!$logementUnifie->getLogements()->contains($logementRef)){
//                $logement = new Logement();
//                $logementUnifie->addLogement($logement);
//            }else{
            $trouve = false;
            /** @var Logement $l */
            foreach ($logementUnifie->getLogements() as $l) {
                if ($l->getSite() == $logementRef->getSite()) {
                    $logement = $l;
                    $trouve = true;
                    break;
                }
            }
            if ($trouve == false) {
                $logement = new Logement();
                $logementUnifie->addLogement($logement);
            }
//            }
            $logement->setFournisseurHebergement($logementRef->getFournisseurHebergement())
                ->setLogementUnifie($logementUnifie)
                ->setAccesPMR($logementRef->getAccesPMR())
                ->setActif($logementRef->getActif())
                ->setCapacite($logementRef->getCapacite())
                ->setNbChambre($logementRef->getNbChambre())
                ->setSite($logementRef->getSite())
                ->setSuperficieMax($logementRef->getSuperficieMax())
                ->setSuperficieMin($logementRef->getSuperficieMin());
            /** @var LogementTraduction $traductionRef */
            foreach ($logementRef->getTraductions() as $traductionRef) {
                $trouve = false;
                /** @var LogementTraduction $t */
                foreach ($logement->getTraductions() as $t) {
                    if ($t->getLangue() == $traductionRef->getLangue()) {
                        $traduction = $t;
                        $trouve = true;
                        break;
                    }
                }
                if ($trouve == false) {
                    $traduction = new LogementTraduction();
                    $logement->addTraduction($traduction);
                }
                $traduction->setDescriptif($traductionRef->getDescriptif())
                    ->setLangue($traductionRef->getLangue())
                    ->setLogement($logement)
                    ->setNom($traductionRef->getNom());
            }
        }
    }

    /**
     * Finds and displays a LogementUnifie entity.
     *
     */
    public function showAction(LogementUnifie $logementUnifie)
    {
        $deleteForm = $this->createDeleteForm($logementUnifie);

        return $this->render('@MondofuteLogement/logementunifie/show.html.twig', array(
            'logementUnifie' => $logementUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a LogementUnifie entity.
     *
     * @param LogementUnifie $logementUnifie The LogementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LogementUnifie $logementUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('logement_logement_delete', array('id' => $logementUnifie->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and displays a LogementUnifie entity.
     *
     */
    public function showPopupAction(LogementUnifie $logementUnifie)
    {
        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('popup_logement_logement_delete', array('id' => $logementUnifie->getId())))
            ->setMethod('DELETE')
            ->getForm();

        /** @var Logement $logement */
        foreach ($logementUnifie->getLogements() as $logement) {
            if (empty($fournisseurHebergement)) {
                $fournisseurHebergement = $logement->getFournisseurHebergement();
                break;
            }
        }

        return $this->render('@MondofuteLogement/logementunifie/show_popup.html.twig', array(
            'logementUnifie' => $logementUnifie,
            'delete_form' => $deleteForm->createView(),
            'fournisseurHebergement' => $fournisseurHebergement,
        ));
    }

    /**
     * Displays a form to edit an existing LogementUnifie entity.
     * @param Request $request
     * @param LogementUnifie $logementUnifie
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, LogementUnifie $logementUnifie)
    {

        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Logement $entity */
            foreach ($logementUnifie->getLogements() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalLogements = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        foreach ($logementUnifie->getLogements() as $logement) {
            $originalLogements->add($logement);
        }
        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);

        $deleteForm = $this->createDeleteForm($logementUnifie);
        $editForm = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => $this->get('translator')->trans('mettre.a.jour'),
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalLogements as $logement) {
                if (!$logementUnifie->getLogements()->contains($logement)) {

//                    //  suppression de la station sur le site
//                    $emSite = $this->getDoctrine()->getManager($logement->getSite()->getLibelle());
//                    $entitySite = $emSite->find(DepartementUnifie::class, $logementUnifie->getId());
//                    $departementSite = $entitySite->getDepartements()->first();
//                    $emSite->remove($departementSite);
//
//                    $emSite->flush();
////                    dump($departement);
//                    $departement->setDepartementUnifie(null);
                    $em->remove($logement);
                }
            }
            $em->persist($logementUnifie);
            $em->flush();

            return $this->redirectToRoute('logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/edit.html.twig', array(
            'logementUnifie' => $logementUnifie,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
        ));
    }

    /**
     * Displays a form to edit an existing LogementUnifie entity.
     * @param Request $request
     * @param LogementUnifie $logementUnifie
     * @return RedirectResponse|Response
     */
    public function editPopupAction(Request $request, LogementUnifie $logementUnifie)
    {

        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Logement $entity */
            foreach ($logementUnifie->getLogements() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalLogements = new ArrayCollection();

        $originalLogementPhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();

//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        /** @var Logement $logement */
        foreach ($logementUnifie->getLogements() as $logement) {
            if (empty($fournisseurHebergement)) {
                $fournisseurHebergement = $logement->getFournisseurHebergement();
            }
            $originalLogements->add($logement);

            // si l'logement est celui du CRM
            if ($logement->getSite()->getCrm() == 1) {
                // on parcourt les logementPhoto pour les comparer ensuite
                /** @var LogementPhoto $logementPhoto */
                foreach ($logement->getPhotos() as $logementPhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalLogementPhotos->add($logementPhoto);
                    $originalPhotos->add($logementPhoto->getPhoto());
                }
            }
        }
        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);

        $deleteForm = $this->createDeleteForm($logementUnifie);
        $editForm = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => $this->get('translator')->trans('mettre.a.jour'),
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($logementUnifie->getLogements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                } else {
                    $entity->setActif(true);
                }
            }

            // ************* suppression photos *************
            // ** CAS OU L'ON SUPPRIME UN "LOGEMENT PHOTO" **
            // on récupère les LogementPhoto de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Logement $logementCrm */
            $logementCrm = $logementUnifie->getLogements()->filter(function (Logement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $logementSites = $logementUnifie->getLogements()->filter(function (Logement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newLogementPhotos = new ArrayCollection();
            foreach ($logementCrm->getPhotos() as $logementPhoto) {
                $newLogementPhotos->add($logementPhoto);
            }
            /** @var LogementPhoto $originalLogementPhoto */
            foreach ($originalLogementPhotos as $key => $originalLogementPhoto) {

                if (false === $newLogementPhotos->contains($originalLogementPhoto)) {
                    $originalLogementPhoto->setLogement(null);
                    $em->remove($originalLogementPhoto->getPhoto());
                    $em->remove($originalLogementPhoto);
                    // on doit supprimer l'hébergementPhoto des autres sites
                    // on parcourt les logement des sites
                    /** @var Logement $logementSite */
                    foreach ($logementSites as $logementSite) {
                        $logementPhotoSite = $em->getRepository(LogementPhoto::class)->findOneBy(
                            array(
                                'logement' => $logementSite,
                                'photo' => $originalLogementPhoto->getPhoto()
                            ));
                        if (!empty($logementPhotoSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($logementPhotoSite->getLogement()->getSite()->getLibelle());
                            $logementSite = $emSite->getRepository(Logement::class)->findOneBy(
                                array(
                                    'logementUnifie' => $logementPhotoSite->getLogement()->getLogementUnifie()
                                ));
                            $logementPhotoSiteSites = new ArrayCollection($emSite->getRepository(LogementPhoto::class)->findBy(
                                array(
                                    'logement' => $logementSite
                                ))
                            );
                            $logementPhotoSiteSite = $logementPhotoSiteSites->filter(function (LogementPhoto $element)
                            use ($logementPhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $logementPhotoSite->getPhoto()->getProviderReference();
                                return $element->getPhoto()->getMetadataValue('crm_ref_id') == $logementPhotoSite->getPhoto()->getId();
                            })->first();
                            if (!empty($logementPhotoSiteSite)) {
                                $emSite->remove($logementPhotoSiteSite->getPhoto());
                                $logementPhotoSiteSite->setLogement(null);
                                $emSite->remove($logementPhotoSiteSite);
                                $emSite->flush();
                            }
                            $logementPhotoSite->setLogement(null);
                            $em->remove($logementPhotoSite->getPhoto());
                            $em->remove($logementPhotoSite);
                        }
                    }
                }
            }
            // ************* fin suppression photos *************

            // CAS D'UN NOUVEAU 'LOGEMENT PHOTO' OU DE MODIFICATION D'UN "LOGEMENT PHOTO"
            /** @var LogementPhoto $logementPhoto */
            // tableau pour la suppression des anciens photos
            $photoToRemoveCollection = new ArrayCollection();
            $keyCrm = $logementUnifie->getLogements()->indexOf($logementCrm);
            // on parcourt les logementPhotos de l'logement crm
            foreach ($logementCrm->getPhotos() as $key => $logementPhoto) {
                // on active le nouveau logementPhoto (CRM) => il doit être toujours actif
                $logementPhoto->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementPhoto pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Logement $logementSite */
                        $logementSite = $logementUnifie->getLogements()->filter(function (Logement $element) use ($site
                        ) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($logementSite)) {
                            // on réinitialise la variable
                            unset($logementPhotoSite);
                            // s'il ne s'agit pas d'un nouveau logementPhoto
                            if (!empty($logementPhoto->getId())) {
                                // on récupère l'logementPhoto pour le modifier
                                $logementPhotoSite = $em->getRepository(LogementPhoto::class)->findOneBy(array(
                                    'logement' => $logementSite,
                                    'photo' => $originalPhotos->get($key)
                                ));
                            }
                            // si l'logementPhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($logementPhoto->getId()) || empty($logementPhotoSite)) {
                                // on récupère la classe correspondant au photo (photo ou video)
                                $typePhoto = (new ReflectionClass($logementPhoto))->getName();
                                // on créé un nouveau LogementPhoto on fonction du type
                                /** @var LogementPhoto $logementPhotoSite */
                                $logementPhotoSite = new $typePhoto();
                                $logementPhotoSite->setLogement($logementSite);
                            }
                            // si l'hébergemenent photo existe déjà pour le site
                            if (!empty($logementPhotoSite)) {
                                if ($logementPhotoSite->getPhoto() != $logementPhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($logementPhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($logementPhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($logementPhotoSite->getPhoto());
//                                    }
                                    // on met le nouveau photo
                                    $logementPhotoSite->setPhoto($logementPhoto->getPhoto());
                                }
                                $logementSite->addPhoto($logementPhotoSite);

                                /** @var LogementPhotoTraduction $traduction */
                                foreach ($logementPhoto->getTraductions() as $traduction) {
                                    /** @var LogementPhotoTraduction $traductionSite */
                                    $traductionSites = $logementPhotoSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (
                                            LogementPhotoTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new LogementPhotoTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $logementPhotoSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                if (!empty($request->get('logement_unifie')['logements'][$keyCrm]['photos'][$key]['sites']) &&
                                    in_array($site->getId(),
                                        $request->get('logement_unifie')['logements'][$keyCrm]['photos'][$key]['sites'])
                                ) {
                                    $logementPhotoSite->setActif(true);
                                } else {
                                    $logementPhotoSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'logementPhoto CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($logementPhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $photoToRemoveCollection->add($originalPhotos->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($logementUnifie);
            $em->flush();

            $this->copieVersSites($logementUnifie, $originalLogementPhotos);


            if (!empty($photoToRemoveCollection)) {
                foreach ($photoToRemoveCollection as $item) {
                    if (!empty($item)) {
                        $em->remove($item);
                    }
                }
                $em->flush();
            }

            return $this->redirectToRoute('popup_logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/edit_popup.html.twig', array(
            'logementUnifie' => $logementUnifie,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'fournisseurHebergement' => $fournisseurHebergement,
        ));
    }

    /**
     * Deletes a LogementUnifie entity.
     *
     */
    public function deleteAction(Request $request, LogementUnifie $logementUnifie)
    {
        $form = $this->createDeleteForm($logementUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($logementUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('logement_logement_index');
    }

    /**
     * Deletes a LogementUnifie entity.
     *
     */
    public function deletePopupAction(Request $request, LogementUnifie $logementUnifie)
    {
        $form = $this->createDeleteForm($logementUnifie);
        $form->handleRequest($request);
        /** @var Logement $logement */
        foreach ($logementUnifie->getLogements() as $logement) {
            if (empty($fournisseurHebergement)) {
                $fournisseurHebergement = $logement->getFournisseurHebergement();
                break;
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
                $logementUnifieSite = $emSite->find(LogementUnifie::class, $logementUnifie->getId());
                if (!empty($logementUnifieSite)) {
                    $emSite->remove($logementUnifieSite);

                    $logementSite = $logementUnifieSite->getLogements()->first();

                    // si il y a des photos pour l'entité, les supprimer
                    if (!empty($logementSite->getPhotos())) {
                        /** @var LogementPhoto $logementPhotoSite */
                        foreach ($logementSite->getPhotos() as $logementPhotoSite) {
                            $photoSite = $logementPhotoSite->getPhoto();
                            $logementPhotoSite->setPhoto(null);
                            if (!empty($photoSite)) {
                                $emSite->remove($photoSite);
                            }
                        }
                    }


                    $emSite->flush();
                }
            }

            if (!empty($logementUnifie)) {
                if (!empty($logementUnifie->getLogements())) {
                    /** @var Logement $logement */
                    foreach ($logementUnifie->getLogements() as $logement) {

                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($logement->getPhotos())) {
                            /** @var LogementPhoto $logementPhoto */
                            foreach ($logement->getPhotos() as $logementPhoto) {
                                $photo = $logementPhoto->getPhoto();
                                $logementPhoto->setPhoto(null);
                                $em->remove($photo);
                            }
                        }
                    }
                    $em->flush();
                }
//                    $emSite->remove($logementUnifieSite);
//                    $emSite->flush();
            }

            $em->remove($logementUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('popup_logement_logement_index', array(
            'idFournisseurHebergement' => $fournisseurHebergement->getId(),
        ));
    }
}
