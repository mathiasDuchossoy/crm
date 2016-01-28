<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\Station;
use Mondofute\Bundle\GeographieBundle\Entity\StationTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\StationUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Form\StationUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * StationUnifie controller.
 *
 */
class StationUnifieController extends Controller
{
    /**
     * Lists all StationUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stationUnifies = $em->getRepository('MondofuteGeographieBundle:StationUnifie')->findAll();

        return $this->render('@MondofuteGeographie/stationunifie/index.html.twig', array(
            'stationUnifies' => $stationUnifies,
        ));
    }

    /**
     * Creates a new StationUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = $request->get('sites');

        $stationUnifie = new StationUnifie();

        $this->ajouterStationsDansForm($stationUnifie);
//        $this->dispacherDonneesCommune($stationUnifie);
        $this->stationsSortByAffichage($stationUnifie);

        $form = $this->createForm(new StationUnifieType(), $stationUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            // dispacher les données communes
            $this->dispacherDonneesCommune($stationUnifie);

            $this->supprimerStations($stationUnifie, $sitesAEnregistrer)
                ->ajouterCrm($stationUnifie);

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationUnifie);
            $em->flush();

            $this->copieVersSites($stationUnifie);
            return $this->redirectToRoute('geographie_station_show', array('id' => $stationUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/stationunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $stationUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param StationUnifie $entity
     */
    private function ajouterStationsDansForm(StationUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getStations() as $station) {
                if ($station->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($station->getTraductions()->filter(function ($element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationTraduction();
                            $traduction->setLangue($langue);
                            $station->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $station = new Station();
                $station->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationTraduction();
                    $traduction->setLangue($langue);
                    $station->addTraduction($traduction);
                }
                $entity->addStation($station);
            }
        }
    }


    /**
     * Classe les stations par classementAffichage
     * @param StationUnifie $entity
     */
    private function stationsSortByAffichage(StationUnifie $entity)
    {

        // Trier les stations en fonction de leurs ordre d'affichage
        $stations = $entity->getStations(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $stations->getIterator();
        unset($stations);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $stations = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($stations);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setStations($stations);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $stations
     */
    private function traductionsSortByLangue($stations)
    {
        foreach ($stations as $station) {
            $traductions = $station->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function ($a, $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $station->setTraductions($traductions);
        }
    }

    /**
     * dispacher les données communes dans chaque stations
     * @param StationUnifie $entity
     */
    private function dispacherDonneesCommune(StationUnifie $entity)
    {
        $stationFirst = $entity->getStations()->first();
        foreach ($entity->getStations() as $station) {
            $zoneTouristique = $stationFirst->getZoneTouristique()->getZoneTouristiqueUnifie()->getZoneTouristiques()->filter(function ($element) use ($station) {
                return $element->getSite() == $station->getSite();
            })->first();
            $station->setZoneTouristique($zoneTouristique);
            $station->setCodePostal($stationFirst->getCodePostal());
//            $station->setMoisOuverture($stationFirst->getMoisOuverture());
//            $station->setJourOuverture($stationFirst->getJourOuverture());
//            $station->setMoisFermeture($stationFirst->getMoisFermeture());
//            $station->setJourFermeture($stationFirst->getJourFermeture());
            $station->setLienMeteo($stationFirst->getLienMeteo());
        }
    }

    /**
     * @param StationUnifie $entity
     * @return $this
     */
    private function ajouterCrm(StationUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $siteCrm = $em->getRepository(Site::class)->findOneBy(array('crm' => 1));
        $stationCrm = null;
        $classementReferentTmp = 0;
        $i = 0;
        // parcourir toute les stations
        foreach ($entity->getStations() as $station) {
            //si i est égal à 0 et que le numéro de classement est inférieur au numéro de classement temporisé
            if ($i === 0 || $station->getSite()->getClassementReferent() < $classementReferentTmp) {
                $stationCrm = clone $station;
                $stationCrm->setSite($siteCrm);
                $zoneTouristique = $station->getZoneTouristique()->getZoneTouristiqueUnifie()->getZoneTouristiques()->filter(function ($element) use ($siteCrm) {
                    return $element->getSite() == $siteCrm;
                })->first();
                $station->setZoneTouristique($zoneTouristique);
                $station->setCodePostal($station->getCodePostal());
                $station->setMoisOuverture($station->getMoisOuverture());
                $station->setJourOuverture($station->getJourOuverture());
                $station->setMoisFermeture($station->getMoisFermeture());
                $station->setJourFermeture($station->getJourFermeture());
                $station->setLienMeteo($station->getLienMeteo());
                $classementReferentTmp = $station->getSite()->getClassementReferent();
            }
            $i++;
        }

        if (!is_null($stationCrm)) {
            $entity->addStation($stationCrm);
        }
        return $this;
    }

    /**
     * retirer de l'entité les stations qui ne doivent pas être enregistrer
     * @param StationUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerStations(StationUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getStations() as $station) {
            if (!in_array($station->getSite()->getId(), $sitesAEnregistrer)) {
                $station->setStationUnifie(null);
                $entity->removeStation($station);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param StationUnifie $entity
     */
    private function copieVersSites(StationUnifie $entity)
    {
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getStations() as $station) {
            if ($station->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($station->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $station->getSite()->getId()));
                $zoneTouristique = $em->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $station->getZoneTouristique()->getZoneTouristiqueUnifie()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(StationUnifie::class)->findOneById(array($entity->getId()))))) {
                    $entitySite = new StationUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationSite = $em->getRepository(Station::class)->findOneBy(array('stationUnifie' => $entitySite))))) {
                    $stationSite = new Station();
                }

//            copie des données station
                $stationSite
                    ->setSite($site)
                    ->setStationUnifie($entitySite)
                    ->setZoneTouristique($zoneTouristique)
                    ->setCodePostal($station->getCodePostal())
                    ->setMoisOuverture($station->getMoisOuverture())
                    ->setJourOuverture($station->getJourOuverture())
                    ->setMoisFermeture($station->getMoisFermeture())
                    ->setJourFermeture($station->getJourFermeture())
                    ->setLienMeteo($station->getLienMeteo());

//            Gestion des traductions
                foreach ($station->getTraductions() as $stationTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $stationTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($stationTraducSite = $em->getRepository(StationTraduction::class)->findOneBy(array(
                        'station' => $stationSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $stationTraducSite = new StationTraduction();
                    }

//                copie des données traductions
                    $stationTraducSite->setLangue($langue)
                        ->setLibelle($stationTraduc->getLibelle())
                        ->setEnVoiture($stationTraduc->getEnVoiture())
                        ->setEnTrain($stationTraduc->getEnTrain())
                        ->setEnAvion($stationTraduc->getEnAvion())
                        ->setDistancesGrandesVilles($stationTraduc->getDistancesGrandesVilles())
                        ->setStation($stationSite);

//                ajout a la collection de traduction de la station distante
                    $stationSite->addTraduction($stationTraducSite);
                }

                $entitySite->addStation($stationSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterStationUnifieSiteDistant($entity->getId(), $entity->getStations());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $stations
     */
    private function ajouterStationUnifieSiteDistant($idUnifie, $stations)
    {
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($stations->matching($criteres)) == 0 && (empty($emSite->getRepository(StationUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new StationUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a StationUnifie entity.
     *
     */
    public function showAction(StationUnifie $stationUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationUnifie);

        return $this->render('@MondofuteGeographie/stationunifie/show.html.twig', array(
            'stationUnifie' => $stationUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a StationUnifie entity.
     *
     * @param StationUnifie $stationUnifie The StationUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationUnifie $stationUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_station_delete', array('id' => $stationUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing StationUnifie entity.
     *
     */
    public function editAction(Request $request, StationUnifie $stationUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($stationUnifie->getStations() as $station) {
                if (empty($station->getSite()->getCrm())) {
                    array_push($sitesAEnregistrer, $station->getSite()->getId());
                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $stationCrm = $this->dissocierStationCrm($stationUnifie);
        $originalStations = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($stationUnifie->getStations() as $station) {
            $originalStations->add($station);
        }

        $this->ajouterStationsDansForm($stationUnifie);
        $this->dispacherDonneesCommune($stationUnifie);
        $this->stationsSortByAffichage($stationUnifie);
        $deleteForm = $this->createDeleteForm($stationUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\StationUnifieType',
            $stationUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispacherDonneesCommune($stationUnifie);
            $this->supprimerStations($stationUnifie, $sitesAEnregistrer);
            $this->mettreAJourStationCrm($stationUnifie, $stationCrm);
            $em->persist($stationCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalStations as $station) {
                if (!$stationUnifie->getStations()->contains($station)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($station->getSite()->getLibelle());
                    $entitySite = $emSite->find(StationUnifie::class, $stationUnifie->getId());
                    $stationSite = $entitySite->getStations()->first();
                    $emSite->remove($stationSite);
                    $emSite->flush();
//                    dump($station);
                    $station->setStationUnifie(null);
                    $em->remove($station);
                }
            }
            $em->persist($stationUnifie);
            $em->flush();


            $this->copieVersSites($stationUnifie);

//            dump($stationUnifie);
//            dump($stationCrm);
//            die;
            return $this->redirectToRoute('geographie_station_edit', array('id' => $stationUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/stationunifie/edit.html.twig', array(
            'entity' => $stationUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * retirer la station crm
     * @param StationUnifie $entity
     *
     * @return mixed
     */
    private function dissocierStationCrm(StationUnifie $entity)
    {
        foreach ($entity->getStations() as $station) {
            if ($station->getSite()->getCrm() == 1) {
//                $station->setStationUnifie(null);
                $entity->removeStation($station);
                return $station;
            }
        }
    }

    /**
     * Mettre à jours ou créer une nouvelle stationCrm (si elle n'existe pas)
     * Permet aussi la gestion des traductions si elles n'existent pas (notament dans le cas d'un ajout de langue)
     * Retourne vrai si elle est seulement mise à jours
     * Retourne faux s'il s'agit d'une nouvelle
     * @param StationUnifie $stationUnifie
     * @param Station $stationCrm
     * @return bool
     */
    private function mettreAJourStationCrm(StationUnifie $stationUnifie, Station $stationCrm)
    {
        $em = $this->getDoctrine()->getManager();
        $tabClassementSiteReferent = array();

//        récupère les classementReferent pour chaque site dans un tableau
        foreach ($stationUnifie->getStations() as $station) {
            $tabClassementSiteReferent[] = $station->getSite()->getClassementReferent();
        }

        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

        // Parcourir toutes les stations
        foreach ($stationUnifie->getStations() as $station) {

            // Si la site de la station est égale au site de référence
            if ($station->getSite() == $siteReferent) {
//                dump($station);
//           ajouter les champs "communs"
                $siteCrm = $stationCrm->getSite();
                $zoneTouristiqueCrm = $station->getZoneTouristique()->getZoneTouristiqueUnifie()->getZoneTouristiques()->filter(function ($element) use ($siteCrm) {
                    return $element->getSite() == $siteCrm;
                })->first();

                $stationCrm
//                    ->setZoneTouristique($station->getZoneTouristique())
                    ->setZoneTouristique($zoneTouristiqueCrm)
                    ->setCodePostal($station->getCodePostal())
                    ->setMoisOuverture($station->getMoisOuverture())
                    ->setJourOuverture($station->getJourOuverture())
                    ->setMoisFermeture($station->getMoisFermeture())
                    ->setJourFermeture($station->getJourFermeture())
                    ->setLienMeteo($station->getLienMeteo());


                foreach ($langues as $langue) {
//                    dump($langue);
//                    recupere la traduction pour l'entite du site referent
                    $stationTraduc = $station->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $stationTraducCrm = $stationCrm->getTraductions()->filter(function ($element) use ($langue
                    ) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($stationTraduc);

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($stationTraduc->getLibelle())) {
                        $stationTraduc->setLibelle('');
                    }
                    if (is_null($stationTraduc->getEnVoiture())) {
                        $stationTraduc->setEnVoiture('');
                    }
                    if (is_null($stationTraduc->getEnTrain())) {
                        $stationTraduc->setEnTrain('');
                    }
                    if (is_null($stationTraduc->getEnAvion())) {
                        $stationTraduc->setEnAvion('');
                    }
                    if (is_null($stationTraduc->getDistancesGrandesVilles())) {
                        $stationTraduc->setDistancesGrandesVilles('');
                    }



//                    Si la traduction n'existe pas dans le crm on creer une nouvelle traduction
                    if (empty($stationTraducCrm)) {
                        $stationTraducCrm = new StationTraduction();
                        $stationTraducCrm->setStation($stationCrm);
                        $stationTraducCrm->setLangue($langue);
//                        dump($stationTraducCrm);
//                        dump($stationTraduc);
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $stationTraducCrm->setLibelle($stationTraduc->getLibelle());
                        $stationTraducCrm->setEnVoiture($stationTraduc->getEnVoiture());
                        $stationTraducCrm->setEnTrain($stationTraduc->getEnTrain());
                        $stationTraducCrm->setEnAvion($stationTraduc->getEnAvion());
                        $stationTraducCrm->setDistancesGrandesVilles($stationTraduc->getDistancesGrandesVilles());
                        $stationCrm->addTraduction($stationTraducCrm);
                    } else {
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $stationTraducCrm->setLibelle($stationTraduc->getLibelle());
                        $stationTraducCrm->setEnVoiture($stationTraduc->getEnVoiture());
                        $stationTraducCrm->setEnTrain($stationTraduc->getEnTrain());
                        $stationTraducCrm->setEnAvion($stationTraduc->getEnAvion());
                        $stationTraducCrm->setDistancesGrandesVilles($stationTraduc->getDistancesGrandesVilles());

                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $stationTraduc = $station->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($stationTraduc->getLibelle())) {
                        $stationTraduc->setLibelle('');
                    }
                    if (is_null($stationTraduc->getEnVoiture())) {
                        $stationTraduc->setEnVoiture('');
                    }
                    if (is_null($stationTraduc->getEnTrain())) {
                        $stationTraduc->setEnTrain('');
                    }
                    if (is_null($stationTraduc->getEnAvion())) {
                        $stationTraduc->setEnAvion('');
                    }
                    if (is_null($stationTraduc->getDistancesGrandesVilles())) {
                        $stationTraduc->setDistancesGrandesVilles('');
                    }

                }
            }
        }
//die;
    }

    /**
     * Deletes a StationUnifie entity.
     *
     */
    public function deleteAction(Request $request, StationUnifie $stationUnifie)
    {
        $form = $this->createDeleteForm($stationUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $stationUnifieSite = $emSite->find(StationUnifie::class, $stationUnifie->getId());
                if (!empty($stationUnifieSite)) {
                    $emSite->remove($stationUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($stationUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('geographie_station_index');
    }

}
