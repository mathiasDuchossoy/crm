<?php

namespace Mondofute\Bundle\MotClefBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\MotClefBundle\Entity\MotClefTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MotClefController extends Controller
{
    public function getmotclefslikeAction(Request $request)
    {
        $like = $request->get('q');
        $langue = $request->get('langue');
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(MotClefTraduction::class)->findByLike($like, $langue);

        return new Response(json_encode($data));
    }


    /**
     * Lists all MotClef entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository(MotClef::class)
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'mondofute_motclef_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteMotClefBundle:MotClef')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteMotClef/motclef/index.html.twig', array(
            'motClefs' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new motClef entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $motClef = new MotClef();
        $this->ajoutTraductions($motClef, $langues);

        $form = $this->createForm('Mondofute\Bundle\MotClefBundle\Form\MotClefType', $motClef);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($motClef);
            $em->flush();

            $this->copieVersSites($motClef);

            $this->addFlash('success', 'Label créé avec succès.');
            return $this->redirectToRoute('mondofute_motclef_edit', array('id' => $motClef->getId()));
        }

        return $this->render('@MondofuteMotClef/motclef/new.html.twig', array(
            'motClef' => $motClef,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param MotClef $motClef
     * @param $langues
     */
    private function ajoutTraductions($motClef, $langues)
    {
        foreach ($langues as $langue) {
            $traduction = $motClef->getTraductions()->filter(function (MotClefTraduction $element) use ($langue
            ) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $traduction) {
                $traduction = new MotClefTraduction();
                $motClef->addTraduction($traduction);
                $traduction->setLangue($langue);
            }
        }

        $this->traductionsSortByLangue($motClef);

    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $motClefs
     */
    private function traductionsSortByLangue($motClefs)
    {
        /** @var ArrayIterator $iterator */
        /** @var MotClef $motClef */
        foreach ($motClefs as $motClef) {
            $traductions = $motClef->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (MotClefTraduction $a, MotClefTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $motClef->setTraductions($traductions);
        }
    }

    /**
     * @param MotClef $motClef
     */
    function copieVersSites($motClef)
    {
        /** @var EntityManager $emSite */
        /** @var MotClefTraduction $traduction */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $motClefSite = $emSite->find(MotClef::class, $motClef->getId());
            if (empty($motClefSite)) {
                $motClefSite = new MotClef();
                $motClefSite->setId($motClef->getId());
                $metadata = $emSite->getClassMetadata(get_class($motClefSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            // *** traductions ***
            foreach ($motClef->getTraductions() as $traduction) {
                $traductionSite = $motClefSite->getTraductions()->filter(function (MotClefTraduction $element
                ) use ($traduction) {
                    return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                })->first();
                if (false === $traductionSite) {
                    $traductionSite = new MotClefTraduction();
                    $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                    $motClefSite->addTraduction($traductionSite);
                }
                $traductionSite->setLibelle($traduction->getLibelle());
            }
            // *** fin traductions ***
            $emSite->persist($motClefSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a motClef entity.
     *
     */
    public function showAction(MotClef $motClef)
    {
        $deleteForm = $this->createDeleteForm($motClef);

        return $this->render('@MondofuteMotClef/motclef/show.html.twig', array(
            'motClef' => $motClef,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a motClef entity.
     *
     * @param MotClef $motClef The motClef entity
     *
     * @return Form The form
     */
    private function createDeleteForm(MotClef $motClef)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mondofute_motclef_delete', array('id' => $motClef->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing motClef entity.
     *
     */
    public function editAction(Request $request, MotClef $motClef)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($motClef);
        $form = $this->createForm('Mondofute\Bundle\MotClefBundle\Form\MotClefType', $motClef)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->copieVersSites($motClef);

            $this->addFlash('success', 'Label modifié avec succès.');

            return $this->redirectToRoute('mondofute_motclef_edit', array('id' => $motClef->getId()));
        }
        $langues = $em->getRepository(Langue::class)->findAll();

        return $this->render('@MondofuteMotClef/motclef/edit.html.twig', array(
            'motClef' => $motClef,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'langues' => $langues
        ));
    }

    /**
     * Deletes a motClef entity.
     *
     */
    public function deleteAction(Request $request, MotClef $motClef)
    {
        $form = $this->createDeleteForm($motClef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $motClefSite = $emSite->find(MotClef::class, $motClef->getId());
                    $emSite->remove($motClefSite);
                    $emSite->flush();
                }

                $em->remove($motClef);
                $em->flush();

                $this->addFlash('success', 'Mot clef supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'La mot clef est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('mondofute_motclef_index');
    }

}
