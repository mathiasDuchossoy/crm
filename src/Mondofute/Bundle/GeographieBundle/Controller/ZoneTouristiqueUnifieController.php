<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie;
use Mondofute\Bundle\GeographieBundle\Form\ZoneTouristiqueUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * ZoneTouristiqueUnifie controller.
 *
 */
class ZoneTouristiqueUnifieController extends Controller
{
    /**
     * Lists all ZoneTouristiqueUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $zoneTouristiqueUnifies = $em->getRepository('MondofuteGeographieBundle:ZoneTouristiqueUnifie')->findAll();
        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/index.html.twig', array(
            'zoneTouristiqueUnifies' => $zoneTouristiqueUnifies,
        ));
    }

    /**
     * Creates a new ZoneTouristiqueUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = $request->get('sites');

        $zoneTouristiqueUnifie = new ZoneTouristiqueUnifie();

        $this->ajouterZoneTouristiquesDansForm($zoneTouristiqueUnifie);
        $this->zoneTouristiquesSortByAffichage($zoneTouristiqueUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ZoneTouristiqueUnifieType',
            $zoneTouristiqueUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->supprimerZoneTouristiques($zoneTouristiqueUnifie, $sitesAEnregistrer)
                ->ajouterCrm($zoneTouristiqueUnifie);

//            $em = $this->getDoctrine()->getManager();
            $em->persist($zoneTouristiqueUnifie);
            $em->flush();

            $this->copieVersSites($zoneTouristiqueUnifie);
            $this->addFlash('success', 'la zone touristique a bien été créée');
            return $this->redirectToRoute('geographie_zonetouristique_edit',
                array('id' => $zoneTouristiqueUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $zoneTouristiqueUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param ZoneTouristiqueUnifie $entity
     */
    private function ajouterZoneTouristiquesDansForm(ZoneTouristiqueUnifie $entity)
    {
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
                if ($zoneTouristique->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($zoneTouristique->getTraductions()->filter(function (ZoneTouristiqueTraduction $element) use
                        (
                            $langue
                        ) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new ZoneTouristiqueTraduction();
                            $traduction->setLangue($langue);
                            $zoneTouristique->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $zoneTouristique = new ZoneTouristique();
                $zoneTouristique->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new ZoneTouristiqueTraduction();
                    $traduction->setLangue($langue);
                    $zoneTouristique->addTraduction($traduction);
                }
                $entity->addZoneTouristique($zoneTouristique);
            }
        }
    }

