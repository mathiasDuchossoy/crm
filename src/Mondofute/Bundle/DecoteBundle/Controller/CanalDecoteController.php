<?php

namespace Mondofute\Bundle\DecoteBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\DecoteBundle\Entity\CanalDecote;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * CanalDecote controller.
 *
 */
class CanalDecoteController extends Controller
{
    /**
     * Lists all CanalDecote entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteDecoteBundle:CanalDecote')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'decote_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteDecoteBundle:CanalDecote')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteDecote/canaldecote/index.html.twig', array(
            'canalDecotes' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new canalDecote entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $canalDecote = new CanalDecote();

        $form = $this->createForm('Mondofute\Bundle\DecoteBundle\Form\CanalDecoteType', $canalDecote);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($canalDecote);
            $em->flush();

            $this->copieVersSites($canalDecote);

            $this->addFlash('success', 'Label créé avec succès.');
            return $this->redirectToRoute('canaldecote_edit', array('id' => $canalDecote->getId()));
        }

        return $this->render('@MondofuteDecote/canaldecote/new.html.twig', array(
            'canalDecote' => $canalDecote,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param CanalDecote $canalDecote
     */
    function copieVersSites($canalDecote)
    {
        /** @var EntityManager $emSite */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $canalDecoteSite = $emSite->find(CanalDecote::class, $canalDecote->getId());
            if (empty($canalDecoteSite)) {
                $canalDecoteSite = new CanalDecote();
                $canalDecoteSite->setId($canalDecote->getId());
                $metadata = $emSite->getClassMetadata(get_class($canalDecoteSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            $canalDecoteSite->setLibelle($canalDecote->getLibelle());
            $emSite->persist($canalDecoteSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a canalDecote entity.
     *
     */
    public function showAction(CanalDecote $canalDecote)
    {
        $deleteForm = $this->createDeleteForm($canalDecote);

        return $this->render('@MondofuteDecote/canaldecote/show.html.twig', array(
            'canalDecote' => $canalDecote,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a canalDecote entity.
     *
     * @param CanalDecote $canalDecote The canalDecote entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CanalDecote $canalDecote)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('canaldecote_delete', array('id' => $canalDecote->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing canalDecote entity.
     *
     */
    public function editAction(Request $request, CanalDecote $canalDecote)
    {
        $deleteForm = $this->createDeleteForm($canalDecote);
        $form = $this->createForm('Mondofute\Bundle\DecoteBundle\Form\CanalDecoteType', $canalDecote)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->copieVersSites($canalDecote);

            $this->addFlash('success', 'Canal modifié avec succès.');

            return $this->redirectToRoute('canaldecote_edit', array('id' => $canalDecote->getId()));
        }

        return $this->render('@MondofuteDecote/canaldecote/edit.html.twig', array(
            'canalDecote' => $canalDecote,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a canalDecote entity.
     *
     */
    public function deleteAction(Request $request, CanalDecote $canalDecote)
    {
        $form = $this->createDeleteForm($canalDecote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $canalDecoteSite = $emSite->find(CanalDecote::class, $canalDecote->getId());
                    if (!empty($canalDecoteSite)) {
                        $emSite->remove($canalDecoteSite);
                        $emSite->flush();
                    }
                }

                $em->remove($canalDecote);
                $em->flush();

                $this->addFlash('success', 'Canal supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Le canal est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('canaldecote_index');
    }
}
