<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie;
use Mondofute\Bundle\GeographieBundle\Form\DepartementUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DepartementUnifie controller.
 *
 */
class DepartementUnifieController extends Controller
{
//    TODO : ajout du lien avec la région (commun aux départements site ou chaque site peut avoir une region différente
    /**
     * Lists all DepartementUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $departementUnifies = $em->getRepository('MondofuteGeographieBundle:DepartementUnifie')->findAll();

        return $this->render('@MondofuteGeographie/departementunifie/index.html.twig', array(
            'departementUnifies' => $departementUnifies,
        ));
    }

    /**
     * Creates a new DepartementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = $request->get('sites');

        $departementUnifie = new DepartementUnifie();

        $this->ajouterDepartementsDansForm($departementUnifie);
        $this->departementsSortByAffichage($departementUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DepartementUnifieType', $departementUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->supprimerDepartements($departementUnifie, $sitesAEnregistrer)
                ->ajouterCrm($departementUnifie);

            $em = $this->getDoctrine()->getManager();
            $em->persist($departementUnifie);
            $em->flush();

            $this->copieVersSites($departementUnifie);
            return $this->redirectToRoute('geographie_departement_show', array('id' => $departementUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/departementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $departementUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DepartementUnifie $entity
     */
    private function ajouterDepartementsDansForm(DepartementUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDepartements() as $departement) {
                if ($departement->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($departement->getTraductions()->filter(function ($element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DepartementTraduction();
                            $traduction->setLangue($langue);
                            $departement->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $departement = new Departement();
                $departement->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DepartementTraduction();
                    $traduction->setLangue($langue);
                    $departement->addTraduction($traduction);
                }
                $entity->addDepartement($departement);
            }
        }
    }


    /**
     * Classe les departements par classementAffichage
     * @param DepartementUnifie $entity
     */
    private function departementsSortByAffichage(DepartementUnifie $entity)
    {

        // Trier les stations en fonction de leurs ordre d'affichage
        $departements = $entity->getDepartements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $departements->getIterator();
        unset($departements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $departements = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($departements);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setDepartements($departements);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $departements
     */
    private function traductionsSortByLangue($departements)
    {
        foreach ($departements as $departement) {
            $traductions = $departement->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function ($a, $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $departement->setTraductions($traductions);
        }
    }

    /**
     * @param DepartementUnifie $entity
     * @return $this
     */
    private function ajouterCrm(DepartementUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $siteCrm = $em->getRepository(Site::class)->findOneBy(array('crm' => 1));
        $departementCrm = null;
        $classementReferentTmp = 0;
        $i = 0;
        // parcourir toute les departements
        foreach ($entity->getDepartements() as $departement) {
            //si i est égal à 0 et que le numéro de classement est inférieur au numéro de classement temporisé
            if ($i === 0 || $departement->getSite()->getClassementReferent() < $classementReferentTmp) {
                $departementCrm = clone $departement;
                $departementCrm->setSite($siteCrm);
                $classementReferentTmp = $departement->getSite()->getClassementReferent();
            }
            $i++;
        }

        if (!is_null($departementCrm)) {
            $entity->addDepartement($departementCrm);
        }
        return $this;
    }

    /**
     * retirer de l'entité les departements qui ne doivent pas être enregistrer
     * @param DepartementUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDepartements(DepartementUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getDepartements() as $departement) {
            if (!in_array($departement->getSite()->getId(), $sitesAEnregistrer)) {
                $departement->setDepartementUnifie(null);
                $entity->removeDepartement($departement);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param DepartementUnifie $entity
     */
    public function copieVersSites(DepartementUnifie $entity)
    {
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDepartements() as $departement) {
            if ($departement->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($departement->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $departement->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(DepartementUnifie::class)->findOneById(array($entity->getId()))))) {
                    $entitySite = new DepartementUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($departementSite = $em->getRepository(Departement::class)->findOneBy(array('departementUnifie' => $entitySite))))) {
                    $departementSite = new Departement();
                }

//            copie des données station
                $departementSite
                    ->setSite($site)
                    ->setDepartementUnifie($entitySite);

//            Gestion des traductions
                foreach ($departement->getTraductions() as $departementTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $departementTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($departementTraducSite = $em->getRepository(DepartementTraduction::class)->findOneBy(array(
                        'departement' => $departementSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $departementTraducSite = new DepartementTraduction();
                    }

//                copie des données traductions
                    $departementTraducSite->setLangue($langue)
                        ->setLibelle($departementTraduc->getLibelle())
                        ->setDescription($departementTraduc->getDescription())
                        ->setDepartement($departementSite);

//                ajout a la collection de traduction de la station distante
                    $departementSite->addTraduction($departementTraducSite);
                }

                $entitySite->addDepartement($departementSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterDepartementUnifieSiteDistant($entity->getId(), $entity->getDepartements());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $departements
     */
    public function ajouterDepartementUnifieSiteDistant($idUnifie, $departements)
    {
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($departements->matching($criteres)) == 0 && (empty($emSite->getRepository(DepartementUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DepartementUnifie();
                $emSite->persist($entity);
                $emSite->flush();
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a DepartementUnifie entity.
     *
     */
    public function showAction(DepartementUnifie $departementUnifie)
    {
        $deleteForm = $this->createDeleteForm($departementUnifie);

        return $this->render('@MondofuteGeographie/departementunifie/show.html.twig', array(
            'departementUnifie' => $departementUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DepartementUnifie entity.
     *
     * @param DepartementUnifie $departementUnifie The DepartementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DepartementUnifie $departementUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_departement_delete', array('id' => $departementUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DepartementUnifie entity.
     *
     */
    public function editAction(Request $request, DepartementUnifie $departementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($departementUnifie->getDepartements() as $departement) {
                if (empty($departement->getSite()->getCrm())) {
                    array_push($sitesAEnregistrer, $departement->getSite()->getId());
                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $departementCrm = $this->dissocierDepartementCrm($departementUnifie);
        $originalDepartements = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($departementUnifie->getDepartements() as $departement) {
            $originalDepartements->add($departement);
        }

        $this->ajouterDepartementsDansForm($departementUnifie);
//        $this->dispacherDonneesCommune($departementUnifie);
        $this->departementsSortByAffichage($departementUnifie);
        $deleteForm = $this->createDeleteForm($departementUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DepartementUnifieType',
            $departementUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerDepartements($departementUnifie, $sitesAEnregistrer);
            $this->mettreAJourDepartementCrm($departementUnifie, $departementCrm);
            $em->persist($departementCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalDepartements as $departement) {
                if (!$departementUnifie->getDepartements()->contains($departement)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($departement->getSite()->getLibelle());
                    $entitySite = $emSite->find(DepartementUnifie::class, $departementUnifie->getId());
                    $departementSite = $entitySite->getDepartements()->first();
                    $emSite->remove($departementSite);
                    $emSite->flush();
//                    dump($departement);
                    $departement->setDepartementUnifie(null);
                    $em->remove($departement);
                }
            }
            $em->persist($departementUnifie);
            $em->flush();


            $this->copieVersSites($departementUnifie);

//            dump($departementUnifie);
//            dump($departementCrm);
//            die;
            return $this->redirectToRoute('geographie_departement_edit', array('id' => $departementUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/departementunifie/edit.html.twig', array(
            'entity' => $departementUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * retirer la departement crm
     * @param DepartementUnifie $entity
     *
     * @return mixed
     */
    private function dissocierDepartementCrm(DepartementUnifie $entity)
    {
        foreach ($entity->getDepartements() as $departement) {
            if ($departement->getSite()->getCrm() == 1) {
//                $station->setStationUnifie(null);
                $entity->removeDepartement($departement);
                return $departement;
            }
        }
    }

    /**
     * Mettre à jours ou créer une nouvelle stationCrm (si elle n'existe pas)
     * Permet aussi la gestion des traductions si elles n'existent pas (notament dans le cas d'un ajout de langue)
     * Retourne vrai si elle est seulement mise à jours
     * Retourne faux s'il s'agit d'une nouvelle
     * @param DepartementUnifie $departementUnifie
     * @param Departement $departementCrm
     * @return bool
     */
    private function mettreAJourDepartementCrm(DepartementUnifie $departementUnifie, Departement $departementCrm)
    {
        $em = $this->getDoctrine()->getManager();
        $tabClassementSiteReferent = array();

//        récupère les classementReferent pour chaque site dans un tableau
        foreach ($departementUnifie->getDepartements() as $departement) {
            $tabClassementSiteReferent[] = $departement->getSite()->getClassementReferent();
        }

        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

        // Parcourir toutes les stations
        foreach ($departementUnifie->getDepartements() as $departement) {

            // Si la site de la station est égale au site de référence
            if ($departement->getSite() == $siteReferent) {
//                dump($departement);
//              ajouter les champs "communs"
                foreach ($langues as $langue) {
//                    dump($langue);
//                    recupere la traduction pour l'entite du site referent
                    $departementTraduc = $departement->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $departementTraducCrm = $departementCrm->getTraductions()->filter(function ($element) use ($langue
                    ) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($departementTraduc);


//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($departementTraduc->getLibelle())) {
                        $departementTraduc->setLibelle('');
                    }
                    if (is_null($departementTraduc->getDescription())) {
                        $departementTraduc->setDescription('');
                    }
//                    Si la traduction n'existe pas dans le crm on creer une nouvelle traduction
                    if (empty($departementTraducCrm)) {
                        $departementTraducCrm = new DepartementTraduction();
                        $departementTraducCrm->setDepartement($departementCrm);
                        $departementTraducCrm->setLangue($langue);
//                        dump($departementTraducCrm);
//                        dump($departementTraduc);
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $departementTraducCrm->setLibelle($departementTraduc->getLibelle());
                        $departementTraducCrm->setDescription($departementTraduc->getDescription());
                        $departementCrm->addTraduction($departementTraducCrm);
                    } else {
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $departementTraducCrm->setLibelle($departementTraduc->getLibelle());
                        $departementTraducCrm->setDescription($departementTraduc->getDescription());
                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $departementTraduc = $departement->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($departementTraduc->getLibelle())) {
                        $departementTraduc->setLibelle('');
                    }
                    if (is_null($departementTraduc->getDescription())) {
                        $departementTraduc->setDescription('');
                    }
                }
            }
        }
//die;
    }

    /**
     * Deletes a DepartementUnifie entity.
     *
     */
    public function deleteAction(Request $request, DepartementUnifie $departementUnifie)
    {
        $form = $this->createDeleteForm($departementUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Parcourir la collection de départements.
            foreach ($departementUnifie->getDepartements() as $departement) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($departement->getSite()->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $departementUnifieSite = $emSite->find(DepartementUnifie::class, $departementUnifie->getId());
                if (!empty($departementUnifieSite)) {
                    $emSite->remove($departementUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($departementUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('geographie_departement_index');
    }

}
