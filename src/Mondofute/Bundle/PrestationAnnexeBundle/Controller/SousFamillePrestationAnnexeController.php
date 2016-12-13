<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Form\SousFamillePrestationAnnexeFamille;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * SousFamillePrestationAnnexe controller.
 *
 */
class SousFamillePrestationAnnexeController extends Controller
{
    /**
     * Lists all SousFamillePrestationAnnexe entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sousFamillePrestationAnnexes = $em->getRepository('MondofutePrestationAnnexeBundle:SousFamillePrestationAnnexe')->findAll();

        return $this->render('sousfamilleprestationannexe/index.html.twig', array(
            'sousFamillePrestationAnnexes' => $sousFamillePrestationAnnexes,
        ));
    }

    /**
     * Creates a new SousFamillePrestationAnnexe entity.
     *
     */
    public function newAction(Request $request)
    {
        $sousFamillePrestationAnnexe = new SousFamillePrestationAnnexe();
        $form = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\SousFamillePrestationAnnexeFamille',
            $sousFamillePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sousFamillePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('sousfamilleprestationannexe_show',
                array('id' => $sousFamillePrestationAnnexe->getId()));
        }

        return $this->render('sousfamilleprestationannexe/new.html.twig', array(
            'sousFamillePrestationAnnexe' => $sousFamillePrestationAnnexe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SousFamillePrestationAnnexe entity.
     *
     */
    public function showAction(SousFamillePrestationAnnexe $sousFamillePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($sousFamillePrestationAnnexe);

        return $this->render('sousfamilleprestationannexe/show.html.twig', array(
            'sousFamillePrestationAnnexe' => $sousFamillePrestationAnnexe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a SousFamillePrestationAnnexe entity.
     *
     * @param SousFamillePrestationAnnexe $sousFamillePrestationAnnexe The SousFamillePrestationAnnexe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SousFamillePrestationAnnexe $sousFamillePrestationAnnexe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sousfamilleprestationannexe_delete',
                array('id' => $sousFamillePrestationAnnexe->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing SousFamillePrestationAnnexe entity.
     *
     */
    public function editAction(Request $request, SousFamillePrestationAnnexe $sousFamillePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($sousFamillePrestationAnnexe);
        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\SousFamillePrestationAnnexeFamille',
            $sousFamillePrestationAnnexe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sousFamillePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('sousfamilleprestationannexe_edit',
                array('id' => $sousFamillePrestationAnnexe->getId()));
        }

        return $this->render('sousfamilleprestationannexe/edit.html.twig', array(
            'sousFamillePrestationAnnexe' => $sousFamillePrestationAnnexe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SousFamillePrestationAnnexe entity.
     *
     */
    public function deleteAction(Request $request, SousFamillePrestationAnnexe $sousFamillePrestationAnnexe)
    {
        $form = $this->createDeleteForm($sousFamillePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sousFamillePrestationAnnexe);
            $em->flush();
        }

        return $this->redirectToRoute('sousfamilleprestationannexe_index');
    }
}
