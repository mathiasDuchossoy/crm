<?php

namespace Mondofute\Bundle\DomaineBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImage;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImageTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentitePhoto;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentitePhotoTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteUnifie;
use Mondofute\Bundle\DomaineBundle\Entity\Handiski;
use Mondofute\Bundle\DomaineBundle\Entity\HandiskiTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\KmPistesAlpin;
use Mondofute\Bundle\DomaineBundle\Entity\KmPistesNordique;
use Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur;
use Mondofute\Bundle\DomaineBundle\Entity\Piste;
use Mondofute\Bundle\DomaineBundle\Entity\RemonteeMecanique;
use Mondofute\Bundle\DomaineBundle\Entity\Snowpark;
use Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\TypePiste;
use Mondofute\Bundle\DomaineBundle\Form\DomaineCarteIdentiteUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Mondofute\Bundle\UniteBundle\Entity\UniteDistance;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DomaineCarteIdentiteUnifie controller.
 *
 */
class DomaineCarteIdentiteUnifieController extends Controller
{
    /**
     * Lists all DomaineCarteIdentiteUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $domaineCarteIdentiteUnifies = $em->getRepository('MondofuteDomaineBundle:DomaineCarteIdentiteUnifie')->findAll();

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/index.html.twig', array(
            'domaineCarteIdentiteUnifies' => $domaineCarteIdentiteUnifies,
        ));
    }

    /**
     * Creates a new DomaineCarteIdentiteUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $domaineCarteIdentiteUnifie = new DomaineCarteIdentiteUnifie();

        $this->ajouterDomaineCarteIdentitesDansForm($domaineCarteIdentiteUnifie)
            ->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);
        $this->ajouterSnowparksDansForm($domaineCarteIdentiteUnifie)
            ->ajouterHandiskiDansForm($domaineCarteIdentiteUnifie);
        $this->ajouterPistesDansForm($domaineCarteIdentiteUnifie)
            ->ajouterRemonteeMecanique($domaineCarteIdentiteUnifie);
//        $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
//        $this->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);

        $form = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineCarteIdentiteUnifieType', $domaineCarteIdentiteUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // dispacher les données communes
//            $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);

            $this->supprimerDomaineCarteIdentites($domaineCarteIdentiteUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();


            // ***** Gestion des Medias *****
            foreach ($request->get('domaine_carte_identite_unifie')['domaineCarteIdentites'] as $key => $domaineCarteIdentite) {
                if (!empty($domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->get($key)) && $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->get($key)->getSite()->getCrm() == 1) {
                    $domaineCarteIdentiteCrm = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->get($key);
                    if (!empty($domaineCarteIdentite['images'])) {
                        foreach ($domaineCarteIdentite['images'] as $keyImage => $image) {
                            /** @var DomaineCarteIdentiteImage $imageCrm */
                            $imageCrm = $domaineCarteIdentiteCrm->getImages()[$keyImage];
                            $imageCrm->setActif(true);
                            $imageCrm->setDomaineCarteIdentite($domaineCarteIdentiteCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
                                    $domaineCarteIdentiteSite = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($domaineCarteIdentiteSite)) {
//                                      $typeImage = (new ReflectionClass($imageCrm))->getShortName();
                                        $typeImage = (new ReflectionClass($imageCrm))->getName();

                                        /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                                        $domaineCarteIdentiteImage = new $typeImage();
                                        $domaineCarteIdentiteImage->setDomaineCarteIdentite($domaineCarteIdentiteSite);
                                        $domaineCarteIdentiteImage->setImage($imageCrm->getImage());
                                        $domaineCarteIdentiteSite->addImage($domaineCarteIdentiteImage);
                                        foreach ($imageCrm->getTraductions() as $traduction) {
                                            $traductionSite = new DomaineCarteIdentiteImageTraduction();
                                            /** @var DomaineCarteIdentiteImageTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $domaineCarteIdentiteImage->addTraduction($traductionSite);
                                        }
                                        if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
                                            $domaineCarteIdentiteImage->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($request->get('domaine_carte_identite_unifie')['domaineCarteIdentites'] as $key => $domaineCarteIdentite) {
                if (!empty($domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->get($key)) && $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->get($key)->getSite()->getCrm() == 1) {
                    $domaineCarteIdentiteCrm = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->get($key);
                    if (!empty($domaineCarteIdentite['photos'])) {
                        foreach ($domaineCarteIdentite['photos'] as $keyPhoto => $photo) {
                            /** @var DomaineCarteIdentitePhoto $photoCrm */
                            $photoCrm = $domaineCarteIdentiteCrm->getPhotos()[$keyPhoto];
                            $photoCrm->setActif(true);
                            $photoCrm->setDomaineCarteIdentite($domaineCarteIdentiteCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
                                    $domaineCarteIdentiteSite = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($domaineCarteIdentiteSite)) {
//                                      $typePhoto = (new ReflectionClass($photoCrm))->getShortName();
                                        $typePhoto = (new ReflectionClass($photoCrm))->getName();

                                        /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                                        $domaineCarteIdentitePhoto = new $typePhoto();
                                        $domaineCarteIdentitePhoto->setDomaineCarteIdentite($domaineCarteIdentiteSite);
                                        $domaineCarteIdentitePhoto->setPhoto($photoCrm->getPhoto());
                                        $domaineCarteIdentiteSite->addPhoto($domaineCarteIdentitePhoto);
                                        foreach ($photoCrm->getTraductions() as $traduction) {
                                            $traductionSite = new DomaineCarteIdentitePhotoTraduction();
                                            /** @var DomaineCarteIdentitePhotoTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $domaineCarteIdentitePhoto->addTraduction($traductionSite);
                                        }
                                        if (!empty($photo['sites']) && in_array($site->getId(), $photo['sites'])) {
                                            $domaineCarteIdentitePhoto->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($domaineCarteIdentiteUnifie);
            $em->flush();

            $this->copieVersSites($domaineCarteIdentiteUnifie);

            // add flash messages
            $this->addFlash(
                'success',
                'Le carte d\'identité du domaine  a bien été créé.'
            );

            return $this->redirectToRoute('domaine_domaineCarteIdentite_edit', array('id' => $domaineCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $domaineCarteIdentiteUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Classe les domaineCarteIdentites par classementAffichage
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function domaineCarteIdentitesSortByAffichage(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les domaineCarteIdentites en fonction de leurs ordre d'affichage
        $domaineCarteIdentites = $entity->getDomaineCarteIdentites(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $domaineCarteIdentites->getIterator();
        unset($domaineCarteIdentites);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (DomaineCarteIdentite $a, DomaineCarteIdentite $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $domaineCarteIdentites = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($domaineCarteIdentites);

        // remplacé les domaineCarteIdentites par ce nouveau tableau (une fonction 'set' a été créé dans DomaineCarteIdentite unifié)
        $entity->setDomaineCarteIdentites($domaineCarteIdentites);

        return $this;
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $domaineCarteIdentites
     */
    private function traductionsSortByLangue($domaineCarteIdentites)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var ArrayIterator $iterator */
        foreach ($domaineCarteIdentites as $domaineCarteIdentite) {
            $traductions = $domaineCarteIdentite->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (DomaineCarteIdentiteTraduction $a, DomaineCarteIdentiteTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $domaineCarteIdentite->setTraductions($traductions);
        }
    }

    /**
     * Ajouter les domaineCarteIdentites qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DomaineCarteIdentiteUnifie $entity
     */
    private function ajouterDomaineCarteIdentitesDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
                if ($domaineCarteIdentite->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaineCarteIdentite->getTraductions()->filter(function (DomaineCarteIdentiteTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DomaineCarteIdentiteTraduction();
                            $traduction->setLangue($langue);
                            $domaineCarteIdentite->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $domaineCarteIdentite = new DomaineCarteIdentite();
                $domaineCarteIdentite->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DomaineCarteIdentiteTraduction();
                    $traduction->setLangue($langue);
                    $domaineCarteIdentite->addTraduction($traduction);
                }
                $entity->addDomaineCarteIdentite($domaineCarteIdentite);
            }
        }
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterHandiskiDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $traduction */
        $em = $this->getDoctrine()->getManager();
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $handiski = !empty($domaineCarteIdentite->getHandiski()) ? $domaineCarteIdentite->getHandiski() : new Handiski();
            if (empty($handiski->getPresent())) {
                $handiski->setPresent($em->find('MondofuteChoixBundle:OuiNonNC', 3));
            }
            foreach ($domaineCarteIdentite->getTraductions() as $traduction) {
                if (!empty($handiski->getTraductions())) {
                    $langue = $traduction->getLangue();
                    if (empty($handiski->getTraductions()->filter(function (HandiskiTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first())
                    ) {
                        $handiskiTraduction = new HandiskiTraduction();
                        $handiskiTraduction->setLangue($traduction->getLangue());
                        $handiski->addTraduction($handiskiTraduction);
                    }
                }
            }
            $domaineCarteIdentite->setHandiski($handiski);
        }
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterSnowparksDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $traduction */
        $em = $this->getDoctrine()->getManager();

        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $snowpark = !empty($domaineCarteIdentite->getSnowpark()) ? $domaineCarteIdentite->getSnowpark() : new Snowpark();
            if (empty($snowpark->getPresent())) {
                $snowpark->setPresent($em->find('MondofuteChoixBundle:OuiNonNC', 3));
            }
            foreach ($domaineCarteIdentite->getTraductions() as $traduction) {
                if (!empty($snowpark->getTraductions())) {
                    $langue = $traduction->getLangue();
                    if (empty($snowpark->getTraductions()->filter(function (SnowparkTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first())
                    ) {
                        $snowparkTraduction = new SnowparkTraduction();
                        $snowparkTraduction->setLangue($traduction->getLangue());
                        $snowpark->addTraduction($snowparkTraduction);
                    }
                }
            }
            $domaineCarteIdentite->setSnowpark($snowpark);
        }
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterRemonteeMecanique(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if (empty($domaineCarteIdentite->getRemonteeMecanique())) {
                $remonteeMecanique = new RemonteeMecanique();
                $domaineCarteIdentite->setRemonteeMecanique($remonteeMecanique);
            }
        }
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterPistesDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        $em = $this->getDoctrine()->getManager();
        $typePistes = $em->getRepository(TypePiste::class)->findAll();
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if (!empty($domaineCarteIdentite->getPistes())) {
                foreach ($typePistes as $typePiste) {
                    if (empty($domaineCarteIdentite->getPistes()->filter(function (Piste $element) use ($typePiste) {
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
        }
        return $this;
    }

    /**
     * retirer de l'entité les domaineCarteIdentites qui ne doivent pas être enregistrer
     * @param DomaineCarteIdentiteUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDomaineCarteIdentites(DomaineCarteIdentiteUnifie $entity, array $sitesAEnregistrer)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if (!in_array($domaineCarteIdentite->getSite()->getId(), $sitesAEnregistrer)) {
                $domaineCarteIdentite->setDomaineCarteIdentiteUnifie(null);
                $entity->removeDomaineCarteIdentite($domaineCarteIdentite);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité domaineCarteIdentite
     * @param DomaineCarteIdentiteUnifie $entity
     */
    public function copieVersSites(DomaineCarteIdentiteUnifie $entity, $originalDomaineCarteIdentiteImages = null, $originalDomaineCarteIdentitePhotos = null)
    {
        /** @var SnowparkTraduction $snowparkTraductionSite */
        /** @var HandiskiTraduction $handiskiTraductionSite */
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $domaineCarteIdentiteTraduc */
//        Boucle sur les domaineCarteIdentites afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if ($domaineCarteIdentite->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($domaineCarteIdentite->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $domaineCarteIdentite->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
//                if (is_null(($entitySite = $emSite->getRepository(DomaineCarteIdentiteUnifie::class)->findOneById(array($entity->getId()))))) {
//                    $entitySite = new DomaineCarteIdentiteUnifie();
//                }

                $entitySite = $emSite->find(DomaineCarteIdentiteUnifie::class, $entity->getId());
                if (empty($entitySite)) {
                    $entitySite = new DomaineCarteIdentiteUnifie();
                    $entitySite->setId($entity->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entitySite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

//            Récupération de la domaineCarteIdentite sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineCarteIdentiteSite = $emSite->getRepository(DomaineCarteIdentite::class)->findOneBy(array('domaineCarteIdentiteUnifie' => $entitySite))))) {
                    $domaineCarteIdentiteSite = new DomaineCarteIdentite();
                    $domaineCarteIdentiteSite->setId($domaineCarteIdentite->getId());
                    $metadata = $emSite->getClassMetadata(get_class($domaineCarteIdentiteSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                    $entitySite->addDomaineCarteIdentite($domaineCarteIdentiteSite);
                }

//            copie des données domaineCarteIdentite
                // ***** Snowpark *****
                $snowparkSite = !empty($domaineCarteIdentiteSite->getSnowpark()) ? $domaineCarteIdentiteSite->getSnowpark() : clone $domaineCarteIdentite->getSnowpark();
                $snowparkSite->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $domaineCarteIdentite->getSnowpark()->getPresent()));
                foreach ($snowparkSite->getTraductions() as $snowparkTraductionSite) {
                    /** @var SnowparkTraduction $snowparkTraduction */
                    $snowparkTraduction = $domaineCarteIdentite->getSnowpark()->getTraductions()->filter(function (SnowparkTraduction $element) use ($snowparkTraductionSite) {
                        return $element->getLangue()->getId() == $snowparkTraductionSite->getLangue()->getId();
                    })->first();
                    $snowparkTraductionSite->setDescription($snowparkTraduction->getDescription());
                    $snowparkTraductionSite->setLangue($emSite->find(Langue::class, $snowparkTraductionSite->getLangue()));
                }
                // ***** Handiski *****
                $handiskiSite = !empty($domaineCarteIdentiteSite->getHandiski()) ? $domaineCarteIdentiteSite->getHandiski() : clone $domaineCarteIdentite->getHandiski();
                $handiskiSite->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $domaineCarteIdentite->getHandiski()->getPresent()));
                foreach ($handiskiSite->getTraductions() as $handiskiTraductionSite) {
                    /** @var HandiskiTraduction $handiskiTraduction */
                    $handiskiTraduction = $domaineCarteIdentite->getHandiski()->getTraductions()->filter(function (HandiskiTraduction $element) use ($handiskiTraductionSite) {
                        return $element->getLangue()->getId() == $handiskiTraductionSite->getLangue()->getId();
                    })->first();
                    $handiskiTraductionSite->setDescription($handiskiTraduction->getDescription());
                    $handiskiTraductionSite->setLangue($emSite->find(Langue::class, $handiskiTraductionSite->getLangue()));
                }
                // ***** Remontee mecanique *****
                $remonteeMecaniqueSite = !empty($domaineCarteIdentiteSite->getRemonteeMecanique()) ? $domaineCarteIdentiteSite->getRemonteeMecanique() : clone $domaineCarteIdentite->getRemonteeMecanique();
                $remonteeMecaniqueSite->setNombre($domaineCarteIdentite->getRemonteeMecanique()->getNombre());

                // ***** Pistes *****
                /** @var Piste $pisteSite */
                /** @var Piste $piste */

//                $pisteSites = !empty($domaineCarteIdentiteSite->getPistes()) ? $domaineCarteIdentiteSite->getPistes() : clone $domaineCarteIdentite->getPistes();
//                dump($pisteSites);die;
                $pisteSitesEmpty = !empty($domaineCarteIdentiteSite->getPistes());
                // récupérer toutes les pistes
                foreach ($domaineCarteIdentite->getPistes() as $piste) {
                    $pisteSite = null;
                    // tester si elle est présente sur le site
                    if (!empty($pisteSitesEmpty)) {
                        $pisteSite = $domaineCarteIdentiteSite->getPistes()->filter(function (Piste $element) use ($piste) {
                            return $element->getTypePiste()->getId() == $piste->getTypePiste()->getId();
                        })->first();
                    }
                    if (!empty($pisteSite)) {
                        $pisteSite->setNombre($piste->getNombre());
                    } else {
                        $pisteSite = new Piste();
                        $pisteSite->setNombre($piste->getNombre())
                            ->setDomaineCarteIdentite($domaineCarteIdentiteSite)
                            ->setTypePiste($emSite->find(TypePiste::class, $piste->getTypePiste()));
                        $domaineCarteIdentiteSite->addPiste($pisteSite);
                    }
                }

                $domaineCarteIdentiteSite
                    ->setSite($site)
                    ->setDomaineCarteIdentiteUnifie($entitySite)
//                    ->setAltitudeMini($domaineCarteIdentite->getAltitudeMini())
//                    ->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi())
//                    ->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin())
//                    ->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique())
                    ->setSnowpark($snowparkSite)
                    ->setHandiski($handiskiSite)
                    ->setRemonteeMecanique($remonteeMecaniqueSite)
                    ->setNiveauSkieur($emSite->find(NiveauSkieur::class, $domaineCarteIdentite->getNiveauSkieur()->getId()));

                if (empty($domaineCarteIdentiteSite->getAltitudeMini())) {
                    $altitudeMini = new Distance();
                    $altitudeMini->setValeur($domaineCarteIdentite->getAltitudeMini()->getValeur());
                    $altitudeMini->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getAltitudeMini()->getUnite()));
                    $domaineCarteIdentiteSite->setAltitudeMini($altitudeMini);
                } else {
                    $domaineCarteIdentiteSite->getAltitudeMini()->setValeur($domaineCarteIdentite->getAltitudeMini()->getValeur());
                    $domaineCarteIdentiteSite->getAltitudeMini()->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getAltitudeMini()->getUnite()));
                }

                if (empty($domaineCarteIdentiteSite->getAltitudeMaxi())) {
                    $altitudeMaxi = new Distance();
                    $altitudeMaxi->setValeur($domaineCarteIdentite->getAltitudeMaxi()->getValeur());
                    $altitudeMaxi->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getAltitudeMaxi()->getUnite()));
                    $domaineCarteIdentiteSite->setAltitudeMaxi($altitudeMaxi);
                } else {
                    $domaineCarteIdentiteSite->getAltitudeMaxi()->setValeur($domaineCarteIdentite->getAltitudeMaxi()->getValeur());
                    $domaineCarteIdentiteSite->getAltitudeMaxi()->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getAltitudeMaxi()->getUnite()));
                }

                if (empty($domaineCarteIdentiteSite->getKmPistesSkiAlpin())) {
                    $kmPistesSkiAlpin = new KmPistesAlpin();
                    $longueur = new Distance();
                    $kmPistesSkiAlpin->setLongueur($longueur);
                    $kmPistesSkiAlpin->getLongueur()->setValeur($domaineCarteIdentite->getKmPistesSkiAlpin()->getLongueur()->getValeur());
                    $kmPistesSkiAlpin->getLongueur()->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getKmPistesSkiAlpin()->getLongueur()->getUnite()));
                    $domaineCarteIdentiteSite->setKmPistesSkiAlpin($kmPistesSkiAlpin);
                } else {
                    $domaineCarteIdentiteSite->getKmPistesSkiAlpin()->getLongueur()->setValeur($domaineCarteIdentite->getKmPistesSkiAlpin()->getLongueur()->getValeur());
                    $domaineCarteIdentiteSite->getKmPistesSkiAlpin()->getLongueur()->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getKmPistesSkiAlpin()->getLongueur()->getUnite()));
                }

                if (empty($domaineCarteIdentiteSite->getKmPistesSkiNordique())) {
                    $kmPistesSkiNordique = new KmPistesNordique();
                    $longueur = new Distance();
                    $kmPistesSkiNordique->setLongueur($longueur);
                    $kmPistesSkiNordique->getLongueur()->setValeur($domaineCarteIdentite->getKmPistesSkiNordique()->getLongueur()->getValeur());
                    $kmPistesSkiNordique->getLongueur()->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getKmPistesSkiNordique()->getLongueur()->getUnite()));
                    $domaineCarteIdentiteSite->setKmPistesSkiNordique($kmPistesSkiNordique);
                } else {
                    $domaineCarteIdentiteSite->getKmPistesSkiNordique()->getLongueur()->setValeur($domaineCarteIdentite->getKmPistesSkiNordique()->getLongueur()->getValeur());
                    $domaineCarteIdentiteSite->getKmPistesSkiNordique()->getLongueur()->setUnite($emSite->find(UniteDistance::class, $domaineCarteIdentite->getKmPistesSkiNordique()->getLongueur()->getUnite()));
                }

