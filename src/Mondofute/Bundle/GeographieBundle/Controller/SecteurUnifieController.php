<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurImage;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurImageTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurPhoto;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurPhotoTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\SecteurUnifie;
use Mondofute\Bundle\GeographieBundle\Form\SecteurUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * SecteurUnifie controller.
 *
 */
class SecteurUnifieController extends Controller
{
    /**
     * Lists all SecteurUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $secteurUnifies = $em->getRepository('MondofuteGeographieBundle:SecteurUnifie')->findAll();

        return $this->render('@MondofuteGeographie/secteurunifie/index.html.twig', array(
            'secteurUnifies' => $secteurUnifies,
        ));
    }

    /**
     * Creates a new SecteurUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $secteurUnifie = new SecteurUnifie();

        $this->ajouterSecteursDansForm($secteurUnifie);
        $this->secteursSortByAffichage($secteurUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\SecteurUnifieType', $secteurUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->supprimerSecteurs($secteurUnifie, $sitesAEnregistrer);
            /** @var Secteur $secteur */

            // ***** Gestion des Medias *****
            foreach ($request->get('secteur_unifie')['secteurs'] as $key => $secteur) {
                if (!empty($secteurUnifie->getSecteurs()->get($key)) && $secteurUnifie->getSecteurs()->get($key)->getSite()->getCrm() == 1) {
                    $secteurCrm = $secteurUnifie->getSecteurs()->get($key);
                    if (!empty($secteur['images'])) {
                        foreach ($secteur['images'] as $keyImage => $image) {
                            /** @var SecteurImage $imageCrm */
                            $imageCrm = $secteurCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setSecteur($secteurCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Secteur $secteurSite */
                                    $secteurSite = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($secteurSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var SecteurImage $secteurImage */
                                        $secteurImage = new $typeImage();
                                        $secteurImage->setSecteur($secteurSite);
                                        $secteurImage->setImage($imageCrm->getImage());
                                        $secteurSite->addImage($secteurImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new SecteurImageTraduction();
                                            /** @var SecteurImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $secteurImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $secteurImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('secteur_unifie')['secteurs'] as $key => $secteur) {
                if (!empty($secteurUnifie->getSecteurs()->get($key)) && $secteurUnifie->getSecteurs()->get($key)->getSite()->getCrm() == 1) {
                    $secteurCrm = $secteurUnifie->getSecteurs()->get($key);
                    if (!empty($secteur['photos'])) {
                        foreach ($secteur['photos'] as $keyPhoto => $photo) {
                            /** @var SecteurPhoto $photoCrm */
                            $photoCrm = $secteurCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setSecteur($secteurCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Secteur $secteurSite */
                                    $secteurSite = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($secteurSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var SecteurPhoto $secteurPhoto */
                                        $secteurPhoto = new $typePhoto();
                                        $secteurPhoto->setSecteur($secteurSite);
                                        $secteurPhoto->setPhoto($photoCrm->getPhoto());
                                        $secteurSite->addPhoto($secteurPhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new SecteurPhotoTraduction();
                                            /** @var SecteurPhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $secteurPhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $secteurPhoto->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($secteurUnifie);

            $em->flush();

            $this->copieVersSites($secteurUnifie);
            $this->addFlash('success', 'le secteur a bien été créé');
            return $this->redirectToRoute('geographie_secteur_edit', array('id' => $secteurUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/secteurunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $secteurUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les secteurs qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param SecteurUnifie $entity
     */
    private function ajouterSecteursDansForm(SecteurUnifie $entity)
    {
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getSecteurs() as $secteur) {
                if ($secteur->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter au secteur
                        if ($secteur->getTraductions()->filter(function (SecteurTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new SecteurTraduction();
                            $traduction->setLangue($langue);
                            $secteur->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $secteur = new Secteur();
                $secteur->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new SecteurTraduction();
                    $traduction->setLangue($langue);
                    $secteur->addTraduction($traduction);
                }
                $entity->addSecteur($secteur);
            }
        }
    }

    /**
     * Classe les secteurs par classementAffichage
     * @param SecteurUnifie $entity
     */
    private function secteursSortByAffichage(SecteurUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les secteurs en fonction de leurs ordre d'affichage
        $secteurs = $entity->getSecteurs(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $secteurs->getIterator();
        unset($secteurs);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Secteur $a, Secteur $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $secteurs = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($secteurs);

        // remplacé les secteurs par ce nouveau tableau (une fonction 'set' a été créé dans Secteur unifié)
        $entity->setSecteurs($secteurs);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param ArrayCollection $secteurs
     */
    private function traductionsSortByLangue(ArrayCollection $secteurs)
    {
        /** @var Secteur $secteur */
        /** @var ArrayIterator $iterator */
        foreach ($secteurs as $secteur) {
            $traductions = $secteur->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (SecteurTraduction $a, SecteurTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $secteur->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les secteurs qui ne doivent pas être enregistrer
     * @param SecteurUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerSecteurs(SecteurUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getSecteurs() as $secteur) {
            if (!in_array($secteur->getSite()->getId(), $sitesAEnregistrer)) {
                $secteur->setSecteurUnifie(null);
                $entity->removeSecteur($secteur);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité secteur
     * @param SecteurUnifie $entity
     */
    public function copieVersSites(SecteurUnifie $entity, $originalSecteurImages = null, $originalSecteurPhotos = null)
    {
        /** @var SecteurTraduction $secteurTraduc */
//        Boucle sur les secteurs afin de savoir sur quel site nous devons l'enregistrer
        /** @var Secteur $secteur */
        foreach ($entity->getSecteurs() as $secteur) {
            if ($secteur->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($secteur->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $secteur->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->find(SecteurUnifie::class, $entity->getId())))) {
                    $entitySite = new SecteurUnifie();
                }

//            Récupération du secteur sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($secteurSite = $emSite->getRepository(Secteur::class)->findOneBy(array('secteurUnifie' => $entitySite))))) {
                    $secteurSite = new Secteur();
                }
//            copie des données secteur
                $secteurSite
                    ->setSite($site)
                    ->setSecteurUnifie($entitySite);

//            Gestion des traductions
                foreach ($secteur->getTraductions() as $secteurTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $secteurTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($secteurTraducSite = $emSite->getRepository(SecteurTraduction::class)->findOneBy(array(
                        'secteur' => $secteurSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $secteurTraducSite = new SecteurTraduction();
                    }

//                copie des données traductions
                    $secteurTraducSite->setLangue($langue)
                        ->setLibelle($secteurTraduc->getLibelle())
                        ->setDescription($secteurTraduc->getDescription())
                        ->setSecteur($secteurSite);

//                ajout a la collection de traduction de la station distante
                    $secteurSite->addTraduction($secteurTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $secteurImages = $secteur->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'secteur de référence
                if (!empty($secteurImages) && !$secteurImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($secteurSite->getImages()) && !$secteurSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $secteurImageSites = new ArrayCollection();
                        foreach ($secteurSite->getImages() as $secteurimageSite) {
                            $secteurImageSites->add($secteurimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var SecteurImage $secteurImage */
                        foreach ($secteurImages as $secteurImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'secteurImage original correspondant sur le crm
                            /** @var ArrayCollection $originalSecteurImages */
                            $originalSecteurImage = $originalSecteurImages->filter(function (SecteurImage $element) use ($secteurImage) {
                                return $element->getImage() == $secteurImage->getImage();
                            })->first();
                            unset($secteurImageSite);
                            if ($originalSecteurImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalSecteurImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalSecteurImage);

                                $secteurImageSite = $secteurImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'secteurImage existe sur la bdd distante, on va le modifier
                            /** @var SecteurImage $secteurImageSite */
                            if (!empty($secteurImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'secteurImage du crm)
                                if ($secteurImageSite->getImage()->getMetadataValue('crm_ref_id') != $secteurImage->getImage()->getId()) {
                                    $cloneImage = clone $secteurImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $secteurImage->getImage()->getId());
                                    $cloneImage->setContext('secteur_image_' . $secteur->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($secteurImageSite->getImage());

                                    $secteurImageSite->setImage($cloneImage);
                                }

                                $secteurImageSite->setActif($secteurImage->getActif());

                                // on parcourt les traductions
                                /** @var SecteurImageTraduction $traduction */
                                foreach ($secteurImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var SecteurImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $secteurImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (SecteurImageTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new SecteurImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $secteurImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createSecteurImage($secteurImage, $secteurSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'secteur de référence
                        /** @var SecteurImage $secteurImage */
                        foreach ($secteurImages as $secteurImage) {
                            $this->createSecteurImage($secteurImage, $secteurSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($secteurImageSites)) {
                        /** @var SecteurImage $secteurImageSite */
                        foreach ($secteurImageSites as $secteurImageSite) {
                            $secteurImageSite->setSecteur(null);
                            $emSite->remove($secteurImageSite->getImage());
                            $emSite->remove($secteurImageSite);
                        }
                    }
                }


                $secteurPhotos = $secteur->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'secteur de référence
                if (!empty($secteurPhotos) && !$secteurPhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($secteurSite->getPhotos()) && !$secteurSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $secteurPhotoSites = new ArrayCollection();
                        foreach ($secteurSite->getPhotos() as $secteurphotoSite) {
                            $secteurPhotoSites->add($secteurphotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var SecteurPhoto $secteurPhoto */
                        foreach ($secteurPhotos as $secteurPhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'secteurPhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalSecteurPhotos */
                            $originalSecteurPhoto = $originalSecteurPhotos->filter(function (SecteurPhoto $element) use ($secteurPhoto) {
                                return $element->getPhoto() == $secteurPhoto->getPhoto();
                            })->first();
                            unset($secteurPhotoSite);
                            if ($originalSecteurPhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalSecteurPhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalSecteurPhoto);

                                $secteurPhotoSite = $secteurPhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'secteurPhoto existe sur la bdd distante, on va le modifier
                            /** @var SecteurPhoto $secteurPhotoSite */
                            if (!empty($secteurPhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'secteurPhoto du crm)
                                if ($secteurPhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $secteurPhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $secteurPhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $secteurPhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('secteur_photo_' . $secteur->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($secteurPhotoSite->getPhoto());

                                    $secteurPhotoSite->setPhoto($clonePhoto);
                                }

                                $secteurPhotoSite->setActif($secteurPhoto->getActif());

                                // on parcourt les traductions
                                /** @var SecteurPhotoTraduction $traduction */
                                foreach ($secteurPhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var SecteurPhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $secteurPhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (SecteurPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new SecteurPhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $secteurPhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createSecteurPhoto($secteurPhoto, $secteurSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'secteur de référence
                        /** @var SecteurPhoto $secteurPhoto */
                        foreach ($secteurPhotos as $secteurPhoto) {
                            $this->createSecteurPhoto($secteurPhoto, $secteurSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($secteurPhotoSites)) {
                        /** @var SecteurPhoto $secteurPhotoSite */
                        foreach ($secteurPhotoSites as $secteurPhotoSite) {
                            $secteurPhotoSite->setSecteur(null);
                            $emSite->remove($secteurPhotoSite->getPhoto());
                            $emSite->remove($secteurPhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********

                $entitySite->addSecteur($secteurSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterSecteurUnifieSiteDistant($entity->getId(), $entity->getSecteurs());
    }

    /**
     * Création d'un nouveau secteurImage
     * @param SecteurImage $secteurImage
     * @param Secteur $secteurSite
     * @param EntityManager $emSite
     */
    private function createSecteurImage(SecteurImage $secteurImage, Secteur $secteurSite, EntityManager $emSite)
    {
        /** @var SecteurImage $secteurImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($secteurImage))->getName();
        // on cré un nouveau SecteurImage on fonction du type
        $secteurImageSite = new $typeImage();
        $secteurImageSite->setSecteur($secteurSite);
        $secteurImageSite->setActif($secteurImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $secteurImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($secteurImage->getImage()->getProviderReference());
        $cloneImage->setName($secteurImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('secteur_image_' . $secteurSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $secteurImage->getImage()->getId());

        $secteurImageSite->setImage($cloneImage);

        $secteurSite->addImage($secteurImageSite);
        // on ajoute les traductions correspondante
        foreach ($secteurImage->getTraductions() as $traduction) {
            $traductionSite = new SecteurImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $secteurImageSite->addTraduction($traductionSite);
        }
    }


    /**
     * Création d'un nouveau secteurPhoto
     * @param SecteurPhoto $secteurPhoto
     * @param Secteur $secteurSite
     * @param EntityManager $emSite
     */
    private function createSecteurPhoto(SecteurPhoto $secteurPhoto, Secteur $secteurSite, EntityManager $emSite)
    {
        /** @var SecteurPhoto $secteurPhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($secteurPhoto))->getName();
        // on cré un nouveau SecteurPhoto on fonction du type
        $secteurPhotoSite = new $typePhoto();
        $secteurPhotoSite->setSecteur($secteurSite);
        $secteurPhotoSite->setActif($secteurPhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $secteurPhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($secteurPhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($secteurPhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('secteur_photo_' . $secteurSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $secteurPhoto->getPhoto()->getId());

        $secteurPhotoSite->setPhoto($clonePhoto);

        $secteurSite->addPhoto($secteurPhotoSite);
        // on ajoute les traductions correspondante
        foreach ($secteurPhoto->getTraductions() as $traduction) {
            $traductionSite = new SecteurPhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $secteurPhotoSite->addTraduction($traductionSite);
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de secteur a enregistrer
     * @param $idUnifie
     * @param Collection $secteurs
     */
    public function ajouterSecteurUnifieSiteDistant($idUnifie, Collection $secteurs)
    {
        /** @var Site $site */
        /** @var ArrayCollection $secteurs */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($secteurs->matching($criteres)) == 0 && (empty($emSite->getRepository(SecteurUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new SecteurUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a SecteurUnifie entity.
     *
     */
    public function showAction(SecteurUnifie $secteurUnifie)
    {
        $deleteForm = $this->createDeleteForm($secteurUnifie);

        return $this->render('@MondofuteGeographie/secteurunifie/show.html.twig', array(
            'secteurUnifie' => $secteurUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a SecteurUnifie entity.
     *
     * @param SecteurUnifie $secteurUnifie The SecteurUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SecteurUnifie $secteurUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_secteur_delete', array('id' => $secteurUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing SecteurUnifie entity.
     *
     */
    public function editAction(Request $request, SecteurUnifie $secteurUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($secteurUnifie->getSecteurs() as $secteur) {
                array_push($sitesAEnregistrer, $secteur->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

//        $secteurCrm = $this->dissocierSecteurCrm($secteurUnifie);
        $originalSecteurs = new ArrayCollection();
        $originalSecteurImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalSecteurPhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($secteurUnifie->getSecteurs() as $secteur) {
            $originalSecteurs->add($secteur);
            // si l'secteur est celui du CRM
            if ($secteur->getSite()->getCrm() == 1) {
                // on parcourt les secteurImage pour les comparer ensuite
                /** @var SecteurImage $secteurImage */
                foreach ($secteur->getImages() as $secteurImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalSecteurImages->add($secteurImage);
                    $originalImages->add($secteurImage->getImage());
                }
                // on parcourt les secteurPhoto pour les comparer ensuite
                /** @var SecteurPhoto $secteurPhoto */
                foreach ($secteur->getPhotos() as $secteurPhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalSecteurPhotos->add($secteurPhoto);
                    $originalPhotos->add($secteurPhoto->getPhoto());
                }
            }
        }

        $this->ajouterSecteursDansForm($secteurUnifie);
//        $this->dispacherDonneesCommune($secteurUnifie);
        $this->secteursSortByAffichage($secteurUnifie);
        $deleteForm = $this->createDeleteForm($secteurUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\SecteurUnifieType', $secteurUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);


//        dump($editForm);die;

        if ($editForm->isSubmitted() && $editForm->isValid()) {


            // ************* suppression images *************
            // ** CAS OU L'ON SUPPRIME UN "SECTEUR IMAGE" **
            // on récupère les SecteurImage de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Secteur $secteurCrm */
            $secteurCrm = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $secteurSites = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newSecteurImages = new ArrayCollection();
            foreach ($secteurCrm->getImages() as $secteurImage) {
                $newSecteurImages->add($secteurImage);
            }
            /** @var SecteurImage $originalSecteurImage */
            foreach ($originalSecteurImages as $key => $originalSecteurImage) {

                if (false === $newSecteurImages->contains($originalSecteurImage)) {
                    $originalSecteurImage->setSecteur(null);
                    $em->remove($originalSecteurImage->getImage());
                    $em->remove($originalSecteurImage);
                    // on doit supprimer l'hébergementImage des autres sites
                    // on parcourt les secteur des sites
                    /** @var Secteur $secteurSite */
                    foreach ($secteurSites as $secteurSite) {
                        $secteurImageSite = $em->getRepository(SecteurImage::class)->findOneBy(
                            array(
                                'secteur' => $secteurSite,
                                'image' => $originalSecteurImage->getImage()
                            ));
                        if (!empty($secteurImageSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($secteurImageSite->getSecteur()->getSite()->getLibelle());
                            $secteurSite = $emSite->getRepository(Secteur::class)->findOneBy(
                                array(
                                    'secteurUnifie' => $secteurImageSite->getSecteur()->getSecteurUnifie()
                                ));
                            $secteurImageSiteSites = new ArrayCollection($emSite->getRepository(SecteurImage::class)->findBy(
                                array(
                                    'secteur' => $secteurSite
                                ))
                            );
                            $secteurImageSiteSite = $secteurImageSiteSites->filter(function (SecteurImage $element)
                            use ($secteurImageSite) {
//                            return $element->getImage()->getProviderReference() == $secteurImageSite->getImage()->getProviderReference();
                                return $element->getImage()->getMetadataValue('crm_ref_id') == $secteurImageSite->getImage()->getId();
                            })->first();
                            if (!empty($secteurImageSiteSite)) {
                                $emSite->remove($secteurImageSiteSite->getImage());
                                $secteurImageSiteSite->setSecteur(null);
                                $emSite->remove($secteurImageSiteSite);
                                $emSite->flush();
                            }
                            $secteurImageSite->setSecteur(null);
                            $em->remove($secteurImageSite->getImage());
                            $em->remove($secteurImageSite);
                        }
                    }
                }
            }
            // ************* fin suppression images *************


            // ************* suppression photos *************
            // ** CAS OU L'ON SUPPRIME UN "SECTEUR PHOTO" **
            // on récupère les SecteurPhoto de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Secteur $secteurCrm */
            $secteurCrm = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $secteurSites = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newSecteurPhotos = new ArrayCollection();
            foreach ($secteurCrm->getPhotos() as $secteurPhoto) {
                $newSecteurPhotos->add($secteurPhoto);
            }
            /** @var SecteurPhoto $originalSecteurPhoto */
            foreach ($originalSecteurPhotos as $key => $originalSecteurPhoto) {

                if (false === $newSecteurPhotos->contains($originalSecteurPhoto)) {
                    $originalSecteurPhoto->setSecteur(null);
                    $em->remove($originalSecteurPhoto->getPhoto());
                    $em->remove($originalSecteurPhoto);
                    // on doit supprimer l'hébergementPhoto des autres sites
                    // on parcourt les secteur des sites
                    /** @var Secteur $secteurSite */
                    foreach ($secteurSites as $secteurSite) {
                        $secteurPhotoSite = $em->getRepository(SecteurPhoto::class)->findOneBy(
                            array(
                                'secteur' => $secteurSite,
                                'photo' => $originalSecteurPhoto->getPhoto()
                            ));
                        if (!empty($secteurPhotoSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($secteurPhotoSite->getSecteur()->getSite()->getLibelle());
                            $secteurSite = $emSite->getRepository(Secteur::class)->findOneBy(
                                array(
                                    'secteurUnifie' => $secteurPhotoSite->getSecteur()->getSecteurUnifie()
                                ));
                            $secteurPhotoSiteSites = new ArrayCollection($emSite->getRepository(SecteurPhoto::class)->findBy(
                                array(
                                    'secteur' => $secteurSite
                                ))
                            );
                            $secteurPhotoSiteSite = $secteurPhotoSiteSites->filter(function (SecteurPhoto $element)
                            use ($secteurPhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $secteurPhotoSite->getPhoto()->getProviderReference();
                                return $element->getPhoto()->getMetadataValue('crm_ref_id') == $secteurPhotoSite->getPhoto()->getId();
                            })->first();
                            if (!empty($secteurPhotoSiteSite)) {
                                $emSite->remove($secteurPhotoSiteSite->getPhoto());
                                $secteurPhotoSiteSite->setSecteur(null);
                                $emSite->remove($secteurPhotoSiteSite);
                                $emSite->flush();
                            }
                            $secteurPhotoSite->setSecteur(null);
                            $em->remove($secteurPhotoSite->getPhoto());
                            $em->remove($secteurPhotoSite);
                        }
                    }
                }
            }
            // ************* fin suppression photos *************
            
            $this->supprimerSecteurs($secteurUnifie, $sitesAEnregistrer);
//            $this->mettreAJourSecteurCrm($secteurUnifie, $secteurCrm);
//            $em->persist($secteurCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalSecteurs as $secteur) {
                if (!$secteurUnifie->getSecteurs()->contains($secteur)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($secteur->getSite()->getLibelle());
                    $entitySite = $emSite->find(SecteurUnifie::class, $secteurUnifie->getId());
                    $secteurSite = $entitySite->getSecteurs()->first();


                    /** @var SecteurImage $secteurImageSite */
                    if (!empty($secteurSite->getImages())) {
                        foreach ($secteurSite->getImages() as $secteurImageSite) {
                            $secteurSite->removeImage($secteurImageSite);
//                                        $secteurImageSite->setSecteur(null);
//                                        $secteurImageSite->setImage(null);
                            $emSite->remove($secteurImageSite);
                            $emSite->remove($secteurImageSite->getImage());
                        }
                        $emSite->flush();
                    }
                    /** @var SecteurPhoto $secteurPhotoSite */
                    if (!empty($secteurSite->getPhotos())) {
                        foreach ($secteurSite->getPhotos() as $secteurPhotoSite) {
                            $secteurSite->removePhoto($secteurPhotoSite);
//                                        $secteurPhotoSite->setSecteur(null);
//                                        $secteurPhotoSite->setPhoto(null);
                            $emSite->remove($secteurPhotoSite);
                            $emSite->remove($secteurPhotoSite->getPhoto());
                        }
                        $emSite->flush();
                    }
                    
                    $emSite->remove($secteurSite);
                    $emSite->flush();
                    $secteur->setSecteurUnifie(null);


                    // *** suppression des secteurImages de l'secteur à supprimer ***
                    /** @var SecteurImage $secteurImage */
                    $secteurImageSites = $em->getRepository(SecteurImage::class)->findBy(array('secteur' => $secteur));
                    if (!empty($secteurImageSites)) {
                        foreach ($secteurImageSites as $secteurImage) {
                            $secteurImage->setImage(null);
                            $secteurImage->setSecteur(null);
                            $em->remove($secteurImage);
                        }
                        $em->flush();
                    }
                    // *** fin suppression des secteurImages de l'secteur à supprimer ***
                    // *** suppression des secteurPhotos de l'secteur à supprimer ***
                    /** @var SecteurPhoto $secteurPhoto */
                    $secteurPhotoSites = $em->getRepository(SecteurPhoto::class)->findBy(array('secteur' => $secteur));
                    if (!empty($secteurPhotoSites)) {
                        foreach ($secteurPhotoSites as $secteurPhoto) {
                            $secteurPhoto->setPhoto(null);
                            $secteurPhoto->setSecteur(null);
                            $em->remove($secteurPhoto);
                        }
                        $em->flush();
                    }
                    // *** fin suppression des secteurPhotos de l'secteur à supprimer ***
                    
                    $em->remove($secteur);
                }
            }


            // ***** Gestion des Medias *****
//            dump($secteurUnifie);die;
            // CAS D'UN NOUVEAU 'SECTEUR IMAGE' OU DE MODIFICATION D'UN "SECTEUR IMAGE"
            /** @var SecteurImage $secteurImage */
            // tableau pour la suppression des anciens images
            $imageToRemoveCollection = new ArrayCollection();
            $keyCrm = $secteurUnifie->getSecteurs()->indexOf($secteurCrm);
            // on parcourt les secteurImages de l'secteur crm
            foreach ($secteurCrm->getImages() as $key => $secteurImage) {
                // on active le nouveau secteurImage (CRM) => il doit être toujours actif
                $secteurImage->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementImage pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Secteur $secteurSite */
                        $secteurSite = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($secteurSite)) {
                            // on réinitialise la variable
                            unset($secteurImageSite);
                            // s'il ne s'agit pas d'un nouveau secteurImage
                            if (!empty($secteurImage->getId())) {
                                // on récupère l'secteurImage pour le modifier
                                $secteurImageSite = $em->getRepository(SecteurImage::class)->findOneBy(array('secteur' => $secteurSite, 'image' => $originalImages->get($key)));
                            }
                            // si l'secteurImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($secteurImage->getId()) || empty($secteurImageSite)) {
                                // on récupère la classe correspondant au image (photo ou video)
                                $typeImage = (new ReflectionClass($secteurImage))->getName();
                                // on créé un nouveau SecteurImage on fonction du type
                                /** @var SecteurImage $secteurImageSite */
                                $secteurImageSite = new $typeImage();
                                $secteurImageSite->setSecteur($secteurSite);
                            }
                            // si l'hébergemenent image existe déjà pour le site
                            if (!empty($secteurImageSite)) {
                                if ($secteurImageSite->getImage() != $secteurImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($secteurImageSite->getImage()) && !$imageToRemoveCollection->contains($secteurImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($secteurImageSite->getImage());
//                                    }
                                    // on met le nouveau image
                                    $secteurImageSite->setImage($secteurImage->getImage());
                                }
                                $secteurSite->addImage($secteurImageSite);

                                /** @var SecteurImageTraduction $traduction */
                                foreach ($secteurImage->getTraductions() as $traduction) {
                                    /** @var SecteurImageTraduction $traductionSite */
                                    $traductionSites = $secteurImageSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (SecteurImageTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new SecteurImageTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $secteurImageSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                if (!empty($request->get('secteur_unifie')['secteurs'][$keyCrm]['images'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('secteur_unifie')['secteurs'][$keyCrm]['images'][$key]['sites'])
                                ) {
                                    $secteurImageSite->setActif(true);
                                } else {
                                    $secteurImageSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'secteurImage CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($secteurImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $imageToRemoveCollection->add($originalImages->get($key));
                    }
                }
            }


            // CAS D'UN NOUVEAU 'SECTEUR PHOTO' OU DE MODIFICATION D'UN "SECTEUR PHOTO"
            /** @var SecteurPhoto $secteurPhoto */
            // tableau pour la suppression des anciens photos
            $photoToRemoveCollection = new ArrayCollection();
            $keyCrm = $secteurUnifie->getSecteurs()->indexOf($secteurCrm);
            // on parcourt les secteurPhotos de l'secteur crm
            foreach ($secteurCrm->getPhotos() as $key => $secteurPhoto) {
                // on active le nouveau secteurPhoto (CRM) => il doit être toujours actif
                $secteurPhoto->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementPhoto pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Secteur $secteurSite */
                        $secteurSite = $secteurUnifie->getSecteurs()->filter(function (Secteur $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($secteurSite)) {
                            // on réinitialise la variable
                            unset($secteurPhotoSite);
                            // s'il ne s'agit pas d'un nouveau secteurPhoto
                            if (!empty($secteurPhoto->getId())) {
                                // on récupère l'secteurPhoto pour le modifier
                                $secteurPhotoSite = $em->getRepository(SecteurPhoto::class)->findOneBy(array('secteur' => $secteurSite, 'photo' => $originalPhotos->get($key)));
                            }
                            // si l'secteurPhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($secteurPhoto->getId()) || empty($secteurPhotoSite)) {
                                // on récupère la classe correspondant au photo (photo ou video)
                                $typePhoto = (new ReflectionClass($secteurPhoto))->getName();
                                // on créé un nouveau SecteurPhoto on fonction du type
                                /** @var SecteurPhoto $secteurPhotoSite */
                                $secteurPhotoSite = new $typePhoto();
                                $secteurPhotoSite->setSecteur($secteurSite);
                            }
                            // si l'hébergemenent photo existe déjà pour le site
                            if (!empty($secteurPhotoSite)) {
                                if ($secteurPhotoSite->getPhoto() != $secteurPhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($secteurPhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($secteurPhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($secteurPhotoSite->getPhoto());
//                                    }
                                    // on met le nouveau photo
                                    $secteurPhotoSite->setPhoto($secteurPhoto->getPhoto());
                                }
                                $secteurSite->addPhoto($secteurPhotoSite);

                                /** @var SecteurPhotoTraduction $traduction */
                                foreach ($secteurPhoto->getTraductions() as $traduction) {
                                    /** @var SecteurPhotoTraduction $traductionSite */
                                    $traductionSites = $secteurPhotoSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (SecteurPhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new SecteurPhotoTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $secteurPhotoSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                if (!empty($request->get('secteur_unifie')['secteurs'][$keyCrm]['photos'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('secteur_unifie')['secteurs'][$keyCrm]['photos'][$key]['sites'])
                                ) {
                                    $secteurPhotoSite->setActif(true);
                                } else {
                                    $secteurPhotoSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'secteurPhoto CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($secteurPhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $photoToRemoveCollection->add($originalPhotos->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($secteurUnifie);
            $em->flush();

            $this->copieVersSites($secteurUnifie, $originalSecteurImages, $originalSecteurPhotos);

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
            
            $this->addFlash('success', 'le secteur a bien été modifié');

            return $this->redirectToRoute('geographie_secteur_edit', array('id' => $secteurUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/secteurunifie/edit.html.twig', array(
            'entity' => $secteurUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SecteurUnifie entity.
     *
     */
    public function deleteAction(Request $request, SecteurUnifie $secteurUnifie)
    {
        $form = $this->createDeleteForm($secteurUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $secteurUnifieSite = $emSite->find(SecteurUnifie::class, $secteurUnifie->getId());
                if (!empty($secteurUnifieSite)) {
                    $emSite->remove($secteurUnifieSite);
                    $secteurSite = $secteurUnifieSite->getSecteurs()->first();

                    // si il y a des images pour l'entité, les supprimer
                    if (!empty($secteurSite->getImages())) {
                        /** @var SecteurImage $secteurImageSite */
                        foreach ($secteurSite->getImages() as $secteurImageSite) {
                            $imageSite = $secteurImageSite->getImage();
                            $secteurImageSite->setImage(null);
                            if (!empty($imageSite)) {
                                $emSite->remove($imageSite);
                            }
                        }
                    }
                    // si il y a des photos pour l'entité, les supprimer
                    if (!empty($secteurSite->getPhotos())) {
                        /** @var SecteurPhoto $secteurPhotoSite */
                        foreach ($secteurSite->getPhotos() as $secteurPhotoSite) {
                            $photoSite = $secteurPhotoSite->getPhoto();
                            $secteurPhotoSite->setPhoto(null);
                            if (!empty($photoSite)) {
                                $emSite->remove($photoSite);
                            }
                        }
                    }
                    
                    $emSite->flush();
                }
            }


            if (!empty($secteurUnifie)) {
                if (!empty($secteurUnifie->getSecteurs())) {
                    /** @var Secteur $secteur */
                    foreach ($secteurUnifie->getSecteurs() as $secteur) {

                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($secteur->getImages())) {
                            /** @var SecteurImage $secteurImage */
                            foreach ($secteur->getImages() as $secteurImage) {
                                $image = $secteurImage->getImage();
                                $secteurImage->setImage(null);
                                $em->remove($image);
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($secteur->getPhotos())) {
                            /** @var SecteurPhoto $secteurPhoto */
                            foreach ($secteur->getPhotos() as $secteurPhoto) {
                                $photo = $secteurPhoto->getPhoto();
                                $secteurPhoto->setPhoto(null);
                                $em->remove($photo);
                            }
                        }
                    }
                    $em->flush();
                }
//                    $emSite->remove($secteurUnifieSite);
//                    $emSite->flush();
            }
            
            $em->remove($secteurUnifie);
            $em->flush();
        }
        $this->addFlash('success', 'le secteur a bien été supprimé');
        return $this->redirectToRoute('geographie_secteur_index');
    }



}
