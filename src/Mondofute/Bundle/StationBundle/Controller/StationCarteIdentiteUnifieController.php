<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\StationBundle\Entity\StationCarteIdentiteUnifie;
use Mondofute\Bundle\StationBundle\Form\StationCarteIdentiteUnifieType;

/**
 * StationCarteIdentiteUnifie controller.
 *
 */
class StationCarteIdentiteUnifieController extends Controller
{
    /**
     * Lists all StationCarteIdentiteUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stationCarteIdentiteUnifies = $em->getRepository('MondofuteStationBundle:StationCarteIdentiteUnifie')->findAll();

        return $this->render('stationcarteidentiteunifie/index.html.twig', array(
            'stationCarteIdentiteUnifies' => $stationCarteIdentiteUnifies,
        ));
    }

    /**
     * Creates a new StationCarteIdentiteUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $stationCarteIdentiteUnifie = new StationCarteIdentiteUnifie();
        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCarteIdentiteUnifieType', $stationCarteIdentiteUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stationCarteIdentiteUnifie);
            $em->flush();

            return $this->redirectToRoute('stationcarteidentite_show', array('id' => $stationCarteIdentiteUnifie->getId()));
        }

        return $this->render('stationcarteidentiteunifie/new.html.twig', array(
            'stationCarteIdentiteUnifie' => $stationCarteIdentiteUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a StationCarteIdentiteUnifie entity.
     *
     */
    public function showAction(StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationCarteIdentiteUnifie);

        return $this->render('stationcarteidentiteunifie/show.html.twig', array(
            'stationCarteIdentiteUnifie' => $stationCarteIdentiteUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a StationCarteIdentiteUnifie entity.
     *
     * @param StationCarteIdentiteUnifie $stationCarteIdentiteUnifie The StationCarteIdentiteUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stationcarteidentite_delete', array('id' => $stationCarteIdentiteUnifie->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing StationCarteIdentiteUnifie entity.
     *
     */
    public function editAction(Request $request, StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationCarteIdentiteUnifie);
        $editForm = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCarteIdentiteUnifieType', $stationCarteIdentiteUnifie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stationCarteIdentiteUnifie);
            $em->flush();

            return $this->redirectToRoute('stationcarteidentite_edit', array('id' => $stationCarteIdentiteUnifie->getId()));
        }

        return $this->render('stationcarteidentiteunifie/edit.html.twig', array(
            'stationCarteIdentiteUnifie' => $stationCarteIdentiteUnifie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a StationCarteIdentiteUnifie entity.
     *
     */
    public function deleteAction(Request $request, StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        $form = $this->createDeleteForm($stationCarteIdentiteUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stationCarteIdentiteUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('stationcarteidentite_index');
    }
}
