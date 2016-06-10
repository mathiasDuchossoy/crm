<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur;
use Mondofute\Bundle\FournisseurBundle\Form\FournisseurType;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction;
use Mondofute\Bundle\ServiceBundle\Entity\CategorieService;
use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Mondofute\Bundle\ServiceBundle\Entity\Service;
use Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService;
use Mondofute\Bundle\ServiceBundle\Entity\TarifService;
use Mondofute\Bundle\ServiceBundle\Entity\TypeService;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\TrancheHoraireBundle\Entity\TrancheHoraire;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Nucleus\MoyenComBundle\Entity\Pays;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fournisseur controller.
 *
 */
class FournisseurController extends Controller
{
    /**
     * Lists all Fournisseur entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $fournisseurs = $em->getRepository('MondofuteFournisseurBundle:Fournisseur')->findAll();

        return $this->render('@MondofuteFournisseur/fournisseur/index.html.twig', array(
            'fournisseurs' => $fournisseurs,
        ));
    }

    public function rechercherTypeHebergementAction(Request $request)
    {
        $enseigne = $request->get('enseigne');
        $em = $this->getDoctrine()->getManager();
        $fournisseurs = $em->getRepository('MondofuteFournisseurBundle:Fournisseur')->rechercherTypeHebergement($enseigne)->getQuery()->getArrayResult();
        if ($request->isXmlHttpRequest()) {
            $response = new Response();

            $data = json_encode($fournisseurs); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
        return new Response();
    }

    /**
     * Creates a new Fournisseur entity.
     *
     */
    public function newAction(Request $request)
    {
        /** @var FournisseurInterlocuteur $interlocuteur */
        /** @var FournisseurInterlocuteur $interlocuteur */
        /** @var Site $site */
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $fournisseur = new Fournisseur();

        // Ajouter une nouvelle adresse au Moyen de communication du fournisseur
        $adresse = new Adresse();
        $fournisseur->addMoyenCom($adresse);

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array('locale' => $request->getLocale()));

        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $interlocuteurController = new InterlocuteurController();
            $interlocuteurController->setContainer($this->container);
            $interlocuteurController->newInterlocuteurUsers($fournisseur->getInterlocuteurs());

            if (!empty($request->get('fournisseur')['typeFournisseurs'])) {
                foreach ($request->get('fournisseur')['typeFournisseurs'] as $type) {
                    $typeFournisseur = new TypeFournisseur();
                    $typeFournisseur->setTypeFournisseur($type);
                    $fournisseur->addType($typeFournisseur);
                }
            }
            foreach ($fournisseur->getListeServices() as $listeService) {
                $listeService->setFournisseur($fournisseur);
                foreach ($listeService->getServices() as $service) {
                    $service->setListeService($listeService);
                    /** @var TarifService $tarifService */
                    foreach ($service->getTarifs() as $tarifService) {
                        $tarifService->setService($service);
                    }
                }
            }
            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                $interlocuteur->setFournisseur($fournisseur);
            }

            if (!$interlocuteurController->testInterlocuteursLoginExist($fournisseur->getInterlocuteurs())) {

                $this->copieVersSites($fournisseur);

                foreach ($fournisseur->getMoyenComs() as $moyenCom) {
                    $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                    switch ($typeComm) {
                        case "Adresse":
                            /** @var Adresse $moyenComSite */
                            $moyenCom->setPays($em->find(Pays::class, $moyenCom->getPays()));
                            break;
                        default:
                            break;
                    }
                }

                foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                    foreach ($interlocuteur->getInterlocuteur()->getMoyenComs() as $moyenCom) {

                        $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                        switch ($typeComm) {
                            case "Adresse":
                                /** @var Adresse $moyenComSite */
                                $moyenCom->setPays($em->find(Pays::class, $moyenCom->getPays()));
                                break;
                            default:
                                break;
                        }
                    }
                }

                $em->persist($fournisseur);
                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le fournisseur a bien été créé.'
                );

                return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
            }
        }

        return $this->render('@MondofuteFournisseur/fournisseur/new.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $form->createView(),
            'langues' => $langues,
        ));
    }

    private function copieVersSites(Fournisseur $fournisseur)
    {
        /** @var MoyenCommunication $moyenComSite */
        /** @var Site $site */
        /** @var FournisseurInterlocuteur $interlocuteur */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $fournisseurSite = clone $fournisseur;
            $this->dupliquerListeServicesSite($fournisseurSite, $fournisseur->getListeServices(), $emSite);
            $moyenComsSite = $fournisseurSite->getMoyenComs();
            if (!empty($moyenComsSite)) {
                foreach ($moyenComsSite as $key => $moyenComSite) {
                    $moyenComsSite[$key] = clone $moyenComSite;

                    $typeComm = (new ReflectionClass($moyenComSite))->getShortName();
                    switch ($typeComm) {
                        case "Adresse":
                            /** @var Adresse $moyenComSite */
                            $moyenComSite->setPays($emSite->find(Pays::class, $moyenComSite->getPays()));
                            $moyenComsSite[$key]->setPays($emSite->find(Pays::class, $moyenComSite->getPays()));
                            break;
                        default:
                            break;
                    }
                }
            }

            if (!empty($fournisseurSite->getFournisseurParent())) {
                $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur',
                    $fournisseurSite->getFournisseurParent()->getId()));
            }

