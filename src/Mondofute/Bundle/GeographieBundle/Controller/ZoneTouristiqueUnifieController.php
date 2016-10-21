<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImageTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiquePhoto;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiquePhotoTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueVideo;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueVideoTraduction;
use Mondofute\Bundle\GeographieBundle\Form\ZoneTouristiqueUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * ZoneTouristiqueUnifie controller.
 *
 */
class ZoneTouristiqueUnifieController extends Controller
{
    /**
     * Lists all ZoneTouristiqueUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
        $count = $em
            ->getRepository('MondofuteGeographieBundle:ZoneTouristiqueUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'geographie_zonetouristique_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteGeographieBundle:ZoneTouristiqueUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/index.html.twig', array(
            'zoneTouristiqueUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new ZoneTouristiqueUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $zoneTouristiqueUnifie = new ZoneTouristiqueUnifie();

        $this->ajouterZoneTouristiquesDansForm($zoneTouristiqueUnifie);
        $this->zoneTouristiquesSortByAffichage($zoneTouristiqueUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ZoneTouristiqueUnifieType',
            $zoneTouristiqueUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ZoneTouristique $entity */
            foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('zone_touristique_unifie')['zoneTouristiques'] as $key => $zoneTouristique) {
                if (!empty($zoneTouristiqueUnifie->getZoneTouristiques()->get($key)) && $zoneTouristiqueUnifie->getZoneTouristiques()->get($key)->getSite()->getCrm() == 1) {
                    $zoneTouristiqueCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->get($key);
                    if (!empty($zoneTouristique['images'])) {
                        foreach ($zoneTouristique['images'] as $keyImage => $image) {
                            /** @var ZoneTouristiqueImage $imageCrm */
                            $imageCrm = $zoneTouristiqueCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setZoneTouristique($zoneTouristiqueCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var ZoneTouristique $zoneTouristiqueSite */
                                    $zoneTouristiqueSite = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($zoneTouristiqueSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var ZoneTouristiqueImage $zoneTouristiqueImage */
                                        $zoneTouristiqueImage = new $typeImage();
                                        $zoneTouristiqueImage->setZoneTouristique($zoneTouristiqueSite);
                                        $zoneTouristiqueImage->setImage($imageCrm->getImage());
                                        $zoneTouristiqueSite->addImage($zoneTouristiqueImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new ZoneTouristiqueImageTraduction();
                                            /** @var ZoneTouristiqueImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $zoneTouristiqueImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $zoneTouristiqueImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('zone_touristique_unifie')['zoneTouristiques'] as $key => $zoneTouristique) {
                if (!empty($zoneTouristiqueUnifie->getZoneTouristiques()->get($key)) && $zoneTouristiqueUnifie->getZoneTouristiques()->get($key)->getSite()->getCrm() == 1) {
                    $zoneTouristiqueCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->get($key);
                    if (!empty($zoneTouristique['photos'])) {
                        foreach ($zoneTouristique['photos'] as $keyPhoto => $photo) {
                            /** @var ZoneTouristiquePhoto $photoCrm */
                            $photoCrm = $zoneTouristiqueCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setZoneTouristique($zoneTouristiqueCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var ZoneTouristique $zoneTouristiqueSite */
                                    $zoneTouristiqueSite = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($zoneTouristiqueSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var ZoneTouristiquePhoto $zoneTouristiquePhoto */
                                        $zoneTouristiquePhoto = new $typePhoto();
                                        $zoneTouristiquePhoto->setZoneTouristique($zoneTouristiqueSite);
                                        $zoneTouristiquePhoto->setPhoto($photoCrm->getPhoto());
                                        $zoneTouristiqueSite->addPhoto($zoneTouristiquePhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new ZoneTouristiquePhotoTraduction();
                                            /** @var ZoneTouristiquePhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $zoneTouristiquePhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $zoneTouristiquePhoto->setActif(true);
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
            /** @var ZoneTouristique $zoneTouristiqueCrm */
            $zoneTouristiqueCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $zoneTouristiqueSites = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                return $element->getSite()->getCrm() == 0;
            });
            /** @var ZoneTouristiqueVideo $zoneTouristiqueVideo */
            foreach ($zoneTouristiqueCrm->getVideos() as $key => $zoneTouristiqueVideo) {
                foreach ($zoneTouristiqueSites as $zoneTouristiqueSite) {
                    $zoneTouristiqueVideoSite = clone $zoneTouristiqueVideo;
                    $zoneTouristiqueSite->addVideo($zoneTouristiqueVideoSite);
                    $actif = false;
                    if (!empty($request->get('zone_touristique_unifie')['zoneTouristiques'][0]['videos'][$key]['sites'])) {
                        if (in_array($zoneTouristiqueSite->getSite()->getId(), $request->get('zone_touristique_unifie')['zoneTouristiques'][0]['videos'][$key]['sites'])) {
                            $actif = true;
                        }
                    }
                    $zoneTouristiqueVideoSite->setActif($actif);
                }
            }
            // *** gestion des videos ***


//            $em = $this->getDoctrine()->getManager();
            $em->persist($zoneTouristiqueUnifie);
            $em->flush();

            $this->copieVersSites($zoneTouristiqueUnifie);
            $this->addFlash('success', 'la zone touristique a bien été créée');
            return $this->redirectToRoute('geographie_zonetouristique_edit',
                array('id' => $zoneTouristiqueUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $zoneTouristiqueUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param ZoneTouristiqueUnifie $entity
     */
    private function ajouterZoneTouristiquesDansForm(ZoneTouristiqueUnifie $entity)
    {
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
                if ($zoneTouristique->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($zoneTouristique->getTraductions()->filter(function (ZoneTouristiqueTraduction $element) use (
                            $langue
                        ) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new ZoneTouristiqueTraduction();
                            $traduction->setLangue($langue);
                            $zoneTouristique->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $zoneTouristique = new ZoneTouristique();
                $zoneTouristique->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new ZoneTouristiqueTraduction();
                    $traduction->setLangue($langue);
                    $zoneTouristique->addTraduction($traduction);
                }
                $entity->addZoneTouristique($zoneTouristique);
            }
        }
    }

    /**
     * Classe les zoneTouristiques par classementAffichage
     * @param ZoneTouristiqueUnifie $entity
     */
    private function zoneTouristiquesSortByAffichage(ZoneTouristiqueUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les stations en fonction de leurs ordre d'affichage
        $zoneTouristiques = $entity->getZoneTouristiques(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $zoneTouristiques->getIterator();
//        unset($zoneTouristiques);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (ZoneTouristique $a, ZoneTouristique $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $zoneTouristiques = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($zoneTouristiques);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setZoneTouristiques($zoneTouristiques);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $zoneTouristiques
     */
    private function traductionsSortByLangue(ArrayCollection $zoneTouristiques)
    {
        /** @var ZoneTouristique $zoneTouristique */
        foreach ($zoneTouristiques as $zoneTouristique) {
            $traductions = $zoneTouristique->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            /** @var ArrayIterator $iterator */
            $iterator->uasort(function (ZoneTouristiqueTraduction $a, ZoneTouristiqueTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $zoneTouristique->setTraductions($traductions);
        }
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param ZoneTouristiqueUnifie $entity
     */
    public function copieVersSites(ZoneTouristiqueUnifie $entity, $originalZoneTouristiqueImages = null, $originalZoneTouristiquePhotos = null)
    {
        /** @var ZoneTouristiqueTraduction $zoneTouristiqueTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
            if ($zoneTouristique->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($zoneTouristique->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $zoneTouristique->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->find(ZoneTouristiqueUnifie::class, $entity->getId())))) {
                    $entitySite = new ZoneTouristiqueUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($zoneTouristiqueSite = $emSite->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $entitySite))))) {
                    $zoneTouristiqueSite = new ZoneTouristique();
                    $entitySite->addZoneTouristique($zoneTouristiqueSite);
                }

//            copie des données station
                $zoneTouristiqueSite
                    ->setSite($site)
                    ->setZoneTouristiqueUnifie($entitySite)
                    ->setActif($zoneTouristique->getActif());

//            Gestion des traductions
                foreach ($zoneTouristique->getTraductions() as $zoneTouristiqueTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $zoneTouristiqueTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($zoneTouristiqueTraducSite = $emSite->getRepository(ZoneTouristiqueTraduction::class)->findOneBy(array(
                        'zoneTouristique' => $zoneTouristiqueSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $zoneTouristiqueTraducSite = new ZoneTouristiqueTraduction();
                    }

//                copie des données traductions
                    $zoneTouristiqueTraducSite->setLangue($langue)
                        ->setLibelle($zoneTouristiqueTraduc->getLibelle())
                        ->setDescription($zoneTouristiqueTraduc->getDescription())
                        ->setZoneTouristique($zoneTouristiqueSite);

//                ajout a la collection de traduction de la station distante
                    $zoneTouristiqueSite->addTraduction($zoneTouristiqueTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $zoneTouristiqueImages = $zoneTouristique->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'zoneTouristique de référence
                if (!empty($zoneTouristiqueImages) && !$zoneTouristiqueImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($zoneTouristiqueSite->getImages()) && !$zoneTouristiqueSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $zoneTouristiqueImageSites = new ArrayCollection();
                        foreach ($zoneTouristiqueSite->getImages() as $zoneTouristiqueimageSite) {
                            $zoneTouristiqueImageSites->add($zoneTouristiqueimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var ZoneTouristiqueImage $zoneTouristiqueImage */
                        foreach ($zoneTouristiqueImages as $zoneTouristiqueImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'zoneTouristiqueImage original correspondant sur le crm
                            /** @var ArrayCollection $originalZoneTouristiqueImages */
                            $originalZoneTouristiqueImage = $originalZoneTouristiqueImages->filter(function (ZoneTouristiqueImage $element) use ($zoneTouristiqueImage) {
                                return $element->getImage() == $zoneTouristiqueImage->getImage();
                            })->first();
                            unset($zoneTouristiqueImageSite);
                            if ($originalZoneTouristiqueImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalZoneTouristiqueImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalZoneTouristiqueImage);

                                $zoneTouristiqueImageSite = $zoneTouristiqueImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'zoneTouristiqueImage existe sur la bdd distante, on va le modifier
                            /** @var ZoneTouristiqueImage $zoneTouristiqueImageSite */
                            if (!empty($zoneTouristiqueImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'zoneTouristiqueImage du crm)
                                if ($zoneTouristiqueImageSite->getImage()->getMetadataValue('crm_ref_id') != $zoneTouristiqueImage->getImage()->getId()) {
                                    $cloneImage = clone $zoneTouristiqueImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $zoneTouristiqueImage->getImage()->getId());
                                    $cloneImage->setContext('zone_touristique_image_' . $zoneTouristique->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($zoneTouristiqueImageSite->getImage());

                                    $zoneTouristiqueImageSite->setImage($cloneImage);
                                }

                                $zoneTouristiqueImageSite->setActif($zoneTouristiqueImage->getActif());

                                // on parcourt les traductions
                                /** @var ZoneTouristiqueImageTraduction $traduction */
                                foreach ($zoneTouristiqueImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var ZoneTouristiqueImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $zoneTouristiqueImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (ZoneTouristiqueImageTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new ZoneTouristiqueImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $zoneTouristiqueImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createZoneTouristiqueImage($zoneTouristiqueImage, $zoneTouristiqueSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'zoneTouristique de référence
                        /** @var ZoneTouristiqueImage $zoneTouristiqueImage */
                        foreach ($zoneTouristiqueImages as $zoneTouristiqueImage) {
                            $this->createZoneTouristiqueImage($zoneTouristiqueImage, $zoneTouristiqueSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($zoneTouristiqueImageSites)) {
                        /** @var ZoneTouristiqueImage $zoneTouristiqueImageSite */
                        foreach ($zoneTouristiqueImageSites as $zoneTouristiqueImageSite) {
                            $zoneTouristiqueImageSite->setZoneTouristique(null);
                            $emSite->remove($zoneTouristiqueImageSite->getImage());
                            $emSite->remove($zoneTouristiqueImageSite);
                        }
                    }
                }


                $zoneTouristiquePhotos = $zoneTouristique->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'zoneTouristique de référence
                if (!empty($zoneTouristiquePhotos) && !$zoneTouristiquePhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($zoneTouristiqueSite->getPhotos()) && !$zoneTouristiqueSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $zoneTouristiquePhotoSites = new ArrayCollection();
                        foreach ($zoneTouristiqueSite->getPhotos() as $zoneTouristiquephotoSite) {
                            $zoneTouristiquePhotoSites->add($zoneTouristiquephotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var ZoneTouristiquePhoto $zoneTouristiquePhoto */
                        foreach ($zoneTouristiquePhotos as $zoneTouristiquePhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'zoneTouristiquePhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalZoneTouristiquePhotos */
                            $originalZoneTouristiquePhoto = $originalZoneTouristiquePhotos->filter(function (ZoneTouristiquePhoto $element) use ($zoneTouristiquePhoto) {
                                return $element->getPhoto() == $zoneTouristiquePhoto->getPhoto();
                            })->first();
                            unset($zoneTouristiquePhotoSite);
                            if ($originalZoneTouristiquePhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalZoneTouristiquePhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalZoneTouristiquePhoto);

                                $zoneTouristiquePhotoSite = $zoneTouristiquePhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'zoneTouristiquePhoto existe sur la bdd distante, on va le modifier
                            /** @var ZoneTouristiquePhoto $zoneTouristiquePhotoSite */
                            if (!empty($zoneTouristiquePhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'zoneTouristiquePhoto du crm)
                                if ($zoneTouristiquePhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $zoneTouristiquePhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $zoneTouristiquePhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $zoneTouristiquePhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('zone_touristique_photo_' . $zoneTouristique->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($zoneTouristiquePhotoSite->getPhoto());

                                    $zoneTouristiquePhotoSite->setPhoto($clonePhoto);
                                }

                                $zoneTouristiquePhotoSite->setActif($zoneTouristiquePhoto->getActif());

                                // on parcourt les traductions
                                /** @var ZoneTouristiquePhotoTraduction $traduction */
                                foreach ($zoneTouristiquePhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var ZoneTouristiquePhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $zoneTouristiquePhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (ZoneTouristiquePhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new ZoneTouristiquePhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $zoneTouristiquePhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createZoneTouristiquePhoto($zoneTouristiquePhoto, $zoneTouristiqueSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'zoneTouristique de référence
                        /** @var ZoneTouristiquePhoto $zoneTouristiquePhoto */
                        foreach ($zoneTouristiquePhotos as $zoneTouristiquePhoto) {
                            $this->createZoneTouristiquePhoto($zoneTouristiquePhoto, $zoneTouristiqueSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($zoneTouristiquePhotoSites)) {
                        /** @var ZoneTouristiquePhoto $zoneTouristiquePhotoSite */
                        foreach ($zoneTouristiquePhotoSites as $zoneTouristiquePhotoSite) {
                            $zoneTouristiquePhotoSite->setZoneTouristique(null);
                            $emSite->remove($zoneTouristiquePhotoSite->getPhoto());
                            $emSite->remove($zoneTouristiquePhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********


                // *** gestion video ***
                if (!empty($zoneTouristique->getVideos()) && !$zoneTouristique->getVideos()->isEmpty()) {
                    /** @var ZoneTouristiqueVideo $zoneTouristiqueVideo */
                    foreach ($zoneTouristique->getVideos() as $zoneTouristiqueVideo) {
                        $zoneTouristiqueVideoSite = $zoneTouristiqueSite->getVideos()->filter(function (ZoneTouristiqueVideo $element) use ($zoneTouristiqueVideo) {
                            return $element->getId() == $zoneTouristiqueVideo->getId();
                        })->first();
                        if (false === $zoneTouristiqueVideoSite) {
                            $zoneTouristiqueVideoSite = new ZoneTouristiqueVideo();
                            $zoneTouristiqueSite->addVideo($zoneTouristiqueVideoSite);
                            $zoneTouristiqueVideoSite
                                ->setId($zoneTouristiqueVideo->getId());
                            $metadata = $emSite->getClassMetadata(get_class($zoneTouristiqueVideoSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        if (empty($zoneTouristiqueVideoSite->getVideo()) || $zoneTouristiqueVideoSite->getVideo()->getId() != $zoneTouristiqueVideo->getVideo()->getId()) {
                            $cloneVideo = clone $zoneTouristiqueVideo->getVideo();
                            $metadata = $emSite->getClassMetadata(get_class($cloneVideo));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $cloneVideo->setContext('zone_touristique_video_' . $zoneTouristiqueSite->getSite()->getLibelle());
                            // on supprime l'ancien photo
                            if (!empty($zoneTouristiqueVideoSite->getVideo())) {
                                $emSite->remove($zoneTouristiqueVideoSite->getVideo());
                                $this->deleteFile($zoneTouristiqueVideoSite->getVideo());
                            }
                            $zoneTouristiqueVideoSite
                                ->setVideo($cloneVideo);
                        }
                        $zoneTouristiqueVideoSite
                            ->setActif($zoneTouristiqueVideo->getActif());
                        // *** traductions ***
                        foreach ($zoneTouristiqueVideo->getTraductions() as $traduction) {
                            $traductionSite = $zoneTouristiqueVideoSite->getTraductions()->filter(function (ZoneTouristiqueVideoTraduction $element) use ($traduction) {
                                return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new ZoneTouristiqueVideoTraduction();
                                $zoneTouristiqueVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }

                        // *** fin traductions ***
                    }
                }

                if (!empty($zoneTouristiqueSite->getVideos()) && !$zoneTouristiqueSite->getVideos()->isEmpty()) {
                    /** @var ZoneTouristiqueVideo $zoneTouristiqueVideo */
                    /** @var ZoneTouristiqueVideo $zoneTouristiqueVideoSite */
                    foreach ($zoneTouristiqueSite->getVideos() as $zoneTouristiqueVideoSite) {
                        $zoneTouristiqueVideo = $zoneTouristique->getVideos()->filter(function (ZoneTouristiqueVideo $element) use ($zoneTouristiqueVideoSite) {
                            return $element->getId() == $zoneTouristiqueVideoSite->getId();
                        })->first();
                        if (false === $zoneTouristiqueVideo) {
                            $emSite->remove($zoneTouristiqueVideoSite);
                            $emSite->remove($zoneTouristiqueVideoSite->getVideo());
                            $this->deleteFile($zoneTouristiqueVideoSite->getVideo());
                        }
                    }
                }
                // *** fin gestion video ***

                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterZoneTouristiqueUnifieSiteDistant($entity->getId(), $entity->getZoneTouristiques());
    }

    /**
     * Création d'un nouveau zoneTouristiqueImage
     * @param ZoneTouristiqueImage $zoneTouristiqueImage
     * @param ZoneTouristique $zoneTouristiqueSite
     * @param EntityManager $emSite
     */
    private function createZoneTouristiqueImage(ZoneTouristiqueImage $zoneTouristiqueImage, ZoneTouristique $zoneTouristiqueSite, EntityManager $emSite)
    {
        /** @var ZoneTouristiqueImage $zoneTouristiqueImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($zoneTouristiqueImage))->getName();
        // on cré un nouveau ZoneTouristiqueImage on fonction du type
        $zoneTouristiqueImageSite = new $typeImage();
        $zoneTouristiqueImageSite->setZoneTouristique($zoneTouristiqueSite);
        $zoneTouristiqueImageSite->setActif($zoneTouristiqueImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $zoneTouristiqueImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($zoneTouristiqueImage->getImage()->getProviderReference());
        $cloneImage->setName($zoneTouristiqueImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('zone_touristique_image_' . $zoneTouristiqueSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $zoneTouristiqueImage->getImage()->getId());

        $zoneTouristiqueImageSite->setImage($cloneImage);

        $zoneTouristiqueSite->addImage($zoneTouristiqueImageSite);
        // on ajoute les traductions correspondante
        foreach ($zoneTouristiqueImage->getTraductions() as $traduction) {
            $traductionSite = new ZoneTouristiqueImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $zoneTouristiqueImageSite->addTraduction($traductionSite);
        }
    }

    /**
     * Création d'un nouveau zoneTouristiquePhoto
     * @param ZoneTouristiquePhoto $zoneTouristiquePhoto
     * @param ZoneTouristique $zoneTouristiqueSite
     * @param EntityManager $emSite
     */
    private function createZoneTouristiquePhoto(ZoneTouristiquePhoto $zoneTouristiquePhoto, ZoneTouristique $zoneTouristiqueSite, EntityManager $emSite)
    {
        /** @var ZoneTouristiquePhoto $zoneTouristiquePhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($zoneTouristiquePhoto))->getName();
        // on cré un nouveau ZoneTouristiquePhoto on fonction du type
        $zoneTouristiquePhotoSite = new $typePhoto();
        $zoneTouristiquePhotoSite->setZoneTouristique($zoneTouristiqueSite);
        $zoneTouristiquePhotoSite->setActif($zoneTouristiquePhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $zoneTouristiquePhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($zoneTouristiquePhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($zoneTouristiquePhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('zone_touristique_photo_' . $zoneTouristiqueSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $zoneTouristiquePhoto->getPhoto()->getId());

        $zoneTouristiquePhotoSite->setPhoto($clonePhoto);

        $zoneTouristiqueSite->addPhoto($zoneTouristiquePhotoSite);
        // on ajoute les traductions correspondante
        foreach ($zoneTouristiquePhoto->getTraductions() as $traduction) {
            $traductionSite = new ZoneTouristiquePhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $zoneTouristiquePhotoSite->addTraduction($traductionSite);
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
     * @param Collection $zoneTouristiques
     */
    public function ajouterZoneTouristiqueUnifieSiteDistant($idUnifie, Collection $zoneTouristiques)
    {
        /** @var Site $site */
        /** @var ArrayCollection $zoneTouristiques */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($zoneTouristiques->matching($criteres)) == 0 && (empty($emSite->getRepository(ZoneTouristiqueUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new ZoneTouristiqueUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a ZoneTouristiqueUnifie entity.
     *
     */
    public function showAction(ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        $deleteForm = $this->createDeleteForm($zoneTouristiqueUnifie);

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/show.html.twig', array(
            'zoneTouristiqueUnifie' => $zoneTouristiqueUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ZoneTouristiqueUnifie entity.
     *
     * @param ZoneTouristiqueUnifie $zoneTouristiqueUnifie The ZoneTouristiqueUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_zonetouristique_delete',
                array('id' => $zoneTouristiqueUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ZoneTouristiqueUnifie entity.
     *
     */
    public function editAction(Request $request, ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var ZoneTouristique $entity */
            foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalZoneTouristiques = new ArrayCollection();
        $originalZoneTouristiqueImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalZoneTouristiquePhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
        $originalZoneTouristiqueVideos = new ArrayCollection();
        $originalVideos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $zoneTouristique) {
            $originalZoneTouristiques->add($zoneTouristique);
            // si l'zoneTouristique est celui du CRM
            if ($zoneTouristique->getSite()->getCrm() == 1) {
                // on parcourt les zoneTouristiqueImage pour les comparer ensuite
                /** @var ZoneTouristiqueImage $zoneTouristiqueImage */
                foreach ($zoneTouristique->getImages() as $zoneTouristiqueImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalZoneTouristiqueImages->add($zoneTouristiqueImage);
                    $originalImages->add($zoneTouristiqueImage->getImage());
                }
                // on parcourt les zoneTouristiquePhoto pour les comparer ensuite
                /** @var ZoneTouristiquePhoto $zoneTouristiquePhoto */
                foreach ($zoneTouristique->getPhotos() as $zoneTouristiquePhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalZoneTouristiquePhotos->add($zoneTouristiquePhoto);
                    $originalPhotos->add($zoneTouristiquePhoto->getPhoto());
                }
                // on parcourt les zoneTouristiqueVideo pour les comparer ensuite
                /** @var ZoneTouristiqueVideo $zoneTouristiqueVideo */
                foreach ($zoneTouristique->getVideos() as $zoneTouristiqueVideo) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalZoneTouristiqueVideos->add($zoneTouristiqueVideo);
                    $originalVideos->set($zoneTouristiqueVideo->getId(), $zoneTouristiqueVideo->getVideo());
                }
            }
        }

        $this->ajouterZoneTouristiquesDansForm($zoneTouristiqueUnifie);
//        $this->dispacherDonneesCommune($zoneTouristiqueUnifie);
        $this->zoneTouristiquesSortByAffichage($zoneTouristiqueUnifie);

        $deleteForm = $this->createDeleteForm($zoneTouristiqueUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ZoneTouristiqueUnifieType',
            $zoneTouristiqueUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);
//        dump($editForm);die();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $entity) {
                    if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                        $entity->setActif(false);
                    } else {
                        $entity->setActif(true);
                    }
                }

                // ************* suppression images *************
                // ** CAS OU L'ON SUPPRIME UN "ZoneTouristique IMAGE" **
                // on récupère les ZoneTouristiqueImage de l'hébergementCrm pour les mettre dans une collection
                // afin de les comparer au originaux.
                /** @var ZoneTouristique $zoneTouristiqueCrm */
                $zoneTouristiqueCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $zoneTouristiqueSites = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                $newZoneTouristiqueImages = new ArrayCollection();
                foreach ($zoneTouristiqueCrm->getImages() as $zoneTouristiqueImage) {
                    $newZoneTouristiqueImages->add($zoneTouristiqueImage);
                }
                /** @var ZoneTouristiqueImage $originalZoneTouristiqueImage */
                foreach ($originalZoneTouristiqueImages as $key => $originalZoneTouristiqueImage) {

                    if (false === $newZoneTouristiqueImages->contains($originalZoneTouristiqueImage)) {
                        $originalZoneTouristiqueImage->setZoneTouristique(null);
                        $em->remove($originalZoneTouristiqueImage->getImage());
                        $em->remove($originalZoneTouristiqueImage);
                        // on doit supprimer l'hébergementImage des autres sites
                        // on parcourt les zoneTouristique des sites
                        /** @var ZoneTouristique $zoneTouristiqueSite */
                        foreach ($zoneTouristiqueSites as $zoneTouristiqueSite) {
                            $zoneTouristiqueImageSite = $em->getRepository(ZoneTouristiqueImage::class)->findOneBy(
                                array(
                                    'zoneTouristique' => $zoneTouristiqueSite,
                                    'image' => $originalZoneTouristiqueImage->getImage()
                                ));
                            if (!empty($zoneTouristiqueImageSite)) {
                                $emSite = $this->getDoctrine()->getEntityManager($zoneTouristiqueImageSite->getZoneTouristique()->getSite()->getLibelle());
                                $zoneTouristiqueSite = $emSite->getRepository(ZoneTouristique::class)->findOneBy(
                                    array(
                                        'zoneTouristiqueUnifie' => $zoneTouristiqueImageSite->getZoneTouristique()->getZoneTouristiqueUnifie()
                                    ));
                                $zoneTouristiqueImageSiteSites = new ArrayCollection($emSite->getRepository(ZoneTouristiqueImage::class)->findBy(
                                    array(
                                        'zoneTouristique' => $zoneTouristiqueSite
                                    ))
                                );
                                $zoneTouristiqueImageSiteSite = $zoneTouristiqueImageSiteSites->filter(function (ZoneTouristiqueImage $element)
                                use ($zoneTouristiqueImageSite) {
//                            return $element->getImage()->getProviderReference() == $zoneTouristiqueImageSite->getImage()->getProviderReference();
                                    return $element->getImage()->getMetadataValue('crm_ref_id') == $zoneTouristiqueImageSite->getImage()->getId();
                                })->first();
                                if (!empty($zoneTouristiqueImageSiteSite)) {
                                    $emSite->remove($zoneTouristiqueImageSiteSite->getImage());
                                    $zoneTouristiqueImageSiteSite->setZoneTouristique(null);
                                    $emSite->remove($zoneTouristiqueImageSiteSite);
                                    $emSite->flush();
                                }
                                $zoneTouristiqueImageSite->setZoneTouristique(null);
                                $em->remove($zoneTouristiqueImageSite->getImage());
                                $em->remove($zoneTouristiqueImageSite);
                            }
                        }
                    }
                }
                // ************* fin suppression images *************


                // ************* suppression photos *************
                // ** CAS OU L'ON SUPPRIME UN "ZoneTouristique PHOTO" **
                // on récupère les ZoneTouristiquePhoto de l'hébergementCrm pour les mettre dans une collection
                // afin de les comparer au originaux.
                /** @var ZoneTouristique $zoneTouristiqueCrm */
                $zoneTouristiqueCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $zoneTouristiqueSites = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                $newZoneTouristiquePhotos = new ArrayCollection();
                foreach ($zoneTouristiqueCrm->getPhotos() as $zoneTouristiquePhoto) {
                    $newZoneTouristiquePhotos->add($zoneTouristiquePhoto);
                }
                /** @var ZoneTouristiquePhoto $originalZoneTouristiquePhoto */
                foreach ($originalZoneTouristiquePhotos as $key => $originalZoneTouristiquePhoto) {

                    if (false === $newZoneTouristiquePhotos->contains($originalZoneTouristiquePhoto)) {
                        $originalZoneTouristiquePhoto->setZoneTouristique(null);
                        $em->remove($originalZoneTouristiquePhoto->getPhoto());
                        $em->remove($originalZoneTouristiquePhoto);
                        // on doit supprimer l'hébergementPhoto des autres sites
                        // on parcourt les zoneTouristique des sites
                        /** @var ZoneTouristique $zoneTouristiqueSite */
                        foreach ($zoneTouristiqueSites as $zoneTouristiqueSite) {
                            $zoneTouristiquePhotoSite = $em->getRepository(ZoneTouristiquePhoto::class)->findOneBy(
                                array(
                                    'zoneTouristique' => $zoneTouristiqueSite,
                                    'photo' => $originalZoneTouristiquePhoto->getPhoto()
                                ));
                            if (!empty($zoneTouristiquePhotoSite)) {
                                $emSite = $this->getDoctrine()->getEntityManager($zoneTouristiquePhotoSite->getZoneTouristique()->getSite()->getLibelle());
                                $zoneTouristiqueSite = $emSite->getRepository(ZoneTouristique::class)->findOneBy(
                                    array(
                                        'zoneTouristiqueUnifie' => $zoneTouristiquePhotoSite->getZoneTouristique()->getZoneTouristiqueUnifie()
                                    ));
                                $zoneTouristiquePhotoSiteSites = new ArrayCollection($emSite->getRepository(ZoneTouristiquePhoto::class)->findBy(
                                    array(
                                        'zoneTouristique' => $zoneTouristiqueSite
                                    ))
                                );
                                $zoneTouristiquePhotoSiteSite = $zoneTouristiquePhotoSiteSites->filter(function (ZoneTouristiquePhoto $element)
                                use ($zoneTouristiquePhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $zoneTouristiquePhotoSite->getPhoto()->getProviderReference();
                                    return $element->getPhoto()->getMetadataValue('crm_ref_id') == $zoneTouristiquePhotoSite->getPhoto()->getId();
                                })->first();
                                if (!empty($zoneTouristiquePhotoSiteSite)) {
                                    $emSite->remove($zoneTouristiquePhotoSiteSite->getPhoto());
                                    $zoneTouristiquePhotoSiteSite->setZoneTouristique(null);
                                    $emSite->remove($zoneTouristiquePhotoSiteSite);
                                    $emSite->flush();
                                }
                                $zoneTouristiquePhotoSite->setZoneTouristique(null);
                                $em->remove($zoneTouristiquePhotoSite->getPhoto());
                                $em->remove($zoneTouristiquePhotoSite);
                            }
                        }
                    }
                }
                // ************* fin suppression photos *************


                // ** suppression videos **
                foreach ($originalZoneTouristiqueVideos as $originalZoneTouristiqueVideo) {
                    if (false === $zoneTouristiqueCrm->getVideos()->contains($originalZoneTouristiqueVideo)) {
                        $videos = $em->getRepository(ZoneTouristiqueVideo::class)->findBy(array('video' => $originalZoneTouristiqueVideo->getVideo()));
                        foreach ($videos as $video) {
                            $em->remove($video);
                        }
                        $em->remove($originalZoneTouristiqueVideo->getVideo());
                        $this->deleteFile($originalZoneTouristiqueVideo->getVideo());
                    }
                }
                // ** fin suppression videos **
                // *** gestion des videos ***
                /** @var ZoneTouristique $zoneTouristiqueCrm */
                $zoneTouristiqueCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $zoneTouristiqueSites = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                /** @var ZoneTouristiqueVideo $zoneTouristiqueVideo */
                foreach ($zoneTouristiqueCrm->getVideos() as $key => $zoneTouristiqueVideo) {
                    foreach ($zoneTouristiqueSites as $zoneTouristiqueSite) {
                        if (empty($zoneTouristiqueVideo->getId())) {
                            $zoneTouristiqueVideoSite = clone $zoneTouristiqueVideo;
                        } else {
                            $zoneTouristiqueVideoSite = $em->getRepository(ZoneTouristiqueVideo::class)->findOneBy(array('video' => $originalVideos->get($zoneTouristiqueVideo->getId()), 'zoneTouristique' => $zoneTouristiqueSite));
                            if ($originalVideos->get($zoneTouristiqueVideo->getId()) != $zoneTouristiqueVideo->getVideo()) {
                                $em->remove($zoneTouristiqueVideoSite->getVideo());
                                $this->deleteFile($zoneTouristiqueVideoSite->getVideo());
                                $zoneTouristiqueVideoSite->setVideo($zoneTouristiqueVideo->getVideo());
                            }
                        }
                        $zoneTouristiqueSite->addVideo($zoneTouristiqueVideoSite);
                        $actif = false;
                        if (!empty($request->get('zone_touristique_unifie')['zoneTouristiques'][0]['videos'][$key]['sites'])) {
                            if (in_array($zoneTouristiqueSite->getSite()->getId(), $request->get('zone_touristique_unifie')['zoneTouristiques'][0]['videos'][$key]['sites'])) {
                                $actif = true;
                            }
                        }
                        $zoneTouristiqueVideoSite->setActif($actif);

                        // *** traductions ***
                        foreach ($zoneTouristiqueVideo->getTraductions() as $traduction) {
                            $traductionSite = $zoneTouristiqueVideoSite->getTraductions()->filter(function (ZoneTouristiqueVideoTraduction $element) use ($traduction) {
                                return $element->getLangue() == $traduction->getLangue();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new ZoneTouristiqueVideoTraduction();
                                $zoneTouristiqueVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($traduction->getLangue());
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }
                        // *** fin traductions ***
                    }
                }
                // *** fin gestion des videos ***



                // ***** Gestion des Medias *****
                // CAS D'UN NOUVEAU 'ZoneTouristique IMAGE' OU DE MODIFICATION D'UN "ZoneTouristique IMAGE"
                /** @var ZoneTouristiqueImage $zoneTouristiqueImage */
                // tableau pour la suppression des anciens images
                $imageToRemoveCollection = new ArrayCollection();
                $keyCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->indexOf($zoneTouristiqueCrm);
                // on parcourt les zoneTouristiqueImages de l'zoneTouristique crm
                foreach ($zoneTouristiqueCrm->getImages() as $key => $zoneTouristiqueImage) {
                    // on active le nouveau zoneTouristiqueImage (CRM) => il doit être toujours actif
                    $zoneTouristiqueImage->setActif(true);
                    // parcourir tout les sites
                    /** @var Site $site */
                    foreach ($sites as $site) {
                        // sauf  le crm (puisqu'on l'a déjà renseigné)
                        // dans le but de créer un hebegrementImage pour chacun
                        if ($site->getCrm() == 0) {
                            // on récupère l'hébegergement du site
                            /** @var ZoneTouristique $zoneTouristiqueSite */
                            $zoneTouristiqueSite = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) use ($site) {
                                return $element->getSite() == $site;
                            })->first();
                            // si hébergement existe
                            if (!empty($zoneTouristiqueSite)) {
                                // on réinitialise la variable
                                unset($zoneTouristiqueImageSite);
                                // s'il ne s'agit pas d'un nouveau zoneTouristiqueImage
                                if (!empty($zoneTouristiqueImage->getId())) {
                                    // on récupère l'zoneTouristiqueImage pour le modifier
                                    $zoneTouristiqueImageSite = $em->getRepository(ZoneTouristiqueImage::class)->findOneBy(array('zoneTouristique' => $zoneTouristiqueSite, 'image' => $originalImages->get($key)));
                                }
                                // si l'zoneTouristiqueImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                                if (empty($zoneTouristiqueImage->getId()) || empty($zoneTouristiqueImageSite)) {
                                    // on récupère la classe correspondant au image (photo ou video)
                                    $typeImage = (new ReflectionClass($zoneTouristiqueImage))->getName();
                                    // on créé un nouveau ZoneTouristiqueImage on fonction du type
                                    /** @var ZoneTouristiqueImage $zoneTouristiqueImageSite */
                                    $zoneTouristiqueImageSite = new $typeImage();
                                    $zoneTouristiqueImageSite->setZoneTouristique($zoneTouristiqueSite);
                                }
                                // si l'hébergemenent image existe déjà pour le site
                                if (!empty($zoneTouristiqueImageSite)) {
                                    if ($zoneTouristiqueImageSite->getImage() != $zoneTouristiqueImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($zoneTouristiqueImageSite->getImage()) && !$imageToRemoveCollection->contains($zoneTouristiqueImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($zoneTouristiqueImageSite->getImage());
//                                    }
                                        // on met le nouveau image
                                        $zoneTouristiqueImageSite->setImage($zoneTouristiqueImage->getImage());
                                    }
                                    $zoneTouristiqueSite->addImage($zoneTouristiqueImageSite);

                                    /** @var ZoneTouristiqueImageTraduction $traduction */
                                    foreach ($zoneTouristiqueImage->getTraductions() as $traduction) {
                                        /** @var ZoneTouristiqueImageTraduction $traductionSite */
                                        $traductionSites = $zoneTouristiqueImageSite->getTraductions();
                                        $traductionSite = null;
                                        if (!$traductionSites->isEmpty()) {
                                            $traductionSite = $traductionSites->filter(function (ZoneTouristiqueImageTraduction $element) use ($traduction) {
                                                return $element->getLangue() == $traduction->getLangue();
                                            })->first();
                                        }
                                        if (empty($traductionSite)) {
                                            $traductionSite = new ZoneTouristiqueImageTraduction();
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $zoneTouristiqueImageSite->addTraduction($traductionSite);
                                        }
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    }
                                    // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                    if (!empty($request->get('zone_touristique_unifie')['zoneTouristiques'][$keyCrm]['images'][$key]['sites']) &&
                                        in_array($site->getId(), $request->get('zone_touristique_unifie')['zoneTouristiques'][$keyCrm]['images'][$key]['sites'])
                                    ) {
                                        $zoneTouristiqueImageSite->setActif(true);
                                    } else {
                                        $zoneTouristiqueImageSite->setActif(false);
                                    }
                                }
                            }
                        }
                        // on est dans zoneTouristiqueImage CRM
                        // s'il s'agit d'un nouveau média
                        elseif (empty($zoneTouristiqueImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                            // on stocke  l'ancien media pour le supprimer après le persist final
                            $imageToRemoveCollection->add($originalImages->get($key));
                        }
                    }
                }


                // CAS D'UN NOUVEAU 'ZoneTouristique PHOTO' OU DE MODIFICATION D'UN "ZoneTouristique PHOTO"
                /** @var ZoneTouristiquePhoto $zoneTouristiquePhoto */
                // tableau pour la suppression des anciens photos
                $photoToRemoveCollection = new ArrayCollection();
                $keyCrm = $zoneTouristiqueUnifie->getZoneTouristiques()->indexOf($zoneTouristiqueCrm);
                // on parcourt les zoneTouristiquePhotos de l'zoneTouristique crm
                foreach ($zoneTouristiqueCrm->getPhotos() as $key => $zoneTouristiquePhoto) {
                    // on active le nouveau zoneTouristiquePhoto (CRM) => il doit être toujours actif
                    $zoneTouristiquePhoto->setActif(true);
                    // parcourir tout les sites
                    /** @var Site $site */
                    foreach ($sites as $site) {
                        // sauf  le crm (puisqu'on l'a déjà renseigné)
                        // dans le but de créer un hebegrementPhoto pour chacun
                        if ($site->getCrm() == 0) {
                            // on récupère l'hébegergement du site
                            /** @var ZoneTouristique $zoneTouristiqueSite */
                            $zoneTouristiqueSite = $zoneTouristiqueUnifie->getZoneTouristiques()->filter(function (ZoneTouristique $element) use ($site) {
                                return $element->getSite() == $site;
                            })->first();
                            // si hébergement existe
                            if (!empty($zoneTouristiqueSite)) {
                                // on réinitialise la variable
                                unset($zoneTouristiquePhotoSite);
                                // s'il ne s'agit pas d'un nouveau zoneTouristiquePhoto
                                if (!empty($zoneTouristiquePhoto->getId())) {
                                    // on récupère l'zoneTouristiquePhoto pour le modifier
                                    $zoneTouristiquePhotoSite = $em->getRepository(ZoneTouristiquePhoto::class)->findOneBy(array('zoneTouristique' => $zoneTouristiqueSite, 'photo' => $originalPhotos->get($key)));
                                }
                                // si l'zoneTouristiquePhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                                if (empty($zoneTouristiquePhoto->getId()) || empty($zoneTouristiquePhotoSite)) {
                                    // on récupère la classe correspondant au photo (photo ou video)
                                    $typePhoto = (new ReflectionClass($zoneTouristiquePhoto))->getName();
                                    // on créé un nouveau ZoneTouristiquePhoto on fonction du type
                                    /** @var ZoneTouristiquePhoto $zoneTouristiquePhotoSite */
                                    $zoneTouristiquePhotoSite = new $typePhoto();
                                    $zoneTouristiquePhotoSite->setZoneTouristique($zoneTouristiqueSite);
                                }
                                // si l'hébergemenent photo existe déjà pour le site
                                if (!empty($zoneTouristiquePhotoSite)) {
                                    if ($zoneTouristiquePhotoSite->getPhoto() != $zoneTouristiquePhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($zoneTouristiquePhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($zoneTouristiquePhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($zoneTouristiquePhotoSite->getPhoto());
//                                    }
                                        // on met le nouveau photo
                                        $zoneTouristiquePhotoSite->setPhoto($zoneTouristiquePhoto->getPhoto());
                                    }
                                    $zoneTouristiqueSite->addPhoto($zoneTouristiquePhotoSite);

                                    /** @var ZoneTouristiquePhotoTraduction $traduction */
                                    foreach ($zoneTouristiquePhoto->getTraductions() as $traduction) {
                                        /** @var ZoneTouristiquePhotoTraduction $traductionSite */
                                        $traductionSites = $zoneTouristiquePhotoSite->getTraductions();
                                        $traductionSite = null;
                                        if (!$traductionSites->isEmpty()) {
                                            $traductionSite = $traductionSites->filter(function (ZoneTouristiquePhotoTraduction $element) use ($traduction) {
                                                return $element->getLangue() == $traduction->getLangue();
                                            })->first();
                                        }
                                        if (empty($traductionSite)) {
                                            $traductionSite = new ZoneTouristiquePhotoTraduction();
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $zoneTouristiquePhotoSite->addTraduction($traductionSite);
                                        }
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    }
                                    // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                    if (!empty($request->get('zone_touristique_unifie')['zoneTouristiques'][$keyCrm]['photos'][$key]['sites']) &&
                                        in_array($site->getId(), $request->get('zone_touristique_unifie')['zoneTouristiques'][$keyCrm]['photos'][$key]['sites'])
                                    ) {
                                        $zoneTouristiquePhotoSite->setActif(true);
                                    } else {
                                        $zoneTouristiquePhotoSite->setActif(false);
                                    }
                                }
                            }
                        }
                        // on est dans zoneTouristiquePhoto CRM
                        // s'il s'agit d'un nouveau média
                        elseif (empty($zoneTouristiquePhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                            // on stocke  l'ancien media pour le supprimer après le persist final
                            $photoToRemoveCollection->add($originalPhotos->get($key));
                        }
                    }
                }
                // ***** Fin Gestion des Medias *****

                $em->persist($zoneTouristiqueUnifie);
                $em->flush();


                $this->copieVersSites($zoneTouristiqueUnifie, $originalZoneTouristiqueImages, $originalZoneTouristiquePhotos);
            } catch (ForeignKeyConstraintViolationException $except) {
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer la zone touristique, elle est utilisée par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirectToRoute('geographie_zonetouristique_edit',
                    array('id' => $zoneTouristiqueUnifie->getId()));
            }
            $this->addFlash('success', 'la zone touristique a bien été modifiée');

            return $this->redirectToRoute('geographie_zonetouristique_edit',
                array('id' => $zoneTouristiqueUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/edit.html.twig', array(
            'entity' => $zoneTouristiqueUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ZoneTouristiqueUnifie entity.
     *
     */
    public function deleteAction(Request $request, ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        $form = $this->createDeleteForm($zoneTouristiqueUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $zoneTouristiqueUnifieSite = $emSite->find(ZoneTouristiqueUnifie::class,
                        $zoneTouristiqueUnifie->getId());
                    if (!empty($zoneTouristiqueUnifieSite)) {
                        $emSite->remove($zoneTouristiqueUnifieSite);

                        $zoneTouristiqueSite = $zoneTouristiqueUnifieSite->getZoneTouristiques()->first();

                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($zoneTouristiqueSite->getImages())) {
                            /** @var ZoneTouristiqueImage $zoneTouristiqueImageSite */
                            foreach ($zoneTouristiqueSite->getImages() as $zoneTouristiqueImageSite) {
                                $imageSite = $zoneTouristiqueImageSite->getImage();
                                $zoneTouristiqueImageSite->setImage(null);
                                if (!empty($imageSite)) {
                                    $emSite->remove($imageSite);
                                }
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($zoneTouristiqueSite->getPhotos())) {
                            /** @var ZoneTouristiquePhoto $zoneTouristiquePhotoSite */
                            foreach ($zoneTouristiqueSite->getPhotos() as $zoneTouristiquePhotoSite) {
                                $photoSite = $zoneTouristiquePhotoSite->getPhoto();
                                $zoneTouristiquePhotoSite->setPhoto(null);
                                if (!empty($photoSite)) {
                                    $emSite->remove($photoSite);
                                }
                            }
                        }

                        // si il y a des videos pour l'entité, les supprimer
                        if (!empty($zoneTouristiqueSite->getVideos())) {
                            /** @var ZoneTouristiqueVideo $zoneTouristiqueVideoSite */
                            foreach ($zoneTouristiqueSite->getVideos() as $zoneTouristiqueVideoSite) {
                                $emSite->remove($zoneTouristiqueVideoSite);
                                $emSite->remove($zoneTouristiqueVideoSite->getVideo());
                            }
                        }


                        $emSite->flush();
                    }
                }
//                $em = $this->getDoctrine()->getManager();

                if (!empty($zoneTouristiqueUnifie)) {
                    if (!empty($zoneTouristiqueUnifie->getZoneTouristiques())) {
                        /** @var ZoneTouristique $zoneTouristique */
                        foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $zoneTouristique) {

                            // si il y a des images pour l'entité, les supprimer
                            if (!empty($zoneTouristique->getImages())) {
                                /** @var ZoneTouristiqueImage $zoneTouristiqueImage */
                                foreach ($zoneTouristique->getImages() as $zoneTouristiqueImage) {
                                    $image = $zoneTouristiqueImage->getImage();
                                    $zoneTouristiqueImage->setImage(null);
                                    $em->remove($image);
                                }
                            }
                            // si il y a des photos pour l'entité, les supprimer
                            if (!empty($zoneTouristique->getPhotos())) {
                                /** @var ZoneTouristiquePhoto $zoneTouristiquePhoto */
                                foreach ($zoneTouristique->getPhotos() as $zoneTouristiquePhoto) {
                                    $photo = $zoneTouristiquePhoto->getPhoto();
                                    $zoneTouristiquePhoto->setPhoto(null);
                                    $em->remove($photo);
                                }
                            }
                            // si il y a des videos pour l'entité, les supprimer
                            if (!empty($zoneTouristique->getVideos())) {
                                /** @var ZoneTouristiqueVideo $zoneTouristiqueVideoSite */
                                foreach ($zoneTouristique->getVideos() as $zoneTouristiqueVideoSite) {
                                    $em->remove($zoneTouristiqueVideoSite);
                                    $em->remove($zoneTouristiqueVideoSite->getVideo());
                                }
                            }
                        }
                        $em->flush();
                    }
//                    $emSite->remove($zoneTouristiqueUnifieSite);
//                    $emSite->flush();
                }

                $em->remove($zoneTouristiqueUnifie);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer la zone touristique, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }
        }
        $this->addFlash('success', 'la zone touristique a bien été supprimée');
        return $this->redirectToRoute('geographie_zonetouristique_index');
    }

}
