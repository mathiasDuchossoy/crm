<?php

namespace Mondofute\Bundle\RemiseClefBundle\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction;
use Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RemiseClef controller.
 *
 */
class RemiseClefController extends Controller
{
    /**
     * Lists all RemiseClef entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $remiseClefs = $em->getRepository('MondofuteRemiseClefBundle:RemiseClef')->findAll();

        return $this->render('@MondofuteRemiseClef/remiseclef/index.html.twig', array(
            'remiseClefs' => $remiseClefs,
        ));
    }

    /**
     * Creates a new RemiseClef entity.
     *
     */
    public function newAction(Request $request)
    {
        $remiseClef = new RemiseClef();
        $form = $this->createForm('Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType', $remiseClef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($remiseClef);
            $em->flush();

            return $this->redirectToRoute('remiseclef_show', array('id' => $remiseClef->getId()));
        }

        return $this->render('@MondofuteRemiseClef/remiseclef/new.html.twig', array(
            'remiseClef' => $remiseClef,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new RemiseClef entity.
     *
     */
    public function newSimpleAction(Request $request)
    {
        $remiseClef = new RemiseClef();
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($langues as $langue) {
            $remiseClefTraduction = new RemiseClefTraduction();
            $remiseClefTraduction->setLangue($langue);
            $remiseClef->addTraduction($remiseClefTraduction);
        }
        $form = $this->createForm('Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType', $remiseClef,
            array('action' => $this->generateUrl('remiseclef_new_simple'), 'method' => 'POST'))
            ->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
//            $idFournisseur = $request->request->get('remise_clef')['fournisseur'];
            $remiseClef->setFournisseur($em->getRepository(Fournisseur::class)->find(intval($request->request->get('remise_clef')['fournisseur'],
                10)));
            $em->persist($remiseClef);
            $em->flush();
            $this->copieVersSites($sites, $remiseClef);

            $json = json_encode(array(
                'remiseClef' => array('id' => $remiseClef->getId(), 'libelle' => $remiseClef->getLibelle())
            ));

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($json);

            return $response;
//            return $this->redirectToRoute('remiseclef_show', array('id' => $remiseClef->getId()));
        }

        return $this->render('@MondofuteRemiseClef/remiseclef/new_simple.html.twig', array(
            'remiseClef' => $remiseClef,
            'langues' => $langues,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Collection $sites
     * @param RemiseClef $remiseClef
     */
    public function copieVersSites($sites, $remiseClef)
    {
        /** @var EntityManager $emSite */
        foreach ($sites as $site) {
            if ($site->getCrm() == false) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                $remiseClefSite = new RemiseClef();

                $remiseClefSite->setId($remiseClef->getId());
                $metadata = $emSite->getClassMetadata(get_class($remiseClefSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $remiseClefSite->setHeureDepartCourtSejour($remiseClef->getHeureDepartCourtSejour())
                    ->setHeureDepartLongSejour($remiseClef->getHeureDepartLongSejour())
                    ->setHeureRemiseClefCourtSejour($remiseClef->getHeureRemiseClefCourtSejour())
                    ->setHeureRemiseClefLongSejour($remiseClef->getHeureRemiseClefLongSejour())
                    ->setHeureTardiveCourtSejour($remiseClef->getHeureTardiveCourtSejour())
                    ->setHeureTardiveLongSejour($remiseClef->getHeureRemiseClefLongSejour())
                    ->setStandard($remiseClef->getStandard())
                    ->setLibelle($remiseClef->getLibelle())
                    ->setFournisseur($emSite->getRepository(Fournisseur::class)->findOneBy(array('id' => $remiseClef->getFournisseur()->getId())));
                /** @var RemiseClefTraduction $traduction */
                foreach ($remiseClef->getTraductions() as $traduction) {
                    $traductionSite = new RemiseClefTraduction();
                    $traductionSite->setRemiseClef($remiseClefSite);
                    $traductionSite->setLieuxRemiseClef($traduction->getLieuxRemiseClef());
                    $traductionSite->setLangue($emSite->getRepository(Langue::class)->findOneBy(array('id' => $traduction->getLangue()->getId())));
                    $emSite->persist($traductionSite);
                    $remiseClefSite->addTraduction($traductionSite);
                }
                $emSite->persist($remiseClefSite);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a RemiseClef entity.
     *
     */
    public function showAction(RemiseClef $remiseClef)
    {
        $deleteForm = $this->createDeleteForm($remiseClef);

        return $this->render('@MondofuteRemiseClef/remiseclef/show.html.twig', array(
            'remiseClef' => $remiseClef,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a RemiseClef entity.
     *
     * @param RemiseClef $remiseClef The RemiseClef entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RemiseClef $remiseClef)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('remiseclef_delete', array('id' => $remiseClef->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing RemiseClef entity.
     *
     */
    public function editAction(Request $request, RemiseClef $remiseClef)
    {
        $deleteForm = $this->createDeleteForm($remiseClef);
        $editForm = $this->createForm('Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType', $remiseClef);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($remiseClef);
            $em->flush();

            return $this->redirectToRoute('remiseclef_edit', array('id' => $remiseClef->getId()));
        }

        return $this->render('@MondofuteRemiseClef/remiseclef/edit.html.twig', array(
            'remiseClef' => $remiseClef,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a RemiseClef entity.
     *
     */
    public function deleteAction(Request $request, RemiseClef $remiseClef)
    {
        $form = $this->createDeleteForm($remiseClef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($remiseClef);
            $em->flush();
        }

        return $this->redirectToRoute('remiseclef_index');
    }
}
