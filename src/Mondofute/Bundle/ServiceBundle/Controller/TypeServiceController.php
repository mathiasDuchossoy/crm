<?php

namespace Mondofute\Bundle\ServiceBundle\Controller;

use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\ServiceBundle\Entity\TypeService;
use Mondofute\Bundle\ServiceBundle\Entity\TypeServiceTraduction;
use Mondofute\Bundle\ServiceBundle\Form\TypeServiceType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * TypeService controller.
 *
 */
class TypeServiceController extends Controller
{
    /**
     * Lists all TypeService entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository(TypeService::class)
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'type_service_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $entities = $this->getDoctrine()->getRepository(TypeService::class)
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteService/typeservice/index.html.twig', array(
            'typeServices' => $entities,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new TypeService entity.
     *
     */
    public function newAction(Request $request)
    {
        $typeService = new TypeService();
        $langues = $this->chargerLangues();
        $this->ajouterTraductions($typeService);
        $form = $this->createForm(TypeServiceType::class, $typeService)
            ->add('submit', SubmitType::class, array('label' => $this->get('translator')->trans('enregistrer')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->copieVersSites($typeService);
            $em->persist($typeService);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('type_service.enregistre'));

            return $this->redirectToRoute('type_service_edit', array('id' => $typeService->getId()));
        }

        return $this->render('@MondofuteService/typeservice/new.html.twig', array(
            'typeService' => $typeService,
            'form' => $form->createView(),
            'langues' => $langues,
        ));
    }

    public function chargerLangues()
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        return $langues;
    }

    /**
     * @param TypeService $typeService
     * @return $this
     */
    public function ajouterTraductions(TypeService $typeService)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        foreach ($langues as $langue) {
            if ($typeService->getTraductions()->filter(function (TypeServiceTraduction $element) use ($langue) {
                return $element->getLangue() == $langue;
            })->isEmpty()
            ) {
                $traduction = new TypeServiceTraduction();
                $traduction->setLangue($langue);
                $typeService->addTraduction($traduction);
            }
        }
        return $this;
    }

    public function copieVersSites(TypeService $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        /** @var Site $site */
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            if (empty($entity->getId()) || is_null(($entitySite = $emSite->getRepository(TypeService::class)->find($entity)))) {
                $entitySite = new TypeService();
            }
            /** @var TypeServiceTraduction $traduction */
            foreach ($entity->getTraductions() as $traduction) {
                if (empty($traduction->getId()) || empty(($traductionSite = $emSite->getRepository(TypeServiceTraduction::class)->findOneBy(array('langue' => $traduction->getLangue()->getId()))))) {
                    $traductionSite = new TypeServiceTraduction();
                    $traductionSite->setLangue($emSite->getRepository(Langue::class)->find($traduction->getLangue()));
                    $entitySite->addTraduction($traductionSite);
                }
                $traductionSite->setLibelle($traduction->getLibelle());
            }
            $emSite->persist($entitySite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a TypeService entity.
     *
     */
    public function showAction(TypeService $typeService)
    {
        $deleteForm = $this->createDeleteForm($typeService);

        return $this->render('@MondofuteService/typeservice/show.html.twig', array(
            'typeService' => $typeService,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a TypeService entity.
     *
     * @param TypeService $typeService The TypeService entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TypeService $typeService)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('type_service_delete', array('id' => $typeService->getId())))
            ->add('delete', SubmitType::class, array('label' => 'supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing TypeService entity.
     *
     */
    public function editAction(Request $request, TypeService $typeService)
    {

        $langues = $this->chargerLangues();
        $this->ajouterTraductions($typeService);
        $deleteForm = $this->createDeleteForm($typeService);
        $editForm = $this->createForm(TypeServiceType::class, $typeService)
            ->add('submit', SubmitType::class, array('label' => $this->get('translator')->trans('modifier')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->copieVersSites($typeService);
            $em->persist($typeService);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('type_service.modifie'));
            return $this->redirectToRoute('type_service_edit', array('id' => $typeService->getId()));
        }

        return $this->render('@MondofuteService/typeservice/edit.html.twig', array(
            'typeService' => $typeService,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'langues' => $langues,
        ));
    }

    /**
     * Deletes a TypeService entity.
     *
     */
    public function deleteAction(Request $request, TypeService $typeService)
    {
        $form = $this->createDeleteForm($typeService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
            /** @var Site $site */
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                $emSite->remove($emSite->getRepository(TypeService::class)->find($typeService->getId()));
                $emSite->flush();
            }
            $em->remove($typeService);
            $em->flush();
        }

        return $this->redirectToRoute('type_service_index');
    }
}