//            $fournisseurSite->setType($emSite->find('MondofuteFournisseurBundle:TypeFournisseur', $fournisseurSite->getType()->getId()));

            foreach ($fournisseurSite->getInterlocuteurs() as $interlocuteur) {

                if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                    $interlocuteur->getInterlocuteur()->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                        $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                }
                if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                    $interlocuteur->getInterlocuteur()->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                        $interlocuteur->getInterlocuteur()->getService()->getId()));
                }

                foreach ($interlocuteur->getInterlocuteur()->getMoyenComs() as $moyenCom) {
                    $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                    switch ($typeComm) {
                        case "Adresse":
                            $moyenCom->setPays($emSite->find(Pays::class, $moyenCom->getPays()));
                            break;
                        default:
                            break;
                    }
                }
            }
            /** @var RemiseClef $remiseClef */
            foreach ($fournisseurSite->getRemiseClefs() as $remiseClef) {
                /** @var RemiseClefTraduction $traduction */
                foreach ($remiseClef->getTraductions() as $traduction) {
                    $traduction->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                }
            }
            $emSite->persist($fournisseurSite);

            $emSite->flush();
        }


    }

    /**
     * @param Fournisseur $fournisseurSite
     * @param $listeServices
     * @param EntityManager $emSite
     * @throws \Doctrine\ORM\ORMException
     */
    public function dupliquerListeServicesSite(
        Fournisseur $fournisseurSite,
        $listeServices,
        EntityManager $emSite
    ) {
        /** @var ListeService $listeService */
        foreach ($listeServices as $listeService) {
            if (empty($listeService->getId()) || empty($listeServiceSite = $emSite->getRepository(ListeService::class)->find($listeService->getId()))) {
                $listeServiceSite = new ListeService();
                $fournisseurSite->addListeService($listeServiceSite);
            }
            $listeServiceSite
                ->setLibelle($listeService->getLibelle())
                ->setFournisseur($fournisseurSite);
            /** @var Service $service */
            foreach ($listeService->getServices() as $service) {
                if (empty($service->getId()) || empty($serviceSite = $emSite->getRepository(Service::class)->find($service->getId()))) {
                    $serviceSite = new Service();
                    $listeServiceSite->addService($serviceSite);
                }
                $serviceSite->setListeService($listeServiceSite)
                    ->setDefaut($service->getDefaut())
                    ->setCategorieService($emSite->getRepository(CategorieService::class)->find($service->getCategorieService()->getId()))
                    ->setSousCategorieService($emSite->getRepository(SousCategorieService::class)->find($service->getSousCategorieService()->getId()))
                    ->setType($emSite->getRepository(TypeService::class)->find($service->getType()->getId()));
                /** @var TarifService $tarifService */
                foreach ($service->getTarifs() as $tarifService) {
                    if (empty($tarifService->getId()) || empty($tarifServiceSite = $emSite->getRepository(TarifService::class)->find($tarifService->getId()))) {
                        $tarifServiceSite = new TarifService();
                        $serviceSite->addTarif($tarifServiceSite);
                    }
                    if (empty($tarifServiceSite->getTarif())) {
                        $tarifSite = new Tarif();
                        $tarifServiceSite->setTarif($tarifSite);
                    }
                    $tarifServiceSite->getTarif()
                        ->setUnite($emSite->getRepository(UniteTarif::class)->find($tarifService->getTarif()->getUnite()->getId()))
                        ->setValeur($tarifService->getTarif()->getValeur());
                    $tarifServiceSite->setService($serviceSite)
                        ->setTypePeriode($emSite->getRepository(TypePeriode::class)->find($tarifService->getTypePeriode()->getId()));
                }
            }
            $fournisseurSite->addListeService($listeServiceSite);
        }
        $emSite->persist($fournisseurSite);
        $emSite->flush();
    }

    /**
     * Finds and displays a Fournisseur entity.
     *
     */
    public function showAction(Fournisseur $fournisseur)
    {
        $deleteForm = $this->createDeleteForm($fournisseur);

        return $this->render('@MondofuteFournisseur/fournisseur/show.html.twig', array(
            'fournisseur' => $fournisseur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Fournisseur entity.
     *
     * @param Fournisseur $fournisseur The Fournisseur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Fournisseur $fournisseur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fournisseur_delete', array('id' => $fournisseur->getId())))
            ->add('Supprimer', SubmitType::class, array('label' => 'supprimer', 'translation_domain' => 'messages'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Fournisseur entity.
     *
     */
    public function editAction(Request $request, Fournisseur $fournisseur)
    {
        /** @var FournisseurInterlocuteur $interlocuteur */
        $originalInterlocuteurs = new ArrayCollection();
        $originalRemiseClefs = new ArrayCollection();
        $originalReceptions = new ArrayCollection();
        $originalTypes = new ArrayCollection();
        $originalListeServices = new ArrayCollection();
        $originalServices = new ArrayCollection();
        $originalTarifsService = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
            $originalInterlocuteurs->add($interlocuteur);
        }

        foreach ($fournisseur->getRemiseClefs() as $remiseClef) {
            $originalRemiseClefs->add($remiseClef);
        }
        foreach ($fournisseur->getReceptions() as $reception) {
            $originalReceptions->add($reception);
        }
        foreach ($fournisseur->getTypes() as $type) {
            $originalTypes->add($type);
        }

        foreach ($fournisseur->getListeServices() as $listeService) {
//            $originalListeService = $listeService;
            /** @var Service $service */
            foreach ($listeService->getServices() as $service) {
//                $originalService = clone $service;
                $originalServices->add($service);
                foreach ($service->getTarifs() as $tarif) {
                    $originalTarifsService->add($tarif);
                }
            }
            $originalListeServices->add($listeService);
        }

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $deleteForm = $this->createDeleteForm($fournisseur);
        $fournisseur->triReceptions();
        $fournisseur->triRemiseClefs();
        $editForm = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur,
            array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'mettre.a.jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // type de fournisseur
            $arrayTypeFournisseur = new ArrayCollection();

            if (!empty($request->request->get('fournisseur')['typeFournisseurs'])) {
                foreach ($request->request->get('fournisseur')['typeFournisseurs'] as $type) {
                    $arrayTypeFournisseur->add(intval($type));
                }
            }
            foreach ($fournisseur->getListeServices() as $listeService) {
                $listeService->setFournisseur($fournisseur);
                foreach ($listeService->getServices() as $service) {
                    $service->setListeService($listeService);
                    /** @var TarifService $tarifService */
                    foreach ($service->getTarifs() as $tarifService) {
                        $tarifService->setService($service);
                    }
                }
            }
            foreach ($request->request->get('fournisseur')['typeFournisseurs'] as $type) {
                $arrayTypeFournisseur->add(intval($type));
            }

            /** @var TypeFournisseur $type */
            foreach ($originalTypes as $type) {
                if (!$arrayTypeFournisseur->contains($type->getTypeFournisseur())) {

                    $fournisseur->getTypes()->removeElement($type);
                    $this->deleteTypeFournisseurSites($type);
                    $em->remove($type);
                }
            }

            /** @var integer $type */
            foreach ($arrayTypeFournisseur as $type) {
                if (!$originalTypes->filter(function (TypeFournisseur $element) use ($type) {
                    return $element->getTypeFournisseur() == $type;
                })->first()
                ) {
                    $typeFournisseur = new TypeFournisseur();
                    $typeFournisseur->setTypeFournisseur($type);
                    $fournisseur->addType($typeFournisseur);
                }
            }

            // *****

            $interlocuteurController = new InterlocuteurController();
            $interlocuteurController->setContainer($this->container);

            foreach ($originalInterlocuteurs as $interlocuteur) {
                if (false === $fournisseur->getInterlocuteurs()->contains($interlocuteur)) {

                    // if it was a many-to-one relationship, remove the relationship like this
                    $this->deleteInterlocuteurSites($interlocuteur);
                    $this->deleteMoyenComs($interlocuteur->getInterlocuteur(), $em);

                    $em->flush();
                    $interlocuteur->setFournisseur(null);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($interlocuteur);
                }
            }


            $interlocuteurController->newInterlocuteurUsers($fournisseur->getInterlocuteurs());


            /** @var ListeService $listeService */
            foreach ($originalListeServices as $listeService) {
                if (false === $fournisseur->getListeServices()->contains($listeService)) {
                    $this->deleteListeServiceSites($listeService);
                    $em->remove($listeService);
                }
            }
            foreach ($originalServices as $service) {
                $trouve = false;
                foreach ($fournisseur->getListeServices() as $listeService) {
                    if ($listeService->getServices()->contains($service) === true) {
                        $trouve = true;
                    }
                }
                if ($trouve === false) {
                    $this->deleteServiceSites($service);
                    $em->remove($service);
                }
            }
            foreach ($originalTarifsService as $tarifService) {
                $trouve = false;
                foreach ($fournisseur->getListeServices() as $listeService) {
                    foreach ($listeService->getServices() as $service) {

                        /** @var TarifService $tarifService */
                        if ($service->getTarifs()->contains($tarifService) === true) {
                            $trouve = true;
                        }
                    }
                }
                if ($trouve === false) {
                    $this->deleteTarifServiceSites($tarifService);
                    $em->remove($tarifService);
                }
            }
            foreach ($originalRemiseClefs as $remiseClef) {
                if (false === $fournisseur->getRemiseClefs()->contains($remiseClef)) {
                    $fournisseur->getRemiseClefs()->removeElement($remiseClef);
                    $this->deleteRemiseClefSites($remiseClef);
                    $em->remove($remiseClef);
                }
            }
            foreach ($originalReceptions as $reception) {
                if (false === $fournisseur->getReceptions()->contains($reception)) {
                    $fournisseur->getReceptions()->removeElement($reception);
                    $this->deleteReceptionSites($reception);
                    $em->remove($reception);
                }
            }
            if (!$interlocuteurController->testInterlocuteursLoginExist($fournisseur->getInterlocuteurs())) {

                $this->mAJSites($fournisseur);

                foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                    $interlocuteur->setFournisseur($fournisseur);
                }

                $em->persist($fournisseur);
                $em->flush();
            /** @var ListeService $listeService */
            foreach ($fournisseur->getListeServices() as $listeService) {
                $listeService->setFournisseur($fournisseur);
            }

            $em->persist($fournisseur);
            $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le fournisseur a bien été modifié.'
                );
                return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
            }
        }

        return $this->render('@MondofuteFournisseur/fournisseur/edit.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $editForm->createView(),
            'langues' => $langues,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function deleteTypeFournisseurSites(TypeFournisseur $typeFournisseur)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $typeFournisseurSite = $emSite->getRepository(TypeFournisseur::class)->findOneBy(array(
                'fournisseur' => $typeFournisseur->getFournisseur(),
                'typeFournisseur' => $typeFournisseur->getTypeFournisseur()
            ));

            if (!empty($typeFournisseurSite)) {
                $typeFournisseurSite->setFournisseur(null);
                $emSite->remove($typeFournisseurSite);
            }
            $emSite->flush();
        }
    }

    private function deleteInterlocuteurSites(FournisseurInterlocuteur $interlocuteur)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $interlocuteurSite = $emSite->find('MondofuteFournisseurBundle:FournisseurInterlocuteur',
                $interlocuteur->getId());

            if (!empty($interlocuteurSite)) {
                $this->deleteMoyenComs($interlocuteurSite->getInterlocuteur(), $emSite);

//                $moyenComs = $interlocuteurSite->getInterlocuteur()->getMoyenComs();
//                if (!empty($moyenComs))
//                {
//                    foreach ($moyenComs as $moyenCom)
//                    {
//                        $interlocuteurSite->getInterlocuteur()->removeMoyenCom($moyenCom);
//                    }
//                }

                $emSite->flush();
                $interlocuteurSite->setFournisseur(null);

                $emSite->remove($interlocuteurSite);

//                die;

            }


        }
    }

    /**
     * @param $entity
     * @param EntityManager $em
     */
    private function deleteMoyenComs($entity, EntityManager $em)
    {
        $moyenComs = $entity->getMoyenComs();
        if (!empty($moyenComs)) {
            foreach ($moyenComs as $moyenCom) {
                $entity->removeMoyenCom($moyenCom);
                $em->remove($moyenCom);
            }
        }
    }

    /**
     * @param ListeService $listeService
     */
    private function deleteListeServiceSites(ListeService $listeService)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
            $listeServiceSite = $emSite->find(ListeService::class,
                $listeService->getId());
            if (!empty($listeServiceSite)) {
                $listeServiceSite->setFournisseur(null);
                $emSite->remove($listeServiceSite);
            }

        }
    }

    /**
     * @param Service $service
     */
    private function deleteServiceSites(Service $service)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
            $serviceSite = $emSite->find(Service::class,
                $service->getId());
            if (!empty($serviceSite)) {
                $serviceSite->setListeService(null);
                $emSite->remove($serviceSite);
            }

        }
    }

    /**
     * @param TarifService $tarifService
     */
    private function deleteTarifServiceSites(TarifService $tarifService)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
            $tarifServiceSite = $emSite->find(TarifService::class,
                $tarifService->getId());
            if (!empty($tarifServiceSite)) {
                $tarifServiceSite->setService(null);
                $emSite->remove($tarifServiceSite);
            }

        }
    }

    private function deleteRemiseClefSites(RemiseClef $remiseClef)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $remiseClefSite = $emSite->find('MondofuteRemiseClefBundle:RemiseClef', $remiseClef->getId());
            if (!empty($remiseClefSite)) {
                $remiseClefSite->setFournisseur(null);

                $emSite->remove($remiseClefSite);
            }
        }
    }

    private function deleteReceptionSites(Reception $reception)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $receptionSite = $emSite->find(Reception::class, $reception->getId());
            if (!empty($receptionSite)) {
                $receptionSite->setFournisseur(null);
                if ($receptionSite->getTranche1() !== null) {
                    $emSite->remove($receptionSite->getTranche1());
                }
                if ($receptionSite->getTranche2() !== null) {
                    $emSite->remove($receptionSite->getTranche2());
                }
                $receptionSite->setTranche1(null);
                $receptionSite->setTranche2(null);
                $emSite->remove($receptionSite);
            }
        }
    }

    private function mAJSites(Fournisseur $fournisseur)
    {
        /** @var TypeFournisseur $type */
        /** @var FournisseurInterlocuteur $interlocuteurSite */
        /** @var Site $site */
        /** @var FournisseurInterlocuteur $interlocuteur */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $fournisseurSite = $emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur->getId());
            $this->dupliquerListeServicesSite($fournisseurSite, $fournisseur->getListeServices(), $emSite);
            $fournisseurSite->setEnseigne($fournisseur->getEnseigne());
            foreach ($fournisseur->getTypes() as $type) {
                $typeSite = $emSite->getRepository(TypeFournisseur::class)->findOneBy(array(
                    'fournisseur' => $fournisseurSite,
                    "typeFournisseur" => $type->getTypeFournisseur()
                ));
                if (empty($typeSite)) {
                    $typeFournisseur = new TypeFournisseur();
                    $typeFournisseur->setTypeFournisseur($type->getTypeFournisseur());
                    $fournisseurSite->addType($typeFournisseur);
                }
            }

            $fournisseurSite->setContient($fournisseur->getContient());
