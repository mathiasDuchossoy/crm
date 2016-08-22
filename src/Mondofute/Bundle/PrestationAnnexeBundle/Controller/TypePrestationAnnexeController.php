<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\TypePrestationAnnexeTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        return $this->render('@MondofutePrestationAnnexe/typeprestationannexe/index.html.twig', array(
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

        return $this->render('@MondofutePrestationAnnexe/typeprestationannexe/new.html.twig', array(
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

        return $this->render('@MondofutePrestationAnnexe/typeprestationannexe/show.html.twig', array(
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
        /** @var Site $site */
//        $deleteForm = $this->createDeleteForm($typePrestationAnnexe);
        $em     = $this->getDoctrine()->getManager();
        $langues    = $em->getRepository(Langue::class)->findAll();

        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\TypePrestationAnnexeType', $typePrestationAnnexe);
        $editForm
            ->add('submit', SubmitType::class, array('label' => 'mettre.a.jour'));
        $editForm->handleRequest($request);

//            dump($typePrestationAnnexe->getSousTypePrestationAnnexes()->first());die;
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($typePrestationAnnexe);
            $em->flush();

            $this->copieVerSites($typePrestationAnnexe);

            return $this->redirectToRoute('typeprestationannexe_edit', array('id' => $typePrestationAnnexe->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/typeprestationannexe/edit.html.twig', array(
            'typePrestationAnnexe'  => $typePrestationAnnexe,
            'form'                  => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
            'langues'                => $langues
        ));
    }

    public function copieVerSites($typePrestationAnnexe){
        /** @var TypePrestationAnnexeTraduction $traductionSite */
        /** @var TypePrestationAnnexe $typePrestationAnnexeSite */
        /** @var TypePrestationAnnexe $typePrestationAnnexe */
        /** @var TypePrestationAnnexeTraduction $traduction */
        $em     = $this->getDoctrine()->getManager();
        $sites  = $em->getRepository(Site::class)->findBy(array('crm'=>0));
        foreach ($sites as $site){
            $emSite  = $this->getDoctrine()->getManager($site->getLibelle());
            $typePrestationAnnexeSite = $emSite->find(TypePrestationAnnexe::class,$typePrestationAnnexe);
            foreach ($typePrestationAnnexeSite->getTraductions() as $traductionSite){
                $traduction = $typePrestationAnnexe->getTraductions()->filter(function (TypePrestationAnnexeTraduction $element) use ($traductionSite){
                    return $element->getLangue()->getId() == $traductionSite->getId();
                })->first();
                $traductionSite->setLibelle($traduction->getLibelle());
            }
            $emSite->persist($typePrestationAnnexeSite);
            $emSite->flush();
        }
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
            ->add('Supprimer', SubmitType::class, array('label' => 'supprimer', 'translation_domain' => 'messages'))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
