<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie;
use Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RegionUnifie controller.
 *
 */
class RegionUnifieController extends Controller
{
    /**
     * Lists all RegionUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
//        $locale = $this->get('request')->getLocale();
//        $langueApp = $em->getRepository(Langue::class)->findOneBy(array('code'=>$locale));

        $regionUnifies = $em->getRepository('MondofuteGeographieBundle:RegionUnifie')->findAll();

        return $this->render('@MondofuteGeographie/regionunifie/index.html.twig', array(
            'regionUnifies' => $regionUnifies,
        ));
    }

    /**
     * Creates a new RegionUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = $request->get('sites');

        $regionUnifie = new RegionUnifie();
        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType', $regionUnifie);
        $form->add('Enregistrer', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->supprimerRegions($regionUnifie, $sitesAEnregistrer)
                ->ajouterCrm($regionUnifie);

            $em = $this->getDoctrine()->getManager();
            $em->persist($regionUnifie);
            $em->flush();

            return $this->redirectToRoute('geographie_region_show', array('id' => $regionunifie->getId()));
        }

        return $this->render('@MondofuteGeographie/regionunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $regionUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RegionUnifie entity.
     *
     */
    public function showAction(RegionUnifie $regionUnifie)
    {
        $deleteForm = $this->createDeleteForm($regionUnifie);

        return $this->render('@MondofuteGeographie/regionunifie/show.html.twig', array(
            'regionUnifie' => $regionUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a RegionUnifie entity.
     *
     * @param RegionUnifie $regionUnifie The RegionUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RegionUnifie $regionUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_region_delete', array('id' => $regionUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing RegionUnifie entity.
     *
     */
    public function editAction(Request $request, RegionUnifie $regionUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = array();
        foreach ($regionUnifie->getRegions() as $region) {
            if (empty($region->getSite()->getCrm())) {
                array_push($sitesAEnregistrer, $region->getSite()->getId());
            }
        }

        $regionCrm = $this->dissocierRegionCrm($regionUnifie);

        $originalRegions = new ArrayCollection();
        // Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($regionUnifie->getRegions() as $region) {
            $originalRegions->add($region);
        }

        $this->ajouterRegionsDansForm($regionUnifie);
//        $this->dispacherDonneesCommune($regionUnifie);
        $this->regionsSortByAffichage($regionUnifie);
        $deleteForm = $this->createDeleteForm($regionUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType', $regionUnifie)
        ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->supprimerRegions($regionUnifie, $sitesAEnregistrer);
//            dump($regionCrm);
            $this->mettreAJourRegionCrm($regionUnifie, $regionCrm);
            $em->persist($regionCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalRegions as $region) {
                if (!$regionUnifie->getRegions()->contains($region)) {

                    //  suppression de la station sur le site
//                    $emSite = $this->getDoctrine()->getEntityManager($region->getSite()->getLibelle());
//                    $entitySite = $emSite->find(RegionUnifie::class, $regionUnifie->getId());
//                    $regionSite = $entitySite->getRegions()->first();
//                    $emSite->remove($regionSite);
//                    $emSite->flush();

                    $region->setRegionUnifie(null);
                    $em->remove($region);
                }
            }

            $em->persist($regionUnifie);
            $em->flush();

            return $this->redirectToRoute('geographie_region_edit', array('id' => $regionUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/regionunifie/edit.html.twig', array(
            'entity' => $regionUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * retirer de l'entité les regions qui ne doivent pas être enregistrer
     * @param RegionUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerRegions(RegionUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getRegions() as $region) {
            if (!in_array($region->getSite()->getId(), $sitesAEnregistrer)) {
                $region->setRegionUnifie(null);
                $entity->removeRegion($region);
            }
        }
        return $this;
    }
    /**
     * Mettre à jours ou créer une nouvelle stationCrm (si elle n'existe pas)
     * Retourne vrai si elle est seulement mise à jours
     * Retourne faux s'il s'agit d'une nouvelle
     * @param RegionUnifie $regionUnifie
     * @param Region $regionCrm
     * @return bool
     */
    private function mettreAJourRegionCrm(RegionUnifie $regionUnifie, Region $regionCrm)
    {
        $em = $this->getDoctrine()->getManager();

        $tabClassementSiteReferent = array();
        foreach ($regionUnifie->getRegions() as $region) {
            $tabClassementSiteReferent[] = $region->getSite()->getClassementReferent();
        }
        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

        // Parcourir toutes les stations
        foreach ($regionUnifie->getRegions() as $region) {
            // Si la site de la station est égale au site de référence
            if ($region->getSite() == $siteReferent) {

//                $regionCrm
//                    ->setNbHabitant($region->getNbHabitant())
//                    ->setNbVache($region->getNbVache());

                foreach ($langues as $langue) {
//                    dump($regionCrm);
                    $regionTraduc = $region->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($regionTraduc);
                    $regionTraducCrm = $regionCrm->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($regionTraducCrm);
//                    die;
                    $regionTraducCrm->setLibelle($regionTraduc->getLibelle());
                }
            }
        }

    }

    /**
     * @param RegionUnifie $entity
     */
    private function regionsSortByAffichage(RegionUnifie $entity)
    {
        // Trier les stations en fonction de leurs ordre d'affichage
        $regions = $entity->getRegions(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $regions->getIterator();
        unset($regions);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $regions = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($regions);
        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setRegions($regions);
    }
private function traductionsSortByLangue($regions){
    foreach($regions as $region){
        $traductions = $region->getTraductions();
        $iterator = $traductions->getIterator();
        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $traductions = new ArrayCollection(iterator_to_array($iterator));
        $region->setTraductions($traductions);
    }
}
    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param RegionUnifie $entity
     */
    private function ajouterRegionsDansForm(RegionUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getRegions() as $region) {
                if ($region->getSite() == $site) {
                    $siteExiste = true;
                    foreach($langues as $langue){
//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if($region->getTraductions()->filter(function($element) use ($langue){
                            return $element->getLangue() == $langue;
                        })->isEmpty()){
                            $traduction = new RegionTraduction();
                            $traduction->setLangue($langue);
                            $region->addTraduction($traduction);
                        }
//                        echo count($a);
//                        if($a->isEmpty()){
//                            $traduction = new RegionTraduction();
//                            $traduction->setLangue($langue);
//                            $region->addTraduction($traduction);
//                        }

//                        $langueExist = 0;
//                        foreach($region->getTraductions() as $traduction){
//                            if($traduction->getLangue() == $langue){
//                                $langueExist = 1;
//                                break;
//                            }
//                        }
//                        if($langueExist == 0){
//                            $traduction = new RegionTraduction();
//                            $traduction->setLangue($langue);
//                            $region->addTraduction($traduction);
//                        }
                    }
                }
            }
            if (!$siteExiste) {
                $region = new Region();
                $region->setSite($site);
                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new RegionTraduction();
                    $traduction->setLangue($langue);
                    $region->addTraduction($traduction);
                }
                $entity->addRegion($region);
            }
        }
//        die;
    }
    /**
     * retirer la region crm
     * @param RegionUnifie $entity
     *
     * @return mixed
     */
    private function dissocierRegionCrm(RegionUnifie $entity)
    {
        foreach ($entity->getRegions() as $region) {
            if ($region->getSite()->getCrm() == 1) {
//                $station->setStationUnifie(null);
                $entity->removeRegion($region);
                return $region;
            }
        }
    }

    /**
     * Deletes a RegionUnifie entity.
     *
     */
    public function deleteAction(Request $request, RegionUnifie $regionUnifie)
    {
        $form = $this->createDeleteForm($regionUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($regionUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('geographie_region_index');
    }
}
