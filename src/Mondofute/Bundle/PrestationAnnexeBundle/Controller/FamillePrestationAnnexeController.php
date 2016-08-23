<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexeTraduction;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexeTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;

/**
 * FamillePrestationAnnexe controller.
 *
 */
class FamillePrestationAnnexeController extends Controller
{
    /**
     * Lists all FamillePrestationAnnexe entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofutePrestationAnnexeBundle:FamillePrestationAnnexe')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'familleprestationannexe_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $entities = $this->getDoctrine()->getRepository('MondofutePrestationAnnexeBundle:FamillePrestationAnnexe')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

//        $famillePrestationAnnexes = $em->getRepository('MondofutePrestationAnnexeBundle:FamillePrestationAnnexe')->findAll();

        return $this->render('@MondofutePrestationAnnexe/familleprestationannexe/index.html.twig', array(
            'famillePrestationAnnexes' => $entities,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new FamillePrestationAnnexe entity.
     *
     */
    public function newAction(Request $request)
    {
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $form = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\FamillePrestationAnnexeType', $famillePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($famillePrestationAnnexe);
            $em->flush();

            return $this->redirectToRoute('familleprestationannexe_show', array('id' => $famillePrestationAnnexe->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/familleprestationannexe/new.html.twig', array(
            'famillePrestationAnnexe' => $famillePrestationAnnexe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FamillePrestationAnnexe entity.
     *
     */
    public function showAction(FamillePrestationAnnexe $famillePrestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($famillePrestationAnnexe);

        return $this->render('@MondofutePrestationAnnexe/familleprestationannexe/show.html.twig', array(
            'famillePrestationAnnexe' => $famillePrestationAnnexe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a FamillePrestationAnnexe entity.
     *
     * @param FamillePrestationAnnexe $famillePrestationAnnexe The FamillePrestationAnnexe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(FamillePrestationAnnexe $famillePrestationAnnexe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('familleprestationannexe_delete', array('id' => $famillePrestationAnnexe->getId())))
            ->add('Supprimer', SubmitType::class, array('label' => 'supprimer', 'translation_domain' => 'messages'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing FamillePrestationAnnexe entity.
     *
     */
    public function editAction(Request $request, FamillePrestationAnnexe $famillePrestationAnnexe)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        foreach ($langues as $langue) {
            $famillePrestationAnnexeTraduction = $famillePrestationAnnexe->getTraductions()->filter(function (FamillePrestationAnnexeTraduction $element) use ($langue) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $famillePrestationAnnexeTraduction) {
                $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
                $famillePrestationAnnexeTraduction->setLangue($langue);
                $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
            }
        }

        $originalSousFamillePrestationAnnexes = new ArrayCollection();

        // Create an ArrayCollection of the current SousFamillePrestationAnnexe objects in the database
        foreach ($famillePrestationAnnexe->getSousFamillePrestationAnnexes() as $sousFamillePrestationAnnexe) {
            $originalSousFamillePrestationAnnexes->add($sousFamillePrestationAnnexe);
        }

        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\FamillePrestationAnnexeType', $famillePrestationAnnexe);
        $editForm
            ->add('submit', SubmitType::class, array('label' => 'mettre.a.jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task
            /** @var SousFamillePrestationAnnexe $sousFamillePrestationAnnexe */
            foreach ($originalSousFamillePrestationAnnexes as $sousFamillePrestationAnnexe) {
                if (false === $famillePrestationAnnexe->getSousFamillePrestationAnnexes()->contains($sousFamillePrestationAnnexe)) {
                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($sousFamillePrestationAnnexe);
                }
            }

            // Récupération du controlleur de SousFamillePrestationAnnexe;
            $sousFamillePrestationAnnexeController = new SousFamillePrestationAnnexeController();
            $sousFamillePrestationAnnexeController->setContainer($this->container);

            $em->persist($famillePrestationAnnexe);
            $em->flush();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            $this->udpateSites($famillePrestationAnnexe, $sites);

            $this->addFlash('success' , 'Le famille de prestation externe a bien été modifié.');

            return $this->redirectToRoute('familleprestationannexe_edit', array('id' => $famillePrestationAnnexe->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/familleprestationannexe/edit.html.twig', array(
            'famillePrestationAnnexe'  => $famillePrestationAnnexe,
            'form'                  => $editForm->createView(),
            'langues'                => $langues
        ));
    }

    public function udpateSites($famillePrestationAnnexe, $sites)
    {
        /** @var SousFamillePrestationAnnexe $sousFamillePrestationAnnexe */
        /** @var SousFamillePrestationAnnexe $sousFamillePrestationAnnexe */
        /** @var FamillePrestationAnnexeTraduction $traductionSite */
        /** @var FamillePrestationAnnexe $famillePrestationAnnexeSite */
        /** @var FamillePrestationAnnexe $famillePrestationAnnexe */
        /** @var FamillePrestationAnnexeTraduction $traduction */
        /** @var Site $site */
        /** @var EntityManager $emSite */
        foreach ($sites as $site){
            $emSite  = $this->getDoctrine()->getManager($site->getLibelle());
            $famillePrestationAnnexeSite = $emSite->find(FamillePrestationAnnexe::class,$famillePrestationAnnexe);

            // modification des traductions du famillePrestationAnnexe
            foreach ($famillePrestationAnnexe->getTraductions() as $traduction) {
                $traductionSite = $famillePrestationAnnexeSite->getTraductions()->filter(function (FamillePrestationAnnexeTraduction $element) use ($traduction) {
                    return $element->getLangue()->getId() == $traduction->getId();
                })->first();
                if (false === $traductionSite) {
                    $traductionSite = new FamillePrestationAnnexeTraduction();
                    $famillePrestationAnnexeSite->addTraduction($traductionSite);
                    $traductionSite
                        ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                }
                $traductionSite
                    ->setLibelle($traduction->getLibelle());
            }

            // remove the relationship between the sousFamillePrestationAnnexeSite and the famillePrestationAnnexeSite
            foreach ($famillePrestationAnnexeSite->getSousFamillePrestationAnnexes() as $sousFamillePrestationAnnexeSite) {
                $sousFamillePrestationAnnexe = $famillePrestationAnnexe->getSousFamillePrestationAnnexes()->filter(function (SousFamillePrestationAnnexe $element) use ($sousFamillePrestationAnnexeSite) {
                    return $element->getId() == $sousFamillePrestationAnnexeSite->getId();
                })->first();
                if (false === $sousFamillePrestationAnnexe) {
                    // On doit le supprimer de l'entité parent
                    $famillePrestationAnnexeSite->removeSousFamillePrestationAnnex($sousFamillePrestationAnnexeSite);
                    // if you wanted to delete the sousFamillePrestationAnnexeSite entirely, you can also do that
                    $emSite->remove($sousFamillePrestationAnnexeSite);
                }
            }

            foreach ($famillePrestationAnnexe->getSousFamillePrestationAnnexes() as $sousFamillePrestationAnnexe) {
                $sousFamillePrestationAnnexeSite = $famillePrestationAnnexeSite->getSousFamillePrestationAnnexes()->filter(function (SousFamillePrestationAnnexe $element) use ($sousFamillePrestationAnnexe) {
                    return $element->getId() == $sousFamillePrestationAnnexe->getId();
                })->first();
                if (false === $sousFamillePrestationAnnexeSite) {
                    $sousFamillePrestationAnnexeSite = new SousFamillePrestationAnnexe();
                    $famillePrestationAnnexeSite->addSousFamillePrestationAnnex($sousFamillePrestationAnnexeSite);
                }
                foreach ($sousFamillePrestationAnnexe->getTraductions() as $traduction) {
                    $traductionSite = $sousFamillePrestationAnnexeSite->getTraductions()->filter(function (SousFamillePrestationAnnexeTraduction $element) use ($traduction) {
                        return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                    })->first();
                    if (false === $traductionSite) {
                        $traductionSite = new SousFamillePrestationAnnexeTraduction();
                        $sousFamillePrestationAnnexeSite->addTraduction($traductionSite);
                        $traductionSite
                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                    }
                    $traductionSite
                        ->setLibelle($traduction->getLibelle());
                }
            }

            $emSite->persist($famillePrestationAnnexeSite);
            $emSite->flush();
        }
    }

    /**
     * Deletes a FamillePrestationAnnexe entity.
     *
     */
    public function deleteAction(Request $request, FamillePrestationAnnexe $famillePrestationAnnexe)
    {
        $form = $this->createDeleteForm($famillePrestationAnnexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($famillePrestationAnnexe);
            $em->flush();
        }

        return $this->redirectToRoute('familleprestationannexe_index');
    }
}
