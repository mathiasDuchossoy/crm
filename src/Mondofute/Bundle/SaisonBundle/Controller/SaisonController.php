<?php

namespace Mondofute\Bundle\SaisonBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SaisonBundle\Entity\Saison;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Saison controller.
 *
 */
class SaisonController extends Controller
{
    /**
     * Lists all Saison entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteSaisonBundle:Saison')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'saison_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteSaisonBundle:Saison')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteSaison/saison/index.html.twig', array(
            'saisons' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new saison entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $saison = new Saison();

        $form = $this->createForm('Mondofute\Bundle\SaisonBundle\Form\SaisonType', $saison);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($saison);
            $em->flush();

            $this->copieVersSites($saison);

            $this->addFlash('success', 'Saison créé avec succès.');
            return $this->redirectToRoute('saison_edit', array('id' => $saison->getId()));
        }

        return $this->render('@MondofuteSaison/saison/new.html.twig', array(
            'saison' => $saison,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param Saison $saison
     */
    function copieVersSites($saison)
    {
        /** @var EntityManager $emSite */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $saisonSite = $emSite->find(Saison::class, $saison->getId());
            if (empty($saisonSite)) {
                $saisonSite = new Saison();
                $saisonSite->setId($saison->getId());
                $metadata = $emSite->getClassMetadata(get_class($saisonSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            $saisonSite->setLibelle($saison->getLibelle())->setEnCours($saison->getEnCours())->setDateDebut($saison->getDateDebut())->setDateFin($saison->getDateFin());
            $emSite->persist($saisonSite);
            $emSite->flush();
        }
    }

    public function setEnCoursAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $saisonEnCours = $em->getRepository(Saison::class)->findOneBy(['enCours' => true]);
        if ($saisonEnCours->getId() != $id) {
            $saisonEnCours->setEnCours(false);
            $saison = $em->find(Saison::class, $id);
            $saison->setEnCours(true);
            $em->persist($saisonEnCours);
            $em->persist($saison);
            $em->flush();
        }
        return new Response();
    }

    /**
     * Finds and displays a saison entity.
     *
     */
    public function showAction(Saison $saison)
    {
        $deleteForm = $this->createDeleteForm($saison);

        return $this->render('@MondofuteSaison/saison/show.html.twig', array(
            'saison' => $saison,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a saison entity.
     *
     * @param Saison $saison The saison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Saison $saison)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('saison_delete', array('id' => $saison->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing saison entity.
     *
     */
    public function editAction(Request $request, Saison $saison)
    {
        $deleteForm = $this->createDeleteForm($saison);
        $form = $this->createForm('Mondofute\Bundle\SaisonBundle\Form\SaisonType', $saison)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->copieVersSites($saison);

            $this->addFlash('success', 'Saison modifié avec succès.');

            return $this->redirectToRoute('saison_edit', array('id' => $saison->getId()));
        }

        return $this->render('@MondofuteSaison/saison/edit.html.twig', array(
            'saison' => $saison,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a saison entity.
     *
     */
    public function deleteAction(Request $request, Saison $saison)
    {
        /** @var Site $site */
        $form = $this->createDeleteForm($saison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $saisonSite = $emSite->find(Saison::class, $saison->getId());
                    if (!empty($saisonSite)) {
                        $emSite->remove($saisonSite);
                        $emSite->flush();
                    }
                }

                $em->remove($saison);
                $em->flush();

                $this->addFlash('success', 'Saison supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'La saison est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('saison_index');
    }
}
