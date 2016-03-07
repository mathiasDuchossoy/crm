<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Proxies\__CG__\Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
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
        $form = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($fournisseur->getInterlocuteurs() as $interlocuteur) {
                $interlocuteur->setFournisseur($fournisseur);
            }
            $this->copieVersSites($fournisseur);

            $em->persist($fournisseur);
            $em->flush();

            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
        }

        return $this->render('@MondofuteFournisseur/fournisseur/new.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $form->createView(),
        ));
    }

    public function copieVersSites(Fournisseur $fournisseur)
    {
        /** @var Site $site */
        /** @var FournisseurInterlocuteur $interlocuteur */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $fournisseurSite = clone $fournisseur;

            foreach ($fournisseurSite->getInterlocuteurs() as $interlocuteur) {
                if (!empty($interlocuteur->getInterlocuteur()->getFonction())) {
                    $interlocuteur->getInterlocuteur()->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction', $interlocuteur->getInterlocuteur()->getFonction()->getId()));
//                    $interlocuteur->getInterlocuteur()->setFonction(null);
//                    dump($interlocuteur->getInterlocuteur()->getFonction());
                }
                if (!empty($interlocuteur->getInterlocuteur()->getService())) {
                    $interlocuteur->getInterlocuteur()->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur', $interlocuteur->getInterlocuteur()->getService()->getId()));
//                    $interlocuteur->getInterlocuteur()->setService(null);
                }
            }
            dump($fournisseurSite);
//            die;
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
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Fournisseur entity.
     *
     */
    public function editAction(Request $request, Fournisseur $fournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        $serviceInterlocuteurs = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();
        $deleteForm = $this->createDeleteForm($fournisseur);
        $editForm = $this->createForm('Mondofute\Bundle\FournisseurBundle\Form\FournisseurType', $fournisseur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em = $this->getDoctrine()->getManager();
            $em->persist($fournisseur);
            $em->flush();

            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
        }

        return $this->render('@MondofuteFournisseur/fournisseur/edit.html.twig', array(
            'serviceInterlocuteurs' => $serviceInterlocuteurs,
            'fournisseur' => $fournisseur,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
            $em = $this->getDoctrine()->getManager();
            $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $fournisseurSite = $emSite->find('MondofuteFournisseurBundle:Fournisseur', $fournisseur->getId());
                if (!empty($fournisseurSite)) {
                    $emSite->remove($fournisseurSite);
                    $emSite->flush();
                }
            }

            $em->remove($fournisseur);
            $em->flush();
        }

        return $this->redirectToRoute('fournisseur_index');
    }

    public function getFormInterlocuteur()
    {

    }

}
