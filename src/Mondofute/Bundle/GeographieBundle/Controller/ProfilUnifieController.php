<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie;
use Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType;

/**
 * ProfilUnifie controller.
 *
 */
class ProfilUnifieController extends Controller
{
    /**
     * Lists all ProfilUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $profilUnifies = $em->getRepository('MondofuteGeographieBundle:ProfilUnifie')->findAll();

        return $this->render('profilunifie/index.html.twig', array(
            'profilUnifies' => $profilUnifies,
        ));
    }

    /**
     * Creates a new ProfilUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $profilUnifie = new ProfilUnifie();
        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType', $profilUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profilUnifie);
            $em->flush();

            return $this->redirectToRoute('profil_show', array('id' => $profilunifie->getId()));
        }

        return $this->render('profilunifie/new.html.twig', array(
            'profilUnifie' => $profilUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ProfilUnifie entity.
     *
     */
    public function showAction(ProfilUnifie $profilUnifie)
    {
        $deleteForm = $this->createDeleteForm($profilUnifie);

        return $this->render('profilunifie/show.html.twig', array(
            'profilUnifie' => $profilUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ProfilUnifie entity.
     *
     * @param ProfilUnifie $profilUnifie The ProfilUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProfilUnifie $profilUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('profil_delete', array('id' => $profilUnifie->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ProfilUnifie entity.
     *
     */
    public function editAction(Request $request, ProfilUnifie $profilUnifie)
    {
        $deleteForm = $this->createDeleteForm($profilUnifie);
        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ProfilUnifieType', $profilUnifie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profilUnifie);
            $em->flush();

            return $this->redirectToRoute('profil_edit', array('id' => $profilUnifie->getId()));
        }

        return $this->render('profilunifie/edit.html.twig', array(
            'profilUnifie' => $profilUnifie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProfilUnifie entity.
     *
     */
    public function deleteAction(Request $request, ProfilUnifie $profilUnifie)
    {
        $form = $this->createDeleteForm($profilUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profilUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('profil_index');
    }
}
