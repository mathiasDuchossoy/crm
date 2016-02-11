<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie;
use Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

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

        $this->ajouterRegionsDansForm($regionUnifie);
        $this->regionsSortByAffichage($regionUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\RegionUnifieType', $regionUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->supprimerRegions($regionUnifie, $sitesAEnregistrer)
                ->ajouterCrm($regionUnifie);

//            $em = $this->getDoctrine()->getManager();
            $em->persist($regionUnifie);
            $em->flush();

            $this->copieVersSites($regionUnifie);
            $this->addFlash('success', 'la région a bien été créée');
            return $this->redirectToRoute('geographie_region_edit', array('id' => $regionUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/regionunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $regionUnifie,
            'form' => $form->createView(),
        ));
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
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($region->getTraductions()->filter(function ($element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new RegionTraduction();
                            $traduction->setLangue($langue);
                            $region->addTraduction($traduction);
                        }
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
    }

    /**
     * Classe les regions par classementAffichage
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

    /**
     * Classe les traductions par rapport à leurs id
     * @param $regions
     */
    private function traductionsSortByLangue($regions)
    {
        foreach ($regions as $region) {
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
     * @param RegionUnifie $entity
     * @return $this
     */
    private function ajouterCrm(RegionUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $siteCrm = $em->getRepository(Site::class)->findOneBy(array('crm' => 1));
        $regionCrm = null;
        $classementReferentTmp = 0;
        $i = 0;
        // parcourir toute les regions
        foreach ($entity->getRegions() as $region) {
            //si i est égal à 0 et que le numéro de classement est inférieur au numéro de classement temporisé
            if ($i === 0 || $region->getSite()->getClassementReferent() < $classementReferentTmp) {
                $regionCrm = clone $region;
                $regionCrm->setSite($siteCrm);
                $classementReferentTmp = $region->getSite()->getClassementReferent();
            }
            $i++;
        }

        if (!is_null($regionCrm)) {
            $entity->addRegion($regionCrm);
        }
        return $this;
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
     * Copie dans la base de données site l'entité station
     * @param RegionUnifie $entity
     */
    public function copieVersSites(RegionUnifie $entity)
    {
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getRegions() as $region) {
            if ($region->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($region->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $region->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(RegionUnifie::class)->findOneById(array($entity->getId()))))) {
                    $entitySite = new RegionUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($regionSite = $em->getRepository(Region::class)->findOneBy(array('regionUnifie' => $entitySite))))) {
                    $regionSite = new Region();
                }

//            copie des données station
                $regionSite
                    ->setSite($site)
                    ->setRegionUnifie($entitySite);

//            Gestion des traductions
                foreach ($region->getTraductions() as $regionTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $regionTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($regionTraducSite = $em->getRepository(RegionTraduction::class)->findOneBy(array(
                        'region' => $regionSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $regionTraducSite = new RegionTraduction();
                    }

//                copie des données traductions
                    $regionTraducSite->setLangue($langue)
                        ->setLibelle($regionTraduc->getLibelle())
                        ->setDescription($regionTraduc->getDescription())
                        ->setRegion($regionSite);

//                ajout a la collection de traduction de la station distante
                    $regionSite->addTraduction($regionTraducSite);
                }

                $entitySite->addRegion($regionSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterRegionUnifieSiteDistant($entity->getId(), $entity->getRegions());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $regions
     */
    public function ajouterRegionUnifieSiteDistant($idUnifie, $regions)
    {
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($regions->matching($criteres)) == 0 && (empty($emSite->getRepository(RegionUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new RegionUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
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

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($regionUnifie->getRegions() as $region) {
                if (empty($region->getSite()->getCrm())) {
                    array_push($sitesAEnregistrer, $region->getSite()->getId());
                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $regionCrm = $this->dissocierRegionCrm($regionUnifie);
        $originalRegions = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
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

            try {
            $this->supprimerRegions($regionUnifie, $sitesAEnregistrer);
            $this->mettreAJourRegionCrm($regionUnifie, $regionCrm);
            $em->persist($regionCrm);
            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalRegions as $region) {
                if (!$regionUnifie->getRegions()->contains($region)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($region->getSite()->getLibelle());
                    $entitySite = $emSite->find(RegionUnifie::class, $regionUnifie->getId());
                    $regionSite = $entitySite->getRegions()->first();

                    $emSite->remove($regionSite);
                    $emSite->flush();
                    $region->setRegionUnifie(null);
                    $em->remove($region);

                }
            }
            $em->persist($regionUnifie);
            $em->flush();


            $this->copieVersSites($regionUnifie);
            } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer la région, elle est utilisée par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirectToRoute('geographie_region_edit', array('id' => $regionUnifie->getId()));
            }
            $this->addFlash('success', 'la région a bien été modifiée');
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
        return false;
    }

    /**
     * Mettre à jours ou créer une nouvelle stationCrm (si elle n'existe pas)
     * Permet aussi la gestion des traductions si elles n'existent pas (notament dans le cas d'un ajout de langue)
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

//        récupère les classementReferent pour chaque site dans un tableau
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
//                dump($region);
//              ajouter les champs "communs"
                foreach ($langues as $langue) {
//                    dump($langue);
//                    recupere la traduction pour l'entite du site referent
                    $regionTraduc = $region->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $regionTraducCrm = $regionCrm->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($regionTraduc);


//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($regionTraduc->getLibelle())) {
                        $regionTraduc->setLibelle('');
                    }
                    if (is_null($regionTraduc->getDescription())) {
                        $regionTraduc->setDescription('');
                    }
//                    Si la traduction n'existe pas dans le crm on creer une nouvelle traduction
                    if (empty($regionTraducCrm)) {
                        $regionTraducCrm = new RegionTraduction();
                        $regionTraducCrm->setRegion($regionCrm);
                        $regionTraducCrm->setLangue($langue);
//                        dump($regionTraducCrm);
//                        dump($regionTraduc);
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $regionTraducCrm->setLibelle($regionTraduc->getLibelle());
                        $regionTraducCrm->setDescription($regionTraduc->getDescription());
                        $regionCrm->addTraduction($regionTraducCrm);
                    } else {
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $regionTraducCrm->setLibelle($regionTraduc->getLibelle());
                        $regionTraducCrm->setDescription($regionTraduc->getDescription());
                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $regionTraduc = $region->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($regionTraduc->getLibelle())) {
                        $regionTraduc->setLibelle('');
                    }
                    if (is_null($regionTraduc->getDescription())) {
                        $regionTraduc->setDescription('');
                    }
                }
            }
        }
    }

    /**
     * Deletes a RegionUnifie entity.
     *
     */
    public function deleteAction(Request $request, RegionUnifie $regionUnifie)
    {
//        dump($request->headers->get('referer')).die();
        try {


            $form = $this->createDeleteForm($regionUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $regionUnifieSite = $emSite->find(RegionUnifie::class, $regionUnifie->getId());
                    if (!empty($regionUnifieSite)) {
                        $emSite->remove($regionUnifieSite);
                        $emSite->flush();
                    }
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($regionUnifie);
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'impossible de supprimer la région, elle est utilisée par une autre entité');
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'la région a bien été supprimée');
        return $this->redirectToRoute('geographie_region_index');
    }

    /**
     * @param Request $request
     */
    public function getRegionsCommunesBySiteAction (Request $request)
    {
        $sites = $request->get('sites');
        $em = $this->getDoctrine()->getEntityManager();

        $regionUnifies  = $em->getRepository(RegionUnifie::class)->findAll();
//        $regionUnifiesNotEmpty  = new ArrayCollection();
        $regionUnifieCollection = new ArrayCollection();
        foreach($regionUnifies as $regionUnifie)
        {
            $regionUnifieCollection->add($regionUnifie);
        }
        dump($regionUnifieCollection);
        foreach($sites as $site)
        {
            $siteEntity    = $em->find(Site::class,$site);
            foreach($regionUnifieCollection as $regionUnifie)
            {
                $region = $regionUnifie->getRegions()->filter(function ($element) use ($siteEntity) {
                    return $element->getSite() == $siteEntity;
                });
                dump($region);
                if (!empty($region))
                {
//                    $regionUnifieCollection->add($regionUnifie);
                    $regionUnifieCollection->remove($regionUnifie);
                }
            }
        }
        dump($regionUnifieCollection);

        die;

    }

}