//            $fournisseurSite->setDateModification(new DateTime());


            foreach ($fournisseur->getMoyenComs() as $key => $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();
                switch ($typeComm) {
                    case "Adresse":
                        $adresse = $fournisseurSite->getMoyenComs()->get($key);
                        $adresse->setCodePostal($moyenCom->getCodePostal());
                        $adresse->setAdresse1($moyenCom->getAdresse1());
                        $adresse->setAdresse2($moyenCom->getAdresse2());
                        $adresse->setAdresse3($moyenCom->getAdresse3());
                        $adresse->setVille($moyenCom->getVille());
                        $adresse->setPays($emSite->find(Pays::class, $moyenCom->getPays()));
//                        $adresse->setDateModification(new DateTime());
                        break;
                    default:
                        break;
                }
            }

            if (!empty($fournisseur->getFournisseurParent())) {
                $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur',
                    $fournisseur->getFournisseurParent()->getId()));
            } else {
                $fournisseurSite->setFournisseurParent(null);
            }

            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {

                $interlocuteurSite = $fournisseurSite->getInterlocuteurs()->filter(function (
                    FournisseurInterlocuteur $element
                ) use ($interlocuteur) {
                    return $element->getId() == $interlocuteur->getId();
                })->first();

                if (!empty($interlocuteurSite)) {
                    $interlocuteurSite->getInterlocuteur()->setPrenom($interlocuteur->getInterlocuteur()->getPrenom());
                    $interlocuteurSite->getInterlocuteur()->setNom($interlocuteur->getInterlocuteur()->getNom());

                    if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                        $interlocuteurSite->getInterlocuteur()->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                            $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                    } else {
                        $interlocuteurSite->getInterlocuteur()->setFonction(null);
                    }
                    if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                        $interlocuteurSite->getInterlocuteur()->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                            $interlocuteur->getInterlocuteur()->getService()->getId()));
                    } else {
                        $interlocuteurSite->getInterlocuteur()->setService(null);
                    }
