<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Controller;

use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Age;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType;

/**
 * ModeleDescriptionForfaitSki controller.
 *
 */
class ModeleDescriptionForfaitSkiController extends Controller
{
    /**
     * Lists all ModeleDescriptionForfaitSki entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $modeleDescriptionForfaitSkis = $em->getRepository('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki')->findAll();

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/index.html.twig', array(
            'modeleDescriptionForfaitSkis' => $modeleDescriptionForfaitSkis,
        ));
    }

    /**
     * Creates a new ModeleDescriptionForfaitSki entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        $modeleDescriptionForfaitSki = new ModeleDescriptionForfaitSki();
        // Récupérer toutes les entitées LigneDescriptionForfaitSki
        $ligneDescriptionForfaitSkis = $em->getRepository('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki')->findAll();
        foreach ($ligneDescriptionForfaitSkis as $ligneDescriptionForfaitSki) {
            $descriptionForfaitSki = new DescriptionForfaitSki();
            $descriptionForfaitSki->setLigneDescriptionForfaitSki($ligneDescriptionForfaitSki);
            $descriptionForfaitSki->setQuantite($ligneDescriptionForfaitSki->getQuantite());
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMin())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMin()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMin()->getValeur());
                $age = clone  $ligneDescriptionForfaitSki->getAgeMin();
            }
            $descriptionForfaitSki->setAgeMin($age);
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMax())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMax()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMax()->getValeur());
                $age = clone $ligneDescriptionForfaitSki->getAgeMax();
            }
            $descriptionForfaitSki->setAgeMax($age);
            $descriptionForfaitSki->setClassement($ligneDescriptionForfaitSki->getClassement());
            $descriptionForfaitSki->setPresent($ligneDescriptionForfaitSki->getPresent());
            $prix = new Tarif();
            if (!empty($ligneDescriptionForfaitSki->getPrix())) {
                $prix = clone $ligneDescriptionForfaitSki->getPrix();
            }
            $descriptionForfaitSki->setPrix($prix);
            foreach ($langues as $langue) {
                $traduction = new DescriptionForfaitSkiTraduction();
                $traduction->setLangue($langue);
                $descriptionForfaitSki->addTraduction($traduction);
            }
            $modeleDescriptionForfaitSki->addDescriptionForfaitSki($descriptionForfaitSki);
        }
//        dump($ligneDescriptionForfaitSkis);die;
        $form = $this->createForm('Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType', $modeleDescriptionForfaitSki);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->copieVersSites($modeleDescriptionForfaitSki);

            $em->persist($modeleDescriptionForfaitSki);
            $em->flush();


            return $this->redirectToRoute('modeledescriptionforfaitski_show', array('id' => $modeleDescriptionForfaitSki->getId()));
        }

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/new.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'form' => $form->createView(),
        ));
    }

    public function copieVersSites(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());


            $modeleDescriptionForfaitSkiSite = clone $modeleDescriptionForfaitSki;

            foreach ($modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis() as $descriptionForfaitSki) {
                foreach ($descriptionForfaitSki->getTraductions() as $traduction) {
                    $traduction->setLangue($emSite->find('MondofuteLangueBundle:Langue', $traduction->getLangue()->getId()));
                }
                if (!empty($descriptionForfaitSki->getPrix()->getUnite())) {
                    $descriptionForfaitSki->getPrix()->setUnite($emSite->find('MondofuteUniteBundle:UniteTarif', $descriptionForfaitSki->getPrix()->getUnite()->getId()));
                }
                if (!empty($descriptionForfaitSki->getAgeMin()->getUnite())) {
                    $descriptionForfaitSki->getAgeMin()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMin()->getUnite()->getId()));
                }
                if (!empty($descriptionForfaitSki->getAgeMax()->getUnite())) {
                    $descriptionForfaitSki->getAgeMax()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMax()->getUnite()->getId()));
                }
                if (!empty($descriptionForfaitSki->getLigneDescriptionForfaitSki())) {
                    $descriptionForfaitSki->setLigneDescriptionForfaitSki($emSite->find('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki', $descriptionForfaitSki->getLigneDescriptionForfaitSki()->getId()));
                }
                if (!empty($descriptionForfaitSki->getPresent())) {
                    $descriptionForfaitSki->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $descriptionForfaitSki->getPresent()->getId()));
                }
            }
            $emSite->persist($modeleDescriptionForfaitSkiSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a ModeleDescriptionForfaitSki entity.
     *
     */
    public function showAction(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $deleteForm = $this->createDeleteForm($modeleDescriptionForfaitSki);

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/show.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ModeleDescriptionForfaitSki entity.
     *
     * @param ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki The ModeleDescriptionForfaitSki entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modeledescriptionforfaitski_delete', array('id' => $modeleDescriptionForfaitSki->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ModeleDescriptionForfaitSki entity.
     *
     */
    public function editAction(Request $request, ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $deleteForm = $this->createDeleteForm($modeleDescriptionForfaitSki);
        $editForm = $this->createForm('Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType', $modeleDescriptionForfaitSki)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->mettreAJourSites($modeleDescriptionForfaitSki);
            $em = $this->getDoctrine()->getManager();
            $em->persist($modeleDescriptionForfaitSki);
            $em->flush();

            return $this->redirectToRoute('modeledescriptionforfaitski_edit', array('id' => $modeleDescriptionForfaitSki->getId()));
        }

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/edit.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function mettreAJourSites(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        /** @var DescriptionForfaitSkiTraduction $traductionSite */
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSkiSite */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
        $em = $this->getDoctrine()->getEntityManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

            $modeleDescriptionForfaitSkiSite = $emSite->find('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki', $modeleDescriptionForfaitSki->getId());
            if (!empty($modeleDescriptionForfaitSkiSite)) {
                foreach ($modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis() as $descriptionForfaitSkiSite) {
                    $descriptionForfaitSki = $modeleDescriptionForfaitSki->getDescriptionForfaitSkis()->filter(function (DescriptionForfaitSki $element) use ($descriptionForfaitSkiSite) {
                        return $element->getLigneDescriptionForfaitSki()->getId() == $descriptionForfaitSkiSite->getLigneDescriptionForfaitSki()->getId();
                    })->first();
                    foreach ($descriptionForfaitSkiSite->getTraductions() as $traductionSite) {
                        $langue = $traductionSite->getLangue();
                        $traduction = $descriptionForfaitSki->getTraductions()->filter(function (DescriptionForfaitSkiTraduction $element) use ($langue) {
                            return $element->getLangue()->getId() == $langue->getId();
                        })->first();
                        $traductionSite->setDescription($traduction->getDescription());
                    }
                    $descriptionForfaitSkiSite->setClassement($descriptionForfaitSki->getClassement());
                    $descriptionForfaitSkiSite->setQuantite($descriptionForfaitSki->getQuantite());
                    $descriptionForfaitSkiSite->getPrix()->setValeur($descriptionForfaitSki->getPrix()->getValeur());
                    if (!empty($descriptionForfaitSkiSite->getPrix()->getUnite())) {
                        $descriptionForfaitSkiSite->getPrix()->setUnite($emSite->find('MondofuteUniteBundle:UniteTarif', $descriptionForfaitSki->getPrix()->getUnite()->getId()));
                    }
                    $descriptionForfaitSki->getAgeMin()->setValeur($descriptionForfaitSki->getAgeMin()->getValeur());
                    if (!empty($descriptionForfaitSkiSite->getAgeMin()->getUnite())) {
                        $descriptionForfaitSkiSite->getAgeMin()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMin()->getUnite()->getId()));
                    }
                    $descriptionForfaitSki->getAgeMax()->setValeur($descriptionForfaitSki->getAgeMax()->getValeur());
                    if (!empty($descriptionForfaitSkiSite->getAgeMax()->getUnite())) {
                        $descriptionForfaitSkiSite->getAgeMax()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMax()->getUnite()->getId()));
                    }
                    if (!empty($descriptionForfaitSkiSite->getLigneDescriptionForfaitSki())) {
                        $descriptionForfaitSkiSite->setLigneDescriptionForfaitSki($emSite->find('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki', $descriptionForfaitSki->getLigneDescriptionForfaitSki()->getId()));
                    }
                    if (!empty($descriptionForfaitSkiSite->getPresent())) {
                        $descriptionForfaitSkiSite->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $descriptionForfaitSki->getPresent()->getId()));
                    }
                }
                $emSite->persist($modeleDescriptionForfaitSkiSite);
                $emSite->flush();
            } else {
//                $this->copieVersSites($modeleDescriptionForfaitSki);
            }
        }

    }

    /**
     * Deletes a ModeleDescriptionForfaitSki entity.
     *
     */
    public function deleteAction(Request $request, ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $form = $this->createDeleteForm($modeleDescriptionForfaitSki);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($modeleDescriptionForfaitSki);
            $em->flush();
        }

        return $this->redirectToRoute('modeledescriptionforfaitski_index');
    }

}
