<?php

namespace Mondofute\Bundle\CodePromoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\CodePromoBundle\Form\CodePromoType;

/**
 * CodePromo controller.
 *
 */
class CodePromoController extends Controller
{
    /**
     * Lists all CodePromo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $codePromos = $em->getRepository('MondofuteCodePromoBundle:CodePromo')->findAll();

        return $this->render('@MondofuteCodePromo/codepromo/index.html.twig', array(
            'codePromos' => $codePromos,
        ));
    }

    /**
     * Creates a new CodePromo entity.
     *
     */
    public function newAction(Request $request)
    {
        $codePromo = new CodePromo();
        $form = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoType', $codePromo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($codePromo);
            $em->flush();

            return $this->redirectToRoute('codepromo_show', array('id' => $codePromo->getId()));
        }

        return $this->render('@MondofuteCodePromo/codepromo/new.html.twig', array(
            'codePromo' => $codePromo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CodePromo entity.
     *
     */
    public function showAction(CodePromo $codePromo)
    {
        $deleteForm = $this->createDeleteForm($codePromo);

        return $this->render('@MondofuteCodePromo/codepromo/show.html.twig', array(
            'codePromo' => $codePromo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CodePromo entity.
     *
     */
    public function editAction(Request $request, CodePromo $codePromo)
    {
        $deleteForm = $this->createDeleteForm($codePromo);
        $editForm = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoType', $codePromo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($codePromo);
            $em->flush();

            return $this->redirectToRoute('codepromo_edit', array('id' => $codePromo->getId()));
        }

        return $this->render('@MondofuteCodePromo/codepromo/edit.html.twig', array(
            'codePromo' => $codePromo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CodePromo entity.
     *
     */
    public function deleteAction(Request $request, CodePromo $codePromo)
    {
        $form = $this->createDeleteForm($codePromo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($codePromo);
            $em->flush();
        }

        return $this->redirectToRoute('codepromo_index');
    }

    /**
     * Creates a form to delete a CodePromo entity.
     *
     * @param CodePromo $codePromo The CodePromo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CodePromo $codePromo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('codepromo_delete', array('id' => $codePromo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