//            Gestion des traductions
                foreach ($domaineCarteIdentite->getTraductions() as $domaineCarteIdentiteTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $domaineCarteIdentiteTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($domaineCarteIdentiteTraducSite = $emSite->getRepository(DomaineCarteIdentiteTraduction::class)->findOneBy(array(
                        'domaineCarteIdentite' => $domaineCarteIdentiteSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $domaineCarteIdentiteTraducSite = new DomaineCarteIdentiteTraduction();
                    }

//                copie des données traductions
                    $domaineCarteIdentiteTraducSite->setLangue($langue)
                        ->setAccroche($domaineCarteIdentiteTraduc->getAccroche())
                        ->setDescription($domaineCarteIdentiteTraduc->getDescription())
                        ->setDomaineCarteIdentite($domaineCarteIdentiteSite);

//                ajout a la collection de traduction de la domaineCarteIdentite distante
                    $domaineCarteIdentiteSite->addTraduction($domaineCarteIdentiteTraducSite);
                }


                // ********** GESTION DES MEDIAS **********

                $domaineCarteIdentiteImages = $domaineCarteIdentite->getImages(); // ce sont les hebegementImages ajouté

                // si il y a des Medias pour l'domaineCarteIdentite de référence
                if (!empty($domaineCarteIdentiteImages) && !$domaineCarteIdentiteImages->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($domaineCarteIdentiteSite->getImages()) && !$domaineCarteIdentiteSite->getImages()->isEmpty()) {
                        // on ajoute les hébergementImages dans un tableau afin de travailler dessus
                        $domaineCarteIdentiteImageSites = new ArrayCollection();
                        foreach ($domaineCarteIdentiteSite->getImages() as $domaineCarteIdentiteimageSite) {
                            $domaineCarteIdentiteImageSites->add($domaineCarteIdentiteimageSite);
                        }
                        // on parcourt les hébergmeentImages de la base
                        /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                        foreach ($domaineCarteIdentiteImages as $domaineCarteIdentiteImage) {
                            // *** récupération de l'hébergementImage correspondant sur la bdd distante ***
                            // récupérer l'domaineCarteIdentiteImage original correspondant sur le crm
                            /** @var ArrayCollection $originalDomaineCarteIdentiteImages */
                            $originalDomaineCarteIdentiteImage = $originalDomaineCarteIdentiteImages->filter(function (DomaineCarteIdentiteImage $element) use ($domaineCarteIdentiteImage) {
                                return $element->getImage() == $domaineCarteIdentiteImage->getImage();
                            })->first();
                            unset($domaineCarteIdentiteImageSite);
                            if ($originalDomaineCarteIdentiteImage !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalDomaineCarteIdentiteImages as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalImage = $tab->indexOf($originalDomaineCarteIdentiteImage);

                                $domaineCarteIdentiteImageSite = $domaineCarteIdentiteImageSites->get($keyoriginalImage);
                            }
                            // *** fin récupération de l'hébergementImage correspondant sur la bdd distante ***

                            // si l'domaineCarteIdentiteImage existe sur la bdd distante, on va le modifier
                            /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
                            if (!empty($domaineCarteIdentiteImageSite)) {
                                // Si le image a été modifié
                                // (que le crm_ref_id est différent de de l'id du image de l'domaineCarteIdentiteImage du crm)
                                if ($domaineCarteIdentiteImageSite->getImage()->getMetadataValue('crm_ref_id') != $domaineCarteIdentiteImage->getImage()->getId()) {
                                    $cloneImage = clone $domaineCarteIdentiteImage->getImage();
                                    $cloneImage->setMetadataValue('crm_ref_id', $domaineCarteIdentiteImage->getImage()->getId());
                                    $cloneImage->setContext('domaine_carte_identite_image_' . $domaineCarteIdentite->getSite()->getLibelle());

                                    // on supprime l'ancien image
                                    $emSite->remove($domaineCarteIdentiteImageSite->getImage());

                                    $domaineCarteIdentiteImageSite->setImage($cloneImage);
                                }

                                $domaineCarteIdentiteImageSite->setActif($domaineCarteIdentiteImage->getActif());

                                // on parcourt les traductions
                                /** @var DomaineCarteIdentiteImageTraduction $traduction */
                                foreach ($domaineCarteIdentiteImage->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var DomaineCarteIdentiteImageTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $domaineCarteIdentiteImageSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (DomaineCarteIdentiteImageTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new DomaineCarteIdentiteImageTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $domaineCarteIdentiteImageSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createDomaineCarteIdentiteImage($domaineCarteIdentiteImage, $domaineCarteIdentiteSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'domaineCarteIdentite de référence
                        /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                        foreach ($domaineCarteIdentiteImages as $domaineCarteIdentiteImage) {
                            $this->createDomaineCarteIdentiteImage($domaineCarteIdentiteImage, $domaineCarteIdentiteSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($domaineCarteIdentiteImageSites)) {
                        /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
                        foreach ($domaineCarteIdentiteImageSites as $domaineCarteIdentiteImageSite) {
                            $domaineCarteIdentiteImageSite->setDomaineCarteIdentite(null);
                            $emSite->remove($domaineCarteIdentiteImageSite->getImage());
                            $emSite->remove($domaineCarteIdentiteImageSite);
                        }
                    }
                }


                $domaineCarteIdentitePhotos = $domaineCarteIdentite->getPhotos(); // ce sont les hebegementPhotos ajouté

                // si il y a des Medias pour l'domaineCarteIdentite de référence
                if (!empty($domaineCarteIdentitePhotos) && !$domaineCarteIdentitePhotos->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($domaineCarteIdentiteSite->getPhotos()) && !$domaineCarteIdentiteSite->getPhotos()->isEmpty()) {
                        // on ajoute les hébergementPhotos dans un tableau afin de travailler dessus
                        $domaineCarteIdentitePhotoSites = new ArrayCollection();
                        foreach ($domaineCarteIdentiteSite->getPhotos() as $domaineCarteIdentitephotoSite) {
                            $domaineCarteIdentitePhotoSites->add($domaineCarteIdentitephotoSite);
                        }
                        // on parcourt les hébergmeentPhotos de la base
                        /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                        foreach ($domaineCarteIdentitePhotos as $domaineCarteIdentitePhoto) {
                            // *** récupération de l'hébergementPhoto correspondant sur la bdd distante ***
                            // récupérer l'domaineCarteIdentitePhoto original correspondant sur le crm
                            /** @var ArrayCollection $originalDomaineCarteIdentitePhotos */
                            $originalDomaineCarteIdentitePhoto = $originalDomaineCarteIdentitePhotos->filter(function (DomaineCarteIdentitePhoto $element) use ($domaineCarteIdentitePhoto) {
                                return $element->getPhoto() == $domaineCarteIdentitePhoto->getPhoto();
                            })->first();
                            unset($domaineCarteIdentitePhotoSite);
                            if ($originalDomaineCarteIdentitePhoto !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalDomaineCarteIdentitePhotos as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalPhoto = $tab->indexOf($originalDomaineCarteIdentitePhoto);

                                $domaineCarteIdentitePhotoSite = $domaineCarteIdentitePhotoSites->get($keyoriginalPhoto);
                            }
                            // *** fin récupération de l'hébergementPhoto correspondant sur la bdd distante ***

                            // si l'domaineCarteIdentitePhoto existe sur la bdd distante, on va le modifier
                            /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
                            if (!empty($domaineCarteIdentitePhotoSite)) {
                                // Si le photo a été modifié
                                // (que le crm_ref_id est différent de de l'id du photo de l'domaineCarteIdentitePhoto du crm)
                                if ($domaineCarteIdentitePhotoSite->getPhoto()->getMetadataValue('crm_ref_id') != $domaineCarteIdentitePhoto->getPhoto()->getId()) {
                                    $clonePhoto = clone $domaineCarteIdentitePhoto->getPhoto();
                                    $clonePhoto->setMetadataValue('crm_ref_id', $domaineCarteIdentitePhoto->getPhoto()->getId());
                                    $clonePhoto->setContext('domaine_carte_identite_photo_' . $domaineCarteIdentite->getSite()->getLibelle());

                                    // on supprime l'ancien photo
                                    $emSite->remove($domaineCarteIdentitePhotoSite->getPhoto());

                                    $domaineCarteIdentitePhotoSite->setPhoto($clonePhoto);
                                }

                                $domaineCarteIdentitePhotoSite->setActif($domaineCarteIdentitePhoto->getActif());

                                // on parcourt les traductions
                                /** @var DomaineCarteIdentitePhotoTraduction $traduction */
                                foreach ($domaineCarteIdentitePhoto->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var DomaineCarteIdentitePhotoTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $domaineCarteIdentitePhotoSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (DomaineCarteIdentitePhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new DomaineCarteIdentitePhotoTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $domaineCarteIdentitePhotoSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createDomaineCarteIdentitePhoto($domaineCarteIdentitePhoto, $domaineCarteIdentiteSite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'domaineCarteIdentite de référence
                        /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                        foreach ($domaineCarteIdentitePhotos as $domaineCarteIdentitePhoto) {
                            $this->createDomaineCarteIdentitePhoto($domaineCarteIdentitePhoto, $domaineCarteIdentiteSite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($domaineCarteIdentitePhotoSites)) {
                        /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
                        foreach ($domaineCarteIdentitePhotoSites as $domaineCarteIdentitePhotoSite) {
                            $domaineCarteIdentitePhotoSite->setDomaineCarteIdentite(null);
                            $emSite->remove($domaineCarteIdentitePhotoSite->getPhoto());
                            $emSite->remove($domaineCarteIdentitePhotoSite);
                        }
                    }
                }

                // ********** FIN GESTION DES MEDIAS **********

                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterDomaineCarteIdentiteUnifieSiteDistant($entity->getId(), $entity->getDomaineCarteIdentites());
    }


    /**
     * Création d'un nouveau domaineCarteIdentiteImage
     * @param DomaineCarteIdentiteImage $domaineCarteIdentiteImage
     * @param DomaineCarteIdentite $domaineCarteIdentiteSite
     * @param EntityManager $emSite
     */
    private function createDomaineCarteIdentiteImage(DomaineCarteIdentiteImage $domaineCarteIdentiteImage, DomaineCarteIdentite $domaineCarteIdentiteSite, EntityManager $emSite)
    {
        /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
        // on récupère la classe correspondant au image (photo ou video)
        $typeImage = (new ReflectionClass($domaineCarteIdentiteImage))->getName();
        // on cré un nouveau DomaineCarteIdentiteImage on fonction du type
        $domaineCarteIdentiteImageSite = new $typeImage();
        $domaineCarteIdentiteImageSite->setDomaineCarteIdentite($domaineCarteIdentiteSite);
        $domaineCarteIdentiteImageSite->setActif($domaineCarteIdentiteImage->getActif());
        // on lui clone l'image
        $cloneImage = clone $domaineCarteIdentiteImage->getImage();

        // **** récupération du image physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneImage->getProviderName());
        $provider->getReferenceImage($cloneImage);

//        $cloneImage->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneImage));
        $cloneImage->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneImage));

        $cloneImage->setProviderReference($domaineCarteIdentiteImage->getImage()->getProviderReference());
        $cloneImage->setName($domaineCarteIdentiteImage->getImage()->getName());
        // **** fin récupération du image physique ****

        // on donne au nouveau image, le context correspondant en fonction du site
        $cloneImage->setContext('domaine_carte_identite_image_' . $domaineCarteIdentiteSite->getSite()->getLibelle());
        // on lui attache l'id de référence du image correspondant sur la bdd crm
        $cloneImage->setMetadataValue('crm_ref_id', $domaineCarteIdentiteImage->getImage()->getId());

        $domaineCarteIdentiteImageSite->setImage($cloneImage);

        $domaineCarteIdentiteSite->addImage($domaineCarteIdentiteImageSite);
        // on ajoute les traductions correspondante
        foreach ($domaineCarteIdentiteImage->getTraductions() as $traduction) {
            $traductionSite = new DomaineCarteIdentiteImageTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $domaineCarteIdentiteImageSite->addTraduction($traductionSite);
        }
    }


    /**
     * Création d'un nouveau domaineCarteIdentitePhoto
     * @param DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto
     * @param DomaineCarteIdentite $domaineCarteIdentiteSite
     * @param EntityManager $emSite
     */
    private function createDomaineCarteIdentitePhoto(DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto, DomaineCarteIdentite $domaineCarteIdentiteSite, EntityManager $emSite)
    {
        /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
        // on récupère la classe correspondant au photo (photo ou video)
        $typePhoto = (new ReflectionClass($domaineCarteIdentitePhoto))->getName();
        // on cré un nouveau DomaineCarteIdentitePhoto on fonction du type
        $domaineCarteIdentitePhotoSite = new $typePhoto();
        $domaineCarteIdentitePhotoSite->setDomaineCarteIdentite($domaineCarteIdentiteSite);
        $domaineCarteIdentitePhotoSite->setActif($domaineCarteIdentitePhoto->getActif());
        // on lui clone l'photo
        $clonePhoto = clone $domaineCarteIdentitePhoto->getPhoto();

        // **** récupération du photo physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($clonePhoto->getProviderName());
        $provider->getReferenceImage($clonePhoto);

//        $clonePhoto->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($clonePhoto));
        $clonePhoto->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($clonePhoto));

        $clonePhoto->setProviderReference($domaineCarteIdentitePhoto->getPhoto()->getProviderReference());
        $clonePhoto->setName($domaineCarteIdentitePhoto->getPhoto()->getName());
        // **** fin récupération du photo physique ****

        // on donne au nouveau photo, le context correspondant en fonction du site
        $clonePhoto->setContext('domaine_carte_identite_photo_' . $domaineCarteIdentiteSite->getSite()->getLibelle());
        // on lui attache l'id de référence du photo correspondant sur la bdd crm
        $clonePhoto->setMetadataValue('crm_ref_id', $domaineCarteIdentitePhoto->getPhoto()->getId());

        $domaineCarteIdentitePhotoSite->setPhoto($clonePhoto);

        $domaineCarteIdentiteSite->addPhoto($domaineCarteIdentitePhotoSite);
        // on ajoute les traductions correspondante
        foreach ($domaineCarteIdentitePhoto->getTraductions() as $traduction) {
            $traductionSite = new DomaineCarteIdentitePhotoTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $domaineCarteIdentitePhotoSite->addTraduction($traductionSite);
        }
    }


    /**
     * @param $idUnifie
     * @param Collection $domaineCarteIdentites
     */
    private function ajouterDomaineCarteIdentiteUnifieSiteDistant($idUnifie, Collection $domaineCarteIdentites)
    {
        /** @var Site $site */
        /** @var ArrayCollection $domaineCarteIdentites */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($domaineCarteIdentites->matching($criteres)) == 0 && (empty($emSite->getRepository(DomaineCarteIdentiteUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DomaineCarteIdentiteUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Creates a new DomaineCarteIdentiteUnifie entity.
     *
     */
    public function newEntity(Domaine $domaine, Request $request)
    {

        /** @var Domaine $domaine */
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));

        $domaineCarteIdentiteUnifie = new  DomaineCarteIdentiteUnifie();
        $domaineCarteIdentite = $domaine->getDomaineCarteIdentite();
        $domaineCarteIdentiteUnifie->addDomaineCarteIdentite($domaineCarteIdentite);


//        // ***** Gestion des Medias *****
//        if ($domaine->getSite()->getCrm()) {
//            foreach ($request->get('domaine_unifie')['domaines'] as $domaineRequest) {
//                if (!empty($domaineRequest['domaineCarteIdentite']['images'])) {
//                    $domaineCarteIdentiteCrm = $domaine->getDomaineCarteIdentite();
//                    foreach ($domaineRequest['domaineCarteIdentite']['images'] as $keyImage => $image) {
//                        /** @var DomaineCarteIdentiteImage $imageCrm */
//                        $imageCrm = $domaineCarteIdentiteCrm->getImages()[$keyImage];
//                        $imageCrm->setActif(true);
//                        $imageCrm->setDomaineCarteIdentite($domaineCarteIdentiteCrm);
//                        foreach ($sites as $site) {
//                            if ($site->getCrm() == 0) {
//                                /** @var Domaine $domaineSite */
//                                $domaineSite = $domaine->getDomaineUnifie()->getDomaines()->filter(function (Domaine $element) use ($site) {
//                                    return $element->getSite() == $site;
//                                })->first();
//                                /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
//                                $domaineCarteIdentiteSite = $domaineSite->getDomaineCarteIdentite();
//                                if (!empty($domaineCarteIdentiteSite)) {
//                                    $typeImage = (new ReflectionClass($imageCrm))->getName();
//
//                                    /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
//                                    $domaineCarteIdentiteImage = new $typeImage();
//                                    $domaineCarteIdentiteImage->setDomaineCarteIdentite($domaineCarteIdentiteSite);
//                                    $domaineCarteIdentiteImage->setImage($imageCrm->getImage());
//                                    $domaineCarteIdentiteSite->addImage($domaineCarteIdentiteImage);
//                                    foreach ($imageCrm->getTraductions() as $traduction) {
//                                        $traductionSite = new DomaineCarteIdentiteImageTraduction();
//                                        /** @var DomaineCarteIdentiteImageTraduction $traduction */
//                                        $traductionSite->setLibelle($traduction->getLibelle());
//                                        $traductionSite->setLangue($traduction->getLangue());
//                                        $domaineCarteIdentiteImage->addTraduction($traductionSite);
//                                    }
//                                    if (!empty($image['sites']) && in_array($site->getId(), $image['sites'])) {
//                                        $domaineCarteIdentiteImage->setActif(true);
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//
//        // ***** Fin Gestion des Medias *****

        $em->persist($domaineCarteIdentiteUnifie);

        return $domaineCarteIdentiteUnifie;
    }

    /**
     * Finds and displays a DomaineCarteIdentiteUnifie entity.
     *
     */
    public function showAction(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        $deleteForm = $this->createDeleteForm($domaineCarteIdentiteUnifie);

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/show.html.twig', array(
            'domaineCarteIdentiteUnifie' => $domaineCarteIdentiteUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DomaineCarteIdentiteUnifie entity.
     *
     * @param DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie The DomaineCarteIdentiteUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('domaine_domaineCarteIdentite_delete', array('id' => $domaineCarteIdentiteUnifie->getId())))
            ->add('delete', SubmitType::class, array('label' => 'Supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DomaineCarteIdentiteUnifie entity.
     *
     */
    public function editAction(Request $request, DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {
//                if (empty($domaineCarteIdentite->getSite()->getCrm())) {
                array_push($sitesAEnregistrer, $domaineCarteIdentite->getSite()->getId());
//                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }


        $originalDomaineCarteIdentites = new ArrayCollection();
        $originalDomaineCarteIdentiteImages = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $originalDomaineCarteIdentitePhotos = new ArrayCollection();
        $originalPhotos = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $originalDomaineCarteIdentites->add($domaineCarteIdentite);
            // si l'domaineCarteIdentite est celui du CRM
            if ($domaineCarteIdentite->getSite()->getCrm() == 1) {
                // on parcourt les domaineCarteIdentiteImage pour les comparer ensuite
                /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                foreach ($domaineCarteIdentite->getImages() as $domaineCarteIdentiteImage) {
                    // on ajoute les image dans la collection de sauvegarde
                    $originalDomaineCarteIdentiteImages->add($domaineCarteIdentiteImage);
                    $originalImages->add($domaineCarteIdentiteImage->getImage());
                }
                // on parcourt les domaineCarteIdentitePhoto pour les comparer ensuite
                /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                foreach ($domaineCarteIdentite->getPhotos() as $domaineCarteIdentitePhoto) {
                    // on ajoute les photo dans la collection de sauvegarde
                    $originalDomaineCarteIdentitePhotos->add($domaineCarteIdentitePhoto);
                    $originalPhotos->add($domaineCarteIdentitePhoto->getPhoto());
                }
            }
        }

        $this->ajouterDomaineCarteIdentitesDansForm($domaineCarteIdentiteUnifie)
//        $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
            ->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);
        $this->ajouterSnowparksDansForm($domaineCarteIdentiteUnifie)
            ->ajouterHandiskiDansForm($domaineCarteIdentiteUnifie);
        $this->ajouterPistesDansForm($domaineCarteIdentiteUnifie)
            ->ajouterRemonteeMecanique($domaineCarteIdentiteUnifie);

        $deleteForm = $this->createDeleteForm($domaineCarteIdentiteUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineCarteIdentiteUnifieType',
            $domaineCarteIdentiteUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // ************* suppression images *************
            // ** CAS OU L'ON SUPPRIME UN "DomaineCarteIdentite IMAGE" **
            // on récupère les DomaineCarteIdentiteImage de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var DomaineCarteIdentite $domaineCarteIdentiteCrm */
            $domaineCarteIdentiteCrm = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $domaineCarteIdentiteSites = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newDomaineCarteIdentiteImages = new ArrayCollection();
            foreach ($domaineCarteIdentiteCrm->getImages() as $domaineCarteIdentiteImage) {
                $newDomaineCarteIdentiteImages->add($domaineCarteIdentiteImage);
            }
            /** @var DomaineCarteIdentiteImage $originalDomaineCarteIdentiteImage */
            foreach ($originalDomaineCarteIdentiteImages as $key => $originalDomaineCarteIdentiteImage) {

                if (false === $newDomaineCarteIdentiteImages->contains($originalDomaineCarteIdentiteImage)) {
                    $originalDomaineCarteIdentiteImage->setDomaineCarteIdentite(null);
                    $em->remove($originalDomaineCarteIdentiteImage->getImage());
                    $em->remove($originalDomaineCarteIdentiteImage);
                    // on doit supprimer l'hébergementImage des autres sites
                    // on parcourt les domaineCarteIdentite des sites
                    /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
                    foreach ($domaineCarteIdentiteSites as $domaineCarteIdentiteSite) {
                        $domaineCarteIdentiteImageSite = $em->getRepository(DomaineCarteIdentiteImage::class)->findOneBy(
                            array(
                                'domaineCarteIdentite' => $domaineCarteIdentiteSite,
                                'image' => $originalDomaineCarteIdentiteImage->getImage()
                            ));
                        if (!empty($domaineCarteIdentiteImageSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($domaineCarteIdentiteImageSite->getDomaineCarteIdentite()->getSite()->getLibelle());
                            $domaineCarteIdentiteSite = $emSite->getRepository(DomaineCarteIdentite::class)->findOneBy(
                                array(
                                    'domaineCarteIdentiteUnifie' => $domaineCarteIdentiteImageSite->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie()
                                ));
                            $domaineCarteIdentiteImageSiteSites = new ArrayCollection($emSite->getRepository(DomaineCarteIdentiteImage::class)->findBy(
                                array(
                                    'domaineCarteIdentite' => $domaineCarteIdentiteSite
                                ))
                            );
                            $domaineCarteIdentiteImageSiteSite = $domaineCarteIdentiteImageSiteSites->filter(function (DomaineCarteIdentiteImage $element)
                            use ($domaineCarteIdentiteImageSite) {
//                            return $element->getImage()->getProviderReference() == $domaineCarteIdentiteImageSite->getImage()->getProviderReference();
                                return $element->getImage()->getMetadataValue('crm_ref_id') == $domaineCarteIdentiteImageSite->getImage()->getId();
                            })->first();
                            if (!empty($domaineCarteIdentiteImageSiteSite)) {
                                $emSite->remove($domaineCarteIdentiteImageSiteSite->getImage());
                                $domaineCarteIdentiteImageSiteSite->setDomaineCarteIdentite(null);
                                $emSite->remove($domaineCarteIdentiteImageSiteSite);
                                $emSite->flush();
                            }
                            $domaineCarteIdentiteImageSite->setDomaineCarteIdentite(null);
                            $em->remove($domaineCarteIdentiteImageSite->getImage());
                            $em->remove($domaineCarteIdentiteImageSite);
                        }
                    }
                }
            }
            // ************* fin suppression images *************


            // ************* suppression photos *************
            // ** CAS OU L'ON SUPPRIME UN "DomaineCarteIdentite PHOTO" **
            // on récupère les DomaineCarteIdentitePhoto de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var DomaineCarteIdentite $domaineCarteIdentiteCrm */
            $domaineCarteIdentiteCrm = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $domaineCarteIdentiteSites = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newDomaineCarteIdentitePhotos = new ArrayCollection();
            foreach ($domaineCarteIdentiteCrm->getPhotos() as $domaineCarteIdentitePhoto) {
                $newDomaineCarteIdentitePhotos->add($domaineCarteIdentitePhoto);
            }
            /** @var DomaineCarteIdentitePhoto $originalDomaineCarteIdentitePhoto */
            foreach ($originalDomaineCarteIdentitePhotos as $key => $originalDomaineCarteIdentitePhoto) {

                if (false === $newDomaineCarteIdentitePhotos->contains($originalDomaineCarteIdentitePhoto)) {
                    $originalDomaineCarteIdentitePhoto->setDomaineCarteIdentite(null);
                    $em->remove($originalDomaineCarteIdentitePhoto->getPhoto());
                    $em->remove($originalDomaineCarteIdentitePhoto);
                    // on doit supprimer l'hébergementPhoto des autres sites
                    // on parcourt les domaineCarteIdentite des sites
                    /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
                    foreach ($domaineCarteIdentiteSites as $domaineCarteIdentiteSite) {
                        $domaineCarteIdentitePhotoSite = $em->getRepository(DomaineCarteIdentitePhoto::class)->findOneBy(
                            array(
                                'domaineCarteIdentite' => $domaineCarteIdentiteSite,
                                'photo' => $originalDomaineCarteIdentitePhoto->getPhoto()
                            ));
                        if (!empty($domaineCarteIdentitePhotoSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($domaineCarteIdentitePhotoSite->getDomaineCarteIdentite()->getSite()->getLibelle());
                            $domaineCarteIdentiteSite = $emSite->getRepository(DomaineCarteIdentite::class)->findOneBy(
                                array(
                                    'domaineCarteIdentiteUnifie' => $domaineCarteIdentitePhotoSite->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie()
                                ));
                            $domaineCarteIdentitePhotoSiteSites = new ArrayCollection($emSite->getRepository(DomaineCarteIdentitePhoto::class)->findBy(
                                array(
                                    'domaineCarteIdentite' => $domaineCarteIdentiteSite
                                ))
                            );
                            $domaineCarteIdentitePhotoSiteSite = $domaineCarteIdentitePhotoSiteSites->filter(function (DomaineCarteIdentitePhoto $element)
                            use ($domaineCarteIdentitePhotoSite) {
//                            return $element->getPhoto()->getProviderReference() == $domaineCarteIdentitePhotoSite->getPhoto()->getProviderReference();
                                return $element->getPhoto()->getMetadataValue('crm_ref_id') == $domaineCarteIdentitePhotoSite->getPhoto()->getId();
                            })->first();
                            if (!empty($domaineCarteIdentitePhotoSiteSite)) {
                                $emSite->remove($domaineCarteIdentitePhotoSiteSite->getPhoto());
                                $domaineCarteIdentitePhotoSiteSite->setDomaineCarteIdentite(null);
                                $emSite->remove($domaineCarteIdentitePhotoSiteSite);
                                $emSite->flush();
                            }
                            $domaineCarteIdentitePhotoSite->setDomaineCarteIdentite(null);
                            $em->remove($domaineCarteIdentitePhotoSite->getPhoto());
                            $em->remove($domaineCarteIdentitePhotoSite);
                        }
                    }
                }
            }
            // ************* fin suppression photos *************

            $this->supprimerDomaineCarteIdentites($domaineCarteIdentiteUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la domaineCarteIdentite et domaineCarteIdentiteUnifie
            foreach ($originalDomaineCarteIdentites as $domaineCarteIdentite) {
                if (!$domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->contains($domaineCarteIdentite)) {

                    //  suppression de la domaineCarteIdentite sur le site
                    $emSite = $this->getDoctrine()->getManager($domaineCarteIdentite->getSite()->getLibelle());
                    $entitySite = $emSite->find(DomaineCarteIdentiteUnifie::class, $domaineCarteIdentiteUnifie->getId());
                    $domaineCarteIdentiteSite = $entitySite->getDomaineCarteIdentites()->first();

                    /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
                    if (!empty($domaineCarteIdentiteSite->getImages())) {
                        foreach ($domaineCarteIdentiteSite->getImages() as $domaineCarteIdentiteImageSite) {
                            $domaineCarteIdentiteSite->removeImage($domaineCarteIdentiteImageSite);
//                                        $domaineCarteIdentiteImageSite->setDomaineCarteIdentite(null);
//                                        $domaineCarteIdentiteImageSite->setImage(null);
                            $emSite->remove($domaineCarteIdentiteImageSite);
                            $emSite->remove($domaineCarteIdentiteImageSite->getImage());
                        }
                        $emSite->flush();
                    }
                    /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
                    if (!empty($domaineCarteIdentiteSite->getPhotos())) {
                        foreach ($domaineCarteIdentiteSite->getPhotos() as $domaineCarteIdentitePhotoSite) {
                            $domaineCarteIdentiteSite->removePhoto($domaineCarteIdentitePhotoSite);
//                                        $domaineCarteIdentitePhotoSite->setDomaineCarteIdentite(null);
//                                        $domaineCarteIdentitePhotoSite->setPhoto(null);
                            $emSite->remove($domaineCarteIdentitePhotoSite);
                            $emSite->remove($domaineCarteIdentitePhotoSite->getPhoto());
                        }
                        $emSite->flush();
                    }


                    $emSite->remove($domaineCarteIdentiteSite);
                    $emSite->flush();
                    $domaineCarteIdentite->setDomaineCarteIdentiteUnifie(null);


                    // *** suppression des domaineCarteIdentiteImages de l'domaineCarteIdentite à supprimer ***
                    /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                    $domaineCarteIdentiteImageSites = $em->getRepository(DomaineCarteIdentiteImage::class)->findBy(array('domaineCarteIdentite' => $domaineCarteIdentite));
                    if (!empty($domaineCarteIdentiteImageSites)) {
                        foreach ($domaineCarteIdentiteImageSites as $domaineCarteIdentiteImage) {
                            $domaineCarteIdentiteImage->setImage(null);
                            $domaineCarteIdentiteImage->setDomaineCarteIdentite(null);
                            $em->remove($domaineCarteIdentiteImage);
                        }
                        $em->flush();
                    }
                    // *** fin suppression des domaineCarteIdentiteImages de l'domaineCarteIdentite à supprimer ***
                    // *** suppression des domaineCarteIdentitePhotos de l'domaineCarteIdentite à supprimer ***
                    /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                    $domaineCarteIdentitePhotoSites = $em->getRepository(DomaineCarteIdentitePhoto::class)->findBy(array('domaineCarteIdentite' => $domaineCarteIdentite));
                    if (!empty($domaineCarteIdentitePhotoSites)) {
                        foreach ($domaineCarteIdentitePhotoSites as $domaineCarteIdentitePhoto) {
                            $domaineCarteIdentitePhoto->setPhoto(null);
                            $domaineCarteIdentitePhoto->setDomaineCarteIdentite(null);
                            $em->remove($domaineCarteIdentitePhoto);
                        }
                        $em->flush();
                    }
                    // *** fin suppression des domaineCarteIdentitePhotos de l'domaineCarteIdentite à supprimer ***


                    $em->remove($domaineCarteIdentite);
                }
            }


            // ***** Gestion des Medias *****
//            dump($domaineCarteIdentiteUnifie);die;
            // CAS D'UN NOUVEAU 'DomaineCarteIdentite IMAGE' OU DE MODIFICATION D'UN "DomaineCarteIdentite IMAGE"
            /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
            // tableau pour la suppression des anciens images
            $imageToRemoveCollection = new ArrayCollection();
            $keyCrm = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->indexOf($domaineCarteIdentiteCrm);
            // on parcourt les domaineCarteIdentiteImages de l'domaineCarteIdentite crm
            foreach ($domaineCarteIdentiteCrm->getImages() as $key => $domaineCarteIdentiteImage) {
                // on active le nouveau domaineCarteIdentiteImage (CRM) => il doit être toujours actif
                $domaineCarteIdentiteImage->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementImage pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
                        $domaineCarteIdentiteSite = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($domaineCarteIdentiteSite)) {
                            // on réinitialise la variable
                            unset($domaineCarteIdentiteImageSite);
                            // s'il ne s'agit pas d'un nouveau domaineCarteIdentiteImage
                            if (!empty($domaineCarteIdentiteImage->getId())) {
                                // on récupère l'domaineCarteIdentiteImage pour le modifier
                                $domaineCarteIdentiteImageSite = $em->getRepository(DomaineCarteIdentiteImage::class)->findOneBy(array('domaineCarteIdentite' => $domaineCarteIdentiteSite, 'image' => $originalImages->get($key)));
                            }
                            // si l'domaineCarteIdentiteImage est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($domaineCarteIdentiteImage->getId()) || empty($domaineCarteIdentiteImageSite)) {
                                // on récupère la classe correspondant au image (photo ou video)
                                $typeImage = (new ReflectionClass($domaineCarteIdentiteImage))->getName();
                                // on créé un nouveau DomaineCarteIdentiteImage on fonction du type
                                /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
                                $domaineCarteIdentiteImageSite = new $typeImage();
                                $domaineCarteIdentiteImageSite->setDomaineCarteIdentite($domaineCarteIdentiteSite);
                            }
                            // si l'hébergemenent image existe déjà pour le site
                            if (!empty($domaineCarteIdentiteImageSite)) {
                                if ($domaineCarteIdentiteImageSite->getImage() != $domaineCarteIdentiteImage->getImage()) {
//                                    // si l'hébergementImageSite avait déjà un image
//                                    if (!empty($domaineCarteIdentiteImageSite->getImage()) && !$imageToRemoveCollection->contains($domaineCarteIdentiteImageSite->getImage()))
//                                    {
//                                        // on met l'ancien image dans un tableau afin de le supprimer plus tard
//                                        $imageToRemoveCollection->add($domaineCarteIdentiteImageSite->getImage());
//                                    }
                                    // on met le nouveau image
                                    $domaineCarteIdentiteImageSite->setImage($domaineCarteIdentiteImage->getImage());
                                }
                                $domaineCarteIdentiteSite->addImage($domaineCarteIdentiteImageSite);

                                /** @var DomaineCarteIdentiteImageTraduction $traduction */
                                foreach ($domaineCarteIdentiteImage->getTraductions() as $traduction) {
                                    /** @var DomaineCarteIdentiteImageTraduction $traductionSite */
                                    $traductionSites = $domaineCarteIdentiteImageSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (DomaineCarteIdentiteImageTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new DomaineCarteIdentiteImageTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $domaineCarteIdentiteImageSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementImage doit être actif sur le site ou non
                                if (!empty($request->get('domaineCarteIdentite_unifie')['domaineCarteIdentites'][$keyCrm]['images'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('domaineCarteIdentite_unifie')['domaineCarteIdentites'][$keyCrm]['images'][$key]['sites'])
                                ) {
                                    $domaineCarteIdentiteImageSite->setActif(true);
                                } else {
                                    $domaineCarteIdentiteImageSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'domaineCarteIdentiteImage CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($domaineCarteIdentiteImage->getImage()->getId()) && !empty($originalImages->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $imageToRemoveCollection->add($originalImages->get($key));
                    }
                }
            }


            // CAS D'UN NOUVEAU 'DomaineCarteIdentite PHOTO' OU DE MODIFICATION D'UN "DomaineCarteIdentite PHOTO"
            /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
            // tableau pour la suppression des anciens photos
            $photoToRemoveCollection = new ArrayCollection();
            $keyCrm = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->indexOf($domaineCarteIdentiteCrm);
            // on parcourt les domaineCarteIdentitePhotos de l'domaineCarteIdentite crm
            foreach ($domaineCarteIdentiteCrm->getPhotos() as $key => $domaineCarteIdentitePhoto) {
                // on active le nouveau domaineCarteIdentitePhoto (CRM) => il doit être toujours actif
                $domaineCarteIdentitePhoto->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementPhoto pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
                        $domaineCarteIdentiteSite = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($domaineCarteIdentiteSite)) {
                            // on réinitialise la variable
                            unset($domaineCarteIdentitePhotoSite);
                            // s'il ne s'agit pas d'un nouveau domaineCarteIdentitePhoto
                            if (!empty($domaineCarteIdentitePhoto->getId())) {
                                // on récupère l'domaineCarteIdentitePhoto pour le modifier
                                $domaineCarteIdentitePhotoSite = $em->getRepository(DomaineCarteIdentitePhoto::class)->findOneBy(array('domaineCarteIdentite' => $domaineCarteIdentiteSite, 'photo' => $originalPhotos->get($key)));
                            }
                            // si l'domaineCarteIdentitePhoto est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($domaineCarteIdentitePhoto->getId()) || empty($domaineCarteIdentitePhotoSite)) {
                                // on récupère la classe correspondant au photo (photo ou video)
                                $typePhoto = (new ReflectionClass($domaineCarteIdentitePhoto))->getName();
                                // on créé un nouveau DomaineCarteIdentitePhoto on fonction du type
                                /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
                                $domaineCarteIdentitePhotoSite = new $typePhoto();
                                $domaineCarteIdentitePhotoSite->setDomaineCarteIdentite($domaineCarteIdentiteSite);
                            }
                            // si l'hébergemenent photo existe déjà pour le site
                            if (!empty($domaineCarteIdentitePhotoSite)) {
                                if ($domaineCarteIdentitePhotoSite->getPhoto() != $domaineCarteIdentitePhoto->getPhoto()) {
//                                    // si l'hébergementPhotoSite avait déjà un photo
//                                    if (!empty($domaineCarteIdentitePhotoSite->getPhoto()) && !$photoToRemoveCollection->contains($domaineCarteIdentitePhotoSite->getPhoto()))
//                                    {
//                                        // on met l'ancien photo dans un tableau afin de le supprimer plus tard
//                                        $photoToRemoveCollection->add($domaineCarteIdentitePhotoSite->getPhoto());
//                                    }
                                    // on met le nouveau photo
                                    $domaineCarteIdentitePhotoSite->setPhoto($domaineCarteIdentitePhoto->getPhoto());
                                }
                                $domaineCarteIdentiteSite->addPhoto($domaineCarteIdentitePhotoSite);

                                /** @var DomaineCarteIdentitePhotoTraduction $traduction */
                                foreach ($domaineCarteIdentitePhoto->getTraductions() as $traduction) {
                                    /** @var DomaineCarteIdentitePhotoTraduction $traductionSite */
                                    $traductionSites = $domaineCarteIdentitePhotoSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (DomaineCarteIdentitePhotoTraduction $element) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new DomaineCarteIdentitePhotoTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $domaineCarteIdentitePhotoSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementPhoto doit être actif sur le site ou non
                                if (!empty($request->get('domaineCarteIdentite_unifie')['domaineCarteIdentites'][$keyCrm]['photos'][$key]['sites']) &&
                                    in_array($site->getId(), $request->get('domaineCarteIdentite_unifie')['domaineCarteIdentites'][$keyCrm]['photos'][$key]['sites'])
                                ) {
                                    $domaineCarteIdentitePhotoSite->setActif(true);
                                } else {
                                    $domaineCarteIdentitePhotoSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'domaineCarteIdentitePhoto CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($domaineCarteIdentitePhoto->getPhoto()->getId()) && !empty($originalPhotos->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $photoToRemoveCollection->add($originalPhotos->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $em->persist($domaineCarteIdentiteUnifie);
            $em->flush();

            $this->copieVersSites($domaineCarteIdentiteUnifie, $originalDomaineCarteIdentiteImages, $originalDomaineCarteIdentitePhotos);

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

            // add flash messages
            $this->addFlash(
                'success',
                'La carte d\'identité du domaine a bien été modifié.'
            );

            return $this->redirectToRoute('domaine_domaineCarteIdentite_edit', array('id' => $domaineCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/edit.html.twig', array(
            'entity' => $domaineCarteIdentiteUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DomaineCarteIdentiteUnifie entity.
     *
     */
    public function deleteAction(Request $request, DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        /** @var Site $siteDistant */
        $form = $this->createDeleteForm($domaineCarteIdentiteUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $domaineCarteIdentiteUnifieSite = $emSite->find(DomaineCarteIdentiteUnifie::class, $domaineCarteIdentiteUnifie->getId());
                if (!empty($domaineCarteIdentiteUnifieSite)) {
                    $emSite->remove($domaineCarteIdentiteUnifieSite);
                    $domaineCarteIdentiteSite = $domaineCarteIdentiteUnifieSite->getDomaineCarteIdentites()->first();

                    if (!empty($domaineCarteIdentiteSite)) {
                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($domaineCarteIdentiteSite->getImages())) {
                            /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
                            foreach ($domaineCarteIdentiteSite->getImages() as $domaineCarteIdentiteImageSite) {
                                $imageSite = $domaineCarteIdentiteImageSite->getImage();
                                $domaineCarteIdentiteImageSite->setImage(null);
                                if (!empty($imageSite)) {
                                    $emSite->remove($imageSite);
                                }
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($domaineCarteIdentiteSite->getPhotos())) {
                            /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
                            foreach ($domaineCarteIdentiteSite->getPhotos() as $domaineCarteIdentitePhotoSite) {
                                $photoSite = $domaineCarteIdentitePhotoSite->getPhoto();
                                $domaineCarteIdentitePhotoSite->setPhoto(null);
                                if (!empty($photoSite)) {
                                    $emSite->remove($photoSite);
                                }
                            }
                        }
                    }
                    $emSite->flush();
                }
            }
//            $em = $this->getDoctrine()->getManager();


            if (!empty($domaineCarteIdentiteUnifie)) {
                if (!empty($domaineCarteIdentiteUnifie->getDomaineCarteIdentites())) {
                    /** @var DomaineCarteIdentite $domaineCarteIdentite */
                    foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {

                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($domaineCarteIdentite->getImages())) {
                            /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                            foreach ($domaineCarteIdentite->getImages() as $domaineCarteIdentiteImage) {
                                $image = $domaineCarteIdentiteImage->getImage();
                                $domaineCarteIdentiteImage->setImage(null);
                                $em->remove($image);
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($domaineCarteIdentite->getPhotos())) {
                            /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                            foreach ($domaineCarteIdentite->getPhotos() as $domaineCarteIdentitePhoto) {
                                $photo = $domaineCarteIdentitePhoto->getPhoto();
                                $domaineCarteIdentitePhoto->setPhoto(null);
                                $em->remove($photo);
                            }
                        }
                    }
                    $em->flush();
                }
//                    $emSite->remove($domaineCarteIdentiteUnifieSite);
//                    $emSite->flush();
            }

            $em->remove($domaineCarteIdentiteUnifie);
            $em->flush();

            // add flash messages
            $this->addFlash('success', 'Le carte d\'identité du domaine a été supprimé avec succès.');
        }

        return $this->redirectToRoute('domaine_domaineCarteIdentite_index');
    }

    public function deleteEntity(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentiteSite */
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        $em = $this->getDoctrine()->getEntityManager();
        $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        // Parcourir les sites non CRM
        foreach ($sitesDistants as $siteDistant) {
            // Récupérer le manager du site.
            $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
            // Récupérer l'entité sur le site distant puis la suprrimer.
            $domaineCarteIdentiteUnifieSite = $emSite->find(DomaineCarteIdentiteUnifie::class, $domaineCarteIdentiteUnifie->getId());
            if (!empty($domaineCarteIdentiteUnifieSite)) {
                foreach ($domaineCarteIdentiteUnifieSite->getDomaineCarteIdentites() as $domaineCarteIdentiteSite) {

//                    $domaineCarteIdentiteSite = $domaineCarteIdentiteUnifieSite->getDomaineCarteIdentites()->first();

                    if (!empty($domaineCarteIdentiteSite)) {
                        // si il y a des images pour l'entité, les supprimer
                        if (!empty($domaineCarteIdentiteSite->getImages())) {
                            /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImageSite */
                            foreach ($domaineCarteIdentiteSite->getImages() as $domaineCarteIdentiteImageSite) {
                                $imageSite = $domaineCarteIdentiteImageSite->getImage();
                                $domaineCarteIdentiteImageSite->setImage(null);
                                if (!empty($imageSite)) {
                                    $emSite->remove($imageSite);
                                }
                            }
                        }
                        // si il y a des photos pour l'entité, les supprimer
                        if (!empty($domaineCarteIdentiteSite->getPhotos())) {
                            /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhotoSite */
                            foreach ($domaineCarteIdentiteSite->getPhotos() as $domaineCarteIdentitePhotoSite) {
                                $photoSite = $domaineCarteIdentitePhotoSite->getPhoto();
                                $domaineCarteIdentitePhotoSite->setPhoto(null);
                                if (!empty($photoSite)) {
                                    $emSite->remove($photoSite);
                                }
                            }
                        }
                    }

                    $emSite->remove($domaineCarteIdentiteSite);
                }
                $emSite->remove($domaineCarteIdentiteUnifieSite);
                $emSite->flush();
            }
        }
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {

            // si il y a des images pour l'entité, les supprimer
            if (!empty($domaineCarteIdentite->getImages())) {
                /** @var DomaineCarteIdentiteImage $domaineCarteIdentiteImage */
                foreach ($domaineCarteIdentite->getImages() as $domaineCarteIdentiteImage) {
                    $image = $domaineCarteIdentiteImage->getImage();
                    $domaineCarteIdentiteImage->setImage(null);
                    $em->remove($image);
                }
            }
            // si il y a des photos pour l'entité, les supprimer
            if (!empty($domaineCarteIdentite->getPhotos())) {
                /** @var DomaineCarteIdentitePhoto $domaineCarteIdentitePhoto */
                foreach ($domaineCarteIdentite->getPhotos() as $domaineCarteIdentitePhoto) {
                    $photo = $domaineCarteIdentitePhoto->getPhoto();
                    $domaineCarteIdentitePhoto->setPhoto(null);
                    $em->remove($photo);
                }
            }

            $em->remove($domaineCarteIdentite);
        }
        $em->remove($domaineCarteIdentiteUnifie);
    }


    /**
     * Displays a form to edit an existing DomaineCarteIdentiteUnifie entity.
     *
     */
    public function editEntity(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        $em = $this->getDoctrine()->getManager();

        $em->persist($domaineCarteIdentiteUnifie);
//      $em->flush();
    }



}
