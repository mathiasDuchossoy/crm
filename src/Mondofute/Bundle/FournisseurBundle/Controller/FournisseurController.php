<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

//use commun\moyencommunicationBundle\Entity\Fixe;
//use commun\moyencommunicationBundle\Entity\Mobile;
//use commun\moyencommunicationBundle\Entity\MoyenCommunication;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Fixe;
use Nucleus\MoyenComBundle\Entity\Mobile;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Proxies\__CG__\Nucleus\MoyenComBundle\Entity\Email;
use ReflectionClass;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Form\FournisseurType;

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
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $fournisseur = new Fournisseur();
        $adresse = new Adresse();
        $adresse->setCoordonneeGPS(new CoordonneesGPS());
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
//        echo 'coucou';die;
        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                $interlocuteur->setFournisseur($fournisseur);
                $interlocuteur->getInterlocuteur()//                    ->setDateNaissance(new DateTime())
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
                $fournisseurSite->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseurSite->getFournisseurParent()->getId()));
            }

            $fournisseurSite->setType($emSite->find('MondofuteFournisseurBundle:TypeFournisseur', $fournisseurSite->getType()->getId()));

            foreach ($fournisseurSite->getInterlocuteurs() as $interlocuteur) {
                if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                    $interlocuteur->getInterlocuteur()->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction', $interlocuteur->getInterlocuteur()->getFonction()->getId()));
                }
                if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                    $interlocuteur->getInterlocuteur()->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur', $interlocuteur->getInterlocuteur()->getService()->getId()));
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
            ->add('Supprimer', SubmitType::class)
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

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
            $originalInterlocuteurs->add($interlocuteur);
        }

        $em = $this->getDoctrine()->getManager();
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $deleteForm = $this->createDeleteForm($fournisseur);
        $editForm = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach ($originalInterlocuteurs as $interlocuteur) {
                if (false === $fournisseur->getInterlocuteurs()->contains($interlocuteur)) {
                    // if it was a many-to-one relationship, remove the relationship like this
                    $this->deleteInterlocuteurSites($interlocuteur);

                    $interlocuteur->setFournisseur(null);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($interlocuteur);
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

            $interlocuteurSite = $emSite->find('MondofuteFournisseurBundle:FournisseurInterlocuteur', $interlocuteur->getId());

            $interlocuteurSite->setFournisseur(null);

            $emSite->remove($interlocuteurSite);
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

                    $moyenComs = $interlocuteurSite->getInterlocuteur()->getMoyenComs();
                    if (!empty($moyenComs)) {
                        // todo : maj les moyens de communications
                    }

                } else {
                    $fournisseurInterlocuteurSite = new FournisseurInterlocuteur();

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

                    $fournisseurSite->addInterlocuteur($fournisseurInterlocuteurSite);

                }

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
                    $moyenComs = $fournisseurSite->getMoyenComs();
                    if (!empty($moyenComs)) {
                        foreach ($moyenComs as $moyenCom) {
                            $fournisseurSite->removeMoyenCom($moyenCom);
                        }
                    }
                    $interlocuteurs = $fournisseurSite->getInterlocuteurs();
                    if (!empty($interlocuteurs)) {
                        foreach ($interlocuteurs as $interlocuteur) {
                            $moyenComs = $interlocuteur->getInterlocuteur()->getMoyenComs();
                            if (!empty($moyenComs)) {
                                foreach ($moyenComs as $moyenCom) {
                                    $interlocuteur->getInterlocuteur()->removeMoyenCom($moyenCom);
                                }
                            }
                        }
                    }
                    // ***** fin suppression des moyen de communications *****

                    if (!empty($fournisseurSite)) {
                        $emSite->remove($fournisseurSite);
                        $emSite->flush();
                    }
                }

                // ***** suppression des moyen de communications *****
                $moyenComs = $fournisseur->getMoyenComs();
                if (!empty($moyenComs)) {
                    foreach ($moyenComs as $moyenCom) {
                        $fournisseur->removeMoyenCom($moyenCom);
                    }
                }
                $interlocuteurs = $fournisseur->getInterlocuteurs();
                if (!empty($interlocuteurs)) {
                    foreach ($interlocuteurs as $interlocuteur) {
                        $moyenComs = $interlocuteur->getInterlocuteur()->getMoyenComs();
                        if (!empty($moyenComs)) {
                            foreach ($moyenComs as $moyenCom) {
                                $interlocuteur->getInterlocuteur()->removeMoyenCom($moyenCom);
                            }
                        }
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

    public function chargerFormInterlocuteur()
    {
        $interlocuteur = new Interlocuteur();
        $interlocuteur->getMoyenComs()
            ->add(new Adresse());
        $interlocuteur
            ->addMoyenCom(new Adresse())
            ->addMoyenCom(new Fixe())
            ->addMoyenCom(new Fixe())
            ->addMoyenCom(new Mobile())
            ->addMoyenCom(new Email());

        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\InterlocuteurType', $interlocuteur);

        return $this->render('@MondofuteFournisseur/fournisseur/new.html.twig', array(
            'interlocuteur' => $interlocuteur,
            'form' => $form->createView(),
        ));
        
    }

}
