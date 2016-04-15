<?php

namespace Mondofute\Bundle\HebergementBundle\Controller;

use ArrayIterator;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * HebergementUnifie controller.
 *
 */
class HebergementUnifieController extends Controller
{
    /**
     * Lists all HebergementUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $hebergementUnifies = $em->getRepository('MondofuteHebergementBundle:HebergementUnifie')->findAll();

        return $this->render('@MondofuteHebergement/hebergementunifie/index.html.twig', array(
            'hebergementUnifies' => $hebergementUnifies,
        ));
    }

    /**
     * Creates a new HebergementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $hebergementUnifie = new HebergementUnifie();

        $this->ajouterHebergementsDansForm($hebergementUnifie);
        $this->hebergementsSortByAffichage($hebergementUnifie);

        $form = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType', $hebergementUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dispacher les données communes
//            $this->dispacherDonneesCommune($hebergementUnifie);

            $this->supprimerHebergements($hebergementUnifie, $sitesAEnregistrer);
            foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                /** @var MoyenCommunication $moyenCom */
                foreach ($hebergement->getMoyenComs() as $moyenCom) {
                    $moyenCom->setDateCreation();
                }
            }
//            $this->gestionDatesMoyenComs($hebergementUnifie);
            $em = $this->getDoctrine()->getManager();
            $em->persist($hebergementUnifie);
            $em->flush();

            $this->copieVersSites($hebergementUnifie);
            $this->addFlash('success', 'l\'hébergement a bien été créé');
            return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $hebergementUnifie->getId()));
        }

        return $this->render('@MondofuteHebergement/hebergementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $hebergementUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param HebergementUnifie $entity
     */
    private function ajouterHebergementsDansForm(HebergementUnifie $entity)
    {
        /** @var Hebergement $hebergement */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getHebergements() as $hebergement) {
                if ($hebergement->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($hebergement->getTraductions()->filter(function (HebergementTraduction $element) use (
                            $langue
                        ) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new HebergementTraduction();
                            $traduction->setLangue($langue);
                            $hebergement->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
//                si l'hébergement n'existe pas on créer un nouvel hébergemùent
                $hebergement = new Hebergement();
//                création d'une adresse
                $adresse = new Adresse();
                $adresse->setDateCreation();
                $hebergement->addMoyenCom($adresse);

                $hebergement->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new HebergementTraduction();
                    $traduction->setLangue($langue);
                    $hebergement->addTraduction($traduction);
                }
                $entity->addHebergement($hebergement);
            }
        }
    }

    /**
     * Classe les departements par classementAffichage
     * @param HebergementUnifie $entity
     */
    private function hebergementsSortByAffichage(HebergementUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les stations en fonction de leurs ordre d'affichage
        $hebergements = $entity->getHebergements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $hebergements->getIterator();
        unset($hebergements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Hebergement $a, Hebergement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $hebergements = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($hebergements);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setHebergements($hebergements);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $hebergements
     */
    private function traductionsSortByLangue($hebergements)
    {
        /** @var ArrayIterator $iterator */
        /** @var Hebergement $hebergement */
        foreach ($hebergements as $hebergement) {
            $traductions = $hebergement->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (HebergementTraduction $a, HebergementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $hebergement->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les departements qui ne doivent pas être enregistrer
     * @param HebergementUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerHebergements(HebergementUnifie $entity, array $sitesAEnregistrer)
    {
        /** @var Hebergement $hebergement */
        foreach ($entity->getHebergements() as $hebergement) {
            if (!in_array($hebergement->getSite()->getId(), $sitesAEnregistrer)) {
//                $hebergement->setClassement(null);
                $hebergement->setHebergementUnifie(null);
                $entity->removeHebergement($hebergement);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param HebergementUnifie $entity
     */
    private function copieVersSites(HebergementUnifie $entity)
    {
        /** @var HebergementTraduction $hebergementTraduc */
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getHebergements() as $hebergement) {
            if ($hebergement->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($hebergement->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $hebergement->getSite()->getId()));
//                $region = $em->getRepository(Region::class)->findOneBy(array('regionUnifie' => $departement->getRegion()->getRegionUnifie()->getId()));
                if (!empty($hebergement->getStation())) {
                    $stationSite = $em->getRepository(Station::class)->findOneBy(array('stationUnifie' => $hebergement->getStation()->getStationUnifie()->getId()));
                } else {
                    $stationSite = null;
                }
//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(HebergementUnifie::class)->find($entity->getId())))) {
                    $entitySite = new HebergementUnifie();
                }

//            Récupération de l'hébergement sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($hebergementSite = $em->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $entitySite))))) {
                    $hebergementSite = new Hebergement();
                }

                $classementSite = !empty($hebergementSite->getClassement()) ? $hebergementSite->getClassement() : clone $hebergement->getClassement();
                /** @var Adresse $adresse */
                /** @var CoordonneesGPS $coordonneesGPSSite */
                /** @var Adresse $adresseSite */
                $adresse = $hebergement->getMoyenComs()->first();
                if (!empty($hebergementSite->getMoyenComs())) {
                    $adresseSite = $hebergementSite->getMoyenComs()->first();
                    $adresseSite->setDateModification(new DateTime());
                } else {
                    $adresseSite = new Adresse();
                    $adresseSite->setDateCreation();
                    $adresseSite->setCoordonneeGPS(new CoordonneesGPS());
                    $hebergementSite->addMoyenCom($adresseSite);
                }
                $adresseSite->setVille($adresse->getVille());
                $adresseSite->setAdresse1($adresse->getAdresse1());
                $adresseSite->setAdresse2($adresse->getAdresse2());
                $adresseSite->setAdresse3($adresse->getAdresse3());
                $adresseSite->setCodePostal($adresse->getCodePostal());
                $adresseSite->setPays($adresse->getPays());
                $adresseSite->getCoordonneeGPS()
                    ->setLatitude($adresse->getCoordonneeGPS()->getLatitude())
                    ->setLongitude($adresse->getCoordonneeGPS()->getLongitude())
                    ->setPrecis($adresse->getCoordonneeGPS()->getPrecis());
                if (!empty($classementSite->getUnite())) {
                    $uniteSite = $em->getRepository(Unite::class)->findOneBy(array('id' => $hebergement->getClassement()->getUnite()->getId()));
                } else {
                    $uniteSite = null;
                }
                $classementSite->setValeur($hebergement->getClassement()->getValeur());
                $classementSite->setUnite($uniteSite);

//            copie des données hébergement
                $hebergementSite
                    ->setSite($site)
                    ->setStation($stationSite)
                    ->setClassement($classementSite)
                    ->setHebergementUnifie($entitySite);

//            Gestion des traductions
                foreach ($hebergement->getTraductions() as $hebergementTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $hebergementTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($hebergementTraducSite = $em->getRepository(HebergementTraduction::class)->findOneBy(array(
                        'hebergement' => $hebergementSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $hebergementTraducSite = new HebergementTraduction();
                    }

//                copie des données traductions
                    $hebergementTraducSite->setLangue($langue)
                        ->setActivites($hebergementTraduc->getActivites())
                        ->setAvisMondofute($hebergementTraduc->getActivites())
                        ->setBienEtre($hebergementTraduc->getBienEtre())
                        ->setNom($hebergementTraduc->getNom())
                        ->setPourLesEnfants($hebergementTraduc->getPourLesEnfants())
                        ->setRestauration($hebergementTraduc->getRestauration())
                        ->setHebergement($hebergementTraduc->getHebergement());

//                ajout a la collection de traduction de la station distante
                    $hebergementSite->addTraduction($hebergementTraducSite);
                }

                $entitySite->addHebergement($hebergementSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterHebergementUnifieSiteDistant($entity->getId(), $entity);
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $hebergementUnifie
     */
    private function ajouterHebergementUnifieSiteDistant($idUnifie, HebergementUnifie $hebergementUnifie)
    {
        /** @var ArrayCollection $hebergements */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($hebergementUnifie->getHebergements()->matching($criteres)) == 0 && (empty($emSite->getRepository(HebergementUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new HebergementUnifie();
                foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
                    $entity->addFournisseur($fournisseur);
                }
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a HebergementUnifie entity.
     *
     */
    public function showAction(HebergementUnifie $hebergementUnifie)
    {
        $deleteForm = $this->createDeleteForm($hebergementUnifie);
        return $this->render('@MondofuteHebergement/hebergementunifie/show.html.twig', array(
            'hebergementUnifie' => $hebergementUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a HebergementUnifie entity.
     *
     * @param HebergementUnifie $hebergementUnifie The HebergementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(HebergementUnifie $hebergementUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hebergement_hebergement_delete',
                array('id' => $hebergementUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing HebergementUnifie entity.
     *
     */
    public function editAction(Request $request, HebergementUnifie $hebergementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                array_push($sitesAEnregistrer, $hebergement->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalHebergements = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($hebergementUnifie->getHebergements() as $hebergement) {
            $originalHebergements->add($hebergement);
        }

        $this->ajouterHebergementsDansForm($hebergementUnifie);
//        $this->dispacherDonneesCommune($departementUnifie);
        $this->hebergementsSortByAffichage($hebergementUnifie);
        $deleteForm = $this->createDeleteForm($hebergementUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType',
            $hebergementUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->supprimerHebergements($hebergementUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalHebergements as $hebergement) {
                /** @var Hebergement $hebergement */
                /** @var Hebergement $hebergementSite */
                if (!$hebergementUnifie->getHebergements()->contains($hebergement)) {
                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($hebergement->getSite()->getLibelle());
                    $entitySite = $emSite->find(HebergementUnifie::class, $hebergementUnifie->getId());
                    if (!empty($entitySite)) {
                        if (!empty($entitySite->getHebergements())) {
                            $hebergementSite = $entitySite->getHebergements()->first();
                            if (!empty($hebergementSite)) {
//                                permet de gérer les contraintes en base de données
                                if (!empty($hebergementSite->getMoyenComs())) {
                                    foreach ($hebergementSite->getMoyenComs() as $moyenComSite) {
                                        $hebergementSite->removeMoyenCom($moyenComSite);
                                        $emSite->remove($moyenComSite);
                                    }
                                    $emSite->flush();
                                }
                                $emSite->remove($hebergementSite);
                                $emSite->flush();
                            }

                        }
                    }
                    if (!empty($hebergement->getMoyenComs())) {
                        foreach ($hebergement->getMoyenComs() as $moyenCom) {
                            $hebergement->removeMoyenCom($moyenCom);
                            $em->remove($moyenCom);
                        }
                    }
//                    permet de gérer les moyens de com sans une erreur d'intégrité
                    $em->persist($hebergement);
                    $em->flush();
                    $hebergement->setHebergementUnifie(null);
                    $em->remove($hebergement);
                } else {
                    /** @var MoyenCommunication $moyenCom */
                    foreach ($hebergement->getMoyenComs() as $moyenCom) {
                        $moyenCom->setDateModification(new DateTime());
                    }
                }
            }
            $em->persist($hebergementUnifie);
            $em->flush();
            $this->copieVersSites($hebergementUnifie);
            $this->addFlash('success', 'l\'hébergement a bien été modifié');
            return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $hebergementUnifie->getId()));
        }

        return $this->render('@MondofuteHebergement/hebergementunifie/edit.html.twig', array(
            'entity' => $hebergementUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a HebergementUnifie entity.
     *
     */
    public function deleteAction(Request $request, HebergementUnifie $hebergementUnifie)
    {
        try {
            $form = $this->createDeleteForm($hebergementUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $hebergementUnifieSite = $emSite->find(HebergementUnifie::class, $hebergementUnifie->getId());
                    if (!empty($hebergementUnifieSite)) {
                        if (!empty($hebergementUnifieSite->getHebergements())) {
                            /** @var Hebergement $hebergementSite */
                            foreach ($hebergementUnifieSite->getHebergements() as $hebergementSite) {
//                                $hebergementSite->setClassement(null);
                                if (!empty($hebergementSite->getMoyenComs())) {
                                    foreach ($hebergementSite->getMoyenComs() as $moyenComSite) {
                                        $hebergementSite->removeMoyenCom($moyenComSite);
                                        $emSite->remove($moyenComSite);
                                    }
                                }
                            }
                            $emSite->flush();
                        }
                        $emSite->remove($hebergementUnifieSite);
                        $emSite->flush();
                    }
                }
                if (!empty($hebergementUnifie)) {
                    if (!empty($hebergementUnifie->getHebergements())) {
                        /** @var Hebergement $hebergement */
                        foreach ($hebergementUnifie->getHebergements() as $hebergement) {
//                            $hebergement->setClassement(null);
                            if (!empty($hebergement->getMoyenComs())) {
                                foreach ($hebergement->getMoyenComs() as $moyenCom) {
                                    $hebergement->removeMoyenCom($moyenCom);
                                    $em->remove($moyenCom);
                                }
                            }
                        }
                        $em->flush();
                    }
//                    $emSite->remove($hebergementUnifieSite);
//                    $emSite->flush();
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($hebergementUnifie);
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $except) {
            /** @var ForeignKeyConstraintViolationException $except */
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'impossible de supprimer l\'hébergement, il est utilisé par une autre entité');
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'l\' hébergement a bien été supprimé');
        return $this->redirectToRoute('hebergement_hebergement_index');
    }

//    /**
//     * @param $entity
//     */
//    private function deleteMoyenComs($entity)
//    {
//        $moyenComs = $entity->getMoyenComs();
//        if (!empty($moyenComs)) {
//            foreach ($moyenComs as $moyenCom) {
//                $entity->removeMoyenCom($moyenCom);
//            }
//        }
//    }
}
