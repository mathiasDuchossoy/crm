<?php

namespace Mondofute\Bundle\DomaineBundle\Controller;

use Application\Sonata\MediaBundle\Entity\Media;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Controller\ModeleDescriptionForfaitSkiController;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineImage;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineImageTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto;
use Mondofute\Bundle\DomaineBundle\Entity\DomainePhotoTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineUnifie;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineVideo;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineVideoTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\HandiskiTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\KmPistesAlpin;
use Mondofute\Bundle\DomaineBundle\Entity\KmPistesNordique;
use Mondofute\Bundle\DomaineBundle\Entity\Piste;
use Mondofute\Bundle\DomaineBundle\Entity\RemonteeMecanique;
use Mondofute\Bundle\DomaineBundle\Entity\Snowpark;
use Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\TypePiste;
use Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Mondofute\Bundle\DomaineBundle\Entity\Handiski;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DomaineUnifie controller.
 *
 */
class DomaineUnifieController extends Controller
{
    /**
     * Lists all DomaineUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteDomaineBundle:DomaineUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'domaine_domaine_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteDomaineBundle:DomaineUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteDomaine/domaineunifie/index.html.twig', array(
            'domaineUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new DomaineUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $domaineUnifie = new DomaineUnifie();

        $this->ajouterDomainesDansForm($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);

        // *** addModeleDescriptionForfaitSkis ***
        $modeleDescriptionForfaitSkiController = new ModeleDescriptionForfaitSkiController();
        $modeleDescriptionForfaitSkiController->setContainer($this->container);
        $modeleDescriptionForfaitSkiController->addModeleDescriptionForfaitSkis($domaineUnifie);
        // *** fin addModeleDescriptionForfaitSkis ***

        $form = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType', $domaineUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Domaine $entity */
            foreach ($domaineUnifie->getDomaines() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            // Récupérer le controleur et lui donner le container de celui dans lequel on est
            $domaineCarteIdentiteController = new DomaineCarteIdentiteUnifieController();
            $domaineCarteIdentiteController->setContainer($this->container);

            // ***** Carte d'identité *****
            /** @var Domaine $domaine */
            $this->carteIdentiteNew($request, $domaineUnifie);
            // ***** Fin Carte d'identité *****

            // on vérifie si les domaines on un parent,
            // s'il le domaine n'a pas de parent on lui met dans tous les cas la liaison aux medias parent à false
            foreach ($domaineUnifie->getDomaines() as $domaine) {
                if (empty($domaine->getDomaineParent())) {
                    $domaine
                        ->setPhotosParent(false)
                        ->setImagesParent(false)
                        ->setVideosParent(false);
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('domaine_unifie')['domaines'] as $key => $domaine) {
                if (!empty($domaineUnifie->getDomaines()->get($key)) && $domaineUnifie->getDomaines()->get($key)->getSite()->getCrm() == 1) {
                    $domaineCrm = $domaineUnifie->getDomaines()->get($key);
                    if (!empty($domaine['images'])) {
                        foreach ($domaine['images'] as $keyImage => $image) {
                            /** @var DomaineImage $imageCrm */
                            $imageCrm = $domaineCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setDomaine($domaineCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Domaine $domaineSite */
                                    $domaineSite = $domaineUnifie->getDomaines()->filter(function (Domaine $element) use
                                    (
                                        $site
                                    ) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($domaineSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var DomaineImage $domaineImage */
                                        $domaineImage = new $typeImage();
                                        $domaineImage->setDomaine($domaineSite);
                                        $domaineImage->setImage($imageCrm->getImage());
                                        $domaineSite->addImage($domaineImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new DomaineImageTraduction();
                                            /** @var DomaineImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $domaineImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $domaineImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('domaine_unifie')['domaines'] as $key => $domaine) {
                if (!empty($domaineUnifie->getDomaines()->get($key)) && $domaineUnifie->getDomaines()->get($key)->getSite()->getCrm() == 1) {
                    $domaineCrm = $domaineUnifie->getDomaines()->get($key);
                    if (!empty($domaine['photos'])) {
                        foreach ($domaine['photos'] as $keyPhoto => $photo) {
                            /** @var DomainePhoto $photoCrm */
                            $photoCrm = $domaineCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setDomaine($domaineCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Domaine $domaineSite */
                                    $domaineSite = $domaineUnifie->getDomaines()->filter(function (Domaine $element) use
                                    (
                                        $site
                                    ) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($domaineSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var DomainePhoto $domainePhoto */
                                        $domainePhoto = new $typePhoto();
                                        $domainePhoto->setDomaine($domaineSite);
                                        $domainePhoto->setPhoto($photoCrm->getPhoto());
                                        $domaineSite->addPhoto($domainePhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new DomainePhotoTraduction();
                                            /** @var DomainePhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $domainePhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $domainePhoto->setActif(true);
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
            /** @var Domaine $domaineCrm */
            $domaineCrm = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $domaineSites = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                return $element->getSite()->getCrm() == 0;
            });
            /** @var DomaineVideo $domaineVideo */
            foreach ($domaineCrm->getVideos() as $key => $domaineVideo) {
                foreach ($domaineSites as $domaineSite) {
                    $domaineVideoSite = clone $domaineVideo;
                    $domaineSite->addVideo($domaineVideoSite);
                    $actif = false;
                    if (!empty($request->get('domaine_unifie')['domaines'][0]['videos'][$key]['sites'])) {
                        if (in_array($domaineSite->getSite()->getId(),
                            $request->get('domaine_unifie')['domaines'][0]['videos'][$key]['sites'])) {
                            $actif = true;
                        }
                    }
                    $domaineVideoSite->setActif($actif);
                }
            }
            // *** gestion des videos ***
            $modeleDescriptionForfaitSkiController->majDomaines($domaineUnifie);

            $em->persist($domaineUnifie);

            try {
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }

            foreach ($domaineUnifie->getDomaines() as $domaine) {
                $domaineCarteIdentiteController->copieVersSites($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
            }
            $this->copieVersSites($domaineUnifie);

            // add flash messages
            $this->addFlash(
                'success',
                'Le domaine a bien été créé.'
            );

            return $this->redirectToRoute('domaine_domaine_edit', array('id' => $domaineUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domaineunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $domaineUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les domaines qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DomaineUnifie $entity
     */
    private function ajouterDomainesDansForm(DomaineUnifie $entity)
    {
        /** @var Domaine $domaine */
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $typePistes = $em->getRepository(TypePiste::class)->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDomaines() as $domaine) {
                if ($domaine->getSite() == $site) {
                    $siteExiste = true;

                    // carte identite
                    $domaineCarteIdentite = $domaine->getDomaineCarteIdentite();
                    if (empty($domaine->getDomaineCarteIdentite())) {
                        $domaineCarteIdentite = new DomaineCarteIdentite();
                        $domaineCarteIdentite->setSite($site);
                        $domaine->setDomaineCarteIdentite($domaineCarteIdentite);
                    }

                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaineCarteIdentite->getTraductions()->filter(function (
                            DomaineCarteIdentiteTraduction $element
                        ) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DomaineCarteIdentiteTraduction();
                            $traduction->setLangue($langue);
                            $domaineCarteIdentite->addTraduction($traduction);
                        }
                    }


                    $snowpark = $domaineCarteIdentite->getSnowpark();
                    if (empty($snowpark)) {
                        $snowpark = new Snowpark();
                        $domaine->getDomaineCarteIdentite()->setSnowpark($snowpark);
                    }
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaineCarteIdentite->getSnowpark()->getTraductions()->filter(function (
                            SnowparkTraduction $element
                        ) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new SnowparkTraduction();
                            $traduction->setLangue($langue);
                            $domaineCarteIdentite->getSnowpark()->addTraduction($traduction);
                        }
                    }

                    $handiski = $domaineCarteIdentite->getHandiski();
                    if (empty($handiski)) {
                        $handiski = new Handiski();
                        $domaine->getDomaineCarteIdentite()->setHandiski($handiski);
                    }
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaineCarteIdentite->getHandiski()->getTraductions()->filter(function (
                            HandiskiTraduction $element
                        ) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new HandiskiTraduction();
                            $traduction->setLangue($langue);
                            $domaineCarteIdentite->getHandiski()->addTraduction($traduction);
                        }
                    }

                    // FIN carte identite
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaine->getTraductions()->filter(function (DomaineTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DomaineTraduction();
                            $traduction->setLangue($langue);
                            $domaine->addTraduction($traduction);
                        }
                    }

                    // *** ajout pistes couleur si non existante ***
                    $typePistes = $em->getRepository(TypePiste::class)->findAll();
                    /** @var Piste $piste */
                    /** @var TypePiste $typePiste */
                    foreach ($typePistes as $typePiste) {
                        $piste = $domaineCarteIdentite->getPistes()->filter(function (Piste $element) use ($typePiste) {
                            return $typePiste == $element->getTypePiste();
                        })->first();
                        if (false === $piste) {
                            $piste = new Piste();
                            $domaineCarteIdentite->addPiste($piste);
                            $piste->setTypePiste($typePiste);
                        }
                    }
                    // *** fin ajout pistes couleur si non existante ***

                }
            }
            if (!$siteExiste) {
                $domaine = new Domaine();
                $domaine->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DomaineTraduction();
                    $traduction->setLangue($langue);
                    $domaine->addTraduction($traduction);
                }


                $domaineCarteIdentite = new DomaineCarteIdentite();
                $domaine->setDomaineCarteIdentite($domaineCarteIdentite);


                if (!empty($domaineCarteIdentite->getPistes())) {
                    foreach ($typePistes as $typePiste) {
                        if (empty($domaineCarteIdentite->getPistes()->filter(function (Piste $element) use ($typePiste
                        ) {
                            return $element->getTypePiste() == $typePiste;
                        })->first())
                        ) {
                            $piste = new Piste();
                            $piste->setTypePiste($typePiste);
                            $domaineCarteIdentite->addPiste($piste);
                        }
                    }
                } else {
                    foreach ($typePistes as $typePiste) {
                        $piste = new Piste();
                        $piste->setTypePiste($typePiste);
                        $domaineCarteIdentite->addPiste($piste);
                    }
                }

                $snowpark = new Snowpark();
                $domaine->getDomaineCarteIdentite()->setSnowpark($snowpark);
                foreach ($langues as $langue) {
                    $traduction = new SnowparkTraduction();
                    $traduction->setLangue($langue);
                    $snowpark->addTraduction($traduction);
                }
//                $domaineCarteIdentite->setSnowpark($snowpark);

                $handiski = new Handiski();
                $domaine->getDomaineCarteIdentite()->setHandiski($handiski);
                foreach ($langues as $langue) {
                    $traduction = new HandiskiTraduction();
                    $traduction->setLangue($langue);
                    $handiski->addTraduction($traduction);
                }
//                $domaineCarteIdentite->setHandiski($handiski);

                $entity->addDomaine($domaine);


                // carte identite
//                $domaineCarteIdentite = new DomaineCarteIdentite();
                $domaineCarteIdentite->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DomaineCarteIdentiteTraduction();
                    $traduction->setLangue($langue);
                    $domaineCarteIdentite->addTraduction($traduction);
                }

                $domaine->setDomaineCarteIdentite($domaineCarteIdentite);
                // fin carte identite

            }
        }
    }

    /**
     * Classe les domaines par classementAffichage
     * @param DomaineUnifie $entity
     */
    private function domainesSortByAffichage(DomaineUnifie $entity)
    {

        // Trier les domaines en fonction de leurs ordre d'affichage
        $domaines = $entity->getDomaines(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        /** @var ArrayIterator $iterator */
        $iterator = $domaines->getIterator();
        unset($domaines);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Domaine $a, Domaine $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $domaines = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($domaines);

        // remplacé les domaines par ce nouveau tableau (une fonction 'set' a été créé dans Domaine unifié)
        $entity->setDomaines($domaines);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $domaines
     */
    private function traductionsSortByLangue($domaines)
    {
        /** @var ArrayIterator $iterator */
        /** @var Domaine $domaine */
        foreach ($domaines as $domaine) {
            $traductions = $domaine->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (DomaineTraduction $a, DomaineTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $domaine->setTraductions($traductions);
        }
    }

    /**
     * @param Request $request
     * @param DomaineUnifie $domaineUnifie
     */
    private function carteIdentiteNew(Request $request, DomaineUnifie $domaineUnifie)
    {
        /** @var Domaine $domaine */
        $domaineCarteIdentiteController = new DomaineCarteIdentiteUnifieController();
        $domaineCarteIdentiteController->setContainer($this->container);

        foreach ($domaineUnifie->getDomaines() as $domaine) {
            // Si la carte d'identité est lié au domaine parent
            if (!empty($request->get('cboxDomaineCarteIdentite_' . $domaine->getSite()->getId())) && !empty($domaine->getDomaineParent())) {
                if (!empty($domaine->getDomaineParent())) {
                    $domaine->setDomaineCarteIdentite($domaine->getDomaineParent()->getDomaineCarteIdentite());
                }
            } else {
                // sinon on on en créé une nouvelle
                $domaineCarteIdentiteUnifie = $domaineCarteIdentiteController->newEntity($domaine, $request);

                $site = $domaine->getSite();
                $domaineCarteIdentite = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (
                    DomaineCarteIdentite $element
                ) use ($site) {
                    return $site == $element->getSite();
                })->first();
                $domaine->setDomaineCarteIdentite($domaineCarteIdentite);
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité domaine
     * @param DomaineUnifie $entity
     */
    public function copieVersSites(DomaineUnifie $entity, $originalDomaineImages = null, $originalDomainePhotos = null)
    {
        /** @var EntityManager $emSite */
        /** @var Domaine $domaine */
        /** @var DomaineTraduction $domaineTraduc */
//        Boucle sur les domaines afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDomaines() as $domaine) {
            if ($domaine->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($domaine->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $domaine->getSite()->getId()));
                if (!empty($domaine->getDomaineParent())) {
                    $domaineParent = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $domaine->getDomaineParent()->getDomaineUnifie()));
                    $photosParent = $domaine->getPhotosParent();
                    $imagesParent = $domaine->getImagesParent();
                    $videosParent = $domaine->getVideosParent();
                } else {
                    $domaineParent = null;
                    $photosParent = false;
                    $imagesParent = false;
                    $videosParent = false;
                }

                if (!empty($domaine->getDomaineCarteIdentite())) {
                    $domaineCarteIdentite = $emSite->getRepository(DomaineCarteIdentite::class)->findOneBy(array('domaineCarteIdentiteUnifie' => $domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie()));
                } else {
                    $domaineCarteIdentite = null;
                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
//                $emSite->getRepository(DomaineUnifie::class)->find(array($entity->getId()
//                    $emSite->find( DomaineUnifie::class, $entity->getId());
                if (is_null(($entitySite = $emSite->find(DomaineUnifie::class, $entity->getId())))) {
                    $entitySite = new DomaineUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

//            Récupération de la domaine sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineSite = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $entitySite))))) {
                    $domaineSite = new Domaine();
                    $entitySite->addDomaine($domaineSite);
                }

//            copie des données domaine
                $domaineSite
                    ->setSite($site)
                    ->setDomaineUnifie($entitySite)
                    ->setDomaineParent($domaineParent)
                    ->setDomaineCarteIdentite($domaineCarteIdentite)
                    ->setImagesParent($imagesParent)
                    ->setPhotosParent($photosParent)
                    ->setVideosParent($videosParent)
                    ->setActif($domaine->getActif());

//            Gestion des traductions
                foreach ($domaine->getTraductions() as $domaineTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $domaineTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($domaineTraducSite = $emSite->getRepository(DomaineTraduction::class)->findOneBy(array(
                        'domaine' => $domaineSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $domaineTraducSite = new DomaineTraduction();
                    }

//                copie des données traductions
                    $domaineTraducSite->setLangue($langue)
                        ->setLibelle($domaineTraduc->getLibelle())
                        ->setAffichageTexte($domaineTraduc->getAffichageTexte())
                        ->setDomaine($domaineSite);

//                ajout a la collection de traduction de la domaine distante
                    $domaineSite->addTraduction($domaineTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $domaineImages = $domaine->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'domaine de référence
                if (!empty($domaineImages) && !$domaineImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($domaineSite->getImages()) && !$domaineSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $domaineImageSites = new ArrayCollection();
                        foreach ($domaineSite->getImages() as $domaineimageSite) {
                            $domaineImageSites->add($domaineimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var DomaineImage $domaineImage */
                        foreach ($domaineImages as $domaineImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'domaineImage original correspondant sur le crm
                            /** @var ArrayCollection $originalDomaineImages */
                            $originalDomaineImage = $originalDomaineImages->filter(function (DomaineImage $element) use
                            (
                                $domaineImage
                            ) {
                                return $element->getImage() == $domaineImage->getImage();
                            })->first();
                            unset($domaineImageSite);
                            if ($originalDomaineImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalDomaineImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalDomaineImage);

                                $domaineImageSite = $domaineImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'domaineImage existe sur la bdd distante, on va le modifier
                            /** @var DomaineImage $domaineImageSite */
                            if (!empty($domaineImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'domaineImage du crm)
                                if ($domaineImageSite->getImage()->getMetadataValue('crm_ref_id') != $domaineImage->getImage()->getId()) {
                                    $cloneImage = clone $domaineImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $domaineImage->getImage()->getId());
                                    $cloneImage->setContext('domaine_image_' . $domaine->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($domaineImageSite->getImage());

                                    $domaineImageSite->setImage($cloneImage);
                                }

                                $domaineImageSite->setActif($domaineImage->getActif());

                                // on parcourt les traductions
                                /** @var DomaineImageTraduction $traduction */
                                foreach ($domaineImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var DomaineImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $domaineImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (
                                            DomaineImageTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new DomaineImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class,
                                                $traduction->getLangue()->getId()));
                                        $domaineImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createDomaineImage($domaineImage, $domaineSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'domaine de référence
                        /** @var DomaineImage $domaineImage */
                        foreach ($domaineImages as $domaineImage) {
                            $this->createDomaineImage($domaineImage, $domaineSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($domaineImageSites)) {
                        /** @var DomaineImage $domaineImageSite */
                        foreach ($domaineImageSites as $domaineImageSite) {
                            $domaineImageSite->setDomaine(null);
                            $emSite->remove($domaineImageSite->getImage());
                            $emSite->remove($domaineImageSite);
                        }
                    }
                }


                $domainePhotos = $domaine->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'domaine de référence
                if (!empty($domainePhotos) && !$domainePhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($domaineSite->getPhotos()) && !$domaineSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $domainePhotoSites = new ArrayCollection();
                        foreach ($domaineSite->getPhotos() as $domainephotoSite) {
                            $domainePhotoSites->add($domainephotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var DomainePhoto $domainePhoto */
                        foreach ($domainePhotos as $domainePhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'domainePhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalDomainePhotos */
                            $originalDomainePhoto = $originalDomainePhotos->filter(function (DomainePhoto $element) use
                            (
                                $domainePhoto
                            ) {
                                return $element->getPhoto() == $domainePhoto->getPhoto();
                            })->first();
                            unset($domainePhotoSite);
                            if ($originalDomainePhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalDomainePhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalDomainePhoto);

                                $domainePhotoSite = $domainePhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'domainePhoto existe sur la bdd distante, on va le modifier
                            /** @var DomainePhoto $domainePhotoSite */
                            if (!empty($domainePhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'domainePhoto du crm)
                                if ($domainePhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $domainePhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $domainePhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $domainePhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('domaine_photo_' . $domaine->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($domainePhotoSite->getPhoto());

                                    $domainePhotoSite->setPhoto($clonePhoto);
                                }

                                $domainePhotoSite->setActif($domainePhoto->getActif());

                                // on parcourt les traductions
                                /** @var DomainePhotoTraduction $traduction */
                                foreach ($domainePhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var DomainePhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $domainePhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (
                                            DomainePhotoTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new DomainePhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class,
                                                $traduction->getLangue()->getId()));
                                        $domainePhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createDomainePhoto($domainePhoto, $domaineSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'domaine de référence
                        /** @var DomainePhoto $domainePhoto */
                        foreach ($domainePhotos as $domainePhoto) {
                            $this->createDomainePhoto($domainePhoto, $domaineSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($domainePhotoSites)) {
                        /** @var DomainePhoto $domainePhotoSite */
                        foreach ($domainePhotoSites as $domainePhotoSite) {
                            $domainePhotoSite->setDomaine(null);
                            $emSite->remove($domainePhotoSite->getPhoto());
                            $emSite->remove($domainePhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********


                // *** gestion video ***
                if (!empty($domaine->getVideos()) && !$domaine->getVideos()->isEmpty()) {
                    /** @var DomaineVideo $domaineVideo */
                    foreach ($domaine->getVideos() as $domaineVideo) {
                        $domaineVideoSite = $domaineSite->getVideos()->filter(function (DomaineVideo $element) use (
                            $domaineVideo
                        ) {
                            return $element->getId() == $domaineVideo->getId();
                        })->first();
                        if (false === $domaineVideoSite) {
                            $domaineVideoSite = new DomaineVideo();
                            $domaineSite->addVideo($domaineVideoSite);
                            $domaineVideoSite
                                ->setId($domaineVideo->getId());
                            $metadata = $emSite->getClassMetadata(get_class($domaineVideoSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        if (empty($domaineVideoSite->getVideo()) || $domaineVideoSite->getVideo()->getId() != $domaineVideo->getVideo()->getId()) {
                            $cloneVideo = clone $domaineVideo->getVideo();
                            $metadata = $emSite->getClassMetadata(get_class($cloneVideo));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $cloneVideo->setContext('domaine_video_' . $domaineSite->getSite()->getLibelle());
                            // on supprime l'ancien photo
                            if (!empty($domaineVideoSite->getVideo())) {
                                $emSite->remove($domaineVideoSite->getVideo());
                                $this->deleteFile($domaineVideoSite->getVideo());
                            }
                            $domaineVideoSite
                                ->setVideo($cloneVideo);
                        }
                        $domaineVideoSite
                            ->setActif($domaineVideo->getActif());
                        // *** traductions ***
                        foreach ($domaineVideo->getTraductions() as $traduction) {
                            $traductionSite = $domaineVideoSite->getTraductions()->filter(function (
                                DomaineVideoTraduction $element
                            ) use ($traduction) {
                                return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new DomaineVideoTraduction();
                                $domaineVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($emSite->find(Langue::class,
                                    $traduction->getLangue()->getId()));
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }

                        // *** fin traductions ***
                    }
                }

                if (!empty($domaineSite->getVideos()) && !$domaineSite->getVideos()->isEmpty()) {
                    /** @var DomaineVideo $domaineVideo */
                    /** @var DomaineVideo $domaineVideoSite */
                    foreach ($domaineSite->getVideos() as $domaineVideoSite) {
                        $domaineVideo = $domaine->getVideos()->filter(function (DomaineVideo $element) use (
                            $domaineVideoSite
                        ) {
                            return $element->getId() == $domaineVideoSite->getId();
                        })->first();
                        if (false === $domaineVideo) {
                            $emSite->remove($domaineVideoSite);
                            $emSite->remove($domaineVideoSite->getVideo());
                            $this->deleteFile($domaineVideoSite->getVideo());
                        }
                    }
                }
                // *** fin gestion video ***

                $modeleDescriptionForfaitSkiController = new ModeleDescriptionForfaitSkiController();
                $modeleDescriptionForfaitSkiController->setContainer($this->container);
                $modeleDescriptionForfaitSkiController->copieToDomaineVersSites($entity, $entitySite);

                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterDomaineUnifieSiteDistant($entity->getId(), $entity->getDomaines());
    }

    /**
     * Création d'un nouveau domaineImage
     * @param DomaineImage $domaineImage
     * @param Domaine $domaineSite
     * @param EntityManager $emSite
     */
    private function createDomaineImage(DomaineImage $domaineImage, Domaine $domaineSite, EntityManager $emSite)
    {
        /** @var DomaineImage $domaineImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($domaineImage))->getName();
        // on cré un nouveau DomaineImage on fonction du type
        $domaineImageSite = new $typeImage();
        $domaineImageSite->setDomaine($domaineSite);
        $domaineImageSite->setActif($domaineImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $domaineImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($domaineImage->getImage()->getProviderReference());
        $cloneImage->setName($domaineImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('domaine_image_' . $domaineSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $domaineImage->getImage()->getId());

        $domaineImageSite->setImage($cloneImage);

        $domaineSite->addImage($domaineImageSite);
        // on ajoute les traductions correspondante
        foreach ($domaineImage->getTraductions() as $traduction) {
            $traductionSite = new DomaineImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $domaineImageSite->addTraduction($traductionSite);
        }
    }

    /**
     * Création d'un nouveau domainePhoto
     * @param DomainePhoto $domainePhoto
     * @param Domaine $domaineSite
     * @param EntityManager $emSite
     */
    private function createDomainePhoto(DomainePhoto $domainePhoto, Domaine $domaineSite, EntityManager $emSite)
    {
        /** @var DomainePhoto $domainePhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($domainePhoto))->getName();
        // on cré un nouveau DomainePhoto on fonction du type
        $domainePhotoSite = new $typePhoto();
        $domainePhotoSite->setDomaine($domaineSite);
        $domainePhotoSite->setActif($domainePhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $domainePhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($domainePhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($domainePhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('domaine_photo_' . $domaineSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $domainePhoto->getPhoto()->getId());

        $domainePhotoSite->setPhoto($clonePhoto);

        $domaineSite->addPhoto($domainePhotoSite);
        // on ajoute les traductions correspondante
        foreach ($domainePhoto->getTraductions() as $traduction) {
            $traductionSite = new DomainePhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $domainePhotoSite->addTraduction($traductionSite);
        }
    }

    private function deleteFile($visuel)
    {
        /** @var Media $visuel */
        if (file_exists($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg')) {
            unlink($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg');
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de domaine a enregistrer
     * @param $idUnifie
     * @param Collection $domaines
     */
    public function ajouterDomaineUnifieSiteDistant($idUnifie, Collection $domaines)
    {
        /** @var Site $site */
        /** @var ArrayCollection $domaines */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($domaines->matching($criteres)) == 0 && (empty($emSite->getRepository(DomaineUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DomaineUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a DomaineUnifie entity.
     *
     */
    public function showAction(DomaineUnifie $domaineUnifie)
    {
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        return $this->render('@MondofuteDomaine/domaineunifie/show.html.twig', array(
            'domaineUnifie' => $domaineUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DomaineUnifie entity.
     *
     * @param DomaineUnifie $domaineUnifie The DomaineUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DomaineUnifie $domaineUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('domaine_domaine_delete', array('id' => $domaineUnifie->getId())))
            ->add('delete', SubmitType::class, ['label' => 'Supprimer'])
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DomaineUnifie entity.
     *
     */
    public function editAction(Request $request, DomaineUnifie $domaineUnifie)
    {
        /** @var Domaine $domaine */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Domaine $entity */
            foreach ($domaineUnifie->getDomaines() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalDomaineImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalDomainePhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
        $originalDomaineVideos = new ArrayCollection();
        $originalVideos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($domaineUnifie->getDomaines() as $domaine) {
            // si l'domaine est celui du CRM
            if ($domaine->getSite()->getCrm() == 1) {
                // on parcourt les domaineImage pour les comparer ensuite
                /** @var DomaineImage $domaineImage */
                foreach ($domaine->getImages() as $domaineImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalDomaineImages->add($domaineImage);
                    $originalImages->add($domaineImage->getImage());
                }
                // on parcourt les domainePhoto pour les comparer ensuite
                /** @var DomainePhoto $domainePhoto */
                foreach ($domaine->getPhotos() as $domainePhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalDomainePhotos->add($domainePhoto);
                    $originalPhotos->add($domainePhoto->getPhoto());
                }
                // on parcourt les domaineVideo pour les comparer ensuite
                /** @var DomaineVideo $domaineVideo */
                foreach ($domaine->getVideos() as $domaineVideo) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalDomaineVideos->add($domaineVideo);
                    $originalVideos->set($domaineVideo->getId(), $domaineVideo->getVideo());
                }
            }
        }

        $this->ajouterDomainesDansForm($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType', $domaineUnifie,
            array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'Mettre à jour',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $domaineCarteIdentiteUnifieController = new DomaineCarteIdentiteUnifieController();
            $domaineCarteIdentiteUnifieController->setContainer($this->container);
            try {
                foreach ($domaineUnifie->getDomaines() as $entity) {
                    if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                        $entity->setActif(false);
                    } else {
                        $entity->setActif(true);
                    }
                }

                // on vérifie si les domaines on un parent,
                // s'il le domaine n'a pas de parent on lui met dans tous les cas la liaison aux medias parent à false
                foreach ($domaineUnifie->getDomaines() as $domaine) {
                    if (empty($domaine->getDomaineParent())) {
                        $domaine
                            ->setPhotosParent(false)
                            ->setImagesParent(false)
                            ->setVideosParent(false);
                    }
                }

                // ************* suppression images *************
                // ** CAS OU L'ON SUPPRIME UN "DOMAINE IMAGE" **
                // on récupère les DomaineImage de l'hébergementCrm pour les mettre dans une collection
                // afin de les comparer au originaux.
                /** @var Domaine $domaineCrm */
                $domaineCrm = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $domaineSites = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                $newDomaineImages = new ArrayCollection();
                foreach ($domaineCrm->getImages() as $domaineImage) {
                    $newDomaineImages->add($domaineImage);
                }
                /** @var DomaineImage $originalDomaineImage */
                foreach ($originalDomaineImages as $key => $originalDomaineImage) {

                    if (false === $newDomaineImages->contains($originalDomaineImage)) {
                        $originalDomaineImage->setDomaine(null);
                        $em->remove($originalDomaineImage->getImage());
                        $em->remove($originalDomaineImage);
                        // on doit supprimer l'hébergementImage des autres sites
                        // on parcourt les domaine des sites
                        /** @var Domaine $domaineSite */
                        foreach ($domaineSites as $domaineSite) {
                            $domaineImageSite = $em->getRepository(DomaineImage::class)->findOneBy(
                                array(
                                    'domaine' => $domaineSite,
                                    'image' => $originalDomaineImage->getImage()
                                ));
                            if (!empty($domaineImageSite)) {
                                $emSite = $this->getDoctrine()->getEntityManager($domaineImageSite->getDomaine()->getSite()->getLibelle());
                                $domaineSite = $emSite->getRepository(Domaine::class)->findOneBy(
                                    array(
                                        'domaineUnifie' => $domaineImageSite->getDomaine()->getDomaineUnifie()
                                    ));
                                $domaineImageSiteSites = new ArrayCollection($emSite->getRepository(DomaineImage::class)->findBy(
                                    array(
                                        'domaine' => $domaineSite
                                    ))
                                );
                                $domaineImageSiteSite = $domaineImageSiteSites->filter(function (DomaineImage $element)
                                use ($domaineImageSite) {
//                            return $element->getImage()->getProviderReference() == $domaineImageSite->getImage()->getProviderReference();
                                    return $element->getImage()->getMetadataValue('crm_ref_id') == $domaineImageSite->getImage()->getId();
                                })->first();
                                if (!empty($domaineImageSiteSite)) {
                                    $emSite->remove($domaineImageSiteSite->getImage());
                                    $domaineImageSiteSite->setDomaine(null);
                                    $emSite->remove($domaineImageSiteSite);
                                    $emSite->flush();
                                }
                                $domaineImageSite->setDomaine(null);
                                $em->remove($domaineImageSite->getImage());
                                $em->remove($domaineImageSite);
                            }
                        }
                    }
                }
                // ************* fin suppression images *************


                // ************* suppression photos *************
                // ** CAS OU L'ON SUPPRIME UN "DOMAINE PHOTO" **
                // on récupère les DomainePhoto de l'hébergementCrm pour les mettre dans une collection
                // afin de les comparer au originaux.
                /** @var Domaine $domaineCrm */
                $domaineCrm = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $domaineSites = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                $newDomainePhotos = new ArrayCollection();
                foreach ($domaineCrm->getPhotos() as $domainePhoto) {
                    $newDomainePhotos->add($domainePhoto);
                }
                /** @var DomainePhoto $originalDomainePhoto */
                foreach ($originalDomainePhotos as $key => $originalDomainePhoto) {

                    if (false === $newDomainePhotos->contains($originalDomainePhoto)) {
                        $originalDomainePhoto->setDomaine(null);
                        $em->remove($originalDomainePhoto->getPhoto());
                        $em->remove($originalDomainePhoto);
                        // on doit supprimer l'hébergementPhoto des autres sites
                        // on parcourt les domaine des sites
                        /** @var Domaine $domaineSite */
                        foreach ($domaineSites as $domaineSite) {
                            $domainePhotoSite = $em->getRepository(DomainePhoto::class)->findOneBy(
                                array(
                                    'domaine' => $domaineSite,
                                    'photo' => $originalDomainePhoto->getPhoto()
                                ));
                            if (!empty($domainePhotoSite)) {
                                $emSite = $this->getDoctrine()->getEntityManager($domainePhotoSite->getDomaine()->getSite()->getLibelle());
                                $domaineSite = $emSite->getRepository(Domaine::class)->findOneBy(
                                    array(
                                        'domaineUnifie' => $domainePhotoSite->getDomaine()->getDomaineUnifie()
                                    ));
                                $domainePhotoSiteSites = new ArrayCollection($emSite->getRepository(DomainePhoto::class)->findBy(
                                    array(
                                        'domaine' => $domaineSite
                                    ))
                                );
                                $domainePhotoSiteSite = $domainePhotoSiteSites->filter(function (DomainePhoto $element)
                                use ($domainePhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $domainePhotoSite->getPhoto()->getProviderReference();
                                    return $element->getPhoto()->getMetadataValue('crm_ref_id') == $domainePhotoSite->getPhoto()->getId();
                                })->first();
                                if (!empty($domainePhotoSiteSite)) {
                                    $emSite->remove($domainePhotoSiteSite->getPhoto());
                                    $domainePhotoSiteSite->setDomaine(null);
                                    $emSite->remove($domainePhotoSiteSite);
                                    $emSite->flush();
                                }
                                $domainePhotoSite->setDomaine(null);
                                $em->remove($domainePhotoSite->getPhoto());
                                $em->remove($domainePhotoSite);
                            }
                        }
                    }
                }
                // ************* fin suppression photos *************


                // ** suppression videos **
                foreach ($originalDomaineVideos as $originalDomaineVideo) {
                    if (false === $domaineCrm->getVideos()->contains($originalDomaineVideo)) {
                        $videos = $em->getRepository(DomaineVideo::class)->findBy(array('video' => $originalDomaineVideo->getVideo()));
                        foreach ($videos as $video) {
                            $em->remove($video);
                        }
                        $em->remove($originalDomaineVideo->getVideo());
                        $this->deleteFile($originalDomaineVideo->getVideo());
                    }
                }
                // ** fin suppression videos **
                // *** gestion des videos ***
                /** @var Domaine $domaineCrm */
                $domaineCrm = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                    return $element->getSite()->getCrm() == 1;
                })->first();
                $domaineSites = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
                    return $element->getSite()->getCrm() == 0;
                });
                /** @var DomaineVideo $domaineVideo */
                foreach ($domaineCrm->getVideos() as $key => $domaineVideo) {
                    foreach ($domaineSites as $domaineSite) {
                        if (empty($domaineVideo->getId())) {
                            $domaineVideoSite = clone $domaineVideo;
                        } else {
                            $domaineVideoSite = $em->getRepository(DomaineVideo::class)->findOneBy(array(
                                'video' => $originalVideos->get($domaineVideo->getId()),
                                'domaine' => $domaineSite
                            ));
                            if ($originalVideos->get($domaineVideo->getId()) != $domaineVideo->getVideo()) {
                                $em->remove($domaineVideoSite->getVideo());
                                $this->deleteFile($domaineVideoSite->getVideo());
                                $domaineVideoSite->setVideo($domaineVideo->getVideo());
                            }
                        }
                        $domaineSite->addVideo($domaineVideoSite);
                        $actif = false;
                        if (!empty($request->get('domaine_unifie')['domaines'][0]['videos'][$key]['sites'])) {
                            if (in_array($domaineSite->getSite()->getId(),
                                $request->get('domaine_unifie')['domaines'][0]['videos'][$key]['sites'])) {
                                $actif = true;
                            }
                        }
                        $domaineVideoSite->setActif($actif);

                        // *** traductions ***
                        foreach ($domaineVideo->getTraductions() as $traduction) {
                            $traductionSite = $domaineVideoSite->getTraductions()->filter(function (
                                DomaineVideoTraduction $element
                            ) use ($traduction) {
                                return $element->getLangue() == $traduction->getLangue();
                            })->first();
                            if (false === $traductionSite) {
                                $traductionSite = new DomaineVideoTraduction();
                                $domaineVideoSite->addTraduction($traductionSite);
                                $traductionSite->setLangue($traduction->getLangue());
                            }
                            $traductionSite->setLibelle($traduction->getLibelle());
                        }
                        // *** fin traductions ***
                    }
                }
                // *** fin gestion des videos ***

                // ***** carte d'identité *****
                $this->carteIdentiteEdit($request, $domaineUnifie);
                // ***** fin carte d'identité *****

                // ***** Gestion des Medias *****
                // CAS D'UN NOUVEAU 'DOMAINE IMAGE' OU DE MODIFICATION D'UN "DOMAINE IMAGE"
                /** @var DomaineImage $domaineImage */
                // tableau pour la suppression des anciens images
                $imageToRemoveCollection = new ArrayCollection();
                $keyCrm = $domaineUnifie->getDomaines()->indexOf($domaineCrm);
                // on parcourt les domaineImages de l'domaine crm
                foreach ($domaineCrm->getImages() as $key => $domaineImage) {
                    // on active le nouveau domaineImage (CRM) => il doit être toujours actif
                    $domaineImage->setActif(true);
                    // parcourir tout les sites
                    /** @var Site $site */
                    foreach ($sites as $site) {
                        // sauf  le crm (puisqu'on l'a déjà renseigné)
                        // dans le but de créer un hebegrementImage pour chacun
                        if ($site->getCrm() == 0) {
                            // on récupère l'hébegergement du site
                            /** @var Domaine $domaineSite */
                            $domaineSite = $domaineUnifie->getDomaines()->filter(function (Domaine $element) use ($site
                            ) {
                                return $element->getSite() == $site;
                            })->first();
                            // si hébergement existe
                            if (!empty($domaineSite)) {
                                // on réinitialise la variable
                                unset($domaineImageSite);
                                // s'il ne s'agit pas d'un nouveau domaineImage
                                if (!empty($domaineImage->getId())) {
                                    // on récupère l'domaineImage pour le modifier
                                    $domaineImageSite = $em->getRepository(DomaineImage::class)->findOneBy(array(
                                        'domaine' => $domaineSite,
                                        'image' => $originalImages->get($key)
                                    ));
                                }
                                // si l'domaineImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                                if (empty($domaineImage->getId()) || empty($domaineImageSite)) {
                                    // on récupère la classe correspondant au image (photo ou video)
                                    $typeImage = (new ReflectionClass($domaineImage))->getName();
                                    // on créé un nouveau DomaineImage on fonction du type
                                    /** @var DomaineImage $domaineImageSite */
                                    $domaineImageSite = new $typeImage();
                                    $domaineImageSite->setDomaine($domaineSite);
                                }
                                // si l'hébergemenent image existe déjà pour le site
                                if (!empty($domaineImageSite)) {
                                    if ($domaineImageSite->getImage() != $domaineImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($domaineImageSite->getImage()) && !$imageToRemoveCollection->contains($domaineImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($domaineImageSite->getImage());
//                                    }
                                        // on met le nouveau image
                                        $domaineImageSite->setImage($domaineImage->getImage());
                                    }
                                    $domaineSite->addImage($domaineImageSite);

                                    /** @var DomaineImageTraduction $traduction */
                                    foreach ($domaineImage->getTraductions() as $traduction) {
                                        /** @var DomaineImageTraduction $traductionSite */
                                        $traductionSites = $domaineImageSite->getTraductions();
                                        $traductionSite = null;
                                        if (!$traductionSites->isEmpty()) {
                                            $traductionSite = $traductionSites->filter(function (
                                                DomaineImageTraduction $element
                                            ) use ($traduction) {
                                                return $element->getLangue() == $traduction->getLangue();
                                            })->first();
                                        }
                                        if (empty($traductionSite)) {
                                            $traductionSite = new DomaineImageTraduction();
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $domaineImageSite->addTraduction($traductionSite);
                                        }
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    }
                                    // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                    if (!empty($request->get('domaine_unifie')['domaines'][$keyCrm]['images'][$key]['sites']) &&
                                        in_array($site->getId(),
                                            $request->get('domaine_unifie')['domaines'][$keyCrm]['images'][$key]['sites'])
                                    ) {
                                        $domaineImageSite->setActif(true);
                                    } else {
                                        $domaineImageSite->setActif(false);
                                    }
                                }
                            }
                        }
                        // on est dans l'domaineImage CRM
                        // s'il s'agit d'un nouveau média
                        elseif (empty($domaineImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                            // on stocke  l'ancien media pour le supprimer après le persist final
                            $imageToRemoveCollection->add($originalImages->get($key));
                        }
                    }
                }


                // CAS D'UN NOUVEAU 'DOMAINE PHOTO' OU DE MODIFICATION D'UN "DOMAINE PHOTO"
                /** @var DomainePhoto $domainePhoto */
                // tableau pour la suppression des anciens photos
                $photoToRemoveCollection = new ArrayCollection();
                $keyCrm = $domaineUnifie->getDomaines()->indexOf($domaineCrm);
                // on parcourt les domainePhotos de l'domaine crm
                foreach ($domaineCrm->getPhotos() as $key => $domainePhoto) {
                    // on active le nouveau domainePhoto (CRM) => il doit être toujours actif
                    $domainePhoto->setActif(true);
                    // parcourir tout les sites
                    /** @var Site $site */
                    foreach ($sites as $site) {
                        // sauf  le crm (puisqu'on l'a déjà renseigné)
                        // dans le but de créer un hebegrementPhoto pour chacun
                        if ($site->getCrm() == 0) {
                            // on récupère l'hébegergement du site
                            /** @var Domaine $domaineSite */
                            $domaineSite = $domaineUnifie->getDomaines()->filter(function (Domaine $element) use ($site
                            ) {
                                return $element->getSite() == $site;
                            })->first();
                            // si hébergement existe
                            if (!empty($domaineSite)) {
                                // on réinitialise la variable
                                unset($domainePhotoSite);
                                // s'il ne s'agit pas d'un nouveau domainePhoto
                                if (!empty($domainePhoto->getId())) {
                                    // on récupère l'domainePhoto pour le modifier
                                    $domainePhotoSite = $em->getRepository(DomainePhoto::class)->findOneBy(array(
                                        'domaine' => $domaineSite,
                                        'photo' => $originalPhotos->get($key)
                                    ));
                                }
                                // si l'domainePhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                                if (empty($domainePhoto->getId()) || empty($domainePhotoSite)) {
                                    // on récupère la classe correspondant au photo (photo ou video)
                                    $typePhoto = (new ReflectionClass($domainePhoto))->getName();
                                    // on créé un nouveau DomainePhoto on fonction du type
                                    /** @var DomainePhoto $domainePhotoSite */
                                    $domainePhotoSite = new $typePhoto();
                                    $domainePhotoSite->setDomaine($domaineSite);
                                }
                                // si l'hébergemenent photo existe déjà pour le site
                                if (!empty($domainePhotoSite)) {
                                    if ($domainePhotoSite->getPhoto() != $domainePhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($domainePhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($domainePhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($domainePhotoSite->getPhoto());
//                                    }
                                        // on met le nouveau photo
                                        $domainePhotoSite->setPhoto($domainePhoto->getPhoto());
                                    }
                                    $domaineSite->addPhoto($domainePhotoSite);

                                    /** @var DomainePhotoTraduction $traduction */
                                    foreach ($domainePhoto->getTraductions() as $traduction) {
                                        /** @var DomainePhotoTraduction $traductionSite */
                                        $traductionSites = $domainePhotoSite->getTraductions();
                                        $traductionSite = null;
                                        if (!$traductionSites->isEmpty()) {
                                            $traductionSite = $traductionSites->filter(function (
                                                DomainePhotoTraduction $element
                                            ) use ($traduction) {
                                                return $element->getLangue() == $traduction->getLangue();
                                            })->first();
                                        }
                                        if (empty($traductionSite)) {
                                            $traductionSite = new DomainePhotoTraduction();
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $domainePhotoSite->addTraduction($traductionSite);
                                        }
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    }
                                    // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                    if (!empty($request->get('domaine_unifie')['domaines'][$keyCrm]['photos'][$key]['sites']) &&
                                        in_array($site->getId(),
                                            $request->get('domaine_unifie')['domaines'][$keyCrm]['photos'][$key]['sites'])
                                    ) {
                                        $domainePhotoSite->setActif(true);
                                    } else {
                                        $domainePhotoSite->setActif(false);
                                    }
                                }
                            }
                        }
                        // on est dans l'domainePhoto CRM
                        // s'il s'agit d'un nouveau média
                        elseif (empty($domainePhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                            // on stocke  l'ancien media pour le supprimer après le persist final
                            $photoToRemoveCollection->add($originalPhotos->get($key));
                        }
                    }
                }
                // ***** Fin Gestion des Medias *****

                $modeleDescriptionForfaitSkiController = new ModeleDescriptionForfaitSkiController();
                $modeleDescriptionForfaitSkiController->setContainer($this->container);
                $modeleDescriptionForfaitSkiController->majDomaines($domaineUnifie);

                $em->persist($domaineUnifie);
                $em->flush();

                foreach ($domaineUnifie->getDomaines() as $domaine) {
                    $domaineCarteIdentiteUnifieController->copieVersSites($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
                }

                $this->copieVersSites($domaineUnifie, $originalDomaineImages, $originalDomainePhotos);

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
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer le domaine, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirectToRoute('domaine_domaine_edit', array('id' => $domaineUnifie->getId()));
            }

            // add flash messages
            $this->addFlash(
                'success',
                'Le domaine a bien été modifié.'
            );

            return $this->redirectToRoute('domaine_domaine_edit', array('id' => $domaineUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domaineunifie/edit.html.twig', array(
            'entity' => $domaineUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function carteIdentiteEdit(Request $request, DomaineUnifie $domaineUnifie)
    {
        /** @var DomaineCarteIdentiteTraduction $traduction */
        /** @var Domaine $domaine */
        $domaineCarteIdentiteUnifieController = new DomaineCarteIdentiteUnifieController();
        $domaineCarteIdentiteUnifieController->setContainer($this->container);
        $em = $this->getDoctrine()->getEntityManager();

        foreach ($domaineUnifie->getDomaines() as $domaine) {
            // si on choisit de lié la carte ID de la mère à la domaine
            if (!empty($domaine->getDomaineParent()) && !empty($request->get('cboxDomaineCarteIdentite_' . $domaine->getSite()->getId()))) {
                $oldCIUnifie = $domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie();
                $domaine->getDomaineCarteIdentite()->removeDomaine($domaine);
//                    $domaine->setDomaineCarteIdentite(null);

                $em->refresh($domaine->getDomaineParent()->getDomaineCarteIdentite());
                $domaine->setDomaineCarteIdentite($domaine->getDomaineParent()->getDomaineCarteIdentite());
                if ($domaine->getDomaineParent()->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie() != $oldCIUnifie) {
                    $this->copieVersSites($domaine->getDomaineUnifie());
                    if (!empty($oldCIUnifie)) {
                        $domaineCarteIdentiteUnifieController->deleteEntity($oldCIUnifie);
                    }
                }

            } else if (!empty($domaine->getDomaineParent()) && $domaine->getDomaineParent()->getDomaineCarteIdentite() === $domaine->getDomaineCarteIdentite()) {
                //
                // OIn fait ça
                $cIParent = $domaine->getDomaineParent()->getDomaineCarteIdentite();
                $cIOld = $domaine->getDomaineCarteIdentite();

                $newCI = new DomaineCarteIdentite();
                $altitudeMini = new Distance();
                $altitudeMini->setUnite($cIOld->getAltitudeMini()->getUnite());
                $altitudeMini->setValeur($cIOld->getAltitudeMini()->getValeur());
                $newCI->setAltitudeMini($altitudeMini);

                $altitudeMaxi = new Distance();
                $altitudeMaxi->setUnite($cIOld->getAltitudeMaxi()->getUnite());
                $altitudeMaxi->setValeur($cIOld->getAltitudeMaxi()->getValeur());
                $newCI->setAltitudeMaxi($altitudeMaxi);

                $kmPistesSkiAlpin = new KmPistesAlpin();
                $kmPistesSkiAlpin->setLongueur(new Distance());
                $kmPistesSkiAlpin->getLongueur()->setUnite($cIOld->getKmPistesSkiAlpin()->getLongueur()->getUnite());
                $kmPistesSkiAlpin->getLongueur()->setValeur($cIOld->getKmPistesSkiAlpin()->getLongueur()->getValeur());
                $newCI->setKmPistesSkiAlpin($kmPistesSkiAlpin);

                $kmPistesSkiNordique = new KmPistesNordique();
                $kmPistesSkiNordique->setLongueur(new Distance());
                $kmPistesSkiNordique->getLongueur()->setUnite($cIOld->getKmPistesSkiNordique()->getLongueur()->getUnite());
                $kmPistesSkiNordique->getLongueur()->setValeur($cIOld->getKmPistesSkiNordique()->getLongueur()->getValeur());
                $newCI->setKmPistesSkiNordique($kmPistesSkiNordique);

                //remontee mécanique
                $newCI->setRemonteeMecanique($cIOld->getRemonteeMecanique());
                //niveauSKieur
                $newCI->setNiveauSkieur($cIOld->getNiveauSkieur());

                //pistes
                /** @var Piste $piste */
                foreach ($cIOld->getPistes() as $piste) {
                    $newPiste = new Piste();
                    $newPiste->setTypePiste($piste->getTypePiste());
                    $newPiste->setNombre($piste->getNombre());
                    $newCI->addPiste($newPiste);
                }

                $snowpark = new Snowpark();
                $newCI->setSnowpark($snowpark);
                $handiski = new Handiski();
                $newCI->setHandiski($handiski);

                foreach ($cIOld->getTraductions() as $traduction) {
                    $newTrad = new DomaineCarteIdentiteTraduction();
                    $newTrad
                        ->setLangue($traduction->getLangue())
                        ->setAccroche($traduction->getAccroche())
                        ->setDescription($traduction->getDescription());
                    $newCI->addTraduction($newTrad);
                }

                $snowpark->setPresent($cIOld->getSnowpark()->getPresent());
                foreach ($cIOld->getSnowpark()->getTraductions() as $traduction) {
                    $newTrad = new SnowparkTraduction();
                    $newTrad
                        ->setLangue($traduction->getLangue())
                        ->setDescription($traduction->getDescription());
                    $newCI->getSnowpark()->addTraduction($newTrad);
                }

                $handiski->setPresent($cIOld->getHandiski()->getPresent());
                foreach ($cIOld->getHandiski()->getTraductions() as $traduction) {
                    $newTrad = new HandiskiTraduction();
                    $newTrad
                        ->setLangue($traduction->getLangue())
                        ->setDescription($traduction->getDescription());
                    $newCI->getHandiski()->addTraduction($newTrad);
                }
                $newCI->setSite($domaine->getDomaineCarteIdentite()->getSite());

                $remonteeMecanique = new RemonteeMecanique();
                $remonteeMecanique->setNombre($cIOld->getRemonteeMecanique()->getNombre());
                $newCI->setRemonteeMecanique($remonteeMecanique);

                $em->persist($newCI);
                $domaine->setDomaineCarteIdentite($newCI);

                $em->refresh($cIParent);
                $em->refresh($cIParent->getAltitudeMini());
                $em->refresh($cIParent->getAltitudeMaxi());
                $em->refresh($cIParent->getKmPistesSkiAlpin());
                $em->refresh($cIParent->getKmPistesSkiNordique());
                $em->refresh($cIParent->getNiveauSkieur());
                $em->refresh($cIParent->getSnowpark());
                $em->refresh($cIParent->getHandiski());
                $em->refresh($cIParent->getRemonteeMecanique());
            } else if (empty($domaine->getDomaineParent()) && count($domaine->getDomaineCarteIdentite()->getDomaines()) > 1) {

                $cIOld = $domaine->getDomaineCarteIdentite();

                $newCI = new DomaineCarteIdentite();
                $altitudeMini = new Distance();
                $altitudeMini->setUnite($cIOld->getAltitudeMini()->getUnite());
                $altitudeMini->setValeur($cIOld->getAltitudeMini()->getValeur());
                $newCI->setAltitudeMini($altitudeMini);

                $altitudeMaxi = new Distance();
                $altitudeMaxi->setUnite($cIOld->getAltitudeMaxi()->getUnite());
                $altitudeMaxi->setValeur($cIOld->getAltitudeMaxi()->getValeur());
                $newCI->setAltitudeMaxi($altitudeMaxi);

                $kmPistesSkiAlpin = new KmPistesAlpin();
                $kmPistesSkiAlpin->setLongueur(new Distance());
                $kmPistesSkiAlpin->getLongueur()->setUnite($cIOld->getKmPistesSkiAlpin()->getLongueur()->getUnite());
                $kmPistesSkiAlpin->getLongueur()->setValeur($cIOld->getKmPistesSkiAlpin()->getLongueur()->getValeur());
                $newCI->setKmPistesSkiAlpin($kmPistesSkiAlpin);

                $kmPistesSkiNordique = new KmPistesNordique();
                $kmPistesSkiNordique->setLongueur(new Distance());
                $kmPistesSkiNordique->getLongueur()->setUnite($cIOld->getKmPistesSkiNordique()->getLongueur()->getUnite());
                $kmPistesSkiNordique->getLongueur()->setValeur($cIOld->getKmPistesSkiNordique()->getLongueur()->getValeur());
                $newCI->setKmPistesSkiNordique($kmPistesSkiNordique);

                //remontee mécanique
                $newCI->setRemonteeMecanique($cIOld->getRemonteeMecanique());
                //niveauSKieur
                $newCI->setNiveauSkieur($cIOld->getNiveauSkieur());

                //pistes
                /** @var Piste $piste */
                foreach ($cIOld->getPistes() as $piste) {
                    $newPiste = new Piste();
                    $newPiste->setTypePiste($piste->getTypePiste());
                    $newPiste->setNombre($piste->getNombre());
                    $newCI->addPiste($newPiste);
                }

                $snowpark = new Snowpark();
                $newCI->setSnowpark($snowpark);
                $handiski = new Handiski();
                $newCI->setHandiski($handiski);

                foreach ($cIOld->getTraductions() as $traduction) {
                    $newTrad = new DomaineCarteIdentiteTraduction();
                    $newTrad
                        ->setLangue($traduction->getLangue())
                        ->setAccroche($traduction->getAccroche())
                        ->setDescription($traduction->getDescription());
                    $newCI->addTraduction($newTrad);
                }

                $snowpark->setPresent($cIOld->getSnowpark()->getPresent());
                foreach ($cIOld->getSnowpark()->getTraductions() as $traduction) {
                    $newTrad = new SnowparkTraduction();
                    $newTrad
                        ->setLangue($traduction->getLangue())
                        ->setDescription($traduction->getDescription());
                    $newCI->getSnowpark()->addTraduction($newTrad);
                }

                $handiski->setPresent($cIOld->getHandiski()->getPresent());
                foreach ($cIOld->getHandiski()->getTraductions() as $traduction) {
                    $newTrad = new HandiskiTraduction();
                    $newTrad
                        ->setLangue($traduction->getLangue())
                        ->setDescription($traduction->getDescription());
                    $newCI->getHandiski()->addTraduction($newTrad);
                }
                $newCI->setSite($domaine->getDomaineCarteIdentite()->getSite());

                $remonteeMecanique = new RemonteeMecanique();
                $remonteeMecanique->setNombre($cIOld->getRemonteeMecanique()->getNombre());
                $newCI->setRemonteeMecanique($remonteeMecanique);

                $em->persist($newCI);
                $domaine->setDomaineCarteIdentite($newCI);

            }
        }

        foreach ($domaineUnifie->getDomaines() as $domaine) {

            if (!empty($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie())) {
                $domaineCarteIdentiteUnifieController->editEntity($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
            } else {
                $domaineCarteIdentiteUnifieController->newEntity($domaine, $request);
            }

//            $em->persist($domaine);
//                $em->flush();
        }

    }

    /**
     * Deletes a DomaineUnifie entity.
     *
     */
    public function deleteAction(Request $request, DomaineUnifie $domaineUnifie)
    {
        /** @var Domaine $domaine */
        $form = $this->createDeleteForm($domaineUnifie);
        $form->handleRequest($request);
        $domaineCarteIdentiteUnifieController = new DomaineCarteIdentiteUnifieController();
        $domaineCarteIdentiteUnifieController->setContainer($this->container);

        $stationEmpty = true;
        foreach ($domaineUnifie->getDomaines() as $domaine) {
            if (!$domaine->getStations()->isEmpty()) {
                if ($stationEmpty) {
                    $this->addFlash('error',
                        'Impossible de supprimer le domaine, il est utilisé par une ou plusieurs stations.');
                    $stationEmpty = false;
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid() && $stationEmpty) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $domaineUnifieSite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                    if (!empty($domaineUnifieSite)) {
                        $emSite->remove($domaineUnifieSite);

                        if (!$domaineUnifieSite->getDomaines()->isEmpty()) {
                            $domaineSite = $domaineUnifieSite->getDomaines()->first();

                            // si il y a des videos pour l'entité, les supprimer
                            if (!empty($domaineSite->getVideos())) {
                                /** @var DomaineVideo $domaineVideoSite */
                                foreach ($domaineSite->getVideos() as $domaineVideoSite) {
                                    if (!empty($domaineVideoSite->getVideo())) {
                                        $emSite->remove($domaineVideoSite->getVideo());
                                        $this->deleteFile($domaineVideoSite->getVideo());
                                    }
                                }
                            }
                        }

                        $emSite->flush();
                    }
                }

                $arrayDomaineCarteIdentiteUnifies = new ArrayCollection();
                /** @var Domaine $domaine */
                foreach ($domaineUnifie->getDomaines() as $domaine) {
                    if (!empty($domaine->getDomaineCarteIdentite()) && (empty($domaine->getDomaineParent()) || (!empty($domaine->getDomaineParent()) && $domaine->getDomaineCarteIdentite() != $domaine->getDomaineParent()->getDomaineCarteIdentite()))) {
                        $arrayDomaineCarteIdentiteUnifies->add($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
                    }

                    // si il y a des images pour l'entité, les supprimer
                    if (!empty($domaine->getImages())) {
                        /** @var DomaineImage $domaineImage */
                        foreach ($domaine->getImages() as $domaineImage) {
                            $image = $domaineImage->getImage();
                            $domaineImage->setImage(null);
                            $em->remove($image);
                        }
                    }
                    // si il y a des photos pour l'entité, les supprimer
                    if (!empty($domaine->getPhotos())) {
                        /** @var DomainePhoto $domainePhoto */
                        foreach ($domaine->getPhotos() as $domainePhoto) {
                            $photo = $domainePhoto->getPhoto();
                            $domainePhoto->setPhoto(null);
                            $em->remove($photo);
                        }
                    }
                    // si il y a des videos pour l'entité, les supprimer
                    if (!empty($domaine->getVideos())) {
                        /** @var DomaineVideo $domaineVideoSite */
                        foreach ($domaine->getVideos() as $domaineVideoSite) {
                            $em->remove($domaineVideoSite);
                            $em->remove($domaineVideoSite->getVideo());
                        }
                    }

                }

                $em->remove($domaineUnifie);

                foreach ($arrayDomaineCarteIdentiteUnifies as $domaineCarteIdentiteUnify) {
                    $domaineCarteIdentiteUnifieController->deleteEntity($domaineCarteIdentiteUnify);
                }

                $em->flush();
            } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer le domaine, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }

            // add flash messages
            $this->addFlash('success', 'Le domaine a été supprimé avec succès.');
        }

        return $this->redirectToRoute('domaine_domaine_index');
    }

}
