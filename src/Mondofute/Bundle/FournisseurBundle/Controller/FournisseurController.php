<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

//use commun\moyencommunicationBundle\Entity\Fixe;
//use commun\moyencommunicationBundle\Entity\Mobile;
//use commun\moyencommunicationBundle\Entity\MoyenCommunication;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Form\FournisseurType;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Fixe;
use Nucleus\MoyenComBundle\Entity\Mobile;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use ReflectionClass;
use Mondofute\Bundle\TrancheHoraireBundle\Entity\TrancheHoraire;
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
        $adresse = new Adresse();
//        $adresse->setCoordonneeGPS(new CoordonneesGPS());
        $fournisseur->addMoyenCom($adresse);
//        $fournisseur->addMoyenCom(ne)
//        dump($fournisseur->getMoyenComs());die;
//        $this->ajouterInterlocuteurMoyenComunnications($fournisseur);
//        dump($fournisseur);die;
        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur);
//        dump($form);die;
//        $moyenCom = new MoyenCommunication();
//        $form->add('');

//        $formMoyenComm = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\InterlocuteurMoyenCommunicationType');
//        $formMoyenComm->add(
//
//        )
//        die;
//        $formMoyenComm = $this->createForm();
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                $interlocuteur->setFournisseur($fournisseur);
                $interlocuteur->getInterlocuteur()
                    //                    ->setDateNaissance(new DateTime())
                ;
            }

            foreach ($fournisseur->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateCreation();
            }
            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                foreach ($interlocuteur->getInterlocuteur()->getMoyenComs() as $moyenCom) {
                    $moyenCom->setDateCreation();
                }
            }

            $this->copieVersSites($fournisseur);

            $em->persist($fournisseur);
            $em->flush();

            // add flash messages
            $this->addFlash(
                'success',
                'Le fournisseur a bien été créé.'
            );

            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
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

            $moyenComsSite = $fournisseurSite->getMoyenComs();
            if (!empty($moyenComsSite)) {
                foreach ($moyenComsSite as $key => $moyenComSite) {
                    $moyenComSite->setDateModification(new DateTime());
                    $moyenComSite->setDateCreation();
                    $moyenComsSite[$key] = clone $moyenComSite;
                }
            }

            if (!empty($fournisseurSite->getFournisseurParent())) {
                $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur',
                    $fournisseurSite->getFournisseurParent()->getId()));
            }

            $fournisseurSite->setType($emSite->find('MondofuteFournisseurBundle:TypeFournisseur', $fournisseurSite->getType()->getId()));

            foreach ($fournisseurSite->getInterlocuteurs() as $interlocuteur) {
                if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                    $interlocuteur->getInterlocuteur()->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction',
                        $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                }
                if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                    $interlocuteur->getInterlocuteur()->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur',
                        $interlocuteur->getInterlocuteur()->getService()->getId()));
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

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $deleteForm = $this->createDeleteForm($fournisseur);
        $fournisseur->triReceptions();
        $fournisseur->triRemiseClefs();
        $editForm = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur)
            ->add('submit', SubmitType::class, array('label' => 'mettre.a.jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach ($originalInterlocuteurs as $interlocuteur) {
                if (false === $fournisseur->getInterlocuteurs()->contains($interlocuteur)) {


                    // if it was a many-to-one relationship, remove the relationship like this
                    $this->deleteInterlocuteurSites($interlocuteur);
                    $this->deleteMoyenComs($interlocuteur->getInterlocuteur());
                    $interlocuteur->setFournisseur(null);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($interlocuteur);
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

            $this->mAJSites($fournisseur);

            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                $interlocuteur->setFournisseur($fournisseur);
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

        return $this->render('@MondofuteFournisseur/fournisseur/edit.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $editForm->createView(),
            'langues' => $langues,
            'delete_form' => $deleteForm->createView(),
        ));
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
                $this->deleteMoyenComs($interlocuteurSite->getInterlocuteur());

//                $moyenComs = $interlocuteurSite->getInterlocuteur()->getMoyenComs();
//                if (!empty($moyenComs))
//                {
//                    foreach ($moyenComs as $moyenCom)
//                    {
//                        $interlocuteurSite->getInterlocuteur()->removeMoyenCom($moyenCom);
//                    }
//                }

                $interlocuteurSite->setFournisseur(null);

                $emSite->remove($interlocuteurSite);
                
            }


        }
    }

    /**
     * @param $entity
     */
    private function deleteMoyenComs($entity)
    {
        $moyenComs = $entity->getMoyenComs();
        if (!empty($moyenComs)) {
            foreach ($moyenComs as $moyenCom) {
                $entity->removeMoyenCom($moyenCom);
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
        /** @var FournisseurInterlocuteur $interlocuteurSite */
        /** @var Site $site */
        /** @var FournisseurInterlocuteur $interlocuteur */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $fournisseurSite = $emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur->getId());

            $fournisseurSite->setEnseigne($fournisseur->getEnseigne());
            $fournisseurSite->setType($emSite->find('MondofuteFournisseurBundle:TypeFournisseur', $fournisseur->getType()->getId()));
            $fournisseurSite->setContient($fournisseur->getContient());
            $fournisseurSite->setDateModification(new DateTime());


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
                        $adresse->setPays($moyenCom->getPays());
                        $adresse->setDateModification(new DateTime());
                        break;
                    default:
                        break;
                }
            }

            if (!empty($fournisseur->getFournisseurParent())) {
                $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur->getFournisseurParent()->getId()));
            } else {
                $fournisseurSite->setFournisseurParent(null);
            }

            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {

                $interlocuteurSite = $fournisseurSite->getInterlocuteurs()->filter(function (FournisseurInterlocuteur $element) use ($interlocuteur) {
                    return $element->getId() == $interlocuteur->getId();
                })->first();

                if (!empty($interlocuteurSite)) {
                    $interlocuteurSite->getInterlocuteur()->setPrenom($interlocuteur->getInterlocuteur()->getPrenom());
                    $interlocuteurSite->getInterlocuteur()->setNom($interlocuteur->getInterlocuteur()->getNom());

                    if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                        $interlocuteurSite->getInterlocuteur()->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction', $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                    } else {
                        $interlocuteurSite->getInterlocuteur()->setFonction(null);
                    }
                    if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                        $interlocuteurSite->getInterlocuteur()->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur', $interlocuteur->getInterlocuteur()->getService()->getId()));
                    } else {
                        $interlocuteurSite->getInterlocuteur()->setService(null);
                    }
                    $interlocuteurSite->getInterlocuteur()->setDateModification(new DateTime());

                    $moyenComsSite = $interlocuteurSite->getInterlocuteur()->getMoyenComs();
                    if (!empty($moyenComsSite)) {
                        // todo : maj les moyens de communications
                        foreach ($moyenComsSite as $key => $moyenComSite) {
                            $typeComm = (new ReflectionClass($moyenComSite))->getShortName();
//                            switch ($typeComm)
                            $firstFixe = true;
                            switch ($typeComm) {
                                case 'Adresse':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function ($element) {
                                        return (new ReflectionClass($element))->getShortName() == 'Adresse';
                                    })->first();
                                    $moyenComSite->setCodePostal($moyenComCrm->getCodePostal());
                                    $moyenComSite->setAdresse1($moyenComCrm->getAdresse1());
                                    $moyenComSite->setAdresse2($moyenComCrm->getAdresse2());
                                    $moyenComSite->setAdresse3($moyenComCrm->getAdresse3());
                                    $moyenComSite->setVille($moyenComCrm->getVille());
                                    $moyenComSite->setPays($moyenComCrm->getPays());
                                    $moyenComSite->setPays($moyenComCrm->getPays());
                                    $moyenComSite->getCoordonneeGPS()->setLatitude($moyenComCrm->getCoordonneeGPS()->getLatitude());
                                    $moyenComSite->getCoordonneeGPS()->setLongitude($moyenComCrm->getCoordonneeGPS()->getLongitude());
                                    $moyenComSite->getCoordonneeGPS()->setPrecis($moyenComCrm->getCoordonneeGPS()->getPrecis());
                                    $moyenComSite->setDateModification(new DateTime());
                                    break;
                                case 'Email':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function ($element) {
                                        return (new ReflectionClass($element))->getShortName() == 'Email';
                                    })->first();
                                    $moyenComSite->setAdresse($moyenComCrm->getAdresse());
                                    $moyenComSite->setDateModification(new DateTime());
                                    break;
                                case 'Mobile':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function ($element) {
                                        return (new ReflectionClass($element))->getShortName() == 'Mobile';
                                    })->first();
                                    $moyenComSite->setNumero($moyenComCrm->getnumero());
                                    $moyenComSite->setDateModification(new DateTime());
                                    break;
                                case 'Fixe':
                                    $moyenComCrm = $interlocuteur->getInterlocuteur()->getMoyenComs()->filter(function ($element) {
                                        return (new ReflectionClass($element))->getShortName() == 'Fixe';
                                    });
                                    if ($firstFixe) {
                                        $moyenComSite->setNumero($moyenComCrm->first()->getNumero());
                                        $moyenComSite->setDateModification(new DateTime());
                                        $firstFixe = false;
                                    } else {
                                        $moyenComSite->setNumero($moyenComSite->last()->getnumero());
                                        $moyenComSite->setDateModification(new DateTime());
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
                        $interlocuteurSite->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction', $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                    }
                    if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                        $interlocuteurSite->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur', $interlocuteur->getInterlocuteur()->getService()->getId()));
                    }

                    $fournisseurInterlocuteurSite->setFournisseur($fournisseurSite);
                    $fournisseurInterlocuteurSite->setInterlocuteur($interlocuteurSite);

                    foreach ($interlocuteur->getInterlocuteur()->getMoyenComs() as $moyenCom) {
                        $moyenCom->setDateCreation();
                        $interlocuteurSite->addMoyenCom(clone $moyenCom);
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
                    $this->deleteMoyenComs($fournisseurSite);
                    $interlocuteurs = $fournisseurSite->getInterlocuteurs();
                    if (!empty($interlocuteurs)) {
                        foreach ($interlocuteurs as $interlocuteur) {
                            $this->deleteMoyenComs($interlocuteur->getInterlocuteur());
//                            $moyenComs = $interlocuteur->getInterlocuteur()->getMoyenComs();
//                            if (!empty($moyenComs)) {
//                                foreach ($moyenComs as $moyenCom) {
//                                    $interlocuteur->getInterlocuteur()->removeMoyenCom($moyenCom);
//                                }
//                            }
                        }
                    }
                    // ***** fin suppression des moyen de communications *****

                    if (!empty($fournisseurSite)) {
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
                $this->deleteMoyenComs($fournisseur);
                $interlocuteurs = $fournisseur->getInterlocuteurs();
                if (!empty($interlocuteurs)) {
                    foreach ($interlocuteurs as $interlocuteur) {
//                        $moyenComs = $interlocuteur->getInterlocuteur()->getMoyenComs();
//                        if (!empty($moyenComs)) {
//                            foreach ($moyenComs as $moyenCom) {
//                                $interlocuteur->getInterlocuteur()->removeMoyenCom($moyenCom);
//                            }
//                        }
                        $this->deleteMoyenComs($interlocuteur->getInterlocuteur());
                    }
                }
                // ***** fin suppression des moyen de communications *****

                $em->remove($fournisseur);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $except) {
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
