<?php

namespace HiDev\Bundle\CodePromoBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HiDev\Bundle\CodePromoBundle\Entity\CodePromo;
use HiDev\Bundle\CodePromoBundle\Form\CodePromoType;

/**
 * CodePromo controller.
 *
 */
class CodePromoController extends Controller
{
//    /**
//     * Lists all CodePromo entities.
//     *
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $codePromos = $em->getRepository('HiDevCodePromoBundle:CodePromo')->findAll();
//
//        return $this->render('@HiDevCodePromo/codepromo/index.html.twig', array(
//            'codePromos' => $codePromos,
//        ));
//    }
//
//    /**
//     * Creates a new CodePromo entity.
//     *
//     */
//    public function newAction(Request $request)
//    {
//        $codePromo = new CodePromo();
//        $form = $this->createForm('HiDev\Bundle\CodePromoBundle\Form\CodePromoType', $codePromo);
//        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
//        $form->handleRequest($request);
////        dump($codePromo);die;
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($codePromo);
//            $em->flush();
//
//            return $this->redirectToRoute('codepromo_show', array('id' => $codePromo->getId()));
//        }
//
//        return $this->render('@HiDevCodePromo/codepromo/new.html.twig', array(
//            'codePromo' => $codePromo,
//            'form' => $form->createView(),
//        ));
//    }
//
//    /**
//     * Finds and displays a CodePromo entity.
//     *
//     */
//    public function showAction(CodePromo $codePromo)
//    {
//        $deleteForm = $this->createDeleteForm($codePromo);
//
//        return $this->render('@HiDevCodePromo/codepromo/show.html.twig', array(
//            'codePromo' => $codePromo,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Displays a form to edit an existing CodePromo entity.
//     *
//     */
//    public function editAction(Request $request, CodePromo $codePromo)
//    {
//        $deleteForm = $this->createDeleteForm($codePromo);
//        $editForm = $this->createForm('HiDev\Bundle\CodePromoBundle\Form\CodePromoType', $codePromo)
//            ->add('submit', SubmitType::class, array('label' => 'Mettre à jours'))
//        ;
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($codePromo);
//            $em->flush();
//
//            return $this->redirectToRoute('codepromo_edit', array('id' => $codePromo->getId()));
//        }
//
//        return $this->render('@HiDevCodePromo/codepromo/edit.html.twig', array(
//            'codePromo' => $codePromo,
//            'form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Deletes a CodePromo entity.
//     *
//     */
//    public function deleteAction(Request $request, CodePromo $codePromo)
//    {
//        $form = $this->createDeleteForm($codePromo);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($codePromo);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('codepromo_index');
//    }
//
//    /**
//     * Creates a form to delete a CodePromo entity.
//     *
//     * @param CodePromo $codePromo The CodePromo entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm(CodePromo $codePromo)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('codepromo_delete', array('id' => $codePromo->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
}
