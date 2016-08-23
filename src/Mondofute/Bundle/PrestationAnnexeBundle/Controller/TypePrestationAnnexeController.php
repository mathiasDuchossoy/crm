<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousTypePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousTypePrestationAnnexeTraduction;
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
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofutePrestationAnnexeBundle:TypePrestationAnnexe')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'typeprestationannexe_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $entities = $this->getDoctrine()->getRepository('MondofutePrestationAnnexeBundle:TypePrestationAnnexe')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

//        $typePrestationAnnexes = $em->getRepository('MondofutePrestationAnnexeBundle:TypePrestationAnnexe')->findAll();

        return $this->render('@MondofutePrestationAnnexe/typeprestationannexe/index.html.twig', array(
            'typePrestationAnnexes' => $entities,
            'pagination' => $pagination
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
            ->getForm();
    }

    /**
     * Displays a form to edit an existing TypePrestationAnnexe entity.
     *
     */
    public function editAction(Request $request, TypePrestationAnnexe $typePrestationAnnexe)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        foreach ($langues as $langue) {
            $typePrestationAnnexeTraduction = $typePrestationAnnexe->getTraductions()->filter(function (TypePrestationAnnexeTraduction $element) use ($langue) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $typePrestationAnnexeTraduction) {
                $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
                $typePrestationAnnexeTraduction->setLangue($langue);
                $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
            }
        }

        $originalSousTypePrestationAnnexes = new ArrayCollection();

        // Create an ArrayCollection of the current SousTypePrestationAnnexe objects in the database
        foreach ($typePrestationAnnexe->getSousTypePrestationAnnexes() as $sousTypePrestationAnnexe) {
            $originalSousTypePrestationAnnexes->add($sousTypePrestationAnnexe);
        }

        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\TypePrestationAnnexeType', $typePrestationAnnexe);
        $editForm
            ->add('submit', SubmitType::class, array('label' => 'mettre.a.jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task
            /** @var SousTypePrestationAnnexe $sousTypePrestationAnnexe */
            foreach ($originalSousTypePrestationAnnexes as $sousTypePrestationAnnexe) {
                if (false === $typePrestationAnnexe->getSousTypePrestationAnnexes()->contains($sousTypePrestationAnnexe)) {
                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($sousTypePrestationAnnexe);
                }
            }

            // Récupération du controlleur de SousTypePrestationAnnexe;
            $sousTypePrestationAnnexeController = new SousTypePrestationAnnexeController();
            $sousTypePrestationAnnexeController->setContainer($this->container);

            $em->persist($typePrestationAnnexe);
            $em->flush();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            $this->udpateSites($typePrestationAnnexe, $sites);

            $this->addFlash('success' , 'Le type de prestation externe a bien été modifié.');

            return $this->redirectToRoute('typeprestationannexe_edit', array('id' => $typePrestationAnnexe->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/typeprestationannexe/edit.html.twig', array(
            'typePrestationAnnexe'  => $typePrestationAnnexe,
            'form'                  => $editForm->createView(),
            'langues'                => $langues
        ));
    }

    public function udpateSites($typePrestationAnnexe, $sites)
    {
        /** @var SousTypePrestationAnnexe $sousTypePrestationAnnexe */
        /** @var SousTypePrestationAnnexe $sousTypePrestationAnnexe */
        /** @var TypePrestationAnnexeTraduction $traductionSite */
        /** @var TypePrestationAnnexe $typePrestationAnnexeSite */
        /** @var TypePrestationAnnexe $typePrestationAnnexe */
        /** @var TypePrestationAnnexeTraduction $traduction */
        /** @var Site $site */
        /** @var EntityManager $emSite */
        foreach ($sites as $site){
            $emSite  = $this->getDoctrine()->getManager($site->getLibelle());
            $typePrestationAnnexeSite = $emSite->find(TypePrestationAnnexe::class,$typePrestationAnnexe);

            // modification des traductions du typePrestationAnnexe
            foreach ($typePrestationAnnexe->getTraductions() as $traduction) {
                $traductionSite = $typePrestationAnnexeSite->getTraductions()->filter(function (TypePrestationAnnexeTraduction $element) use ($traduction) {
                    return $element->getLangue()->getId() == $traduction->getId();
                })->first();
                if (false === $traductionSite) {
                    $traductionSite = new TypePrestationAnnexeTraduction();
                    $typePrestationAnnexeSite->addTraduction($traductionSite);
                    $traductionSite
                        ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                }
                $traductionSite
                    ->setLibelle($traduction->getLibelle());
            }

            // remove the relationship between the sousTypePrestationAnnexeSite and the typePrestationAnnexeSite
            foreach ($typePrestationAnnexeSite->getSousTypePrestationAnnexes() as $sousTypePrestationAnnexeSite) {
                $sousTypePrestationAnnexe = $typePrestationAnnexe->getSousTypePrestationAnnexes()->filter(function (SousTypePrestationAnnexe $element) use ($sousTypePrestationAnnexeSite) {
                    return $element->getId() == $sousTypePrestationAnnexeSite->getId();
                })->first();
                if (false === $sousTypePrestationAnnexe) {
                    // On doit le supprimer de l'entité parent
                    $typePrestationAnnexeSite->removeSousTypePrestationAnnex($sousTypePrestationAnnexeSite);
                    // if you wanted to delete the sousTypePrestationAnnexeSite entirely, you can also do that
                    $emSite->remove($sousTypePrestationAnnexeSite);
                }
            }

            foreach ($typePrestationAnnexe->getSousTypePrestationAnnexes() as $sousTypePrestationAnnexe) {
                $sousTypePrestationAnnexeSite = $typePrestationAnnexeSite->getSousTypePrestationAnnexes()->filter(function (SousTypePrestationAnnexe $element) use ($sousTypePrestationAnnexe) {
                    return $element->getId() == $sousTypePrestationAnnexe->getId();
                })->first();
                if (false === $sousTypePrestationAnnexeSite) {
                    $sousTypePrestationAnnexeSite = new SousTypePrestationAnnexe();
                    $typePrestationAnnexeSite->addSousTypePrestationAnnex($sousTypePrestationAnnexeSite);
                }
                foreach ($sousTypePrestationAnnexe->getTraductions() as $traduction) {
                    $traductionSite = $sousTypePrestationAnnexeSite->getTraductions()->filter(function (SousTypePrestationAnnexeTraduction $element) use ($traduction) {
                        return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                    })->first();
                    if (false === $traductionSite) {
                        $traductionSite = new SousTypePrestationAnnexeTraduction();
                        $sousTypePrestationAnnexeSite->addTraduction($traductionSite);
                        $traductionSite
                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                    }
                    $traductionSite
                        ->setLibelle($traduction->getLibelle());
                }
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
}
