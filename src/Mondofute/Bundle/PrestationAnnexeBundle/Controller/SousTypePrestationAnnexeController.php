<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousTypePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Form\SousTypePrestationAnnexeType;

/**
 * SousTypePrestationAnnexe controller.
 *
 */
class SousTypePrestationAnnexeController extends Controller
{
    /**
     * Lists all SousTypePrestationAnnexe entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sousTypePrestationAnnexes = $em->getRepository('MondofutePrestationAnnexeBundle:SousTypePrestationAnnexe')->findAll();

        return $this->render('soustypeprestationannexe/index.html.twig', array(
            'sousTypePrestationAnnexes' => $sousTypePrestationAnnexes,
        ));
    }

    /**
     * Creates a new SousTypePrestationAnnexe entity.
     *
     */
    public function newAction(Request $request)
    {
        $sousTypePrestationAnnexe = new SousTypePrestationAnnexe();
        $form = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\SousTypePrestationAnnexeType', $sousTypePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sousTypePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('soustypeprestationannexe_show', array('id' => $sousTypePrestationAnnexe->getId()));
        }

        return $this->render('soustypeprestationannexe/new.html.twig', array(
            'sousTypePrestationAnnexe' => $sousTypePrestationAnnexe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SousTypePrestationAnnexe entity.
     *
     */
    public function showAction(SousTypePrestationAnnexe $sousTypePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($sousTypePrestationAnnexe);

        return $this->render('soustypeprestationannexe/show.html.twig', array(
            'sousTypePrestationAnnexe' => $sousTypePrestationAnnexe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SousTypePrestationAnnexe entity.
     *
     */
    public function editAction(Request $request, SousTypePrestationAnnexe $sousTypePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($sousTypePrestationAnnexe);
        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\SousTypePrestationAnnexeType', $sousTypePrestationAnnexe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sousTypePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('soustypeprestationannexe_edit', array('id' => $sousTypePrestationAnnexe->getId()));
        }

        return $this->render('soustypeprestationannexe/edit.html.twig', array(
            'sousTypePrestationAnnexe' => $sousTypePrestationAnnexe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SousTypePrestationAnnexe entity.
     *
     */
    public function deleteAction(Request $request, SousTypePrestationAnnexe $sousTypePrestationAnnexe)
    {
        $form = $this->createDeleteForm($sousTypePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sousTypePrestationAnnexe);
            $em->flush();
        }

        return $this->redirectToRoute('soustypeprestationannexe_index');
    }

    /**
     * Creates a form to delete a SousTypePrestationAnnexe entity.
     *
     * @param SousTypePrestationAnnexe $sousTypePrestationAnnexe The SousTypePrestationAnnexe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SousTypePrestationAnnexe $sousTypePrestationAnnexe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('soustypeprestationannexe_delete', array('id' => $sousTypePrestationAnnexe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
