<?php

namespace Mondofute\Bundle\DomaineBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineUnifie;
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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $domaineUnifies = $em->getRepository(DomaineUnifie::class)->findAll();
        return $this->render('@MondofuteDomaine/domaineunifie/index.html.twig', array(
            'domaineUnifies' => $domaineUnifies,
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

        $form = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType', $domaineUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le controleur et lui donner le container de celui dans lequel on est
            $domaineCarteIdentiteController = new DomaineCarteIdentiteUnifieController();
            $domaineCarteIdentiteController->setContainer($this->container);
            
            $this->supprimerDomaines($domaineUnifie, $sitesAEnregistrer);

            // ***** Carte d'identité *****
            /** @var Domaine $domaine */
            $this->carteIdentiteNew($request, $domaineUnifie);
            // ***** Fin Carte d'identité *****

            $em->persist($domaineUnifie);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo "Exception Found - " . $e->getMessage() . "<br/>";
                die;
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
                        if ($domaineCarteIdentite->getTraductions()->filter(function (DomaineCarteIdentiteTraduction $element) use ($langue) {
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
                        if ($domaineCarteIdentite->getSnowpark()->getTraductions()->filter(function (SnowparkTraduction $element) use ($langue) {
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
                        if ($domaineCarteIdentite->getHandiski()->getTraductions()->filter(function (HandiskiTraduction $element) use ($langue) {
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
     * retirer de l'entité les domaines qui ne doivent pas être enregistrer
     * @param DomaineUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDomaines(DomaineUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getDomaines() as $domaine) {
            if (!in_array($domaine->getSite()->getId(), $sitesAEnregistrer)) {
                $domaine->setDomaineUnifie(null);
                $entity->removeDomaine($domaine);
            }
        }
        return $this;
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
                $domaineCarteIdentite = $domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->filter(function (DomaineCarteIdentite $element) use ($site) {
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
    public function copieVersSites(DomaineUnifie $entity)
    {
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
                } else {
                    $domaineParent = null;
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
                }

//            Récupération de la domaine sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineSite = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $entitySite))))) {
                    $domaineSite = new Domaine();
                }

//            copie des données domaine
                $domaineSite
                    ->setSite($site)
                    ->setDomaineUnifie($entitySite)
                    ->setDomaineParent($domaineParent)
                    ->setDomaineCarteIdentite($domaineCarteIdentite);

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

                $entitySite->addDomaine($domaineSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterDomaineUnifieSiteDistant($entity->getId(), $entity->getDomaines());
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
            ->add('delete', SubmitType::class)
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
            foreach ($domaineUnifie->getDomaines() as $domaine) {
                array_push($sitesAEnregistrer, $domaine->getSite()->getId());
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalDomaines = new ArrayCollection();
//          Créer un ArrayCollection des objets de domaines courants dans la base de données
        foreach ($domaineUnifie->getDomaines() as $domaine) {
            $originalDomaines->add($domaine);
        }

        $this->ajouterDomainesDansForm($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineUnifieType', $domaineUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $domaineCarteIdentiteUnifieController = new DomaineCarteIdentiteUnifieController();
            $domaineCarteIdentiteUnifieController->setContainer($this->container);
            try {
                $this->supprimerDomaines($domaineUnifie, $sitesAEnregistrer);

                // Supprimer la relation entre la domaine et domaineUnifie
                foreach ($originalDomaines as $domaine) {
                    if (!$domaineUnifie->getDomaines()->contains($domaine)) {

                        //  suppression de la domaine sur le site
                        $emSite = $this->getDoctrine()->getEntityManager($domaine->getSite()->getLibelle());
                        $entitySite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                        $domaineSite = $entitySite->getDomaines()->first();
                        $emSite->remove($domaineSite);
                        $emSite->flush();

                        $domaineUnifie->removeDomaine($domaine);
                        $domaine->setDomaineUnifie(null);
                        $domaineParent = $domaine->getDomaineParent();


                        $domaineCI = $domaine->getDomaineCarteIdentite();
                        $domaine->getDomaineCarteIdentite()->removeDomaine($domaine);
                        
                        $em->remove($domaine);

                        if (empty($domaineParent) || $domaineCI != $domaineParent->getDomaineCarteIdentite()) {
                            $domaineCarteIdentiteUnifieController->deleteEntity($domaineCI->getDomaineCarteIdentiteUnifie());
                        }
                    }
                }

                // ***** carte d'identité *****
                $this->carteIdentiteEdit($request, $domaineUnifie);
                // ***** fin carte d'identité *****

                $em->persist($domaineUnifie);
                $em->flush();

                foreach ($domaineUnifie->getDomaines() as $domaine) {
                    $domaineCarteIdentiteUnifieController->copieVersSites($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
                }
                
                $this->copieVersSites($domaineUnifie);
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
            if (!empty($request->get('cboxDomaineCarteIdentite_' . $domaine->getSite()->getId()))) {
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

            } else {
                //
                if (!empty($domaine->getDomaineParent()) && $domaine->getDomaineParent()->getDomaineCarteIdentite() === $domaine->getDomaineCarteIdentite()) {
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
                }
            }
        }

        foreach ($domaineUnifie->getDomaines() as $domaine) {

            if (!empty($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie())) {
                $domaineCarteIdentiteUnifieController->editEntity($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
            } else {
                $domaineCarteIdentiteUnifieController->newEntity($domaine, $request);
            }

            $em->persist($domaine);
//                $em->flush();
        }

    }

    /**
     * Deletes a DomaineUnifie entity.
     *
     */
    public function deleteAction(Request $request, DomaineUnifie $domaineUnifie)
    {
        $form = $this->createDeleteForm($domaineUnifie);
        $form->handleRequest($request);
        $domaineCarteIdentiteUnifieController = new DomaineCarteIdentiteUnifieController();
        $domaineCarteIdentiteUnifieController->setContainer($this->container);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $domaineUnifieSite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                    if (!empty($domaineUnifieSite)) {
                        $emSite->remove($domaineUnifieSite);
                        $emSite->flush();
                    }
                }
//                $em = $this->getDoctrine()->getManager();


                $arrayDomaineCarteIdentiteUnifies = new ArrayCollection();
                /** @var Domaine $domaine */
                foreach ($domaineUnifie->getDomaines() as $domaine) {
                    if (empty($domaine->getDomaineParent()) || (!empty($domaine->getDomaineParent()) && $domaine->getDomaineCarteIdentite() != $domaine->getDomaineParent()->getDomaineCarteIdentite())) {
                        $arrayDomaineCarteIdentiteUnifies->add($domaine->getDomaineCarteIdentite()->getDomaineCarteIdentiteUnifie());
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