//                    $interlocuteurSite->getInterlocuteur()->setDateModification(new DateTime());

                    $moyenComsSite = $interlocuteurSite->getInterlocuteur()->getMoyenComs();
                    if (!empty($moyenComsSite)) {
                        // todo : maj les moyens de communications
                        foreach ($moyenComsSite as $key => $moyenComSite) {
                            $typeComm = (new ReflectionClass($moyenComSite))->getShortName();
//                            switch ($typeComm)
                            $firstFixe = true;
                            switch ($typeComm) {
                                case 'Adresse':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function (
                                        $element
                                    ) {
                                        return (new ReflectionClass($element))->getShortName() == 'Adresse';
                                    })->first();
                                    if ($moyenComCrm) {
                                        $moyenComSite->setCodePostal($moyenComCrm->getCodePostal());
                                        $moyenComSite->setAdresse1($moyenComCrm->getAdresse1());
                                        $moyenComSite->setAdresse2($moyenComCrm->getAdresse2());
                                        $moyenComSite->setAdresse3($moyenComCrm->getAdresse3());
                                        $moyenComSite->setVille($moyenComCrm->getVille());
                                        $moyenComSite->setPays($emSite->find(Pays::class, $moyenComCrm->getPays()));
                                        $moyenComSite->getCoordonneeGPS()->setLatitude($moyenComCrm->getCoordonneeGPS()->getLatitude());
                                        $moyenComSite->getCoordonneeGPS()->setLongitude($moyenComCrm->getCoordonneeGPS()->getLongitude());
                                        $moyenComSite->getCoordonneeGPS()->setPrecis($moyenComCrm->getCoordonneeGPS()->getPrecis());
                                    }
//                                    $moyenComSite->setDateModification(new DateTime());
                                    break;
                                case 'Email':
//                                    dump($interlocuteur->getInterlocuteur()->getMoyenComs());
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function (
                                        $element
                                    ) {
                                        return (new ReflectionClass($element))->getShortName() == 'Email';
                                    })->first();
                                    if ($moyenComCrm) {
                                        $moyenComSite->setAdresse($moyenComCrm->getAdresse());
                                    }
//                                    $moyenComSite->setDateModification(new DateTime());
                                    break;
                                case 'Mobile':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function (
                                        $element
                                    ) {
                                        return (new ReflectionClass($element))->getShortName() == 'Mobile';
                                    })->first();
                                    if ($moyenComCrm) {
                                        $moyenComSite->setNumero($moyenComCrm->getnumero());
                                    }
