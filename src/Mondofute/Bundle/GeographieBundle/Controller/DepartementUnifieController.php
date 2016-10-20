<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementImage;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementImageTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementPhotoTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementVideo;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementVideoTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Mondofute\Bundle\GeographieBundle\Form\DepartementUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DepartementUnifie controller.
 *
 */
class DepartementUnifieController extends Controller
{
    /**
     * Lists all DepartementUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
        $count = $em
            ->getRepository('MondofuteGeographieBundle:DepartementUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'geographie_departement_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteGeographieBundle:DepartementUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteGeographie/departementunifie/index.html.twig', array(
            'departementUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new DepartementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $departementUnifie = new DepartementUnifie();

        $this->ajouterDepartementsDansForm($departementUnifie);
        $this->departementsSortByAffichage($departementUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DepartementUnifieType', $departementUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Departement $entity */
            foreach ($departementUnifie->getDepartements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('departement_unifie')['departements'] as $key => $departement) {
                if (!empty($departementUnifie->getDepartements()->get($key)) && $departementUnifie->getDepartements()->get($key)->getSite()->getCrm() == 1) {
                    $departementCrm = $departementUnifie->getDepartements()->get($key);
                    if (!empty($departement['images'])) {
                        foreach ($departement['images'] as $keyImage => $image) {
                            /** @var DepartementImage $imageCrm */
                            $imageCrm = $departementCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setDepartement($departementCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Departement $departementSite */
                                    $departementSite = $departementUnifie->getDepartements()->filter(function (Departement $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($departementSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var DepartementImage $departementImage */
                                        $departementImage = new $typeImage();
                                        $departementImage->setDepartement($departementSite);
                                        $departementImage->setImage($imageCrm->getImage());
                                        $departementSite->addImage($departementImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new DepartementImageTraduction();
                                            /** @var DepartementImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $departementImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $departementImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('departement_unifie')['departements'] as $key => $departement) {
                if (!empty($departementUnifie->getDepartements()->get($key)) && $departementUnifie->getDepartements()->get($key)->getSite()->getCrm() == 1) {
                    $departementCrm = $departementUnifie->getDepartements()->get($key);
                    if (!empty($departement['photos'])) {
                        foreach ($departement['photos'] as $keyPhoto => $photo) {
                            /** @var DepartementPhoto $photoCrm */
                            $photoCrm = $departementCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setDepartement($departementCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Departement $departementSite */
                                    $departementSite = $departementUnifie->getDepartements()->filter(function (Departement $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($departementSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var DepartementPhoto $departementPhoto */
                                        $departementPhoto = new $typePhoto();
                                        $departementPhoto->setDepartement($departementSite);
                                        $departementPhoto->setPhoto($photoCrm->getPhoto());
                                        $departementSite->addPhoto($departementPhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new DepartementPhotoTraduction();
                                            /** @var DepartementPhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $departementPhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $departementPhoto->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            // *** gestion des videos ***
            /** @var Departement $departementCrm */
            $departementCrm = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $departementSites = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            /** @var DepartementVideo $departementVideo */
            foreach ($departementCrm->getVideos() as $key => $departementVideo) {
                foreach ($departementSites as $departementSite) {
                    $departementVideoSite = clone $departementVideo;
                    $departementSite->addVideo($departementVideoSite);
                    $actif = false;
                    if (!empty($request->get('departement_unifie')['departements'][0]['videos'][$key]['sites'])) {
                        if (in_array($departementSite->getSite()->getId(), $request->get('departement_unifie')['departements'][0]['videos'][$key]['sites'])) {
                            $actif = true;
                        }
                    }
                    $departementVideoSite->setActif($actif);
                }
            }
            // *** gestion des videos ***


            $em = $this->getDoctrine()->getManager();
            $em->persist($departementUnifie);
            $em->flush();

            $this->copieVersSites($departementUnifie);
            $this->addFlash('success', 'le département a bien été créé');
            return $this->redirectToRoute('geographie_departement_edit', array('id' => $departementUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/departementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $departementUnifie,
            'form' => $form->createView(),
        ));
    }
//    /**
//     *
//     * @param StationUnifie $entity
//     */
//    private function affilierEntities(DepartementUnifie $entity)
//    {
//        foreach ($entity->getDepartements() as $departement) {
//            if (!empty($departement->getDepartement())) {
//                $zoneTouristique = $station->getZoneTouristique()->getZoneTouristiqueUnifie()->getZoneTouristiques()->filter(function ($element) use ($station) {
//                    return $element->getSite() == $station->getSite();
//                })->first();
//                $station->setZoneTouristique($zoneTouristique);
//            }
//        }
//    }
    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DepartementUnifie $entity
     */
    private function ajouterDepartementsDansForm(DepartementUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDepartements() as $departement) {
                if ($departement->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($departement->getTraductions()->filter(function (DepartementTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DepartementTraduction();
                            $traduction->setLangue($langue);
                            $departement->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $departement = new Departement();
                $departement->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DepartementTraduction();
                    $traduction->setLangue($langue);
                    $departement->addTraduction($traduction);
                }
                $entity->addDepartement($departement);
            }
        }
    }


    /**
     * Classe les departements par classementAffichage
     * @param DepartementUnifie $entity
     */
    private function departementsSortByAffichage(DepartementUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les stations en fonction de leurs ordre d'affichage
        $departements = $entity->getDepartements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $departements->getIterator();
        unset($departements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Departement $a, Departement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $departements = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($departements);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setDepartements($departements);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $departements
     */
    private function traductionsSortByLangue($departements)
    {
        /** @var ArrayIterator $iterator */
        /** @var Departement $departement */
        foreach ($departements as $departement) {
            $traductions = $departement->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (DepartementTraduction $a, DepartementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $departement->setTraductions($traductions);
        }
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param DepartementUnifie $entity
     */
    private function copieVersSites(DepartementUnifie $entity, $originalDepartementImages = null, $originalDepartementPhotos = null)
    {
        /** @var DepartementTraduction $departementTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDepartements() as $departement) {
            if ($departement->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($departement->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $departement->getSite()->getId()));
//                $region = $emSite->getRepository(Region::class)->findOneBy(array('regionUnifie' => $departement->getRegion()->getRegionUnifie()->getId()));
                $region = $emSite->getRepository(Region::class)->findOneBy(array('regionUnifie' => $departement->getRegion()->getRegionUnifie()));
//                dump($region);die;
                // todo: prendre en compte le fait qu'une région n'est pas sur un site (faire un message d'infos dans a page?)
//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->getRepository(DepartementUnifie::class)->find($entity->getId())))) {
                    $entitySite = new DepartementUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($departementSite = $emSite->getRepository(Departement::class)->findOneBy(array('departementUnifie' => $entitySite))))) {
                    $departementSite = new Departement();
                    $entitySite->addDepartement($departementSite);
                }

//            copie des données station
                $departementSite
                    ->setSite($site)
                    ->setDepartementUnifie($entitySite)
                    ->setRegion($region)
                    ->setActif($departement->getActif());

//            Gestion des traductions
                foreach ($departement->getTraductions() as $departementTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $departementTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($departementTraducSite = $emSite->getRepository(DepartementTraduction::class)->findOneBy(array(
                        'departement' => $departementSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $departementTraducSite = new DepartementTraduction();
                    }

//                copie des données traductions
                    $departementTraducSite->setLangue($langue)
                        ->setLibelle($departementTraduc->getLibelle())
                        ->setDescription($departementTraduc->getDescription())
                        ->setDepartement($departementSite);

//                ajout a la collection de traduction de la station distante
                    $departementSite->addTraduction($departementTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $departementImages = $departement->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'departement de référence
                if (!empty($departementImages) && !$departementImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($departementSite->getImages()) && !$departementSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $departementImageSites = new ArrayCollection();
                        foreach ($departementSite->getImages() as $departementimageSite) {
                            $departementImageSites->add($departementimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var DepartementImage $departementImage */
                        foreach ($departementImages as $departementImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'departementImage original correspondant sur le crm
                            /** @var ArrayCollection $originalDepartementImages */
                            $originalDepartementImage = $originalDepartementImages->filter(function (DepartementImage $element) use ($departementImage) {
                                return $element->getImage() == $departementImage->getImage();
                            })->first();
                            unset($departementImageSite);
                            if ($originalDepartementImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalDepartementImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalDepartementImage);

                                $departementImageSite = $departementImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'departementImage existe sur la bdd distante, on va le modifier
                            /** @var DepartementImage $departementImageSite */
                            if (!empty($departementImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'departementImage du crm)
                                if ($departementImageSite->getImage()->getMetadataValue('crm_ref_id') != $departementImage->getImage()->getId()) {
                                    $cloneImage = clone $departementImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $departementImage->getImage()->getId());
                                    $cloneImage->setContext('departement_image_' . $departement->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($departementImageSite->getImage());

                                    $departementImageSite->setImage($cloneImage);
                                }

                                $departementImageSite->setActif($departementImage->getActif());

                                // on parcourt les traductions
                                /** @var DepartementImageTraduction $traduction */
                                foreach ($departementImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var DepartementImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $departementImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (DepartementImageTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new DepartementImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $departementImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createDepartementImage($departementImage, $departementSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'departement de référence
                        /** @var DepartementImage $departementImage */
                        foreach ($departementImages as $departementImage) {
                            $this->createDepartementImage($departementImage, $departementSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($departementImageSites)) {
                        /** @var DepartementImage $departementImageSite */
                        foreach ($departementImageSites as $departementImageSite) {
                            $departementImageSite->setDepartement(null);
                            $emSite->remove($departementImageSite->getImage());
                            $emSite->remove($departementImageSite);
                        }
                    }
                }


                $departementPhotos = $departement->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'departement de référence
                if (!empty($departementPhotos) && !$departementPhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($departementSite->getPhotos()) && !$departementSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $departementPhotoSites = new ArrayCollection();
                        foreach ($departementSite->getPhotos() as $departementphotoSite) {
                            $departementPhotoSites->add($departementphotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var DepartementPhoto $departementPhoto */
                        foreach ($departementPhotos as $departementPhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'departementPhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalDepartementPhotos */
                            $originalDepartementPhoto = $originalDepartementPhotos->filter(function (DepartementPhoto $element) use ($departementPhoto) {
                                return $element->getPhoto() == $departementPhoto->getPhoto();
                            })->first();
                            unset($departementPhotoSite);
                            if ($originalDepartementPhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalDepartementPhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalDepartementPhoto);

                                $departementPhotoSite = $departementPhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'departementPhoto existe sur la bdd distante, on va le modifier
                            /** @var DepartementPhoto $departementPhotoSite */
                            if (!empty($departementPhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'departementPhoto du crm)
                                if ($departementPhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $departementPhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $departementPhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $departementPhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('departement_photo_' . $departement->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($departementPhotoSite->getPhoto());

                                    $departementPhotoSite->setPhoto($clonePhoto);
                                }

                                $departementPhotoSite->setActif($departementPhoto->getActif());

                                // on parcourt les traductions
                                /** @var DepartementPhotoTraduction $traduction */
                                foreach ($departementPhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var DepartementPhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $departementPhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (DepartementPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new DepartementPhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $departementPhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createDepartementPhoto($departementPhoto, $departementSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'departement de référence
                        /** @var DepartementPhoto $departementPhoto */
                        foreach ($departementPhotos as $departementPhoto) {
                            $this->createDepartementPhoto($departementPhoto, $departementSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($departementPhotoSites)) {
                        /** @var DepartementPhoto $departementPhotoSite */
                        foreach ($departementPhotoSites as $departementPhotoSite) {
                            $departementPhotoSite->setDepartement(null);
                            $emSite->remove($departementPhotoSite->getPhoto());
                            $emSite->remove($departementPhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********

                // *** gestion video ***
                if (!empty($departement->getVideos()) && !$departement->getVideos()->isEmpty()) {
                    /** @var DepartementVideo $departementVideo */
                    foreach ($departement->getVideos() as $departementVideo) {
                        $departementVideoSite = $departementSite->getVideos()->filter(function (DepartementVideo $element) use ($departementVideo) {
                            return $element->getId() == $departementVideo->getId();
                        })->first();
                        if (false === $departementVideoSite) {
                            $departementVideoSite = new DepartementVideo();
                            $departementSite->addVideo($departementVideoSite);
                            $departementVideoSite
                                ->setId($departementVideo->getId());
                            $metadata = $emSite->getClassMetadata(get_class($departementVideoSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        if (empty($departementVideoSite->getVideo()) || $departementVideoSite->getVideo()->getId() != $departementVideo->getVideo()->getId()) {
                            $cloneVideo = clone $departementVideo->getVideo();
                            $metadata = $emSite->getClassMetadata(get_class($cloneVideo));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $cloneVideo->setContext('departement_video_' . $departementSite->getSite()->getLibelle());
                            // on supprime l'ancien photo
                            if (!empty($departementVideoSite->getVideo())) {
                                $emSite->remove($departementVideoSite->getVideo());
                                $this->deleteFile($departementVideoSite->getVideo());
                            }
                            $departementVideoSite
                                ->setVideo($cloneVideo);
                        }
                        $departementVideoSite
                            ->setActif($departementVideo->getActif());
                        // *** traductions ***
                        foreach ($departementVideo->getTraductions() as $traduction) {
                            $traductionSite = $departementVideoSite->getTraductions()->filter(function (DepartementVideoTraduction $element) use ($traduction) {
                                return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new DepartementVideoTraduction();
                                $departementVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }

                        // *** fin traductions ***
                    }
                }

                if (!empty($departementSite->getVideos()) && !$departementSite->getVideos()->isEmpty()) {
                    /** @var DepartementVideo $departementVideo */
                    /** @var DepartementVideo $departementVideoSite */
                    foreach ($departementSite->getVideos() as $departementVideoSite) {
                        $departementVideo = $departement->getVideos()->filter(function (DepartementVideo $element) use ($departementVideoSite) {
                            return $element->getId() == $departementVideoSite->getId();
                        })->first();
                        if (false === $departementVideo) {
                            $emSite->remove($departementVideoSite);
                            $emSite->remove($departementVideoSite->getVideo());
                            $this->deleteFile($departementVideoSite->getVideo());
                        }
                    }
                }
                // *** fin gestion video ***

                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterDepartementUnifieSiteDistant($entity->getId(), $entity->getDepartements());
    }

    /**
     * Création d'un nouveau departementImage
     * @param DepartementImage $departementImage
     * @param Departement $departementSite
     * @param EntityManager $emSite
     */
    private function createDepartementImage(DepartementImage $departementImage, Departement $departementSite, EntityManager $emSite)
    {
        /** @var DepartementImage $departementImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($departementImage))->getName();
        // on cré un nouveau DepartementImage on fonction du type
        $departementImageSite = new $typeImage();
        $departementImageSite->setDepartement($departementSite);
        $departementImageSite->setActif($departementImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $departementImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($departementImage->getImage()->getProviderReference());
        $cloneImage->setName($departementImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('departement_image_' . $departementSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $departementImage->getImage()->getId());

        $departementImageSite->setImage($cloneImage);

        $departementSite->addImage($departementImageSite);
        // on ajoute les traductions correspondante
        foreach ($departementImage->getTraductions() as $traduction) {
            $traductionSite = new DepartementImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $departementImageSite->addTraduction($traductionSite);
        }
    }

    /**
     * Création d'un nouveau departementPhoto
     * @param DepartementPhoto $departementPhoto
     * @param Departement $departementSite
     * @param EntityManager $emSite
     */
    private function createDepartementPhoto(DepartementPhoto $departementPhoto, Departement $departementSite, EntityManager $emSite)
    {
        /** @var DepartementPhoto $departementPhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($departementPhoto))->getName();
        // on cré un nouveau DepartementPhoto on fonction du type
        $departementPhotoSite = new $typePhoto();
        $departementPhotoSite->setDepartement($departementSite);
        $departementPhotoSite->setActif($departementPhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $departementPhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($departementPhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($departementPhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('departement_photo_' . $departementSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $departementPhoto->getPhoto()->getId());

        $departementPhotoSite->setPhoto($clonePhoto);

        $departementSite->addPhoto($departementPhotoSite);
        // on ajoute les traductions correspondante
        foreach ($departementPhoto->getTraductions() as $traduction) {
            $traductionSite = new DepartementPhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $departementPhotoSite->addTraduction($traductionSite);
        }
    }

    private function deleteFile($visuel)
    {
        if (file_exists($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg')) {
            unlink($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg');
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $departements
     */
    private function ajouterDepartementUnifieSiteDistant($idUnifie, $departements)
    {
        /** @var ArrayCollection $departements */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($departements->matching($criteres)) == 0 && (empty($emSite->getRepository(DepartementUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DepartementUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a DepartementUnifie entity.
     *
     */
    public function showAction(DepartementUnifie $departementUnifie)
    {
        $deleteForm = $this->createDeleteForm($departementUnifie);

        return $this->render('@MondofuteGeographie/departementunifie/show.html.twig', array(
            'departementUnifie' => $departementUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DepartementUnifie entity.
     *
     * @param DepartementUnifie $departementUnifie The DepartementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DepartementUnifie $departementUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_departement_delete', array('id' => $departementUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DepartementUnifie entity.
     *
     */
    public function editAction(Request $request, DepartementUnifie $departementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Departement $entity */
            foreach ($departementUnifie->getDepartements() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalDepartementImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalDepartementPhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
        $originalDepartementVideos = new ArrayCollection();
        $originalVideos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($departementUnifie->getDepartements() as $departement) {
            // si l'departement est celui du CRM
            if ($departement->getSite()->getCrm() == 1) {
                // on parcourt les departementImage pour les comparer ensuite
                /** @var DepartementImage $departementImage */
                foreach ($departement->getImages() as $departementImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalDepartementImages->add($departementImage);
                    $originalImages->add($departementImage->getImage());
                }
                // on parcourt les departementPhoto pour les comparer ensuite
                /** @var DepartementPhoto $departementPhoto */
                foreach ($departement->getPhotos() as $departementPhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalDepartementPhotos->add($departementPhoto);
                    $originalPhotos->add($departementPhoto->getPhoto());
                }
                // on parcourt les departementVideo pour les comparer ensuite
                /** @var DepartementVideo $departementVideo */
                foreach ($departement->getVideos() as $departementVideo) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalDepartementVideos->add($departementVideo);
                    $originalVideos->set($departementVideo->getId(), $departementVideo->getVideo());
                }
            }
        }

        $this->ajouterDepartementsDansForm($departementUnifie);
//        $this->dispacherDonneesCommune($departementUnifie);
        $this->departementsSortByAffichage($departementUnifie);
        $deleteForm = $this->createDeleteForm($departementUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DepartementUnifieType',
            $departementUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Update', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($departementUnifie->getDepartements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                } else {
                    $entity->setActif(true);
                }
            }

            // ************* suppression images *************
            // ** CAS OU L'ON SUPPRIME UN "DEPARTEMENT IMAGE" **
            // on récupère les DepartementImage de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Departement $departementCrm */
            $departementCrm = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $departementSites = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newDepartementImages = new ArrayCollection();
            foreach ($departementCrm->getImages() as $departementImage) {
                $newDepartementImages->add($departementImage);
            }
            /** @var DepartementImage $originalDepartementImage */
            foreach ($originalDepartementImages as $key => $originalDepartementImage) {

                if (false === $newDepartementImages->contains($originalDepartementImage)) {
                    $originalDepartementImage->setDepartement(null);
                    $em->remove($originalDepartementImage->getImage());
                    $em->remove($originalDepartementImage);
                    // on doit supprimer l'hébergementImage des autres sites
                    // on parcourt les departement des sites
                    /** @var Departement $departementSite */
                    foreach ($departementSites as $departementSite) {
                        $departementImageSite = $em->getRepository(DepartementImage::class)->findOneBy(
                            array(
                                'departement' => $departementSite,
                                'image' => $originalDepartementImage->getImage()
                            ));
                        if (!empty($departementImageSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($departementImageSite->getDepartement()->getSite()->getLibelle());
                            $departementSite = $emSite->getRepository(Departement::class)->findOneBy(
                                array(
                                    'departementUnifie' => $departementImageSite->getDepartement()->getDepartementUnifie()
                                ));
                            $departementImageSiteSites = new ArrayCollection($emSite->getRepository(DepartementImage::class)->findBy(
                                array(
                                    'departement' => $departementSite
                                ))
                            );
                            $departementImageSiteSite = $departementImageSiteSites->filter(function (DepartementImage $element)
                            use ($departementImageSite) {
//                            return $element->getImage()->getProviderReference() == $departementImageSite->getImage()->getProviderReference();
                                return $element->getImage()->getMetadataValue('crm_ref_id') == $departementImageSite->getImage()->getId();
                            })->first();
                            if (!empty($departementImageSiteSite)) {
                                $emSite->remove($departementImageSiteSite->getImage());
                                $departementImageSiteSite->setDepartement(null);
                                $emSite->remove($departementImageSiteSite);
                                $emSite->flush();
                            }
                            $departementImageSite->setDepartement(null);
                            $em->remove($departementImageSite->getImage());
                            $em->remove($departementImageSite);
                        }
                    }
                }
            }
            // ************* fin suppression images *************

            // ************* suppression photos *************
            // ** CAS OU L'ON SUPPRIME UN "DEPARTEMENT PHOTO" **
            // on récupère les DepartementPhoto de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Departement $departementCrm */
            $departementCrm = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $departementSites = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newDepartementPhotos = new ArrayCollection();
            foreach ($departementCrm->getPhotos() as $departementPhoto) {
                $newDepartementPhotos->add($departementPhoto);
            }
            /** @var DepartementPhoto $originalDepartementPhoto */
            foreach ($originalDepartementPhotos as $key => $originalDepartementPhoto) {

                if (false === $newDepartementPhotos->contains($originalDepartementPhoto)) {
                    $originalDepartementPhoto->setDepartement(null);
                    $em->remove($originalDepartementPhoto->getPhoto());
                    $em->remove($originalDepartementPhoto);
                    // on doit supprimer l'hébergementPhoto des autres sites
                    // on parcourt les departement des sites
                    /** @var Departement $departementSite */
                    foreach ($departementSites as $departementSite) {
                        $departementPhotoSite = $em->getRepository(DepartementPhoto::class)->findOneBy(
                            array(
                                'departement' => $departementSite,
                                'photo' => $originalDepartementPhoto->getPhoto()
                            ));
                        if (!empty($departementPhotoSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($departementPhotoSite->getDepartement()->getSite()->getLibelle());
                            $departementSite = $emSite->getRepository(Departement::class)->findOneBy(
                                array(
                                    'departementUnifie' => $departementPhotoSite->getDepartement()->getDepartementUnifie()
                                ));
                            $departementPhotoSiteSites = new ArrayCollection($emSite->getRepository(DepartementPhoto::class)->findBy(
                                array(
                                    'departement' => $departementSite
                                ))
                            );
                            $departementPhotoSiteSite = $departementPhotoSiteSites->filter(function (DepartementPhoto $element)
                            use ($departementPhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $departementPhotoSite->getPhoto()->getProviderReference();
                                return $element->getPhoto()->getMetadataValue('crm_ref_id') == $departementPhotoSite->getPhoto()->getId();
                            })->first();
                            if (!empty($departementPhotoSiteSite)) {
                                $emSite->remove($departementPhotoSiteSite->getPhoto());
                                $departementPhotoSiteSite->setDepartement(null);
                                $emSite->remove($departementPhotoSiteSite);
                                $emSite->flush();
                            }
                            $departementPhotoSite->setDepartement(null);
                            $em->remove($departementPhotoSite->getPhoto());
                            $em->remove($departementPhotoSite);
                        }
                    }
                }
            }
            // ************* fin suppression photos *************

            // ** suppression videos **
            foreach ($originalDepartementVideos as $originalDepartementVideo) {
                if (false === $departementCrm->getVideos()->contains($originalDepartementVideo)) {
                    $videos = $em->getRepository(DepartementVideo::class)->findBy(array('video' => $originalDepartementVideo->getVideo()));
                    foreach ($videos as $video) {
                        $em->remove($video);
                    }
                    $em->remove($originalDepartementVideo->getVideo());
                    $this->deleteFile($originalDepartementVideo->getVideo());
                }
            }
            // ** fin suppression videos **
            // *** gestion des videos ***
            /** @var Departement $departementCrm */
            $departementCrm = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $departementSites = $departementUnifie->getDepartements()->filter(function (Departement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            /** @var DepartementVideo $departementVideo */
            foreach ($departementCrm->getVideos() as $key => $departementVideo) {
                foreach ($departementSites as $departementSite) {
                    if (empty($departementVideo->getId())) {
                        $departementVideoSite = clone $departementVideo;
                    } else {
                        $departementVideoSite = $em->getRepository(DepartementVideo::class)->findOneBy(array('video' => $originalVideos->get($departementVideo->getId()), 'departement' => $departementSite));
                        if ($originalVideos->get($departementVideo->getId()) != $departementVideo->getVideo()) {
                            $em->remove($departementVideoSite->getVideo());
                            $this->deleteFile($departementVideoSite->getVideo());
                            $departementVideoSite->setVideo($departementVideo->getVideo());
                        }
                    }
                    $departementSite->addVideo($departementVideoSite);
                    $actif = false;
                    if (!empty($request->get('departement_unifie')['departements'][0]['videos'][$key]['sites'])) {
                        if (in_array($departementSite->getSite()->getId(), $request->get('departement_unifie')['departements'][0]['videos'][$key]['sites'])) {
                            $actif = true;
                        }
                    }
                    $departementVideoSite->setActif($actif);

                    // *** traductions ***
                    foreach ($departementVideo->getTraductions() as $traduction) {
                        $traductionSite = $departementVideoSite->getTraductions()->filter(function (DepartementVideoTraduction $element) use ($traduction) {
                            return $element->getLangue() == $traduction->getLangue();
                        })->first();
                        if (false === $traductionSite) {
                            $traductionSite = new DepartementVideoTraduction();
                            $departementVideoSite->addTraduction($traductionSite);
                            $traductionSite->setLangue($traduction->getLangue());
                        }
                        $traductionSite->setLibelle($traduction->getLibelle());
                    }
                    // *** fin traductions ***
                }
            }
            // *** fin gestion des videos ***

            // ***** Gestion des Medias *****
//            dump($departementUnifie);die;
            // CAS D'UN NOUVEAU 'DEPARTEMENT IMAGE' OU DE MODIFICATION D'UN "DEPARTEMENT IMAGE"
            /** @var DepartementImage $departementImage */
            // tableau pour la suppression des anciens images
            $imageToRemoveCollection = new ArrayCollection();
            $keyCrm = $departementUnifie->getDepartements()->indexOf($departementCrm);
            // on parcourt les departementImages de l'departement crm
            foreach ($departementCrm->getImages() as $key => $departementImage) {
                // on active le nouveau departementImage (CRM) => il doit être toujours actif
                $departementImage->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementImage pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Departement $departementSite */
                        $departementSite = $departementUnifie->getDepartements()->filter(function (Departement $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($departementSite)) {
                            // on réinitialise la variable
                            unset($departementImageSite);
                            // s'il ne s'agit pas d'un nouveau departementImage
                            if (!empty($departementImage->getId())) {
                                // on récupère l'departementImage pour le modifier
                                $departementImageSite = $em->getRepository(DepartementImage::class)->findOneBy(array('departement' => $departementSite, 'image' => $originalImages->get($key)));
                            }
                            // si l'departementImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($departementImage->getId()) || empty($departementImageSite)) {
                                // on récupère la classe correspondant au image (photo ou video)
                                $typeImage = (new ReflectionClass($departementImage))->getName();
                                // on créé un nouveau DepartementImage on fonction du type
                                /** @var DepartementImage $departementImageSite */
                                $departementImageSite = new $typeImage();
                                $departementImageSite->setDepartement($departementSite);
                            }
                            // si l'hébergemenent image existe déjà pour le site
                            if (!empty($departementImageSite)) {
                                if ($departementImageSite->getImage() != $departementImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($departementImageSite->getImage()) && !$imageToRemoveCollection->contains($departementImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($departementImageSite->getImage());
//                                    }
                                    // on met le nouveau image
                                    $departementImageSite->setImage($departementImage->getImage());
                                }
                                $departementSite->addImage($departementImageSite);

                                /** @var DepartementImageTraduction $traduction */
                                foreach ($departementImage->getTraductions() as $traduction) {
                                    /** @var DepartementImageTraduction $traductionSite */
                                    $traductionSites = $departementImageSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (DepartementImageTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new DepartementImageTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $departementImageSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                if (!empty($request->get('departement_unifie')['departements'][$keyCrm]['images'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('departement_unifie')['departements'][$keyCrm]['images'][$key]['sites'])
                                ) {
                                    $departementImageSite->setActif(true);
                                } else {
                                    $departementImageSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'departementImage CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($departementImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $imageToRemoveCollection->add($originalImages->get($key));
                    }
                }
            }


            // CAS D'UN NOUVEAU 'DEPARTEMENT PHOTO' OU DE MODIFICATION D'UN "DEPARTEMENT PHOTO"
            /** @var DepartementPhoto $departementPhoto */
            // tableau pour la suppression des anciens photos
            $photoToRemoveCollection = new ArrayCollection();
            $keyCrm = $departementUnifie->getDepartements()->indexOf($departementCrm);
            // on parcourt les departementPhotos de l'departement crm
            foreach ($departementCrm->getPhotos() as $key => $departementPhoto) {
                // on active le nouveau departementPhoto (CRM) => il doit être toujours actif
                $departementPhoto->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementPhoto pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Departement $departementSite */
                        $departementSite = $departementUnifie->getDepartements()->filter(function (Departement $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($departementSite)) {
                            // on réinitialise la variable
                            unset($departementPhotoSite);
                            // s'il ne s'agit pas d'un nouveau departementPhoto
                            if (!empty($departementPhoto->getId())) {
                                // on récupère l'departementPhoto pour le modifier
                                $departementPhotoSite = $em->getRepository(DepartementPhoto::class)->findOneBy(array('departement' => $departementSite, 'photo' => $originalPhotos->get($key)));
                            }
                            // si l'departementPhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($departementPhoto->getId()) || empty($departementPhotoSite)) {
                                // on récupère la classe correspondant au photo (photo ou video)
                                $typePhoto = (new ReflectionClass($departementPhoto))->getName();
                                // on créé un nouveau DepartementPhoto on fonction du type
                                /** @var DepartementPhoto $departementPhotoSite */
                                $departementPhotoSite = new $typePhoto();
                                $departementPhotoSite->setDepartement($departementSite);
                            }
                            // si l'hébergemenent photo existe déjà pour le site
                            if (!empty($departementPhotoSite)) {
                                if ($departementPhotoSite->getPhoto() != $departementPhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($departementPhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($departementPhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($departementPhotoSite->getPhoto());
//                                    }
                                    // on met le nouveau photo
                                    $departementPhotoSite->setPhoto($departementPhoto->getPhoto());
                                }
                                $departementSite->addPhoto($departementPhotoSite);

                                /** @var DepartementPhotoTraduction $traduction */
                                foreach ($departementPhoto->getTraductions() as $traduction) {
                                    /** @var DepartementPhotoTraduction $traductionSite */
                                    $traductionSites = $departementPhotoSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (DepartementPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new DepartementPhotoTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $departementPhotoSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                if (!empty($request->get('departement_unifie')['departements'][$keyCrm]['photos'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('departement_unifie')['departements'][$keyCrm]['photos'][$key]['sites'])
                                ) {
                                    $departementPhotoSite->setActif(true);
                                } else {
                                    $departementPhotoSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'departementPhoto CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($departementPhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $photoToRemoveCollection->add($originalPhotos->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($departementUnifie);
            $em->flush();


            $this->copieVersSites($departementUnifie, $originalDepartementImages, $originalDepartementPhotos);

            if (!empty($imageToRemoveCollection)) {
                foreach ($imageToRemoveCollection as $item) {
                    if (!empty($item)) {
                        $em->remove($item);
                    }
                }
                $em->flush();
            }
            if (!empty($photoToRemoveCollection)) {
                foreach ($photoToRemoveCollection as $item) {
                    if (!empty($item)) {
                        $em->remove($item);
                    }
                }
                $em->flush();
            }

            $this->addFlash('success', 'le département a bien été modifié');

            return $this->redirectToRoute('geographie_departement_edit', array('id' => $departementUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/departementunifie/edit.html.twig', array(
            'entity' => $departementUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function deleteFile($visuel)
    {
        if (file_exists($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg')) {
            unlink($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg');
        }
    }


    /**
     * Deletes a DepartementUnifie entity.
     *
     */
    public function deleteAction(Request $request, DepartementUnifie $departementUnifie)
    {
        try {
            $form = $this->createDeleteForm($departementUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $departementUnifieSite = $emSite->find(DepartementUnifie::class, $departementUnifie->getId());
                    if (!empty($departementUnifieSite)) {
                        $emSite->remove($departementUnifieSite);
                        $departementSite = $departementUnifieSite->getDepartements()->first();

                        if (!empty($departementSite)) {
                            // si il y a des images pour l'entité, les supprimer
                            if (!empty($departementSite->getImages())) {
                                /** @var DepartementImage $departementImageSite */
                                foreach ($departementSite->getImages() as $departementImageSite) {
                                    $imageSite = $departementImageSite->getImage();
                                    $departementImageSite->setImage(null);
                                    if (!empty($imageSite)) {
                                        $emSite->remove($imageSite);
                                    }
                                }
                            }
                            // si il y a des photos pour l'entité, les supprimer
                            if (!empty($departementSite->getPhotos())) {
                                /** @var DepartementPhoto $departementPhotoSite */
                                foreach ($departementSite->getPhotos() as $departementPhotoSite) {
                                    $photoSite = $departementPhotoSite->getPhoto();
                                    $departementPhotoSite->setPhoto(null);
                                    if (!empty($photoSite)) {
                                        $emSite->remove($photoSite);
                                    }
                                }
                            }
                            // si il y a des videos pour l'entité, les supprimer
                            if (!empty($departementSite->getVideos())) {
                                /** @var DepartementVideo $departementVideoSite */
                                foreach ($departementSite->getVideos() as $departementVideoSite) {
                                    $emSite->remove($departementVideoSite);
                                    $emSite->remove($departementVideoSite->getVideo());
                                }
                            }
                        }
                        $emSite->flush();
                    }
                }

                if (!empty($departementUnifie)) {
                    if (!empty($departementUnifie->getDepartements())) {
                        /** @var Departement $departement */
                        foreach ($departementUnifie->getDepartements() as $departement) {

                            // si il y a des images pour l'entité, les supprimer
                            if (!empty($departement->getImages())) {
                                /** @var DepartementImage $departementImage */
                                foreach ($departement->getImages() as $departementImage) {
                                    $image = $departementImage->getImage();
                                    $departementImage->setImage(null);
                                    $em->remove($image);
                                }
                            }
                            // si il y a des photos pour l'entité, les supprimer
                            if (!empty($departement->getPhotos())) {
                                /** @var DepartementPhoto $departementPhoto */
                                foreach ($departement->getPhotos() as $departementPhoto) {
                                    $photo = $departementPhoto->getPhoto();
                                    $departementPhoto->setPhoto(null);
                                    $em->remove($photo);
                                }
                            }
                            // si il y a des videos pour l'entité, les supprimer
                            if (!empty($departement->getVideos())) {
                                /** @var DepartementVideo $departementVideoSite */
                                foreach ($departement->getVideos() as $departementVideoSite) {
                                    $em->remove($departementVideoSite);
                                    $em->remove($departementVideoSite->getVideo());
                                }
                            }
                        }
                        $em->flush();
                    }
//                    $emSite->remove($departementUnifieSite);
//                    $emSite->flush();
                }

//                $em = $this->getDoctrine()->getManager();
                $em->remove($departementUnifie);
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'impossible de supprimer le département, il est utilisé par une autre entité');
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'le département a bien été supprimé');
        return $this->redirectToRoute('geographie_departement_index');
    }

    /**
     * retirer de l'entité les departements qui ne doivent pas être enregistrer
     * @param DepartementUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDepartements(DepartementUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getDepartements() as $departement) {
            if (!in_array($departement->getSite()->getId(), $sitesAEnregistrer)) {
                $departement->setDepartementUnifie(null);
                $entity->removeDepartement($departement);
            }
        }
        return $this;
    }

}
