<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Mondofute\Bundle\GeographieBundle\Entity\RegionImage;
use Mondofute\Bundle\GeographieBundle\Entity\RegionImageTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\RegionPhoto;
use Mondofute\Bundle\GeographieBundle\Entity\RegionPhotoTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\RegionVideo;
use Mondofute\Bundle\GeographieBundle\Entity\RegionVideoTraduction;
use Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * RegionUnifie controller.
 *
 */
class RegionUnifieController extends Controller
{
    /**
     * Lists all RegionUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
        $count = $em
            ->getRepository('MondofuteGeographieBundle:RegionUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'geographie_region_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteGeographieBundle:RegionUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteGeographie/regionunifie/index.html.twig', array(
            'regionUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new RegionUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $regionUnifie = new RegionUnifie();

        $this->ajouterRegionsDansForm($regionUnifie);
        $this->regionsSortByAffichage($regionUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType', $regionUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Region $entity */
            foreach ($regionUnifie->getRegions() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('region_unifie')['regions'] as $key => $region) {
                if (!empty($regionUnifie->getRegions()->get($key)) && $regionUnifie->getRegions()->get($key)->getSite()->getCrm() == 1) {
                    $regionCrm = $regionUnifie->getRegions()->get($key);
                    if (!empty($region['images'])) {
                        foreach ($region['images'] as $keyImage => $image) {
                            /** @var RegionImage $imageCrm */
                            $imageCrm = $regionCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setRegion($regionCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Region $regionSite */
                                    $regionSite = $regionUnifie->getRegions()->filter(function (Region $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($regionSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var RegionImage $regionImage */
                                        $regionImage = new $typeImage();
                                        $regionImage->setRegion($regionSite);
                                        $regionImage->setImage($imageCrm->getImage());
                                        $regionSite->addImage($regionImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new RegionImageTraduction();
                                            /** @var RegionImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $regionImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $regionImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('region_unifie')['regions'] as $key => $region) {
                if (!empty($regionUnifie->getRegions()->get($key)) && $regionUnifie->getRegions()->get($key)->getSite()->getCrm() == 1) {
                    $regionCrm = $regionUnifie->getRegions()->get($key);
                    if (!empty($region['photos'])) {
                        foreach ($region['photos'] as $keyPhoto => $photo) {
                            /** @var RegionPhoto $photoCrm */
                            $photoCrm = $regionCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setRegion($regionCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Region $regionSite */
                                    $regionSite = $regionUnifie->getRegions()->filter(function (Region $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($regionSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var RegionPhoto $regionPhoto */
                                        $regionPhoto = new $typePhoto();
                                        $regionPhoto->setRegion($regionSite);
                                        $regionPhoto->setPhoto($photoCrm->getPhoto());
                                        $regionSite->addPhoto($regionPhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new RegionPhotoTraduction();
                                            /** @var RegionPhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $regionPhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $regionPhoto->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // *** gestion des videos ***
            /** @var Region $regionCrm */
            $regionCrm = $regionUnifie->getRegions()->filter(function (Region $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $regionSites = $regionUnifie->getRegions()->filter(function (Region $element) {
                return $element->getSite()->getCrm() == 0;
            });
            /** @var RegionVideo $regionVideo */
            foreach ($regionCrm->getVideos() as $key => $regionVideo) {
                foreach ($regionSites as $regionSite) {
                    $regionVideoSite = clone $regionVideo;
                    $regionSite->addVideo($regionVideoSite);
                    $actif = false;
                    if (!empty($request->get('region_unifie')['regions'][0]['videos'][$key]['sites'])) {
                        if (in_array($regionSite->getSite()->getId(), $request->get('region_unifie')['regions'][0]['videos'][$key]['sites'])) {
                            $actif = true;
                        }
                    }
                    $regionVideoSite->setActif($actif);
                }
            }
            // *** gestion des videos ***
            // ***** Fin Gestion des Medias *****
//            dump($request);die;
            $em->persist($regionUnifie);
            $em->flush();

            $this->copieVersSites($regionUnifie);
            $this->addFlash('success', 'la région a bien été créée');
            return $this->redirectToRoute('geographie_region_edit', array('id' => $regionUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/regionunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $regionUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param RegionUnifie $entity
     */
    private function ajouterRegionsDansForm(RegionUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getRegions() as $region) {
                if ($region->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($region->getTraductions()->filter(function (RegionTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new RegionTraduction();
                            $traduction->setLangue($langue);
                            $region->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $region = new Region();
                $region->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new RegionTraduction();
                    $traduction->setLangue($langue);
                    $region->addTraduction($traduction);
                }
                $entity->addRegion($region);
            }
        }
    }

    /**
     * Classe les regions par classementAffichage
     * @param RegionUnifie $entity
     */
    private function regionsSortByAffichage(RegionUnifie $entity)
    {

        // Trier les stations en fonction de leurs ordre d'affichage
        $regions = $entity->getRegions(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        /** @var ArrayIterator $iterator */
        $iterator = $regions->getIterator();
        unset($regions);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Region $a, Region $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $regions = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($regions);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setRegions($regions);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $regions
     */
    private function traductionsSortByLangue($regions)
    {
        /** @var ArrayIterator $iterator */
        /** @var Region $region */
        foreach ($regions as $region) {
            $traductions = $region->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (RegionTraduction $a, RegionTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $region->setTraductions($traductions);
        }
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param RegionUnifie $entity
     */
    public function copieVersSites(RegionUnifie $entity, $originalRegionImages = null, $originalRegionPhotos = null)
    {
        /** @var RegionTraduction $regionTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getRegions() as $region) {
            if ($region->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($region->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $region->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
//                if (is_null(($entitySite = $emSite->getRepository(RegionUnifie::class)->findOneById(array($entity->getId()))))) {
                if (is_null($entitySite = $emSite->find(RegionUnifie::class, $entity->getId()))) {
                    $entitySite = new RegionUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($regionSite = $emSite->getRepository(Region::class)->findOneBy(array('regionUnifie' => $entitySite))))) {
                    $regionSite = new Region();
                    $entitySite->addRegion($regionSite);
                }

//            copie des données station
                $regionSite
                    ->setSite($site)
                    ->setRegionUnifie($entitySite)
                    ->setActif($region->getActif());

//            Gestion des traductions
                foreach ($region->getTraductions() as $regionTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $regionTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($regionTraducSite = $emSite->getRepository(RegionTraduction::class)->findOneBy(array(
                        'region' => $regionSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $regionTraducSite = new RegionTraduction();
                    }

//                copie des données traductions
                    $regionTraducSite->setLangue($langue)
                        ->setLibelle($regionTraduc->getLibelle())
                        ->setDescription($regionTraduc->getDescription())
                        ->setRegion($regionSite);

//                ajout a la collection de traduction de la station distante
                    $regionSite->addTraduction($regionTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $regionImages = $region->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'region de référence
                if (!empty($regionImages) && !$regionImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($regionSite->getImages()) && !$regionSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $regionImageSites = new ArrayCollection();
                        foreach ($regionSite->getImages() as $regionimageSite) {
                            $regionImageSites->add($regionimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var RegionImage $regionImage */
                        foreach ($regionImages as $regionImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'regionImage original correspondant sur le crm
                            /** @var ArrayCollection $originalRegionImages */
                            $originalRegionImage = $originalRegionImages->filter(function (RegionImage $element) use ($regionImage) {
                                return $element->getImage() == $regionImage->getImage();
                            })->first();
                            unset($regionImageSite);
                            if ($originalRegionImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalRegionImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalRegionImage);

                                $regionImageSite = $regionImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'regionImage existe sur la bdd distante, on va le modifier
                            /** @var RegionImage $regionImageSite */
                            if (!empty($regionImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'regionImage du crm)
                                if ($regionImageSite->getImage()->getMetadataValue('crm_ref_id') != $regionImage->getImage()->getId()) {
                                    $cloneImage = clone $regionImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $regionImage->getImage()->getId());
                                    $cloneImage->setContext('region_image_' . $region->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($regionImageSite->getImage());

                                    $regionImageSite->setImage($cloneImage);
                                }

                                $regionImageSite->setActif($regionImage->getActif());

                                // on parcourt les traductions
                                /** @var RegionImageTraduction $traduction */
                                foreach ($regionImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var RegionImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $regionImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (RegionImageTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new RegionImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $regionImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createRegionImage($regionImage, $regionSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'region de référence
                        /** @var RegionImage $regionImage */
                        foreach ($regionImages as $regionImage) {
                            $this->createRegionImage($regionImage, $regionSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($regionImageSites)) {
                        /** @var RegionImage $regionImageSite */
                        foreach ($regionImageSites as $regionImageSite) {
                            $regionImageSite->setRegion(null);
                            $emSite->remove($regionImageSite->getImage());
                            $emSite->remove($regionImageSite);
                        }
                    }
                }


                $regionPhotos = $region->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'region de référence
                if (!empty($regionPhotos) && !$regionPhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($regionSite->getPhotos()) && !$regionSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $regionPhotoSites = new ArrayCollection();
                        foreach ($regionSite->getPhotos() as $regionphotoSite) {
                            $regionPhotoSites->add($regionphotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var RegionPhoto $regionPhoto */
                        foreach ($regionPhotos as $regionPhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'regionPhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalRegionPhotos */
                            $originalRegionPhoto = $originalRegionPhotos->filter(function (RegionPhoto $element) use ($regionPhoto) {
                                return $element->getPhoto() == $regionPhoto->getPhoto();
                            })->first();
                            unset($regionPhotoSite);
                            if ($originalRegionPhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalRegionPhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalRegionPhoto);

                                $regionPhotoSite = $regionPhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'regionPhoto existe sur la bdd distante, on va le modifier
                            /** @var RegionPhoto $regionPhotoSite */
                            if (!empty($regionPhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'regionPhoto du crm)
                                if ($regionPhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $regionPhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $regionPhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $regionPhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('region_photo_' . $region->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($regionPhotoSite->getPhoto());

                                    $regionPhotoSite->setPhoto($clonePhoto);
                                }

                                $regionPhotoSite->setActif($regionPhoto->getActif());

                                // on parcourt les traductions
                                /** @var RegionPhotoTraduction $traduction */
                                foreach ($regionPhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var RegionPhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $regionPhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (RegionPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new RegionPhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $regionPhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createRegionPhoto($regionPhoto, $regionSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'region de référence
                        /** @var RegionPhoto $regionPhoto */
                        foreach ($regionPhotos as $regionPhoto) {
                            $this->createRegionPhoto($regionPhoto, $regionSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($regionPhotoSites)) {
                        /** @var RegionPhoto $regionPhotoSite */
                        foreach ($regionPhotoSites as $regionPhotoSite) {
                            $regionPhotoSite->setRegion(null);
                            $emSite->remove($regionPhotoSite->getPhoto());
                            $emSite->remove($regionPhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********


                // *** gestion video ***
                if (!empty($region->getVideos()) && !$region->getVideos()->isEmpty()) {
                    /** @var RegionVideo $regionVideo */
                    foreach ($region->getVideos() as $regionVideo) {
                        $regionVideoSite = $regionSite->getVideos()->filter(function (RegionVideo $element) use ($regionVideo) {
                            return $element->getId() == $regionVideo->getId();
                        })->first();
                        if (false === $regionVideoSite) {
                            $regionVideoSite = new RegionVideo();
                            $regionSite->addVideo($regionVideoSite);
                            $regionVideoSite
                                ->setId($regionVideo->getId());
                            $metadata = $emSite->getClassMetadata(get_class($regionVideoSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        if (empty($regionVideoSite->getVideo()) || $regionVideoSite->getVideo()->getId() != $regionVideo->getVideo()->getId()) {
                            $cloneVideo = clone $regionVideo->getVideo();
                            $metadata = $emSite->getClassMetadata(get_class($cloneVideo));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $cloneVideo->setContext('region_video_' . $regionSite->getSite()->getLibelle());
                            // on supprime l'ancien photo
                            if (!empty($regionVideoSite->getVideo())) {
                                $emSite->remove($regionVideoSite->getVideo());
                                $this->deleteFile($regionVideoSite->getVideo());
                            }
                            $regionVideoSite
                                ->setVideo($cloneVideo);
                        }
                        $regionVideoSite
                            ->setActif($regionVideo->getActif());
                        // *** traductions ***
                        foreach ($regionVideo->getTraductions() as $traduction) {
                            $traductionSite = $regionVideoSite->getTraductions()->filter(function (RegionVideoTraduction $element) use ($traduction) {
                                return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new RegionVideoTraduction();
                                $regionVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }

                        // *** fin traductions ***
                    }
                }

                if (!empty($regionSite->getVideos()) && !$regionSite->getVideos()->isEmpty()) {
                    /** @var RegionVideo $regionVideo */
                    /** @var RegionVideo $regionVideoSite */
                    foreach ($regionSite->getVideos() as $regionVideoSite) {
                        $regionVideo = $region->getVideos()->filter(function (RegionVideo $element) use ($regionVideoSite) {
                            return $element->getId() == $regionVideoSite->getId();
                        })->first();
                        if (false === $regionVideo) {
                            $emSite->remove($regionVideoSite);
                            $emSite->remove($regionVideoSite->getVideo());
                            $this->deleteFile($regionVideoSite->getVideo());
                        }
                    }
                }
                // *** fin gestion video ***

                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterRegionUnifieSiteDistant($entity->getId(), $entity->getRegions());
    }

    /**
     * Création d'un nouveau regionImage
     * @param RegionImage $regionImage
     * @param Region $regionSite
     * @param EntityManager $emSite
     */
    private function createRegionImage(RegionImage $regionImage, Region $regionSite, EntityManager $emSite)
    {
        /** @var RegionImage $regionImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($regionImage))->getName();
        // on cré un nouveau RegionImage on fonction du type
        $regionImageSite = new $typeImage();
        $regionImageSite->setRegion($regionSite);
        $regionImageSite->setActif($regionImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $regionImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($regionImage->getImage()->getProviderReference());
        $cloneImage->setName($regionImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('region_image_' . $regionSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $regionImage->getImage()->getId());

        $regionImageSite->setImage($cloneImage);

        $regionSite->addImage($regionImageSite);
        // on ajoute les traductions correspondante
        foreach ($regionImage->getTraductions() as $traduction) {
            $traductionSite = new RegionImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $regionImageSite->addTraduction($traductionSite);
        }
    }

    /**
     * Création d'un nouveau regionPhoto
     * @param RegionPhoto $regionPhoto
     * @param Region $regionSite
     * @param EntityManager $emSite
     */
    private function createRegionPhoto(RegionPhoto $regionPhoto, Region $regionSite, EntityManager $emSite)
    {
        /** @var RegionPhoto $regionPhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($regionPhoto))->getName();
        // on cré un nouveau RegionPhoto on fonction du type
        $regionPhotoSite = new $typePhoto();
        $regionPhotoSite->setRegion($regionSite);
        $regionPhotoSite->setActif($regionPhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $regionPhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($regionPhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($regionPhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('region_photo_' . $regionSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $regionPhoto->getPhoto()->getId());

        $regionPhotoSite->setPhoto($clonePhoto);

        $regionSite->addPhoto($regionPhotoSite);
        // on ajoute les traductions correspondante
        foreach ($regionPhoto->getTraductions() as $traduction) {
            $traductionSite = new RegionPhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $regionPhotoSite->addTraduction($traductionSite);
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
     * @param $regions
     */
    public function ajouterRegionUnifieSiteDistant($idUnifie, $regions)
    {
        /** @var ArrayCollection $regions */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($regions->matching($criteres)) == 0 && (empty($emSite->getRepository(RegionUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new RegionUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a RegionUnifie entity.
     *
     */
    public function showAction(RegionUnifie $regionUnifie)
    {
        $deleteForm = $this->createDeleteForm($regionUnifie);

        return $this->render('@MondofuteGeographie/regionunifie/show.html.twig', array(
            'regionUnifie' => $regionUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a RegionUnifie entity.
     *
     * @param RegionUnifie $regionUnifie The RegionUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RegionUnifie $regionUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_region_delete', array('id' => $regionUnifie->getId())))
            ->add('delete', SubmitType::class, array('label' => 'Supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing RegionUnifie entity.
     *
     */
    public function editAction(Request $request, RegionUnifie $regionUnifie)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Region $entity */
            foreach ($regionUnifie->getRegions() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalRegions = new ArrayCollection();
        $originalRegionImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalRegionPhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
        $originalRegionVideos = new ArrayCollection();
        $originalVideos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        /** @var $region $region */
        foreach ($regionUnifie->getRegions() as $region) {
            $originalRegions->add($region);
            // si l'region est celui du CRM
            if ($region->getSite()->getCrm() == 1) {
                // on parcourt les regionImage pour les comparer ensuite
                /** @var RegionImage $regionImage */
                foreach ($region->getImages() as $regionImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalRegionImages->add($regionImage);
                    $originalImages->add($regionImage->getImage());
                }
                // on parcourt les regionPhoto pour les comparer ensuite
                /** @var RegionPhoto $regionPhoto */
                foreach ($region->getPhotos() as $regionPhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalRegionPhotos->add($regionPhoto);
                    $originalPhotos->add($regionPhoto->getPhoto());
                }

                // on parcourt les regionVideo pour les comparer ensuite
                /** @var RegionVideo $regionVideo */
                foreach ($region->getVideos() as $regionVideo) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalRegionVideos->add($regionVideo);
                    $originalVideos->set($regionVideo->getId(), $regionVideo->getVideo());
                }
            }
        }

        $this->ajouterRegionsDansForm($regionUnifie);
//        $this->dispacherDonneesCommune($regionUnifie);
        $this->regionsSortByAffichage($regionUnifie);
        $deleteForm = $this->createDeleteForm($regionUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType', $regionUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);


        // ***** Validation du formulaire *****
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            try {
                foreach ($regionUnifie->getRegions() as $entity) {
                    if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                        $entity->setActif(false);
                    } else {
                        $entity->setActif(true);
                    }
                }

                // ************* suppression images *************
                // ** CAS OU L'ON SUPPRIME UN "REGION IMAGE" **
                // on récupère les RegionImage de l'hébergementCrm pour les mettre dans une collection
                // afin de les comparer au originaux.
                /** @var Region $regionCrm */
                $regionCrm = $regionUnifie->getRegions()->filter(function (Region $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $regionSites = $regionUnifie->getRegions()->filter(function (Region $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                $newRegionImages = new ArrayCollection();
                foreach ($regionCrm->getImages() as $regionImage) {
                    $newRegionImages->add($regionImage);
                }
                /** @var RegionImage $originalRegionImage */
                foreach ($originalRegionImages as $key => $originalRegionImage) {

                    if (false === $newRegionImages->contains($originalRegionImage)) {
                        $originalRegionImage->setRegion(null);
                        $em->remove($originalRegionImage->getImage());
                        $em->remove($originalRegionImage);
                        // on doit supprimer l'hébergementImage des autres sites
                        // on parcourt les region des sites
                        /** @var Region $regionSite */
                        foreach ($regionSites as $regionSite) {
                            $regionImageSite = $em->getRepository(RegionImage::class)->findOneBy(
                                array(
                                    'region' => $regionSite,
                                    'image' => $originalRegionImage->getImage()
                                ));
                            if (!empty($regionImageSite)) {
                                $emSite = $this->getDoctrine()->getEntityManager($regionImageSite->getRegion()->getSite()->getLibelle());
                                $regionSite = $emSite->getRepository(Region::class)->findOneBy(
                                    array(
                                        'regionUnifie' => $regionImageSite->getRegion()->getRegionUnifie()
                                    ));
                                $regionImageSiteSites = new ArrayCollection($emSite->getRepository(RegionImage::class)->findBy(
                                    array(
                                        'region' => $regionSite
                                    ))
                                );
                                $regionImageSiteSite = $regionImageSiteSites->filter(function (RegionImage $element)
                                use ($regionImageSite) {
//                            return $element->getImage()->getProviderReference() == $regionImageSite->getImage()->getProviderReference();
                                    return $element->getImage()->getMetadataValue('crm_ref_id') == $regionImageSite->getImage()->getId();
                                })->first();
                                if (!empty($regionImageSiteSite)) {
                                    $emSite->remove($regionImageSiteSite->getImage());
                                    $regionImageSiteSite->setRegion(null);
                                    $emSite->remove($regionImageSiteSite);
                                    $emSite->flush();
                                }
                                $regionImageSite->setRegion(null);
                                $em->remove($regionImageSite->getImage());
                                $em->remove($regionImageSite);
                            }
                        }
                    }
                }
                // ************* fin suppression images *************

                // ** suppression videos **
                foreach ($originalRegionVideos as $originalRegionVideo) {
                    if (false === $regionCrm->getVideos()->contains($originalRegionVideo)) {
                        $videos = $em->getRepository(RegionVideo::class)->findBy(array('video' => $originalRegionVideo->getVideo()));
                        foreach ($videos as $video) {
                            $em->remove($video);
                        }
                        $em->remove($originalRegionVideo->getVideo());
                        $this->deleteFile($originalRegionVideo->getVideo());
                    }
                }
                // ** fin suppression videos **
                // *** gestion des videos ***
                /** @var Region $regionCrm */
                $regionCrm = $regionUnifie->getRegions()->filter(function (Region $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $regionSites = $regionUnifie->getRegions()->filter(function (Region $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                /** @var RegionVideo $regionVideo */
                foreach ($regionCrm->getVideos() as $key => $regionVideo) {
                    foreach ($regionSites as $regionSite) {
                        if (empty($regionVideo->getId())) {
                            $regionVideoSite = clone $regionVideo;
                        } else {
                            $regionVideoSite = $em->getRepository(RegionVideo::class)->findOneBy(array('video' => $originalVideos->get($regionVideo->getId()), 'region' => $regionSite));
                            if ($originalVideos->get($regionVideo->getId()) != $regionVideo->getVideo()) {
                                $em->remove($regionVideoSite->getVideo());
                                $this->deleteFile($regionVideoSite->getVideo());
                                $regionVideoSite->setVideo($regionVideo->getVideo());
                            }
                        }
                        $regionSite->addVideo($regionVideoSite);
                        $actif = false;
                        if (!empty($request->get('region_unifie')['regions'][0]['videos'][$key]['sites'])) {
                            if (in_array($regionSite->getSite()->getId(), $request->get('region_unifie')['regions'][0]['videos'][$key]['sites'])) {
                                $actif = true;
                            }
                        }
                        $regionVideoSite->setActif($actif);

                        // *** traductions ***
                        foreach ($regionVideo->getTraductions() as $traduction) {
                            $traductionSite = $regionVideoSite->getTraductions()->filter(function (RegionVideoTraduction $element) use ($traduction) {
                                return $element->getLangue() == $traduction->getLangue();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new RegionVideoTraduction();
                                $regionVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($traduction->getLangue());
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }
                        // *** fin traductions ***
                    }
                }
                // *** fin gestion des videos ***


                // ************* suppression photos *************
                // ** CAS OU L'ON SUPPRIME UN "REGION PHOTO" **
                // on récupère les RegionPhoto de l'hébergementCrm pour les mettre dans une collection
                // afin de les comparer au originaux.
                /** @var Region $regionCrm */
                $regionCrm = $regionUnifie->getRegions()->filter(function (Region $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $regionSites = $regionUnifie->getRegions()->filter(function (Region $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                $newRegionPhotos = new ArrayCollection();
                foreach ($regionCrm->getPhotos() as $regionPhoto) {
                    $newRegionPhotos->add($regionPhoto);
                }
                /** @var RegionPhoto $originalRegionPhoto */
                foreach ($originalRegionPhotos as $key => $originalRegionPhoto) {

                    if (false === $newRegionPhotos->contains($originalRegionPhoto)) {
                        $originalRegionPhoto->setRegion(null);
                        $em->remove($originalRegionPhoto->getPhoto());
                        $em->remove($originalRegionPhoto);
                        // on doit supprimer l'hébergementPhoto des autres sites
                        // on parcourt les region des sites
                        /** @var Region $regionSite */
                        foreach ($regionSites as $regionSite) {
                            $regionPhotoSite = $em->getRepository(RegionPhoto::class)->findOneBy(
                                array(
                                    'region' => $regionSite,
                                    'photo' => $originalRegionPhoto->getPhoto()
                                ));
                            if (!empty($regionPhotoSite)) {
                                $emSite = $this->getDoctrine()->getEntityManager($regionPhotoSite->getRegion()->getSite()->getLibelle());
                                $regionSite = $emSite->getRepository(Region::class)->findOneBy(
                                    array(
                                        'regionUnifie' => $regionPhotoSite->getRegion()->getRegionUnifie()
                                    ));
                                $regionPhotoSiteSites = new ArrayCollection($emSite->getRepository(RegionPhoto::class)->findBy(
                                    array(
                                        'region' => $regionSite
                                    ))
                                );
                                $regionPhotoSiteSite = $regionPhotoSiteSites->filter(function (RegionPhoto $element)
                                use ($regionPhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $regionPhotoSite->getPhoto()->getProviderReference();
                                    return $element->getPhoto()->getMetadataValue('crm_ref_id') == $regionPhotoSite->getPhoto()->getId();
                                })->first();
                                if (!empty($regionPhotoSiteSite)) {
                                    $emSite->remove($regionPhotoSiteSite->getPhoto());
                                    $regionPhotoSiteSite->setRegion(null);
                                    $emSite->remove($regionPhotoSiteSite);
                                    $emSite->flush();
                                }
                                $regionPhotoSite->setRegion(null);
                                $em->remove($regionPhotoSite->getPhoto());
                                $em->remove($regionPhotoSite);
                            }
                        }
                    }
                }
                // ************* fin suppression photos *************

                // ***** Gestion des Medias *****
                // CAS D'UN NOUVEAU 'REGION IMAGE' OU DE MODIFICATION D'UN "REGION IMAGE"
                /** @var RegionImage $regionImage */
                // tableau pour la suppression des anciens images
                $imageToRemoveCollection = new ArrayCollection();
                $keyCrm = $regionUnifie->getRegions()->indexOf($regionCrm);
                // on parcourt les regionImages de l'region crm
                foreach ($regionCrm->getImages() as $key => $regionImage) {
                    // on active le nouveau regionImage (CRM) => il doit être toujours actif
                    $regionImage->setActif(true);
                    // parcourir tout les sites
                    /** @var Site $site */
                    foreach ($sites as $site) {
                        // sauf  le crm (puisqu'on l'a déjà renseigné)
                        // dans le but de créer un hebegrementImage pour chacun
                        if ($site->getCrm() == 0) {
                            // on récupère l'hébegergement du site
                            /** @var Region $regionSite */
                            $regionSite = $regionUnifie->getRegions()->filter(function (Region $element) use ($site) {
                                return $element->getSite() == $site;
                            })->first();
                            // si hébergement existe
                            if (!empty($regionSite)) {
                                // on réinitialise la variable
                                unset($regionImageSite);
                                // s'il ne s'agit pas d'un nouveau regionImage
                                if (!empty($regionImage->getId())) {
                                    // on récupère l'regionImage pour le modifier
                                    $regionImageSite = $em->getRepository(RegionImage::class)->findOneBy(array('region' => $regionSite, 'image' => $originalImages->get($key)));
                                }
                                // si l'regionImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                                if (empty($regionImage->getId()) || empty($regionImageSite)) {
                                    // on récupère la classe correspondant au image (photo ou video)
                                    $typeImage = (new ReflectionClass($regionImage))->getName();
                                    // on créé un nouveau RegionImage on fonction du type
                                    /** @var RegionImage $regionImageSite */
                                    $regionImageSite = new $typeImage();
                                    $regionImageSite->setRegion($regionSite);
                                }
                                // si l'hébergemenent image existe déjà pour le site
                                if (!empty($regionImageSite)) {
                                    if ($regionImageSite->getImage() != $regionImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($regionImageSite->getImage()) && !$imageToRemoveCollection->contains($regionImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($regionImageSite->getImage());
//                                    }
                                        // on met le nouveau image
                                        $regionImageSite->setImage($regionImage->getImage());
                                    }
                                    $regionSite->addImage($regionImageSite);

                                    /** @var RegionImageTraduction $traduction */
                                    foreach ($regionImage->getTraductions() as $traduction) {
                                        /** @var RegionImageTraduction $traductionSite */
                                        $traductionSites = $regionImageSite->getTraductions();
                                        $traductionSite = null;
                                        if (!$traductionSites->isEmpty()) {
                                            $traductionSite = $traductionSites->filter(function (RegionImageTraduction $element) use ($traduction) {
                                                return $element->getLangue() == $traduction->getLangue();
                                            })->first();
                                        }
                                        if (empty($traductionSite)) {
                                            $traductionSite = new RegionImageTraduction();
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $regionImageSite->addTraduction($traductionSite);
                                        }
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    }
                                    // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                    if (!empty($request->get('region_unifie')['regions'][$keyCrm]['images'][$key]['sites']) &&
                                        in_array($site->getId(), $request->get('region_unifie')['regions'][$keyCrm]['images'][$key]['sites'])
                                    ) {
                                        $regionImageSite->setActif(true);
                                    } else {
                                        $regionImageSite->setActif(false);
                                    }
                                }
                            }
                        }
                        // on est dans l'regionImage CRM
                        // s'il s'agit d'un nouveau média
                        elseif (empty($regionImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                            // on stocke  l'ancien media pour le supprimer après le persist final
                            $imageToRemoveCollection->add($originalImages->get($key));
                        }
                    }
                }


                // CAS D'UN NOUVEAU 'REGION PHOTO' OU DE MODIFICATION D'UN "REGION PHOTO"
                /** @var RegionPhoto $regionPhoto */
                // tableau pour la suppression des anciens photos
                $photoToRemoveCollection = new ArrayCollection();
                $keyCrm = $regionUnifie->getRegions()->indexOf($regionCrm);
                // on parcourt les regionPhotos de l'region crm
                foreach ($regionCrm->getPhotos() as $key => $regionPhoto) {
                    // on active le nouveau regionPhoto (CRM) => il doit être toujours actif
                    $regionPhoto->setActif(true);
                    // parcourir tout les sites
                    /** @var Site $site */
                    foreach ($sites as $site) {
                        // sauf  le crm (puisqu'on l'a déjà renseigné)
                        // dans le but de créer un hebegrementPhoto pour chacun
                        if ($site->getCrm() == 0) {
                            // on récupère l'hébegergement du site
                            /** @var Region $regionSite */
                            $regionSite = $regionUnifie->getRegions()->filter(function (Region $element) use ($site) {
                                return $element->getSite() == $site;
                            })->first();
                            // si hébergement existe
                            if (!empty($regionSite)) {
                                // on réinitialise la variable
                                unset($regionPhotoSite);
                                // s'il ne s'agit pas d'un nouveau regionPhoto
                                if (!empty($regionPhoto->getId())) {
                                    // on récupère l'regionPhoto pour le modifier
                                    $regionPhotoSite = $em->getRepository(RegionPhoto::class)->findOneBy(array('region' => $regionSite, 'photo' => $originalPhotos->get($key)));
                                }
                                // si l'regionPhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                                if (empty($regionPhoto->getId()) || empty($regionPhotoSite)) {
                                    // on récupère la classe correspondant au photo (photo ou video)
                                    $typePhoto = (new ReflectionClass($regionPhoto))->getName();
                                    // on créé un nouveau RegionPhoto on fonction du type
                                    /** @var RegionPhoto $regionPhotoSite */
                                    $regionPhotoSite = new $typePhoto();
                                    $regionPhotoSite->setRegion($regionSite);
                                }
                                // si l'hébergemenent photo existe déjà pour le site
                                if (!empty($regionPhotoSite)) {
                                    if ($regionPhotoSite->getPhoto() != $regionPhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($regionPhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($regionPhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($regionPhotoSite->getPhoto());
//                                    }
                                        // on met le nouveau photo
                                        $regionPhotoSite->setPhoto($regionPhoto->getPhoto());
                                    }
                                    $regionSite->addPhoto($regionPhotoSite);

                                    /** @var RegionPhotoTraduction $traduction */
                                    foreach ($regionPhoto->getTraductions() as $traduction) {
                                        /** @var RegionPhotoTraduction $traductionSite */
                                        $traductionSites = $regionPhotoSite->getTraductions();
                                        $traductionSite = null;
                                        if (!$traductionSites->isEmpty()) {
                                            $traductionSite = $traductionSites->filter(function (RegionPhotoTraduction $element) use ($traduction) {
                                                return $element->getLangue() == $traduction->getLangue();
                                            })->first();
                                        }
                                        if (empty($traductionSite)) {
                                            $traductionSite = new RegionPhotoTraduction();
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $regionPhotoSite->addTraduction($traductionSite);
                                        }
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    }
                                    // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                    if (!empty($request->get('region_unifie')['regions'][$keyCrm]['photos'][$key]['sites']) &&
                                        in_array($site->getId(), $request->get('region_unifie')['regions'][$keyCrm]['photos'][$key]['sites'])
                                    ) {
                                        $regionPhotoSite->setActif(true);
                                    } else {
                                        $regionPhotoSite->setActif(false);
                                    }
                                }
                            }
                        }
                        // on est dans l'regionPhoto CRM
                        // s'il s'agit d'un nouveau média
                        elseif (empty($regionPhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                            // on stocke  l'ancien media pour le supprimer après le persist final
                            $photoToRemoveCollection->add($originalPhotos->get($key));
                        }
                    }
                }
                // ***** Fin Gestion des Medias *****

                $em->persist($regionUnifie);
                $em->flush();


                $this->copieVersSites($regionUnifie, $originalRegionImages, $originalRegionPhotos);

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

            } catch (ForeignKeyConstraintViolationException $except) {
                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer la région, elle est utilisée par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirectToRoute('geographie_region_edit', array('id' => $regionUnifie->getId()));
            }
            $this->addFlash('success', 'la région a bien été modifiée');
            return $this->redirectToRoute('geographie_region_edit', array('id' => $regionUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/regionunifie/edit.html.twig', array(
            'entity' => $regionUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a RegionUnifie entity.
     *
     */
    public function deleteAction(Request $request, RegionUnifie $regionUnifie)
    {
        try {
            $form = $this->createDeleteForm($regionUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $regionUnifieSite = $emSite->find(RegionUnifie::class, $regionUnifie->getId());
                    if (!empty($regionUnifieSite)) {
                        $emSite->remove($regionUnifieSite);


                        $regionSite = $regionUnifieSite->getRegions()->first();

                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($regionSite->getImages())) {
                            /** @var RegionImage $regionImageSite */
                            foreach ($regionSite->getImages() as $regionImageSite) {
                                $imageSite = $regionImageSite->getImage();
                                $regionImageSite->setImage(null);
                                if (!empty($imageSite)) {
                                    $emSite->remove($imageSite);
                                }
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($regionSite->getPhotos())) {
                            /** @var RegionPhoto $regionPhotoSite */
                            foreach ($regionSite->getPhotos() as $regionPhotoSite) {
                                $photoSite = $regionPhotoSite->getPhoto();
                                $regionPhotoSite->setPhoto(null);
                                if (!empty($photoSite)) {
                                    $emSite->remove($photoSite);
                                }
                            }
                        }
                        // si il y a des videos pour l'entité, les supprimer
                        if (!empty($regionSite->getVideos())) {
                            /** @var RegionVideo $regionVideoSite */
                            foreach ($regionSite->getVideos() as $regionVideoSite) {
                                $emSite->remove($regionVideoSite);
                                $emSite->remove($regionVideoSite->getVideo());
                            }
                        }

                        $emSite->flush();
                    }
                }

                if (!empty($regionUnifie)) {
                    if (!empty($regionUnifie->getRegions())) {
                        /** @var Region $region */
                        foreach ($regionUnifie->getRegions() as $region) {

                            // si il y a des images pour l'entité, les supprimer
                            if (!empty($region->getImages())) {
                                /** @var RegionImage $regionImage */
                                foreach ($region->getImages() as $regionImage) {
                                    $image = $regionImage->getImage();
                                    $regionImage->setImage(null);
                                    $em->remove($image);
                                }
                            }
                            // si il y a des photos pour l'entité, les supprimer
                            if (!empty($region->getPhotos())) {
                                /** @var RegionPhoto $regionPhoto */
                                foreach ($region->getPhotos() as $regionPhoto) {
                                    $photo = $regionPhoto->getPhoto();
                                    $regionPhoto->setPhoto(null);
                                    $em->remove($photo);
                                }
                            }
                            // si il y a des videos pour l'entité, les supprimer
                            if (!empty($region->getVideos())) {
                                /** @var RegionVideo $regionVideoSite */
                                foreach ($region->getVideos() as $regionVideoSite) {
                                    $em->remove($regionVideoSite);
                                    $em->remove($regionVideoSite->getVideo());
                                }
                            }
                        }
                        $em->flush();
                    }
//                    $emSite->remove($regionUnifieSite);
//                    $emSite->flush();
                }

//                $em = $this->getDoctrine()->getManager();
                $em->remove($regionUnifie);
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'impossible de supprimer la région, elle est utilisée par une autre entité');
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'la région a bien été supprimée');
        return $this->redirectToRoute('geographie_region_index');
    }

    /**
     * @param Request $request
     */
    public function getRegionsCommunesBySiteAction(Request $request)
    {
        $sites = $request->get('sites');
        $em = $this->getDoctrine()->getManager();

        $regionUnifies = $em->getRepository(RegionUnifie::class)->findAll();
//        $regionUnifiesNotEmpty  = new ArrayCollection();
        $regionUnifieCollection = new ArrayCollection();
        foreach ($regionUnifies as $regionUnifie) {
            $regionUnifieCollection->add($regionUnifie);
        }
        foreach ($sites as $site) {
            $siteEntity = $em->find(Site::class, $site);
            foreach ($regionUnifieCollection as $regionUnifie) {
                $region = $regionUnifie->getRegions()->filter(function (Region $element) use ($siteEntity) {
                    return $element->getSite() == $siteEntity;
                });
                if (!empty($region)) {
//                    $regionUnifieCollection->add($regionUnifie);
                    $regionUnifieCollection->remove($regionUnifie);
                }
            }
        }

        die;

    }
}