//                                    $moyenComSite->setDateModification(new DateTime());
                                    break;
                                case 'Fixe':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function (
                                        $element
                                    ) {
                                        return (new ReflectionClass($element))->getShortName() == 'Fixe';
                                    });
                                    if ($moyenComCrm) {
                                        if ($firstFixe) {
                                            $moyenComSite->setNumero($moyenComCrm->first()->getNumero());
//                                        $moyenComSite->setDateModification(new DateTime());
                                            $firstFixe = false;
                                        } else {
                                            $moyenComSite->setNumero($moyenComSite->last()->getnumero());
//                                        $moyenComSite->setDateModification(new DateTime());
                                        }
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                } else {
                    $fournisseurInterlocuteurSite = new FournisseurInterlocuteur();

                    /** @var Interlocuteur $interlocuteurSite */
                    $interlocuteurSite = new Interlocuteur();

                    $interlocuteurSite->setPrenom($interlocuteur->getInterlocuteur()->getPrenom());
                    $interlocuteurSite->setNom($interlocuteur->getInterlocuteur()->getNom());

                    if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                        $interlocuteurSite->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                            $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                    }
                    if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                        $interlocuteurSite->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                            $interlocuteur->getInterlocuteur()->getService()->getId()));
                    }

                    $fournisseurInterlocuteurSite->setFournisseur($fournisseurSite);
                    $fournisseurInterlocuteurSite->setInterlocuteur($interlocuteurSite);

                    foreach ($interlocuteur->getInterlocuteur()->getMoyenComs() as $moyenCom) {
//                        $moyenCom->setDateCreation();
                        $moyenComClone = clone $moyenCom;
                        $interlocuteurSite->addMoyenCom($moyenComClone);

                        $typeComm = (new ReflectionClass($moyenComClone))->getShortName();
                        switch ($typeComm) {
                            case "Adresse":
                                /** @var Adresse $moyenComSite */
                                $moyenComClone->setPays($emSite->find(Pays::class, $moyenComClone->getPays()));
                                break;
                            default:
                                break;
                        }
//                        dump($moyenCom);
                    }

                    $fournisseurSite->addInterlocuteur($fournisseurInterlocuteurSite);

                }

            }
            /** @var RemiseClef $remiseClef */
            foreach ($fournisseur->getRemiseClefs() as $remiseClef) {
                if (!empty($remiseClef->getId())) {
                    $remiseClefSite = $fournisseurSite->getRemiseClefs()->filter(function (RemiseClef $element) use (
                        $remiseClef
                    ) {
                        return $element->getId() == $remiseClef->getId();
                    })->first();
                } else {
                    $remiseClefSite = null;
                }
                if (empty($remiseClefSite)) {
                    $remiseClefSite = new RemiseClef();
                }
                $remiseClefSite->setLibelle($remiseClef->getLibelle());
                if (!empty($remiseClef->getHeureDepartCourtSejour())) {
                    $remiseClefSite->setHeureDepartCourtSejour($remiseClef->getHeureDepartCourtSejour());
                } else {
                    $remiseClefSite->setHeureDepartCourtSejour(null);
                }
                if (!empty($remiseClef->getHeureTardiveCourtSejour())) {
                    $remiseClefSite->setHeureTardiveCourtSejour($remiseClef->getHeureTardiveCourtSejour());
                } else {
                    $remiseClefSite->setHeureTardiveCourtSejour(null);
                }
                if (!empty($remiseClef->getFournisseur())) {
                    $remiseClefSite->setFournisseur($emSite->find(Fournisseur::class,
                        $remiseClef->getFournisseur()->getId()));
                } else {
                    $remiseClefSite->setFournisseur(null);
                }
                if (!empty($remiseClef->getHeureDepartLongSejour())) {
                    $remiseClefSite->setHeureDepartLongSejour($remiseClef->getHeureDepartLongSejour());
                } else {
                    $remiseClefSite->setHeureDepartLongSejour(null);
                }
                if (!empty($remiseClef->getHeureRemiseClefCourtSejour())) {
                    $remiseClefSite->setHeureRemiseClefCourtSejour($remiseClef->getHeureRemiseClefCourtSejour());
                } else {
                    $remiseClefSite->setHeureRemiseClefCourtSejour(null);
                }
                if (!empty($remiseClef->getHeureRemiseClefLongSejour())) {
                    $remiseClefSite->setHeureRemiseClefLongSejour($remiseClef->getHeureRemiseClefLongSejour());
                } else {
                    $remiseClefSite->setHeureRemiseClefLongSejour(null);
                }
                if (!empty($remiseClef->getHeureTardiveLongSejour())) {
                    $remiseClefSite->setHeureTardiveLongSejour($remiseClef->getHeureTardiveLongSejour());
                } else {
                    $remiseClefSite->setHeureTardiveLongSejour(null);
                }
                if (!empty($remiseClef->getStandard())) {
                    $remiseClefSite->setStandard($remiseClef->getStandard());
                } else {
                    $remiseClefSite->setStandard(false);
                }
                if (!empty($remiseClef->getHeureTardiveLongSejour())) {
                    $remiseClefSite->setHeureTardiveLongSejour($remiseClef->getHeureTardiveLongSejour());
                } else {
                    $remiseClefSite->setHeureTardiveLongSejour(null);
                }
                /** @var RemiseClefTraduction $remiseClefTraduction */
                foreach ($remiseClef->getTraductions() as $remiseClefTraduction) {
                    if (!empty($remiseClefTraduction->getId())) {
                        $remiseClefTraductionSite = $remiseClefSite->getTraductions()->filter(function (
                            RemiseClefTraduction $element
                        ) use (
                            $remiseClefTraduction
                        ) {
                            return ($element->getLangue()->getId() == $remiseClefTraduction->getLangue()->getId()) && ($element->getRemiseClef()->getId() == $remiseClefTraduction->getRemiseClef()->getId());
                        })->first();
                    } else {
                        $remiseClefTraductionSite = null;
                    }
                    if (empty($remiseClefTraductionSite)) {
                        $remiseClefTraductionSite = new RemiseClefTraduction();
                    }
                    if (!empty($remiseClefTraduction->getLangue())) {
                        $remiseClefTraductionSite->setLangue($emSite->find(Langue::class,
                            $remiseClefTraduction->getLangue()->getId()));
                    }
                    if (!empty($remiseClefTraduction->getLieuxRemiseClef())) {
                        $remiseClefTraductionSite->setLieuxRemiseClef($remiseClefTraduction->getLieuxRemiseClef());
                    } else {
                        $remiseClefTraductionSite->setLieuxRemiseClef('');
                    }
                    $remiseClefSite->addTraduction($remiseClefTraductionSite);
//                    if(!empty($remiseClefTraduction->getRemiseClef())){
//                        $remiseClefTraductionSite->setRemiseClef($emSite->find(RemiseClef::class,$remiseClefTraduction->getRemiseClef()->getId()));
//                    }else{
//                        $remiseClefTraductionSite->setRemiseClef(null);
//                    }
                }
                $fournisseurSite->addRemiseClef($remiseClefSite);
            }
            /** @var Reception $reception */
            foreach ($fournisseur->getReceptions() as $reception) {
                if (!empty($reception->getId())) {
                    $receptionSite = $fournisseurSite->getReceptions()->filter(function (Reception $element) use (
                        $reception
                    ) {
                        return $element->getId() == $reception->getId();
                    })->first();
                } else {
                    $receptionSite = null;
                }
//                if(empty($receptionSite = $emSite->getRepository(Reception::class)->find($reception->getId()))){
//
////                }
                if (empty($receptionSite)) {
                    $receptionSite = new Reception();
                    $fournisseurSite->addReception($receptionSite);
                }
                if (!empty($reception->getTranche1())) {
//                    if(empty($tranche1Site = $emSite->getRepository(TrancheHoraire::class)->find($receptionSite->getTranche1()))){
                    if (empty($receptionSite->getTranche1())) {
                        $tranche1Site = new TrancheHoraire();
                    } else {
                        $tranche1Site = $receptionSite->getTranche1();
                    }
                    $tranche1Site->setDebut($reception->getTranche1()->getDebut())
                        ->setFin($reception->getTranche1()->getFin());
                    $receptionSite->setTranche1($tranche1Site);
                }
                if (!empty($reception->getTranche2())) {
//                    if(empty($tranche2Site = $emSite->getRepository(TrancheHoraire::class)->find($receptionSite->getTranche2()))){
                    if (empty($receptionSite->getTranche2())) {
                        $tranche2Site = new TrancheHoraire();
                    } else {
                        $tranche2Site = $receptionSite->getTranche2();
                    }
                    $tranche2Site->setDebut($reception->getTranche2()->getDebut())
                        ->setFin($reception->getTranche2()->getFin());
                    $receptionSite->setTranche2($tranche2Site);
                }
//                if (!empty($reception->getTranche2())) {
//                    $receptionSite->setTranche2($reception->getTranche2());
//                }
                if (!empty($reception->getJour())) {
                    $receptionSite->setJour($reception->getJour());
                }
//                $fournisseurSite->addReception($receptionSite);
            }
            $emSite->persist($fournisseurSite);
            $emSite->flush();
        }
    }

    /**
     * Deletes a Fournisseur entity.
     *
     */
    public function deleteAction(Request $request, Fournisseur $fournisseur)
    {
        /** @var EntityManager $em */
        /** @var Site $site */
        $form = $this->createDeleteForm($fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $fournisseurSite = $emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur->getId());

                    // ***** suppression des moyen de communications *****
//                    $moyenComs = $fournisseurSite->getMoyenComs();
//                    if (!empty($moyenComs)) {
//                        foreach ($moyenComs as $moyenCom) {
//                            $fournisseurSite->removeMoyenCom($moyenCom);
//                        }
//                    }

                    if (!empty($fournisseurSite)) {
                        $this->deleteMoyenComs($fournisseurSite, $emSite);


                        $emSite->flush();
                        $interlocuteurs = $fournisseurSite->getInterlocuteurs();
                        if (!empty($interlocuteurs)) {
                            foreach ($interlocuteurs as $interlocuteur) {
                                $this->deleteMoyenComs($interlocuteur->getInterlocuteur(), $emSite);
//                            $moyenComs = $interlocuteur->getInterlocuteur()->getMoyenComs();
//                            if (!empty($moyenComs)) {
//                                foreach ($moyenComs as $moyenCom) {
//                                    $interlocuteur->getInterlocuteur()->removeMoyenCom($moyenCom);
//                                }
//                            }


                                $emSite->flush();
                                $emSite->remove($interlocuteur);
                            }
                        }
                        // ***** fin suppression des moyen de communications *****

                        //                    if (!empty($fournisseurSite)) {
                        $emSite->remove($fournisseurSite);
                        $emSite->flush();
                    }
                }

                // ***** suppression des moyen de communications *****