    /**
     * Classe les zoneTouristiques par classementAffichage
     * @param ZoneTouristiqueUnifie $entity
     */
    private function zoneTouristiquesSortByAffichage(ZoneTouristiqueUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les stations en fonction de leurs ordre d'affichage
        $zoneTouristiques = $entity->getZoneTouristiques(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $zoneTouristiques->getIterator();
        unset($zoneTouristiques);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (ZoneTouristique $a, ZoneTouristique $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $zoneTouristiques = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($zoneTouristiques);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setZoneTouristiques($zoneTouristiques);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $zoneTouristiques
     */
    private function traductionsSortByLangue(ArrayCollection $zoneTouristiques)
    {
        /** @var ZoneTouristique $zoneTouristique */
        foreach ($zoneTouristiques as $zoneTouristique) {
            $traductions = $zoneTouristique->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            /** @var ArrayIterator $iterator */
            $iterator->uasort(function (ZoneTouristiqueTraduction $a, ZoneTouristiqueTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $zoneTouristique->setTraductions($traductions);
        }
    }

    /**
     * @param ZoneTouristiqueUnifie $entity
     * @return $this
     */
    private function ajouterCrm(ZoneTouristiqueUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $siteCrm = $em->getRepository(Site::class)->findOneBy(array('crm' => 1));
        $zoneTouristiqueCrm = null;
        $classementReferentTmp = 0;
        $i = 0;
        // parcourir toute les zoneTouristiques
        foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
            //si i est égal à 0 et que le numéro de classement est inférieur au numéro de classement temporisé
            if ($i === 0 || $zoneTouristique->getSite()->getClassementReferent() < $classementReferentTmp) {
                $zoneTouristiqueCrm = clone $zoneTouristique;
                $zoneTouristiqueCrm->setSite($siteCrm);
                $classementReferentTmp = $zoneTouristique->getSite()->getClassementReferent();
            }
            $i++;
        }

        if (!is_null($zoneTouristiqueCrm)) {
            $entity->addZoneTouristique($zoneTouristiqueCrm);
        }
        return $this;
    }

    /**
     * retirer de l'entité les zoneTouristiques qui ne doivent pas être enregistrer
     * @param ZoneTouristiqueUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerZoneTouristiques(ZoneTouristiqueUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
            if (!in_array($zoneTouristique->getSite()->getId(), $sitesAEnregistrer)) {
                $zoneTouristique->setZoneTouristiqueUnifie(null);
                $entity->removeZoneTouristique($zoneTouristique);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param ZoneTouristiqueUnifie $entity
     */
    public function copieVersSites(ZoneTouristiqueUnifie $entity)
    {
        /** @var ZoneTouristiqueTraduction $zoneTouristiqueTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
            if ($zoneTouristique->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($zoneTouristique->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $zoneTouristique->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->find(ZoneTouristiqueUnifie::class, $entity->getId())))) {
                    $entitySite = new ZoneTouristiqueUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($zoneTouristiqueSite = $em->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $entitySite))))) {
                    $zoneTouristiqueSite = new ZoneTouristique();
                }

//            copie des données station
                $zoneTouristiqueSite
                    ->setSite($site)
                    ->setZoneTouristiqueUnifie($entitySite);

//            Gestion des traductions
                foreach ($zoneTouristique->getTraductions() as $zoneTouristiqueTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $zoneTouristiqueTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($zoneTouristiqueTraducSite = $em->getRepository(ZoneTouristiqueTraduction::class)->findOneBy(array(
                        'zoneTouristique' => $zoneTouristiqueSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $zoneTouristiqueTraducSite = new ZoneTouristiqueTraduction();
                    }

//                copie des données traductions
                    $zoneTouristiqueTraducSite->setLangue($langue)
                        ->setLibelle($zoneTouristiqueTraduc->getLibelle())
                        ->setDescription($zoneTouristiqueTraduc->getDescription())
                        ->setZoneTouristique($zoneTouristiqueSite);

//                ajout a la collection de traduction de la station distante
                    $zoneTouristiqueSite->addTraduction($zoneTouristiqueTraducSite);
                }

                $entitySite->addZoneTouristique($zoneTouristiqueSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterZoneTouristiqueUnifieSiteDistant($entity->getId(), $entity->getZoneTouristiques());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param Collection $zoneTouristiques
     */
    public function ajouterZoneTouristiqueUnifieSiteDistant($idUnifie, Collection $zoneTouristiques)
    {
        /** @var Site $site */
        /** @var ArrayCollection $zoneTouristiques */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($zoneTouristiques->matching($criteres)) == 0 && (empty($emSite->getRepository(ZoneTouristiqueUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new ZoneTouristiqueUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a ZoneTouristiqueUnifie entity.
     *
     */
    public function showAction(ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        $deleteForm = $this->createDeleteForm($zoneTouristiqueUnifie);

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/show.html.twig', array(
            'zoneTouristiqueUnifie' => $zoneTouristiqueUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ZoneTouristiqueUnifie entity.
     *
     * @param ZoneTouristiqueUnifie $zoneTouristiqueUnifie The ZoneTouristiqueUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_zonetouristique_delete',
                array('id' => $zoneTouristiqueUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ZoneTouristiqueUnifie entity.
     *
     */
    public function editAction(Request $request, ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $zoneTouristique) {
                if (empty($zoneTouristique->getSite()->getCrm())) {
                    array_push($sitesAEnregistrer, $zoneTouristique->getSite()->getId());
                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $zoneTouristiqueCrm = $this->dissocierZoneTouristiqueCrm($zoneTouristiqueUnifie);
        $originalZoneTouristiques = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $zoneTouristique) {
            $originalZoneTouristiques->add($zoneTouristique);
        }

        $this->ajouterZoneTouristiquesDansForm($zoneTouristiqueUnifie);
//        $this->dispacherDonneesCommune($zoneTouristiqueUnifie);
        $this->zoneTouristiquesSortByAffichage($zoneTouristiqueUnifie);
        $deleteForm = $this->createDeleteForm($zoneTouristiqueUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\ZoneTouristiqueUnifieType',
            $zoneTouristiqueUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerZoneTouristiques($zoneTouristiqueUnifie, $sitesAEnregistrer);
            $this->mettreAJourZoneTouristiqueCrm($zoneTouristiqueUnifie, $zoneTouristiqueCrm);
            $em->persist($zoneTouristiqueCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalZoneTouristiques as $zoneTouristique) {
                if (!$zoneTouristiqueUnifie->getZoneTouristiques()->contains($zoneTouristique)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($zoneTouristique->getSite()->getLibelle());
                    $entitySite = $emSite->find(ZoneTouristiqueUnifie::class, $zoneTouristiqueUnifie->getId());
                    $zoneTouristiqueSite = $entitySite->getZoneTouristiques()->first();
                    $emSite->remove($zoneTouristiqueSite);
                    $emSite->flush();
                    $zoneTouristique->setZoneTouristiqueUnifie(null);
                    $em->remove($zoneTouristique);
                }
            }
            $em->persist($zoneTouristiqueUnifie);
            $em->flush();


            $this->copieVersSites($zoneTouristiqueUnifie);
            $this->addFlash('success', 'la zone touristique a bien été modifiée');

//            dump($zoneTouristiqueUnifie);
//            dump($zoneTouristiqueCrm);
//            die;
            return $this->redirectToRoute('geographie_zonetouristique_edit',
                array('id' => $zoneTouristiqueUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/zonetouristiqueunifie/edit.html.twig', array(
            'entity' => $zoneTouristiqueUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * retirer la zoneTouristique crm
     * @param ZoneTouristiqueUnifie $entity
     *
     * @return mixed
     */
    private function dissocierZoneTouristiqueCrm(ZoneTouristiqueUnifie $entity)
    {
        foreach ($entity->getZoneTouristiques() as $zoneTouristique) {
            if ($zoneTouristique->getSite()->getCrm() == 1) {
//                $station->setStationUnifie(null);
                $entity->removeZoneTouristique($zoneTouristique);
                return $zoneTouristique;
            }
        }
        return false;
    }

    /**
     * Mettre à jours ou créer une nouvelle stationCrm (si elle n'existe pas)
     * Permet aussi la gestion des traductions si elles n'existent pas (notament dans le cas d'un ajout de langue)
     * Retourne vrai si elle est seulement mise à jours
     * Retourne faux s'il s'agit d'une nouvelle
     * @param ZoneTouristiqueUnifie $zoneTouristiqueUnifie
     * @param ZoneTouristique $zoneTouristiqueCrm
     * @return bool
     */
    private function mettreAJourZoneTouristiqueCrm(
        ZoneTouristiqueUnifie $zoneTouristiqueUnifie,
        ZoneTouristique $zoneTouristiqueCrm
    ) {
        /** @var ZoneTouristiqueTraduction $zoneTouristiqueTraduc */
        $em = $this->getDoctrine()->getManager();
        $tabClassementSiteReferent = array();

//        récupère les classementReferent pour chaque site dans un tableau
        foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $zoneTouristique) {
            $tabClassementSiteReferent[] = $zoneTouristique->getSite()->getClassementReferent();
        }

        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

        // Parcourir toutes les stations
        foreach ($zoneTouristiqueUnifie->getZoneTouristiques() as $zoneTouristique) {

            // Si la site de la station est égale au site de référence
            if ($zoneTouristique->getSite() == $siteReferent) {
//                dump($zoneTouristique);
//              ajouter les champs "communs"
                foreach ($langues as $langue) {
//                    dump($langue);
//                    recupere la traduction pour l'entite du site referent
                    $zoneTouristiqueTraduc = $zoneTouristique->getTraductions()->filter(function (
                        ZoneTouristiqueTraduction $element
                    ) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $zoneTouristiqueTraducCrm = $zoneTouristiqueCrm->getTraductions()->filter(function (
                        ZoneTouristiqueTraduction $element
                    ) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($zoneTouristiqueTraduc);


//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($zoneTouristiqueTraduc->getLibelle())) {
                        $zoneTouristiqueTraduc->setLibelle('');
                    }
                    if (is_null($zoneTouristiqueTraduc->getDescription())) {
                        $zoneTouristiqueTraduc->setDescription('');
                    }
//                    Si la traduction n'existe pas dans le crm on creer une nouvelle traduction
                    if (empty($zoneTouristiqueTraducCrm)) {
                        $zoneTouristiqueTraducCrm = new ZoneTouristiqueTraduction();
                        $zoneTouristiqueTraducCrm->setZoneTouristique($zoneTouristiqueCrm);
                        $zoneTouristiqueTraducCrm->setLangue($langue);
//                        dump($zoneTouristiqueTraducCrm);
//                        dump($zoneTouristiqueTraduc);
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $zoneTouristiqueTraducCrm->setLibelle($zoneTouristiqueTraduc->getLibelle());
                        $zoneTouristiqueTraducCrm->setDescription($zoneTouristiqueTraduc->getDescription());
                        $zoneTouristiqueCrm->addTraduction($zoneTouristiqueTraducCrm);
                    } else {
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $zoneTouristiqueTraducCrm->setLibelle($zoneTouristiqueTraduc->getLibelle());
                        $zoneTouristiqueTraducCrm->setDescription($zoneTouristiqueTraduc->getDescription());
                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $zoneTouristiqueTraduc = $zoneTouristique->getTraductions()->filter(function (
                        ZoneTouristiqueTraduction $element
                    ) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($zoneTouristiqueTraduc->getLibelle())) {
                        $zoneTouristiqueTraduc->setLibelle('');
                    }
                    if (is_null($zoneTouristiqueTraduc->getDescription())) {
                        $zoneTouristiqueTraduc->setDescription('');
                    }
                }
            }
        }
    }

    /**
     * Deletes a ZoneTouristiqueUnifie entity.
     *
     */
    public function deleteAction(Request $request, ZoneTouristiqueUnifie $zoneTouristiqueUnifie)
    {
        $form = $this->createDeleteForm($zoneTouristiqueUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $zoneTouristiqueUnifieSite = $emSite->find(ZoneTouristiqueUnifie::class,
                        $zoneTouristiqueUnifie->getId());
                    if (!empty($zoneTouristiqueUnifieSite)) {
                        $emSite->remove($zoneTouristiqueUnifieSite);
                        $emSite->flush();
                    }
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($zoneTouristiqueUnifie);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $except) {
//                dump($except);
                switch ($except->getCode()) {
                    case 0:
                        $this->addFlash('error',
                            'impossible de supprimer la zone touristique, il est utilisé par une autre entité');
                        break;
                    default:
                        $this->addFlash('error', 'une erreur inconnue');
                        break;
                }
                return $this->redirect($request->headers->get('referer'));
            }
        }
        $this->addFlash('success', 'la zone touristique a bien été supprimée');
        return $this->redirectToRoute('geographie_zonetouristique_index');
    }


}
