<?php

namespace Mondofute\Bundle\CommandeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\CommandeBundle\Entity\Commande;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLigne;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commande controller.
 *
 */
class CommandeController extends Controller
{
    /**
     * Lists all Commande entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteCommandeBundle:Commande')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'commande_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteCommandeBundle:Commande')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteCommande/commande/index.html.twig', array(
            'commandes' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new commande entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $commande = new Commande();

        $form = $this->createForm('Mondofute\Bundle\CommandeBundle\Form\CommandeType', $commande);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commande);
            $em->flush();

//            $this->copieVersSites($commande);

            $this->addFlash('success', 'Commande créé avec succès.');
            return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
        }

        return $this->render('@MondofuteCommande/commande/new.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param Commande $commande
     */
    function copieVersSites($commande)
    {
        /** @var EntityManager $emSite */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $commandeSite = $emSite->find(Commande::class, $commande->getId());
            if (empty($commandeSite)) {
                $commandeSite = new Commande();
//                $commandeSite->setId($commande->getId());
//                $metadata = $emSite->getClassMetadata(get_class($commandeSite));
//                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            $commandeSite->setEtat($commande->getEtat())->setDateCommande($commande->getDateCommande())->setNumCommande($commande->getNumCommande());
            $emSite->persist($commandeSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a commande entity.
     *
     */
    public function showAction(Commande $commande)
    {
        $deleteForm = $this->createDeleteForm($commande);

        return $this->render('@MondofuteCommande/commande/show.html.twig', array(
            'commande' => $commande,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a commande entity.
     *
     * @param Commande $commande The commande entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commande $commande)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commande_delete', array('id' => $commande->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     */
    public function editAction(Request $request, Commande $commande)
    {
        $deleteForm = $this->createDeleteForm($commande);
        $form = $this->createForm('Mondofute\Bundle\CommandeBundle\Form\CommandeType', $commande)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $originalCommandeLignes = new ArrayCollection();
//        $originalCommandeLignePrestationAnnexeSejours = new ArrayCollection();
        /** @var CommandeLigne $commandeLigne */
        foreach ($commande->getCommandeLignes() as $commandeLigne) {

            $originalCommandeLignes->add($commandeLigne);
//            $oReflectionClass = new ReflectionClass($commandeLigne);
//            if ($oReflectionClass->getShortName() == 'CommandeLigneSejour') {
//                /** @var CommandeLigneSejour $commandeLigne */
//                foreach ($commandeLigne->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
//                    if($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId())) {
//                        $originalCommandeLignePrestationAnnexeSejours->set($commandeLigne->getId(),$commandeLignePrestationAnnex);
//                    }
//                }
//            }
        }

        $form->handleRequest($request);
        dump($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            dump($commande->getCommandeLignes());
            foreach ($originalCommandeLignes as $originalCommandeLigne) {
//                foreach ($originalCommandeLignePrestationAnnexeSejours as $key => $originalCommandeLignePrestationAnnexeSejour) {
//                    if (false === $commande->getCommandeLignes()->get($key)->getCommandeLignePrestationAnnexes()->contains($originalCommandeLignePrestationAnnexeSejour)) {
//                        dump($originalCommandeLignePrestationAnnexeSejour);
//                        $em->remove($originalCommandeLignePrestationAnnexeSejour);
//                    }
//                }
                if (false === $commande->getCommandeLignes()->contains($originalCommandeLigne)) {
                    $em->remove($originalCommandeLigne);
                }
//                else {
//
//                }
            }
            dump($commande->getCommandeLignes());
            die;
            $em->flush();

//            $this->copieVersSites($commande);

            $this->addFlash('success', 'Commande modifiée avec succès.');

            return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
        }

        return $this->render('@MondofuteCommande/commande/edit.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a commande entity.
     *
     */
    public function deleteAction(Request $request, Commande $commande)
    {
        /** @var Site $site */
        $form = $this->createDeleteForm($commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

//                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
//                foreach ($sites as $site) {
//                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//                    $commandeSite = $emSite->find(Commande::class, $commande->getId());
//                    if (!empty($commandeSite)) {
//                        $emSite->remove($commandeSite);
//                        $emSite->flush();
//                    }
//                }

                $em->remove($commande);
                $em->flush();

                $this->addFlash('success', 'Commande supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'la commande est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('commande_index');
    }
}
