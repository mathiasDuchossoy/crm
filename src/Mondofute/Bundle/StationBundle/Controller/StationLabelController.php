<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\StationLabel;
use Mondofute\Bundle\StationBundle\Entity\StationLabelTraduction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stationlabel controller.
 *
 */
class StationLabelController extends Controller
{
    /**
     * Lists all StationLabel entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteStationBundle:StationLabel')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'station_station_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteStationBundle:StationLabel')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteStation/stationlabel/index.html.twig', array(
            'stationLabels' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new stationLabel entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $stationLabel = new Stationlabel();
        $this->ajoutTraductions($stationLabel, $langues);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationLabelType', $stationLabel);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($stationLabel);
            $em->flush();

            $this->copieVersSites($stationLabel);

            $this->addFlash('success', 'Label créé avec succès.');
            return $this->redirectToRoute('stationlabel_edit', array('id' => $stationLabel->getId()));
        }

        return $this->render('@MondofuteStation/stationlabel/new.html.twig', array(
            'stationLabel' => $stationLabel,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param StationLabel $stationLabel
     * @param $langues
     */
    private function ajoutTraductions($stationLabel, $langues)
    {
        foreach ($langues as $langue) {
            $traduction = $stationLabel->getTraductions()->filter(function (StationLabelTraduction $element) use (
                $langue
            ) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $traduction) {
                $traduction = new StationLabelTraduction();
                $stationLabel->addTraduction($traduction);
                $traduction->setLangue($langue);
            }
        }

        $this->traductionsSortByLangue($stationLabel);

    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $stationLabels
     */
    private function traductionsSortByLangue($stationLabels)
    {
        /** @var ArrayIterator $iterator */
        /** @var StationLabel $stationLabel */
        foreach ($stationLabels as $stationLabel) {
            $traductions = $stationLabel->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (StationLabelTraduction $a, StationLabelTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $stationLabel->setTraductions($traductions);
        }
    }

    /**
     * @param StationLabel $stationLabel
     */
    function copieVersSites($stationLabel)
    {
        /** @var StationLabelTraduction $traduction */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $stationLabelSite = $emSite->find(StationLabel::class, $stationLabel->getId());
            if (empty($stationLabelSite)) {
                $stationLabelSite = new StationLabel();
                $stationLabelSite->setId($stationLabel->getId());
                $metadata = $emSite->getClassMetadata(get_class($stationLabelSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            // *** traductions ***
            foreach ($stationLabel->getTraductions() as $traduction) {
                $traductionSite = $stationLabelSite->getTraductions()->filter(function (StationLabelTraduction $element
                ) use ($traduction) {
                    return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                })->first();
                if (false === $traductionSite) {
                    $traductionSite = new StationLabelTraduction();
                    $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                    $stationLabelSite->addTraduction($traductionSite);
                }
                $traductionSite->setLibelle($traduction->getLibelle());
            }
            // *** fin traductions ***
            $emSite->persist($stationLabelSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a stationLabel entity.
     *
     */
    public function showAction(StationLabel $stationLabel)
    {
        $deleteForm = $this->createDeleteForm($stationLabel);

        return $this->render('@MondofuteStation/stationlabel/show.html.twig', array(
            'stationLabel' => $stationLabel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a stationLabel entity.
     *
     * @param StationLabel $stationLabel The stationLabel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationLabel $stationLabel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stationlabel_delete', array('id' => $stationLabel->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing stationLabel entity.
     *
     */
    public function editAction(Request $request, StationLabel $stationLabel)
    {
        $deleteForm = $this->createDeleteForm($stationLabel);
        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationLabelType', $stationLabel)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->copieVersSites($stationLabel);

            $this->addFlash('success', 'Label modifié avec succès.');

            return $this->redirectToRoute('stationlabel_edit', array('id' => $stationLabel->getId()));
        }

        return $this->render('@MondofuteStation/stationlabel/edit.html.twig', array(
            'stationLabel' => $stationLabel,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a stationLabel entity.
     *
     */
    public function deleteAction(Request $request, StationLabel $stationLabel)
    {
        $form = $this->createDeleteForm($stationLabel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $stationLabelSite = $emSite->find(StationLabel::class, $stationLabel->getId());
                    $emSite->remove($stationLabelSite);
                    $emSite->flush();
                }

                $em->remove($stationLabel);
                $em->flush();

                $this->addFlash('success', 'Label supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Le label est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('stationlabel_index');
    }
}
