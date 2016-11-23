<?php

namespace Mondofute\Bundle\LogementBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\LogementBundle\Entity\NombreDeChambre;
use Mondofute\Bundle\LogementBundle\Entity\NombreDeChambreTraduction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * NombreDeChambre controller.
 *
 */
class NombreDeChambreController extends Controller
{
    /**
     * Lists all NombreDeChambre entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteLogementBundle:NombreDeChambre')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'logement_logement_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'entity.classement' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteLogementBundle:NombreDeChambre')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteLogement/nombredechambre/index.html.twig', array(
            'nombreDeChambres' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new nombreDeChambre entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $nombreDeChambre = new NombreDeChambre();
        $this->ajoutTraductions($nombreDeChambre, $langues);

        $form = $this->createForm('Mondofute\Bundle\LogementBundle\Form\NombreDeChambreType', $nombreDeChambre);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $count = $em
                ->getRepository('MondofuteLogementBundle:NombreDeChambre')
                ->countTotal();

            $nombreDeChambre->setClassement($count + 1);

            $em->persist($nombreDeChambre);
            $em->flush();

            $this->copieVersSites($nombreDeChambre);

            $this->addFlash('success', 'Label créé avec succès.');
            return $this->redirectToRoute('nombredechambre_edit', array('id' => $nombreDeChambre->getId()));
        }

        return $this->render('@MondofuteLogement/nombredechambre/new.html.twig', array(
            'nombreDeChambre' => $nombreDeChambre,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param NombreDeChambre $nombreDeChambre
     * @param $langues
     */
    private function ajoutTraductions($nombreDeChambre, $langues)
    {
        foreach ($langues as $langue) {
            $traduction = $nombreDeChambre->getTraductions()->filter(function (NombreDeChambreTraduction $element) use ($langue) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $traduction) {
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction->setLangue($langue);
            }
        }

        $this->traductionsSortByLangue($nombreDeChambre);

    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $nombreDeChambres
     */
    private function traductionsSortByLangue($nombreDeChambres)
    {
        /** @var ArrayIterator $iterator */
        /** @var NombreDeChambre $nombreDeChambre */
        foreach ($nombreDeChambres as $nombreDeChambre) {
            $traductions = $nombreDeChambre->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (NombreDeChambreTraduction $a, NombreDeChambreTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $nombreDeChambre->setTraductions($traductions);
        }
    }

    /**
     * @param NombreDeChambre $nombreDeChambre
     */
    function copieVersSites($nombreDeChambre)
    {
        /** @var EntityManager $emSite */
        /** @var NombreDeChambreTraduction $traduction */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $nombreDeChambreSite = $emSite->find(NombreDeChambre::class, $nombreDeChambre->getId());
            if (empty($nombreDeChambreSite)) {
                $nombreDeChambreSite = new NombreDeChambre();
                $nombreDeChambreSite
                    ->setId($nombreDeChambre->getId())
                    ->setClassement($nombreDeChambre->getClassement());
                $metadata = $emSite->getClassMetadata(get_class($nombreDeChambreSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            // *** traductions ***
            foreach ($nombreDeChambre->getTraductions() as $traduction) {
                $traductionSite = $nombreDeChambreSite->getTraductions()->filter(function (NombreDeChambreTraduction $element) use ($traduction) {
                    return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                })->first();
                if (false === $traductionSite) {
                    $traductionSite = new NombreDeChambreTraduction();
                    $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                    $nombreDeChambreSite->addTraduction($traductionSite);
                }
                $traductionSite->setLibelle($traduction->getLibelle());
            }
            // *** fin traductions ***
            $emSite->persist($nombreDeChambreSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a nombreDeChambre entity.
     *
     */
    public function showAction(NombreDeChambre $nombreDeChambre)
    {
        $deleteForm = $this->createDeleteForm($nombreDeChambre);

        return $this->render('@MondofuteLogement/nombredechambre/show.html.twig', array(
            'nombreDeChambre' => $nombreDeChambre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a nombreDeChambre entity.
     *
     * @param NombreDeChambre $nombreDeChambre The nombreDeChambre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(NombreDeChambre $nombreDeChambre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nombredechambre_delete', array('id' => $nombreDeChambre->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing nombreDeChambre entity.
     *
     */
    public function editAction(Request $request, NombreDeChambre $nombreDeChambre)
    {
        $deleteForm = $this->createDeleteForm($nombreDeChambre);
        $form = $this->createForm('Mondofute\Bundle\LogementBundle\Form\NombreDeChambreType', $nombreDeChambre)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->copieVersSites($nombreDeChambre);

            $this->addFlash('success', 'Label modifié avec succès.');

            return $this->redirectToRoute('nombredechambre_edit', array('id' => $nombreDeChambre->getId()));
        }

        return $this->render('@MondofuteLogement/nombredechambre/edit.html.twig', array(
            'nombreDeChambre' => $nombreDeChambre,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function editClassementAction(Request $request)
    {
        $data = json_decode($request->get('data'));
        $em = $this->getDoctrine()->getManager();

        $sites = $em->getRepository(Site::class)->findAll();

        $nombreDeChambres = new ArrayCollection();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            foreach ($data as $key => $item) {
                $nombreDeChambre = $emSite->find(NombreDeChambre::class, $item);
                $nombreDeChambres->add($nombreDeChambre);
                $nombreDeChambre->setClassement($key + 1);
                $emSite->persist($nombreDeChambre);
            }
            $emSite->flush();
        }

        return new Response();
    }

    /**
     * Deletes a nombreDeChambre entity.
     *
     */
    public function deleteAction(Request $request, NombreDeChambre $nombreDeChambre)
    {
        $form = $this->createDeleteForm($nombreDeChambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $nombreDeChambreSite = $emSite->find(NombreDeChambre::class, $nombreDeChambre->getId());
                    $emSite->remove($nombreDeChambreSite);
                    $emSite->flush();
                }

                $em->remove($nombreDeChambre);
                $em->flush();

                $this->addFlash('success', 'Label supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Le label est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('nombredechambre_index');
    }
}
