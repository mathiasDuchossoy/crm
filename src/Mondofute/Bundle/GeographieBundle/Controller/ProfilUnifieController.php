<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilImage;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilImageTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilPhotoTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie;
use Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * ProfilUnifie controller.
 *
 */
class ProfilUnifieController extends Controller
{
    /**
     * Lists all ProfilUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
        $count = $em
            ->getRepository('MondofuteGeographieBundle:ProfilUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'geographie_profil_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteGeographieBundle:ProfilUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteGeographie/profilunifie/index.html.twig', array(
            'profilUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new ProfilUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $profilUnifie = new ProfilUnifie();

        $this->ajouterProfilsDansForm($profilUnifie);
        $this->profilsSortByAffichage($profilUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType', $profilUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Profil $entity */
            foreach ($profilUnifie->getProfils() as $entity){
                if(false === in_array($entity->getSite()->getId(),$sitesAEnregistrer)){
                    $entity->setActif(false);
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('profil_unifie')['profils'] as $key => $profil) {
                if (!empty($profilUnifie->getProfils()->get($key)) && $profilUnifie->getProfils()->get($key)->getSite()->getCrm() == 1) {
                    $profilCrm = $profilUnifie->getProfils()->get($key);
                    if (!empty($profil['images'])) {
                        foreach ($profil['images'] as $keyImage => $image) {
                            /** @var ProfilImage $imageCrm */
                            $imageCrm = $profilCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setProfil($profilCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Profil $profilSite */
                                    $profilSite = $profilUnifie->getProfils()->filter(function (Profil $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($profilSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var ProfilImage $profilImage */
                                        $profilImage = new $typeImage();
                                        $profilImage->setProfil($profilSite);
                                        $profilImage->setImage($imageCrm->getImage());
                                        $profilSite->addImage($profilImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new ProfilImageTraduction();
                                            /** @var ProfilImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $profilImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $profilImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('profil_unifie')['profils'] as $key => $profil) {
                if (!empty($profilUnifie->getProfils()->get($key)) && $profilUnifie->getProfils()->get($key)->getSite()->getCrm() == 1) {
                    $profilCrm = $profilUnifie->getProfils()->get($key);
                    if (!empty($profil['photos'])) {
                        foreach ($profil['photos'] as $keyPhoto => $photo) {
                            /** @var ProfilPhoto $photoCrm */
                            $photoCrm = $profilCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setProfil($profilCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Profil $profilSite */
                                    $profilSite = $profilUnifie->getProfils()->filter(function (Profil $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($profilSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var ProfilPhoto $profilPhoto */
                                        $profilPhoto = new $typePhoto();
                                        $profilPhoto->setProfil($profilSite);
                                        $profilPhoto->setPhoto($photoCrm->getPhoto());
                                        $profilSite->addPhoto($profilPhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new ProfilPhotoTraduction();
                                            /** @var ProfilPhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $profilPhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $profilPhoto->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****


            $em->persist($profilUnifie);
            $em->flush();

            $this->copieVersSites($profilUnifie);
            $this->addFlash('success', 'le profil a bien été créé');
            return $this->redirectToRoute('geographie_profil_edit', array('id' => $profilUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/profilunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $profilUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param ProfilUnifie $entity
     */
    private function ajouterProfilsDansForm(ProfilUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getProfils() as $profil) {
                if ($profil->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {
//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($profil->getTraductions()->filter(function (ProfilTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new ProfilTraduction();
                            $traduction->setLangue($langue);
                            $profil->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $profil = new Profil();
                $profil->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new ProfilTraduction();
                    $traduction->setLangue($langue);
                    $profil->addTraduction($traduction);
                }
                $entity->addProfil($profil);
            }
        }
    }

    /**
     * Classe les profils par classementAffichage
     * @param ProfilUnifie $entity
     */
    private function profilsSortByAffichage(ProfilUnifie $entity)
    {
        /** @var ArrayCollection $profils */
        // Trier les stations en fonction de leurs ordre d'affichage
        $profils = $entity->getProfils(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $profils->getIterator();
        unset($profils);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Profil $a, Profil $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $profils = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($profils);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setProfils($profils);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $profils
     */
    private function traductionsSortByLangue(ArrayCollection $profils)
    {
        /** @var Profil $profil */
        /** @var ArrayIterator $iterator */
        foreach ($profils as $profil) {
            $traductions = $profil->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (ProfilTraduction $a, ProfilTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $profil->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les profils qui ne doivent pas être enregistrer
     * @param ProfilUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerProfils(ProfilUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getProfils() as $profil) {
            if (!in_array($profil->getSite()->getId(), $sitesAEnregistrer)) {
                $profil->setProfilUnifie(null);
                $entity->removeProfil($profil);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param ProfilUnifie $entity
     */
    public function copieVersSites(ProfilUnifie $entity, $originalProfilImages = null, $originalProfilPhotos = null)
    {
        /** @var ProfilTraduction $profilTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getProfils() as $profil) {
            if ($profil->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($profil->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $profil->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->find(ProfilUnifie::class, $entity->getId())))) {
                    $entitySite = new ProfilUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($profilSite = $emSite->getRepository(Profil::class)->findOneBy(array('profilUnifie' => $entitySite))))) {
                    $profilSite = new Profil();
                }

//            copie des données station
                $profilSite
                    ->setSite($site)
                    ->setProfilUnifie($entitySite)
                    ->setActif($profil->getActif())
                ;

//            Gestion des traductions
                foreach ($profil->getTraductions() as $profilTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $profilTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($profilTraducSite = $emSite->getRepository(ProfilTraduction::class)->findOneBy(array(
                        'profil' => $profilSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $profilTraducSite = new ProfilTraduction();
                    }

//                copie des données traductions
                    $profilTraducSite->setLangue($langue)
                        ->setLibelle($profilTraduc->getLibelle())
                        ->setDescription($profilTraduc->getDescription())
                        ->setAccueil($profilTraduc->getAccueil())
                        ->setProfil($profilSite);

//                ajout a la collection de traduction de la station distante
                    $profilSite->addTraduction($profilTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $profilImages = $profil->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'profil de référence
                if (!empty($profilImages) && !$profilImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($profilSite->getImages()) && !$profilSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $profilImageSites = new ArrayCollection();
                        foreach ($profilSite->getImages() as $profilimageSite) {
                            $profilImageSites->add($profilimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var ProfilImage $profilImage */
                        foreach ($profilImages as $profilImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'profilImage original correspondant sur le crm
                            /** @var ArrayCollection $originalProfilImages */
                            $originalProfilImage = $originalProfilImages->filter(function (ProfilImage $element) use ($profilImage) {
                                return $element->getImage() == $profilImage->getImage();
                            })->first();
                            unset($profilImageSite);
                            if ($originalProfilImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalProfilImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalProfilImage);

                                $profilImageSite = $profilImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'profilImage existe sur la bdd distante, on va le modifier
                            /** @var ProfilImage $profilImageSite */
                            if (!empty($profilImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'profilImage du crm)
                                if ($profilImageSite->getImage()->getMetadataValue('crm_ref_id') != $profilImage->getImage()->getId()) {
                                    $cloneImage = clone $profilImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $profilImage->getImage()->getId());
                                    $cloneImage->setContext('profil_image_' . $profil->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($profilImageSite->getImage());

                                    $profilImageSite->setImage($cloneImage);
                                }

                                $profilImageSite->setActif($profilImage->getActif());

                                // on parcourt les traductions
                                /** @var ProfilImageTraduction $traduction */
                                foreach ($profilImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var ProfilImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $profilImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (ProfilImageTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new ProfilImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $profilImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createProfilImage($profilImage, $profilSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'profil de référence
                        /** @var ProfilImage $profilImage */
                        foreach ($profilImages as $profilImage) {
                            $this->createProfilImage($profilImage, $profilSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($profilImageSites)) {
                        /** @var ProfilImage $profilImageSite */
                        foreach ($profilImageSites as $profilImageSite) {
                            $profilImageSite->setProfil(null);
                            $emSite->remove($profilImageSite->getImage());
                            $emSite->remove($profilImageSite);
                        }
                    }
                }


                $profilPhotos = $profil->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'profil de référence
                if (!empty($profilPhotos) && !$profilPhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($profilSite->getPhotos()) && !$profilSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $profilPhotoSites = new ArrayCollection();
                        foreach ($profilSite->getPhotos() as $profilphotoSite) {
                            $profilPhotoSites->add($profilphotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var ProfilPhoto $profilPhoto */
                        foreach ($profilPhotos as $profilPhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'profilPhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalProfilPhotos */
                            $originalProfilPhoto = $originalProfilPhotos->filter(function (ProfilPhoto $element) use ($profilPhoto) {
                                return $element->getPhoto() == $profilPhoto->getPhoto();
                            })->first();
                            unset($profilPhotoSite);
                            if ($originalProfilPhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalProfilPhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalProfilPhoto);

                                $profilPhotoSite = $profilPhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'profilPhoto existe sur la bdd distante, on va le modifier
                            /** @var ProfilPhoto $profilPhotoSite */
                            if (!empty($profilPhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'profilPhoto du crm)
                                if ($profilPhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $profilPhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $profilPhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $profilPhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('profil_photo_' . $profil->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($profilPhotoSite->getPhoto());

                                    $profilPhotoSite->setPhoto($clonePhoto);
                                }

                                $profilPhotoSite->setActif($profilPhoto->getActif());

                                // on parcourt les traductions
                                /** @var ProfilPhotoTraduction $traduction */
                                foreach ($profilPhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var ProfilPhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $profilPhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (ProfilPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new ProfilPhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $profilPhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createProfilPhoto($profilPhoto, $profilSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'profil de référence
                        /** @var ProfilPhoto $profilPhoto */
                        foreach ($profilPhotos as $profilPhoto) {
                            $this->createProfilPhoto($profilPhoto, $profilSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($profilPhotoSites)) {
                        /** @var ProfilPhoto $profilPhotoSite */
                        foreach ($profilPhotoSites as $profilPhotoSite) {
                            $profilPhotoSite->setProfil(null);
                            $emSite->remove($profilPhotoSite->getPhoto());
                            $emSite->remove($profilPhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********



                $entitySite->addProfil($profilSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterProfilUnifieSiteDistant($entity->getId(), $entity->getProfils());
    }


    /**
     * Création d'un nouveau profilImage
     * @param ProfilImage $profilImage
     * @param Profil $profilSite
     * @param EntityManager $emSite
     */
    private function createProfilImage(ProfilImage $profilImage, Profil $profilSite, EntityManager $emSite)
    {
        /** @var ProfilImage $profilImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($profilImage))->getName();
        // on cré un nouveau ProfilImage on fonction du type
        $profilImageSite = new $typeImage();
        $profilImageSite->setProfil($profilSite);
        $profilImageSite->setActif($profilImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $profilImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($profilImage->getImage()->getProviderReference());
        $cloneImage->setName($profilImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('profil_image_' . $profilSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $profilImage->getImage()->getId());

        $profilImageSite->setImage($cloneImage);

        $profilSite->addImage($profilImageSite);
        // on ajoute les traductions correspondante
        foreach ($profilImage->getTraductions() as $traduction) {
            $traductionSite = new ProfilImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $profilImageSite->addTraduction($traductionSite);
        }
    }


    /**
     * Création d'un nouveau profilPhoto
     * @param ProfilPhoto $profilPhoto
     * @param Profil $profilSite
     * @param EntityManager $emSite
     */
    private function createProfilPhoto(ProfilPhoto $profilPhoto, Profil $profilSite, EntityManager $emSite)
    {
        /** @var ProfilPhoto $profilPhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($profilPhoto))->getName();
        // on cré un nouveau ProfilPhoto on fonction du type
        $profilPhotoSite = new $typePhoto();
        $profilPhotoSite->setProfil($profilSite);
        $profilPhotoSite->setActif($profilPhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $profilPhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($profilPhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($profilPhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('profil_photo_' . $profilSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $profilPhoto->getPhoto()->getId());

        $profilPhotoSite->setPhoto($clonePhoto);

        $profilSite->addPhoto($profilPhotoSite);
        // on ajoute les traductions correspondante
        foreach ($profilPhoto->getTraductions() as $traduction) {
            $traductionSite = new ProfilPhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $profilPhotoSite->addTraduction($traductionSite);
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param Collection $profils
     */
    public function ajouterProfilUnifieSiteDistant($idUnifie, Collection $profils)
    {
        /** @var Site $site */
        /** @var ArrayCollection $profils */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($profils->matching($criteres)) == 0 && (empty($emSite->getRepository(ProfilUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new ProfilUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a ProfilUnifie entity.
     *
     */
    public function showAction(ProfilUnifie $profilUnifie)
    {
        $deleteForm = $this->createDeleteForm($profilUnifie);

        return $this->render('@MondofuteGeographie/profilunifie/show.html.twig', array(
            'profilUnifie' => $profilUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ProfilUnifie entity.
     *
     * @param ProfilUnifie $profilUnifie The ProfilUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProfilUnifie $profilUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_profil_delete', array('id' => $profilUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ProfilUnifie entity.
     *
     */
    public function editAction(Request $request, ProfilUnifie $profilUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Profil $entity */
            foreach ($profilUnifie->getProfils() as $entity) {
                if ($entity->getActif()){
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalProfilImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalProfilPhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($profilUnifie->getProfils() as $profil) {
            // si l'profil est celui du CRM
            if ($profil->getSite()->getCrm() == 1) {
                // on parcourt les profilImage pour les comparer ensuite
                /** @var ProfilImage $profilImage */
                foreach ($profil->getImages() as $profilImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalProfilImages->add($profilImage);
                    $originalImages->add($profilImage->getImage());
                }
                // on parcourt les profilPhoto pour les comparer ensuite
                /** @var ProfilPhoto $profilPhoto */
                foreach ($profil->getPhotos() as $profilPhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalProfilPhotos->add($profilPhoto);
                    $originalPhotos->add($profilPhoto->getPhoto());
                }
            }
        }

        $this->ajouterProfilsDansForm($profilUnifie);
        $this->profilsSortByAffichage($profilUnifie);
        $deleteForm = $this->createDeleteForm($profilUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType', $profilUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($profilUnifie->getProfils() as $entity){
                if(false === in_array($entity->getSite()->getId(),$sitesAEnregistrer)){
                    $entity->setActif(false);
                }else{
                    $entity->setActif(true);
                }
            }

            // ************* suppression images *************
            // ** CAS OU L'ON SUPPRIME UN "PROFIL IMAGE" **
            // on récupère les ProfilImage de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Profil $profilCrm */
            $profilCrm = $profilUnifie->getProfils()->filter(function (Profil $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $profilSites = $profilUnifie->getProfils()->filter(function (Profil $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newProfilImages = new ArrayCollection();
            foreach ($profilCrm->getImages() as $profilImage) {
                $newProfilImages->add($profilImage);
            }
            /** @var ProfilImage $originalProfilImage */
            foreach ($originalProfilImages as $key => $originalProfilImage) {

                if (false === $newProfilImages->contains($originalProfilImage)) {
                    $originalProfilImage->setProfil(null);
                    $em->remove($originalProfilImage->getImage());
                    $em->remove($originalProfilImage);
                    // on doit supprimer l'hébergementImage des autres sites
                    // on parcourt les profil des sites
                    /** @var Profil $profilSite */
                    foreach ($profilSites as $profilSite) {
                        $profilImageSite = $em->getRepository(ProfilImage::class)->findOneBy(
                            array(
                                'profil' => $profilSite,
                                'image' => $originalProfilImage->getImage()
                            ));
                        if (!empty($profilImageSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($profilImageSite->getProfil()->getSite()->getLibelle());
                            $profilSite = $emSite->getRepository(Profil::class)->findOneBy(
                                array(
                                    'profilUnifie' => $profilImageSite->getProfil()->getProfilUnifie()
                                ));
                            $profilImageSiteSites = new ArrayCollection($emSite->getRepository(ProfilImage::class)->findBy(
                                array(
                                    'profil' => $profilSite
                                ))
                            );
                            $profilImageSiteSite = $profilImageSiteSites->filter(function (ProfilImage $element)
                            use ($profilImageSite) {
//                            return $element->getImage()->getProviderReference() == $profilImageSite->getImage()->getProviderReference();
                                return $element->getImage()->getMetadataValue('crm_ref_id') == $profilImageSite->getImage()->getId();
                            })->first();
                            if (!empty($profilImageSiteSite)) {
                                $emSite->remove($profilImageSiteSite->getImage());
                                $profilImageSiteSite->setProfil(null);
                                $emSite->remove($profilImageSiteSite);
                                $emSite->flush();
                            }
                            $profilImageSite->setProfil(null);
                            $em->remove($profilImageSite->getImage());
                            $em->remove($profilImageSite);
                        }
                    }
                }
            }
            // ************* fin suppression images *************


            // ************* suppression photos *************
            // ** CAS OU L'ON SUPPRIME UN "PROFIL PHOTO" **
            // on récupère les ProfilPhoto de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Profil $profilCrm */
            $profilCrm = $profilUnifie->getProfils()->filter(function (Profil $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $profilSites = $profilUnifie->getProfils()->filter(function (Profil $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newProfilPhotos = new ArrayCollection();
            foreach ($profilCrm->getPhotos() as $profilPhoto) {
                $newProfilPhotos->add($profilPhoto);
            }
            /** @var ProfilPhoto $originalProfilPhoto */
            foreach ($originalProfilPhotos as $key => $originalProfilPhoto) {

                if (false === $newProfilPhotos->contains($originalProfilPhoto)) {
                    $originalProfilPhoto->setProfil(null);
                    $em->remove($originalProfilPhoto->getPhoto());
                    $em->remove($originalProfilPhoto);
                    // on doit supprimer l'hébergementPhoto des autres sites
                    // on parcourt les profil des sites
                    /** @var Profil $profilSite */
                    foreach ($profilSites as $profilSite) {
                        $profilPhotoSite = $em->getRepository(ProfilPhoto::class)->findOneBy(
                            array(
                                'profil' => $profilSite,
                                'photo' => $originalProfilPhoto->getPhoto()
                            ));
                        if (!empty($profilPhotoSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($profilPhotoSite->getProfil()->getSite()->getLibelle());
                            $profilSite = $emSite->getRepository(Profil::class)->findOneBy(
                                array(
                                    'profilUnifie' => $profilPhotoSite->getProfil()->getProfilUnifie()
                                ));
                            $profilPhotoSiteSites = new ArrayCollection($emSite->getRepository(ProfilPhoto::class)->findBy(
                                array(
                                    'profil' => $profilSite
                                ))
                            );
                            $profilPhotoSiteSite = $profilPhotoSiteSites->filter(function (ProfilPhoto $element)
                            use ($profilPhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $profilPhotoSite->getPhoto()->getProviderReference();
                                return $element->getPhoto()->getMetadataValue('crm_ref_id') == $profilPhotoSite->getPhoto()->getId();
                            })->first();
                            if (!empty($profilPhotoSiteSite)) {
                                $emSite->remove($profilPhotoSiteSite->getPhoto());
                                $profilPhotoSiteSite->setProfil(null);
                                $emSite->remove($profilPhotoSiteSite);
                                $emSite->flush();
                            }
                            $profilPhotoSite->setProfil(null);
                            $em->remove($profilPhotoSite->getPhoto());
                            $em->remove($profilPhotoSite);
                        }
                    }
                }
            }
            // ************* fin suppression photos *************

            // Supprimer la relation entre la station et stationUnifie


            // ***** Gestion des Medias *****
//            dump($profilUnifie);die;
            // CAS D'UN NOUVEAU 'PROFIL IMAGE' OU DE MODIFICATION D'UN "PROFIL IMAGE"
            /** @var ProfilImage $profilImage */
            // tableau pour la suppression des anciens images
            $imageToRemoveCollection = new ArrayCollection();
            $keyCrm = $profilUnifie->getProfils()->indexOf($profilCrm);
            // on parcourt les profilImages de l'profil crm
            foreach ($profilCrm->getImages() as $key => $profilImage) {
                // on active le nouveau profilImage (CRM) => il doit être toujours actif
                $profilImage->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementImage pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Profil $profilSite */
                        $profilSite = $profilUnifie->getProfils()->filter(function (Profil $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($profilSite)) {
                            // on réinitialise la variable
                            unset($profilImageSite);
                            // s'il ne s'agit pas d'un nouveau profilImage
                            if (!empty($profilImage->getId())) {
                                // on récupère l'profilImage pour le modifier
                                $profilImageSite = $em->getRepository(ProfilImage::class)->findOneBy(array('profil' => $profilSite, 'image' => $originalImages->get($key)));
                            }
                            // si l'profilImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($profilImage->getId()) || empty($profilImageSite)) {
                                // on récupère la classe correspondant au image (photo ou video)
                                $typeImage = (new ReflectionClass($profilImage))->getName();
                                // on créé un nouveau ProfilImage on fonction du type
                                /** @var ProfilImage $profilImageSite */
                                $profilImageSite = new $typeImage();
                                $profilImageSite->setProfil($profilSite);
                            }
                            // si l'hébergemenent image existe déjà pour le site
                            if (!empty($profilImageSite)) {
                                if ($profilImageSite->getImage() != $profilImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($profilImageSite->getImage()) && !$imageToRemoveCollection->contains($profilImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($profilImageSite->getImage());
//                                    }
                                    // on met le nouveau image
                                    $profilImageSite->setImage($profilImage->getImage());
                                }
                                $profilSite->addImage($profilImageSite);

                                /** @var ProfilImageTraduction $traduction */
                                foreach ($profilImage->getTraductions() as $traduction) {
                                    /** @var ProfilImageTraduction $traductionSite */
                                    $traductionSites = $profilImageSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (ProfilImageTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new ProfilImageTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $profilImageSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                if (!empty($request->get('profil_unifie')['profils'][$keyCrm]['images'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('profil_unifie')['profils'][$keyCrm]['images'][$key]['sites'])
                                ) {
                                    $profilImageSite->setActif(true);
                                } else {
                                    $profilImageSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'profilImage CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($profilImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $imageToRemoveCollection->add($originalImages->get($key));
                    }
                }
            }


            // CAS D'UN NOUVEAU 'PROFIL PHOTO' OU DE MODIFICATION D'UN "PROFIL PHOTO"
            /** @var ProfilPhoto $profilPhoto */
            // tableau pour la suppression des anciens photos
            $photoToRemoveCollection = new ArrayCollection();
            $keyCrm = $profilUnifie->getProfils()->indexOf($profilCrm);
            // on parcourt les profilPhotos de l'profil crm
            foreach ($profilCrm->getPhotos() as $key => $profilPhoto) {
                // on active le nouveau profilPhoto (CRM) => il doit être toujours actif
                $profilPhoto->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementPhoto pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Profil $profilSite */
                        $profilSite = $profilUnifie->getProfils()->filter(function (Profil $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($profilSite)) {
                            // on réinitialise la variable
                            unset($profilPhotoSite);
                            // s'il ne s'agit pas d'un nouveau profilPhoto
                            if (!empty($profilPhoto->getId())) {
                                // on récupère l'profilPhoto pour le modifier
                                $profilPhotoSite = $em->getRepository(ProfilPhoto::class)->findOneBy(array('profil' => $profilSite, 'photo' => $originalPhotos->get($key)));
                            }
                            // si l'profilPhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($profilPhoto->getId()) || empty($profilPhotoSite)) {
                                // on récupère la classe correspondant au photo (photo ou video)
                                $typePhoto = (new ReflectionClass($profilPhoto))->getName();
                                // on créé un nouveau ProfilPhoto on fonction du type
                                /** @var ProfilPhoto $profilPhotoSite */
                                $profilPhotoSite = new $typePhoto();
                                $profilPhotoSite->setProfil($profilSite);
                            }
                            // si l'hébergemenent photo existe déjà pour le site
                            if (!empty($profilPhotoSite)) {
                                if ($profilPhotoSite->getPhoto() != $profilPhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($profilPhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($profilPhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($profilPhotoSite->getPhoto());
//                                    }
                                    // on met le nouveau photo
                                    $profilPhotoSite->setPhoto($profilPhoto->getPhoto());
                                }
                                $profilSite->addPhoto($profilPhotoSite);

                                /** @var ProfilPhotoTraduction $traduction */
                                foreach ($profilPhoto->getTraductions() as $traduction) {
                                    /** @var ProfilPhotoTraduction $traductionSite */
                                    $traductionSites = $profilPhotoSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (ProfilPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new ProfilPhotoTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $profilPhotoSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                if (!empty($request->get('profil_unifie')['profils'][$keyCrm]['photos'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('profil_unifie')['profils'][$keyCrm]['photos'][$key]['sites'])
                                ) {
                                    $profilPhotoSite->setActif(true);
                                } else {
                                    $profilPhotoSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'profilPhoto CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($profilPhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $photoToRemoveCollection->add($originalPhotos->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****
            
            $em->persist($profilUnifie);
            $em->flush();

            $this->copieVersSites($profilUnifie, $originalProfilImages, $originalProfilPhotos);
            $this->addFlash('success', 'le profil a bien été modifié');

            return $this->redirectToRoute('geographie_profil_edit', array('id' => $profilUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/profilunifie/edit.html.twig', array(
            'entity' => $profilUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProfilUnifie entity.
     *
     */
    public function deleteAction(Request $request, ProfilUnifie $profilUnifie)
    {
//        dump($profilUnifie);die;
        $form = $this->createDeleteForm($profilUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $profilUnifieSite = $emSite->find(ProfilUnifie::class, $profilUnifie->getId());
                if (!empty($profilUnifieSite)) {
                    $emSite->remove($profilUnifieSite);


                    $profilSite = $profilUnifieSite->getProfils()->first();

                    // si il y a des images pour l'entité, les supprimer
                    if (!empty($profilSite->getImages())) {
                        /** @var ProfilImage $profilImageSite */
                        foreach ($profilSite->getImages() as $profilImageSite) {
                            $imageSite = $profilImageSite->getImage();
                            $profilImageSite->setImage(null);
                            if (!empty($imageSite)) {
                                $emSite->remove($imageSite);
                            }
                        }
                    }
                    // si il y a des photos pour l'entité, les supprimer
                    if (!empty($profilSite->getPhotos())) {
                        /** @var ProfilPhoto $profilPhotoSite */
                        foreach ($profilSite->getPhotos() as $profilPhotoSite) {
                            $photoSite = $profilPhotoSite->getPhoto();
                            $profilPhotoSite->setPhoto(null);
                            if (!empty($photoSite)) {
                                $emSite->remove($photoSite);
                            }
                        }
                    }
                    
                    $emSite->flush();
                }
            }


            if (!empty($profilUnifie)) {
                if (!empty($profilUnifie->getProfils())) {
                    /** @var Profil $profil */
                    foreach ($profilUnifie->getProfils() as $profil) {

                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($profil->getImages())) {
                            /** @var ProfilImage $profilImage */
                            foreach ($profil->getImages() as $profilImage) {
                                $image = $profilImage->getImage();
                                $profilImage->setImage(null);
                                $em->remove($image);
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($profil->getPhotos())) {
                            /** @var ProfilPhoto $profilPhoto */
                            foreach ($profil->getPhotos() as $profilPhoto) {
                                $photo = $profilPhoto->getPhoto();
                                $profilPhoto->setPhoto(null);
                                $em->remove($photo);
                            }
                        }
                    }
                    $em->flush();
                }
//                    $emSite->remove($profilUnifieSite);
//                    $emSite->flush();
            }

//            $em = $this->getDoctrine()->getManager();
            $em->remove($profilUnifie);
            $em->flush();
        }
        $this->addFlash('success', 'le profil a bien été supprimé');
        return $this->redirectToRoute('geographie_profil_index');
    }
}
