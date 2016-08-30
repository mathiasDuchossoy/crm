<?php

namespace Mondofute\Bundle\CodePromoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie;
use Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType;

/**
 * CodePromoUnifie controller.
 *
 */
class CodePromoUnifieController extends Controller
{
    /**
     * Lists all CodePromoUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $codePromoUnifies = $em->getRepository('MondofuteCodePromoBundle:CodePromoUnifie')->findAll();

        return $this->render('@MondofuteCodePromo/codepromounifie/index.html.twig', array(
            'codePromoUnifies' => $codePromoUnifies,
        ));
    }

    /**
     * Creates a new CodePromoUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $codePromoUnifie = new CodePromoUnifie();
        $form = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType', $codePromoUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($codePromoUnifie);
            $em->flush();

            return $this->redirectToRoute('codepromo_show', array('id' => $codePromoUnifie->getId()));
        }

        return $this->render('@MondofuteCodePromo/codepromounifie/new.html.twig', array(
            'codePromoUnifie' => $codePromoUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CodePromoUnifie entity.
     *
     */
    public function showAction(CodePromoUnifie $codePromoUnifie)
    {
        $deleteForm = $this->createDeleteForm($codePromoUnifie);

        return $this->render('@MondofuteCodePromo/codepromounifie/show.html.twig', array(
            'codePromoUnifie' => $codePromoUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CodePromoUnifie entity.
     *
     */
    public function editAction(Request $request, CodePromoUnifie $codePromoUnifie)
    {
        $deleteForm = $this->createDeleteForm($codePromoUnifie);
        $editForm = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType', $codePromoUnifie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($codePromoUnifie);
            $em->flush();

            return $this->redirectToRoute('codepromo_edit', array('id' => $codePromoUnifie->getId()));
        }

        return $this->render('@MondofuteCodePromo/codepromounifie/edit.html.twig', array(
            'codePromoUnifie' => $codePromoUnifie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CodePromoUnifie entity.
     *
     */
    public function deleteAction(Request $request, CodePromoUnifie $codePromoUnifie)
    {
        $form = $this->createDeleteForm($codePromoUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($codePromoUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('codepromo_index');
    }

    /**
     * Creates a form to delete a CodePromoUnifie entity.
     *
     * @param CodePromoUnifie $codePromoUnifie The CodePromoUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CodePromoUnifie $codePromoUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('codepromo_delete', array('id' => $codePromoUnifie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
