<?php

namespace Mondofute\Bundle\HebergementBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\HebergementBundle\Form\ReceptionType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\TrancheHoraireBundle\Entity\TrancheHoraire;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reception controller.
 *
 */
class ReceptionController extends Controller
{
    /**
     * Lists all Reception entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $receptions = $em->getRepository('MondofuteHebergementBundle:Reception')->findAll();

        return $this->render('@MondofuteHebergement/reception/index.html.twig', array(
            'receptions' => $receptions,
        ));
    }

    /**
     * Creates a new Reception entity.
     *
     */
    public function newAction(Request $request)
    {
        $reception = new Reception();
        $form = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\ReceptionType', $reception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reception);
            $em->flush();

            return $this->redirectToRoute('reception_show', array('id' => $reception->getId()));
        }

        return $this->render('@MondofuteHebergement/reception/new.html.twig', array(
            'reception' => $reception,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Reception entity.
     *
     */
    public function newSimpleAction(Request $request)
    {
//        $optionsJour = array(
//            '1' => 'lundi',
//            '2' => 'mardi',
//            '3' => 'mercredi',
//            '4' => 'jeudi',
//            '5' => 'vendredi',
//            '6' => 'samedi',
//            '0' => 'dimanche'
//        );

        $reception = new Reception();
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findAll();
        $form = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\ReceptionType', $reception,
            array('action' => $this->generateUrl('reception_new_simple'), 'method' => 'POST'))
            ->add('enregistrer', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receptions = new ArrayCollection();
            foreach ($reception->getJour() as $jour) {
                $each = new Reception();
                $each->setFournisseur($reception->getFournisseur())
                    ->setTranche1(clone $reception->getTranche1())
                    ->setTranche2(clone $reception->getTranche2())
                    ->setJour($jour);
                $receptions->add($each);
                $em->persist($each);
            }
            $em->flush();

            $this->copieVersSites($sites, $receptions);
            $jsonReceptions = array();
            /** @var Reception $reception */
            foreach ($receptions as $reception) {
                $id = "";
                $libelle = "";
                $idFournisseur = "";
                $tabReception = array();
                $id = $reception->getId();
                $idFournisseur = $reception->getFournisseur()->getId();
                $libelle = $this->container->get('translator')->trans('Jour' . intval($reception->getJour(),
                            10) . 'Libelle') . ' ' . $this->container->get('translator')->trans('de') . ' ' . $reception->getTranche1()->getDebut()->format('H:i') . ' ' . $this->container->get('translator')->trans('à') . ' ' . $reception->getTranche1()->getFin()->format('H:i');
                if (!empty($reception->getTranche2())) {
                    if ($reception->getTranche2()->getDebut() != $reception->getTranche2()->getFin()) {
                        $libelle .= ' ' . $this->container->get('translator')->trans('et') . ' ' . $this->container->get('translator')->trans('de') . ' ' . $reception->getTranche2()->getDebut()->format('H:i') . ' ' . $this->container->get('translator')->trans('à') . ' ' . $reception->getTranche2()->getFin()->format('H:i');
                    }
                }
                $tabReception['id'] = $id;
                $tabReception['idFournisseur'] = $idFournisseur;
                $tabReception['libelle'] = $libelle;
//                foreach ($reception->getTranche1)
                $jsonReceptions[] = $tabReception;
            }
            $json = json_encode(array(
                'receptions' => $jsonReceptions
            ));

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($json);

            return $response;
//            return $this->redirectToRoute('reception_show', array('id' => $reception->getId()));
        }

        return $this->render('@MondofuteHebergement/reception/new_simple.html.twig', array(
            'reception' => $reception,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param array $sites
     * @param ArrayCollection $receptions
     */
    public function copieVersSites(array $sites, ArrayCollection $receptions)
    {
        foreach ($sites as $site) {
            if ($site->getCrm() == false) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                foreach ($receptions as $reception) {
                    $recepetionSite = new Reception();
                    $recepetionSite->setJour($reception->getJour())
                        ->setFournisseur($emSite->getRepository(Fournisseur::class)->findOneBy(array('id' => $reception->getFournisseur()->getId())));
                    if (!empty($reception->getTranche1())) {
                        $trancheHoraire1Site = new TrancheHoraire();
                        $trancheHoraire1Site->setDebut($reception->getTranche1()->getDebut())
                            ->setFin($reception->getTranche1()->getFin());
                        $recepetionSite->setTranche1($trancheHoraire1Site);
                    } else {
                        $recepetionSite->setTranche1();
                    }
                    if (!empty($reception->getTranche2())) {
                        $trancheHoraire2Site = new TrancheHoraire();
                        $trancheHoraire2Site->setDebut($reception->getTranche2()->getDebut())
                            ->setFin($reception->getTranche2()->getFin());
                        $recepetionSite->setTranche2($trancheHoraire2Site);
                    } else {
                        $recepetionSite->setTranche2();
                    }
                    $emSite->persist($recepetionSite);
                }
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a Reception entity.
     *
     */
    public function showAction(Reception $reception)
    {
        $deleteForm = $this->createDeleteForm($reception);

        return $this->render('@MondofuteHebergement/reception/show.html.twig', array(
            'reception' => $reception,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Reception entity.
     *
     * @param Reception $reception The Reception entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reception $reception)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reception_delete', array('id' => $reception->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Reception entity.
     *
     */
    public function editAction(Request $request, Reception $reception)
    {
        $deleteForm = $this->createDeleteForm($reception);
        $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\ReceptionType', $reception);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reception);
            $em->flush();

            return $this->redirectToRoute('reception_edit', array('id' => $reception->getId()));
        }

        return $this->render('@MondofuteHebergement/reception/edit.html.twig', array(
            'reception' => $reception,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reception entity.
     *
     */
    public function deleteAction(Request $request, Reception $reception)
    {
        $form = $this->createDeleteForm($reception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reception);
            $em->flush();
        }

        return $this->redirectToRoute('reception_index');
    }
}
