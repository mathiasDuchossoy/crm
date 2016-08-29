<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\TypePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Form\TypePrestationAnnexeType;

/**
 * TypePrestationAnnexe controller.
 *
 */
class TypePrestationAnnexeController extends Controller
{
    /**
     * Lists all TypePrestationAnnexe entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $typePrestationAnnexes = $em->getRepository('MondofutePrestationAnnexeBundle:TypePrestationAnnexe')->findAll();

        return $this->render('typeprestationannexe/index.html.twig', array(
            'typePrestationAnnexes' => $typePrestationAnnexes,
        ));
    }

    /**
     * Creates a new TypePrestationAnnexe entity.
     *
     */
    public function newAction(Request $request)
    {
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $form = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\TypePrestationAnnexeType', $typePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('typeprestationannexe_show', array('id' => $typePrestationAnnexe->getId()));
        }

        return $this->render('typeprestationannexe/new.html.twig', array(
            'typePrestationAnnexe' => $typePrestationAnnexe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TypePrestationAnnexe entity.
     *
     */
    public function showAction(TypePrestationAnnexe $typePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($typePrestationAnnexe);

        return $this->render('typeprestationannexe/show.html.twig', array(
            'typePrestationAnnexe' => $typePrestationAnnexe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TypePrestationAnnexe entity.
     *
     */
    public function editAction(Request $request, TypePrestationAnnexe $typePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($typePrestationAnnexe);
        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\TypePrestationAnnexeType', $typePrestationAnnexe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('typeprestationannexe_edit', array('id' => $typePrestationAnnexe->getId()));
        }

        return $this->render('typeprestationannexe/edit.html.twig', array(
            'typePrestationAnnexe' => $typePrestationAnnexe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TypePrestationAnnexe entity.
     *
     */
    public function deleteAction(Request $request, TypePrestationAnnexe $typePrestationAnnexe)
    {
        $form = $this->createDeleteForm($typePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($typePrestationAnnexe);
            $em->flush();
        }

        return $this->redirectToRoute('typeprestationannexe_index');
    }

    /**
     * Creates a form to delete a TypePrestationAnnexe entity.
     *
     * @param TypePrestationAnnexe $typePrestationAnnexe The TypePrestationAnnexe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TypePrestationAnnexe $typePrestationAnnexe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('typeprestationannexe_delete', array('id' => $typePrestationAnnexe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
