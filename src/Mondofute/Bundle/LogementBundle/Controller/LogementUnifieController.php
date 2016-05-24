<?php

namespace Mondofute\Bundle\LogementBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementBundle\Form\LogementUnifieType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $logementUnifies = $em->getRepository('MondofuteLogementBundle:LogementUnifie')->findAll();

        return $this->render('@MondofuteLogement/logementunifie/index.html.twig', array(
            'logementUnifies' => $logementUnifies,
        ));
    }

    /**
     * Lists all LogementUnifie entities.
     *
     */
    public function indexPopupAction($idFournisseurHebergement)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurHebergement = $em->getRepository(FournisseurHebergement::class)->find($idFournisseurHebergement);
        $logementUnifies = $em->getRepository('MondofuteLogementBundle:LogementUnifie')->rechercherParFournisseurHebergement($fournisseurHebergement);

        return $this->render('@MondofuteLogement/logementunifie/index_popup.html.twig', array(
            'logementUnifies' => $logementUnifies,
            'fournisseurHebergement' => $fournisseurHebergement,
        ));
    }

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
            $this->supprimerLogements($logementUnifie, $sitesAEnregistrer);
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
     * retirer de l'entité les departements qui ne doivent pas être enregistrer
     * @param LogementUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerLogements(LogementUnifie $entity, array $sitesAEnregistrer)
    {
        /** @var Logement $logement */
        foreach ($entity->getLogements() as $logement) {
            if (!in_array($logement->getSite()->getId(), $sitesAEnregistrer)) {
                $entity->removeLogement($logement);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité hébergement
     * @param LogementUnifie $entity
     */
    private function copieVersSites(LogementUnifie $entity)
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
                }
                $fournisseurHebergementSite = $emSite->getRepository(FournisseurHebergement::class)->findOneBy(array(
                    'fournisseur' => $logement->getFournisseurHebergement()->getFournisseur(),
                    'hebergement' => $logement->getFournisseurHebergement()->getHebergement()
                ));
                if (empty($entity->getId()) || is_null($logementSite = $emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $entity->getId())))) {
                    $logementSite = new Logement();
                    $entitySite->addLogement($logementSite);
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
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
            foreach ($logementUnifie->getLogements() as $logement) {
                array_push($sitesAEnregistrer, $logement->getSite()->getId());
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
            $this->supprimerLogements($logementUnifie, $sitesAEnregistrer);
            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalLogements as $logement) {
                if (!$logementUnifie->getLogements()->contains($logement)) {

//                    //  suppression de la station sur le site
//                    $emSite = $this->getDoctrine()->getEntityManager($logement->getSite()->getLibelle());
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
            foreach ($logementUnifie->getLogements() as $logement) {
                array_push($sitesAEnregistrer, $logement->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }
        $originalLogements = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        /** @var Logement $logement */
        foreach ($logementUnifie->getLogements() as $logement) {
            if (empty($fournisseurHebergement)) {
                $fournisseurHebergement = $logement->getFournisseurHebergement();
            }
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
            $this->supprimerLogements($logementUnifie, $sitesAEnregistrer);
            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalLogements as $logement) {
                if (!$logementUnifie->getLogements()->contains($logement)) {

//                    //  suppression de la station sur le site
//                    $emSite = $this->getDoctrine()->getEntityManager($logement->getSite()->getLibelle());
//                    $entitySite = $emSite->find(DepartementUnifie::class, $logementUnifie->getId());
//                    $departementSite = $entitySite->getDepartements()->first();
//                    $emSite->remove($departementSite);
//
//                    $emSite->flush();
////                    dump($departement);
//                    $departement->setDepartementUnifie(null);
                    $emSite = $this->getDoctrine()->getManager($logement->getSite()->getLibelle());
                    $logementSite = $emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $logementUnifie));
                    $emSite->remove($logementSite);
                    $emSite->flush();
                    $em->remove($logement);
                }
            }
            $this->copieVersSites($logementUnifie);
            $em->persist($logementUnifie);
            $em->flush();

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
                    $emSite->flush();
                }
            }
            $em->remove($logementUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('popup_logement_logement_index', array(
            'idFournisseurHebergement' => $fournisseurHebergement->getId(),
        ));
    }
}
