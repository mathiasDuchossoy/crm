<?php

namespace Mondofute\Bundle\UtilisateurBundle\Controller;

use DateTime;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Nucleus\MoyenComBundle\Entity\Email;
use ReflectionClass;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Mondofute\Bundle\UtilisateurBundle\Form\UtilisateurType;

/**
 * Utilisateur controller.
 *
 */
class UtilisateurController extends Controller
{
    /**
     * Lists all Utilisateur entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $utilisateurs = $em->getRepository('MondofuteUtilisateurBundle:Utilisateur')->findAll();

        return $this->render('@MondofuteUtilisateur/utilisateur/index.html.twig', array(
            'utilisateurs' => $utilisateurs,
        ));
    }

    /**
     * Creates a new Utilisateur entity.
     *
     */
    public function newAction(Request $request)
    {
        /** @var Site $site */
        $utilisateur = new Utilisateur();
        $this->addMoyenComs($utilisateur);
        $form = $this->createForm('Mondofute\Bundle\UtilisateurBundle\Form\UtilisateurType', $utilisateur);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getEntityManager();


            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateCreation();
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $utilisateur->setLogin($login);
                }
            }
            $utilisateur->setDateCreation();

            if (!$this->loginExist($utilisateur)) {
                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $utilisateurClone = clone $utilisateur;
                    $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

                    $this->dupliquerMoyenComs($utilisateur);

                    $emSite->persist($utilisateurClone);

                    $emSite->flush();
                }

                $em->persist($utilisateur);

                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le utilisateur ' . $utilisateur->getLogin() . ' a bien été créé.'
                );

                return $this->redirectToRoute('utilisateur_edit', array('id' => $utilisateur->getId()));
            }

        }

        return $this->render('@MondofuteUtilisateur/utilisateur/new.html.twig', array(
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Utilisateur $utilisateur
     */
    private function addMoyenComs(Utilisateur $utilisateur)
    {
        $utilisateur
            ->addMoyenCom(new Email());
    }

    private function loginExist(Utilisateur $utilisateur)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $utilisateurSite = $em->getRepository(Utilisateur::class)->findOneBy(array('login' => $utilisateur->getLogin()));
        if (!empty($utilisateurSite) && $utilisateur != $utilisateurSite) {
            $this->addFlash(
                'error',
                'Le Login/Email ' . $utilisateur->getLogin() . ' éxiste déjà.'
            );

            return true;
        }
        return false;
    }

    private function dupliquerMoyenComs(Utilisateur $utilisateur)
    {
        foreach ($utilisateur->getMoyenComs() as $moyenCom) {
            $typeComm = (new ReflectionClass($moyenCom))->getShortName();
            switch ($typeComm) {
                case 'Email':
                    /** @var Email $moyenCom */
                    $newMoyenCom = new Email();
                    $newMoyenCom
                        ->setAdresse($moyenCom->getAdresse());
                    break;
                default:
                    break;
            }
            if (!empty($newMoyenCom)) {
//                $newMoyenCom->setDateCreation($moyenCom->getDateCreation());
                $newMoyenCom->setDateCreation();
                $utilisateur->removeMoyenCom($moyenCom);
                $utilisateur->addMoyenCom($newMoyenCom);
            }
        }
    }

    /**
     * Finds and displays a Utilisateur entity.
     *
     */
    public function showAction(Utilisateur $utilisateur)
    {
        $deleteForm = $this->createDeleteForm($utilisateur);

        return $this->render('@MondofuteUtilisateur/utilisateur/show.html.twig', array(
            'utilisateur' => $utilisateur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Utilisateur entity.
     *
     * @param Utilisateur $utilisateur The Utilisateur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Utilisateur $utilisateur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_delete', array('id' => $utilisateur->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Utilisateur entity.
     *
     */
    public function editAction(Request $request, Utilisateur $utilisateur)
    {
        $deleteForm = $this->createDeleteForm($utilisateur);
        $editForm = $this->createForm('Mondofute\Bundle\UtilisateurBundle\Form\UtilisateurType', $utilisateur)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $dateModification = new DateTime();

            $utilisateur->setDateModification($dateModification);
            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateModification($dateModification);
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $utilisateur->setLogin($login);
                }
            }

            if (!$this->loginExist($utilisateur)) {

                $em = $this->getDoctrine()->getManager();

                $this->majSites($utilisateur);

                $em->persist($utilisateur);
                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le utilisateur ' . $utilisateur->getLogin() . ' a bien été modifié.'
                );

                return $this->redirectToRoute('utilisateur_edit', array('id' => $utilisateur->getId()));
            }
        }

        return $this->render('@MondofuteUtilisateur/utilisateur/edit.html.twig', array(
            'utilisateur' => $utilisateur,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function majSites(Utilisateur $utilisateur)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
            $utilisateurSite = $emSite->find(Utilisateur::class, $utilisateur);
            $utilisateurSite
                ->setPassword($utilisateur->getPassword())
                ->setLogin($utilisateur->getPassword())
                ->setNom($utilisateur->getNom())
                ->setPrenom($utilisateur->getPrenom());
            $utilisateurSite->setDateModification($utilisateur->getDateModification());

            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                $moyenComSite = $utilisateurSite->getMoyenComs()->filter(function ($element) use ($typeComm) {
                    $typeCommSite = (new ReflectionClass($element))->getShortName();
                    return $typeCommSite == $typeComm;
                });
                switch ($typeComm) {
                    case 'Email':
                        /** @var Email $email */
                        foreach ($moyenComSite as $key => $email) {
                            $moyenComSite->get($key)
                                ->setAdresse($moyenCom->getAdresse())
                                ->setDateModification($moyenCom->getDateModification());
                        }
                        break;
                    default:
                        break;
                }
            }

            $emSite->persist($utilisateurSite);
            $emSite->flush();
        }
    }

    /**
     * Deletes a Utilisateur entity.
     *
     */
    public function deleteAction(Request $request, Utilisateur $utilisateur)
    {
        $form = $this->createDeleteForm($utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
                $utilisateurSite = $emSite->find(Utilisateur::class, $utilisateur);

                foreach ($utilisateurSite->getMoyenComs() as $moyenComSite) {
                    $utilisateurSite->removeMoyenCom($moyenComSite);
                    $emSite->remove($moyenComSite);
                }

                $emSite->flush();

                $emSite->remove($utilisateurSite);
                $emSite->flush();
            }

            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
                $utilisateur->removeMoyenCom($moyenCom);
                $em->remove($moyenCom);
            }

            $em->flush();

            $em->remove($utilisateur);
            $em->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }
}
