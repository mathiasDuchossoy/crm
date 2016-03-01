<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Controller;

use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki;
use Mondofute\Bundle\UniteBundle\Entity\Age;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType;

/**
 * ModeleDescriptionForfaitSki controller.
 *
 */
class ModeleDescriptionForfaitSkiController extends Controller
{
    /**
     * Lists all ModeleDescriptionForfaitSki entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $modeleDescriptionForfaitSkis = $em->getRepository('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki')->findAll();

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/index.html.twig', array(
            'modeleDescriptionForfaitSkis' => $modeleDescriptionForfaitSkis,
        ));
    }

    /**
     * Creates a new ModeleDescriptionForfaitSki entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $modeleDescriptionForfaitSki = new ModeleDescriptionForfaitSki();
        // Récupérer toutes les entitées LigneDescriptionForfaitSki
        $ligneDescriptionForfaitSkis = $em->getRepository('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki')->findAll();
        foreach ($ligneDescriptionForfaitSkis as $ligneDescriptionForfaitSki) {
            $descriptionForfaitSki = new DescriptionForfaitSki();
            $descriptionForfaitSki->setLigneDescriptionForfaitSki($ligneDescriptionForfaitSki);
            $descriptionForfaitSki->setQuantite($ligneDescriptionForfaitSki->getQuantite());
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMin())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMin()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMin()->getValeur());
                $age = clone  $ligneDescriptionForfaitSki->getAgeMin();
            }
            $descriptionForfaitSki->setAgeMin($age);
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMax())) {
                $age->setUnite($ligneDescriptionForfaitSki->getAgeMax()->getUnite());
                $age->setValeur($ligneDescriptionForfaitSki->getAgeMax()->getValeur());
                $age = clone $ligneDescriptionForfaitSki->getAgeMax();
            }
            $descriptionForfaitSki->setAgeMax($age);
            $descriptionForfaitSki->setClassement($ligneDescriptionForfaitSki->getClassement());
            $descriptionForfaitSki->setPresent($ligneDescriptionForfaitSki->getPresent());
            $prix = new Tarif();
            if (!empty($ligneDescriptionForfaitSki->getPrix())) {
                $prix = clone $ligneDescriptionForfaitSki->getPrix();
            }
            $descriptionForfaitSki->setPrix($prix);
            $modeleDescriptionForfaitSki->addDescriptionForfaitSki($descriptionForfaitSki);
        }
//        dump($ligneDescriptionForfaitSkis);die;
        $form = $this->createForm('Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType', $modeleDescriptionForfaitSki);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($modeleDescriptionForfaitSki);
            $em->flush();

            return $this->redirectToRoute('modeledescriptionforfaitski_show', array('id' => $modeleDescriptionForfaitSki->getId()));
        }

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/new.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ModeleDescriptionForfaitSki entity.
     *
     */
    public function showAction(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $deleteForm = $this->createDeleteForm($modeleDescriptionForfaitSki);

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/show.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ModeleDescriptionForfaitSki entity.
     *
     * @param ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki The ModeleDescriptionForfaitSki entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modeledescriptionforfaitski_delete', array('id' => $modeleDescriptionForfaitSki->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ModeleDescriptionForfaitSki entity.
     *
     */
    public function editAction(Request $request, ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $deleteForm = $this->createDeleteForm($modeleDescriptionForfaitSki);
        $editForm = $this->createForm('Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType', $modeleDescriptionForfaitSki);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modeleDescriptionForfaitSki);
            $em->flush();

            return $this->redirectToRoute('modeledescriptionforfaitski_edit', array('id' => $modeleDescriptionForfaitSki->getId()));
        }

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/edit.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ModeleDescriptionForfaitSki entity.
     *
     */
    public function deleteAction(Request $request, ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $form = $this->createDeleteForm($modeleDescriptionForfaitSki);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($modeleDescriptionForfaitSki);
            $em->flush();
        }

        return $this->redirectToRoute('modeledescriptionforfaitski_index');
    }
}