//                $moyenComs = $fournisseur->getMoyenComs();
//                if (!empty($moyenComs)) {
//                    foreach ($moyenComs as $moyenCom) {
//                        $fournisseur->removeMoyenCom($moyenCom);
//                    }
//                }

                $this->deleteMoyenComs($fournisseur, $em);
                $em->flush();

                $interlocuteurs = $fournisseur->getInterlocuteurs();
                if (!empty($interlocuteurs)) {
                    foreach ($interlocuteurs as $interlocuteur) {
//                        $moyenComs = $interlocuteur->getInterlocuteur()->getMoyenComs();
//                        if (!empty($moyenComs)) {
//                            foreach ($moyenComs as $moyenCom) {
//                                $interlocuteur->getInterlocuteur()->removeMoyenCom($moyenCom);
//                                $em->remove($moyenCom);
//                            }
//                        }
                        $this->deleteMoyenComs($interlocuteur->getInterlocuteur(), $em);

//                        $interlocuteur->getInterlocuteur()->getMoyenComs()->clear();
//                        die;
                        $em->flush();
//                        die;
                        $em->remove($interlocuteur);
                    }
                }

//                $this->deleteMoyenComs($fournisseur, $em);

//                $em->flush();

//                $utilisateurSite = $utilisateurUserSite->getUtilisateur();
//                foreach ($utilisateurSite->getMoyenComs() as $moyenComSite) {
//                    $utilisateurSite->removeMoyenCom($moyenComSite);
//                    $emSite->remove($moyenComSite);
//                }
//
//                $emSite->flush();
//
//                $emSite->remove($utilisateurSite);
//                $emSite->remove($utilisateurUserSite);
//                $emSite->flush();
                // ***** fin suppression des moyen de communications *****

                $em->remove($fournisseur);
                $em->flush();

            } catch (ForeignKeyConstraintViolationException $except) {

//                dump($except);
//                die;
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'Impossible de supprimer le fournisseur, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreure inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }


            // add flash messages
            $this->addFlash('success', 'Le fournisseur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('fournisseur_index');
    }

//    private function ajouterInterlocuteurMoyenComunnications(Fournisseur $fournisseur)
//    {
//        /** @var FournisseurInterlocuteur $interlocuteur */
//        $interlocuteurs = $fournisseur->getInterlocuteurs();
//        foreach ($interlocuteurs as $interlocuteur) {
//            $interlocuteur->getInterlocuteur()->addMoyenCommunication(new Mobile())
//                ->addMoyenCommunication(new Fixe())
//                ->addMoyenCommunication(new Fixe());
//        }
//    }
//
//    public function chargerFormInterlocuteur()
//    {
//        $interlocuteur = new Interlocuteur();
//        $interlocuteur->getMoyenComs()
//            ->add(new Adresse());
//        $interlocuteur
//            ->addMoyenCom(new Adresse())
//            ->addMoyenCom(new Fixe())
//            ->addMoyenCom(new Fixe())
//            ->addMoyenCom(new Mobile())
//            ->addMoyenCom(new Email());
//
//        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\InterlocuteurType', $interlocuteur);
//
//        return $this->render('@MondofuteFournisseur/fournisseur/new.html.twig', array(
//            'interlocuteur' => $interlocuteur,
//            'form' => $form->createView(),
//        ));
//
//    }

}
