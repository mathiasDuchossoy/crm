<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\GrandeVille;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenir;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction;
use Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie;
use Mondofute\Bundle\StationBundle\Form\StationCommentVenirUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * StationCommentVenirUnifie controller.
 *
 */
class StationCommentVenirUnifieController extends Controller
{
    /**
     * Lists all StationCommentVenirUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stationCommentVenirUnifies = $em->getRepository('MondofuteStationBundle:StationCommentVenirUnifie')->findAll();

        return $this->render('@MondofuteStation/stationcommentvenirunifie/index.html.twig', array(
            'stationCommentVenirUnifies' => $stationCommentVenirUnifies,
        ));
    }

    public function testnewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $stationCommentVenirUnifie = new StationCommentVenirUnifie();

        $this->ajouterStationCommentVenirsDansForm($stationCommentVenirUnifie);
        $this->ajouterGrandesVillesDansForm($stationCommentVenirUnifie);
        $this->stationCommentVenirsSortByAffichage($stationCommentVenirUnifie);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCommentVenirUnifieType', $stationCommentVenirUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // affilier les entités liés
//            $this->affilierEntities($stationCommentVenirUnifie);

            $this->supprimerStationCommentVenirs($stationCommentVenirUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationCommentVenirUnifie);
            $em->flush();

            $this->copieVersSites($stationCommentVenirUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La StationCommentVenir a bien été créé.'
            );

            return $this->redirectToRoute('stationcommentvenir_edit', array('id' => $stationCommentVenirUnifie->getId()));
        }

        return $this;
    }

    /**
     * Ajouter les StationCommentVenirs qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param StationCommentVenirUnifie $entity
     */
    private function ajouterStationCommentVenirsDansForm(StationCommentVenirUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getStationCommentVenirs() as $stationCommentVenir) {
                if ($stationCommentVenir->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($stationCommentVenir->getTraductions()->filter(function (StationCommentVenirTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new StationCommentVenirTraduction();
                            $traduction->setLangue($langue);
                            $stationCommentVenir->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $stationCommentVenir = new StationCommentVenir();
                $stationCommentVenir->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new StationCommentVenirTraduction();
                    $traduction->setLangue($langue);
                    $stationCommentVenir->addTraduction($traduction);
                }
                $entity->addStationCommentVenir($stationCommentVenir);
            }
        }
    }

    private function ajouterGrandesVillesDansForm(StationCommentVenirUnifie $entity)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $grandeVilles = $em->getRepository(GrandeVille::class)->findAll();
        /** @var StationCommentVenir $stationCommentVenir */
        foreach ($entity->getStationCommentVenirs() as $stationCommentVenir) {
            foreach ($grandeVilles as $grandeVille) {
                $stationCommentVenirGranceVille = new StationCommentVenirGrandeVille();
                $stationCommentVenirGranceVille->setGrandeVille($grandeVille);
                $stationCommentVenir->addGrandeVille($stationCommentVenirGranceVille);
            }
        }

    }

    /**
     * Classe les StationCommentVenirs par classementAffichage
     * @param StationCommentVenirUnifie $entity
     */
    private function stationCommentVenirsSortByAffichage(StationCommentVenirUnifie $entity)
    {

        // Trier les StationCommentVenirs en fonction de leurs ordre d'affichage
        $stationCommentVenirs = $entity->getStationCommentVenirs(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $stationCommentVenirs->getIterator();
        unset($stationCommentVenirs);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (StationCommentVenir $a, StationCommentVenir $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $stationCommentVenirs = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($stationCommentVenirs);

        // remplacé les StationCommentVenirs par ce nouveau tableau (une fonction 'set' a été créé dans StationCommentVenir unifié)
        $entity->setStationCommentVenirs($stationCommentVenirs);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $stationCommentVenirs
     */
    private function traductionsSortByLangue($stationCommentVenirs)
    {
        /** @var ArrayIterator $iterator */
        /** @var StationCommentVenir $stationCommentVenir */
        foreach ($stationCommentVenirs as $stationCommentVenir) {
            $traductions = $stationCommentVenir->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (StationCommentVenirTraduction $a, StationCommentVenirTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $stationCommentVenir->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les StationCommentVenirs qui ne doivent pas être enregistrer
     * @param StationCommentVenirUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerStationCommentVenirs(StationCommentVenirUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getStationCommentVenirs() as $stationCommentVenir) {
            if (!in_array($stationCommentVenir->getSite()->getId(), $sitesAEnregistrer)) {
                $stationCommentVenir->setStationCommentVenirUnifie(null);
                $entity->removeStationCommentVenir($stationCommentVenir);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité StationCommentVenir
     * @param StationCommentVenirUnifie $entity
     */
    private function copieVersSites(StationCommentVenirUnifie $entity)
    {
        /** @var StationCommentVenirTraduction $stationCommentVenirTraduc */
//        Boucle sur les StationCommentVenirs afin de savoir sur quel site nous devons l'enregistrer
        /** @var StationCommentVenir $stationCommentVenir */
        foreach ($entity->getStationCommentVenirs() as $stationCommentVenir) {
            if ($stationCommentVenir->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($stationCommentVenir->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $stationCommentVenir->getSite()->getId()));

//                if (!empty($stationCommentVenir->getZoneTouristique())) {
//                    $zoneTouristique = $emSite->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $stationCommentVenir->getZoneTouristique()->getZoneTouristiqueUnifie()));
//                } else {
//                    $zoneTouristique = null;
//                }
//                if (!empty($stationCommentVenir->getSecteur())) {
//                    $secteur = $emSite->getRepository(Secteur::class)->findOneBy(array('secteurUnifie' => $stationCommentVenir->getSecteur()->getSecteurUnifie()));
//                } else {
//                    $secteur = null;
//                }
//                if (!empty($stationCommentVenir->getDomaine())) {
//                    $domaine = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $stationCommentVenir->getDomaine()->getDomaineUnifie()));
//                } else {
//                    $domaine = null;
//                }
//                if (!empty($stationCommentVenir->getDepartement())) {
//                    $departement = $emSite->getRepository(Departement::class)->findOneBy(array('departementUnifie' => $stationCommentVenir->getDepartement()->getDepartementUnifie()));
//                } else {
//                    $departement = null;
//                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $emSite->getRepository(StationCommentVenirUnifie::class)->find($entity->getId())))) {
                    $entitySite = new StationCommentVenirUnifie();
                }
//                if (is_null(($entitySite = $em->getRepository('MondofuteStationCommentVenirBundle:StationCommentVenirUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new StationCommentVenirUnifie();
//                }


//            Récupération de la StationCommentVenir sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationCommentVenirSite = $emSite->getRepository(StationCommentVenir::class)->findOneBy(array('stationCommentVenirUnifie' => $entitySite))))) {
                    $stationCommentVenirSite = new StationCommentVenir();
                }

//            copie des données StationCommentVenir
                $stationCommentVenirSite
                    ->setSite($site)
                    ->setStationCommentVenirUnifie($entitySite);

//            Gestion des traductions
//                dump($stationCommentVenir->getTraductions());die;
                foreach ($stationCommentVenir->getTraductions() as $stationCommentVenirTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $stationCommentVenirTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($stationCommentVenirTraducSite = $emSite->getRepository(StationCommentVenirTraduction::class)->findOneBy(array(
                        'stationCommentVenir' => $stationCommentVenirSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $stationCommentVenirTraducSite = new StationCommentVenirTraduction();
                    }

//                copie des données traductions
                    $stationCommentVenirTraducSite
                        ->setLangue($langue)
                        ->setStationCommentVenir($stationCommentVenirSite)
                        ->setEnVoiture($stationCommentVenirTraduc->getEnVoiture())
                        ->setEnTrain($stationCommentVenirTraduc->getEnTrain())
                        ->setEnAvion($stationCommentVenirTraduc->getEnAvion())
                        ->setDistancesGrandesVilles($stationCommentVenirTraduc->getDistancesGrandesVilles());

//                ajout a la collection de traduction de la StationCommentVenir distante
                    $stationCommentVenirSite->addTraduction($stationCommentVenirTraducSite);
                }

                $entitySite->addStationCommentVenir($stationCommentVenirSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterStationCommentVenirUnifieSiteDistant($entity->getId(), $entity->getStationCommentVenirs());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de StationCommentVenir a enregistrer
     * @param $idUnifie
     * @param $stationCommentVenirs
     */
    private function ajouterStationCommentVenirUnifieSiteDistant($idUnifie, $stationCommentVenirs)
    {
        /** @var ArrayCollection $stationCommentVenirs */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($stationCommentVenirs->matching($criteres)) == 0 && (empty($emSite->getRepository(StationCommentVenirUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new StationCommentVenirUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Creates a new StationCommentVenirUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $stationCommentVenirUnifie = new StationCommentVenirUnifie();

        $this->ajouterStationCommentVenirsDansForm($stationCommentVenirUnifie);
        $this->ajouterGrandesVillesDansForm($stationCommentVenirUnifie);
        $this->stationCommentVenirsSortByAffichage($stationCommentVenirUnifie);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCommentVenirUnifieType', $stationCommentVenirUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // affilier les entités liés
//            $this->affilierEntities($stationCommentVenirUnifie);

            $this->supprimerStationCommentVenirs($stationCommentVenirUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationCommentVenirUnifie);
            $em->flush();

            $this->copieVersSites($stationCommentVenirUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La StationCommentVenir a bien été créé.'
            );

            return $this->redirectToRoute('stationcommentvenir_edit', array('id' => $stationCommentVenirUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationcommentvenirunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $stationCommentVenirUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a StationCommentVenirUnifie entity.
     *
     */
    public function showAction(StationCommentVenirUnifie $stationCommentVenirUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationCommentVenirUnifie);

        return $this->render('@MondofuteStation/stationcommentvenirunifie/show.html.twig', array(
            'stationCommentVenirUnifie' => $stationCommentVenirUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a StationCommentVenirUnifie entity.
     *
     * @param StationCommentVenirUnifie $stationCommentVenirUnifie The StationCommentVenirUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationCommentVenirUnifie $stationCommentVenirUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stationcommentvenir_delete', array('id' => $stationCommentVenirUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing StationCommentVenirUnifie entity.
     *
     */
    public function editAction(Request $request, StationCommentVenirUnifie $stationCommentVenirUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($stationCommentVenirUnifie->getStationCommentVenirs() as $stationCommentVenir) {
                array_push($sitesAEnregistrer, $stationCommentVenir->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalStationCommentVenirs = new ArrayCollection();
//          Créer un ArrayCollection des objets de stationCommentVenirs courants dans la base de données
        foreach ($stationCommentVenirUnifie->getStationCommentVenirs() as $stationCommentVenir) {
            $originalStationCommentVenirs->add($stationCommentVenir);
        }

        $this->ajouterStationCommentVenirsDansForm($stationCommentVenirUnifie);
//        $this->affilierEntities($stationCommentVenirUnifie);

        $this->stationCommentVenirsSortByAffichage($stationCommentVenirUnifie);
        $deleteForm = $this->createDeleteForm($stationCommentVenirUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCommentVenirUnifieType',
            $stationCommentVenirUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

//        dump($editForm);die;

        $editForm->handleRequest($request);
//        dump($stationCommentVenirUnifie);die;

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerStationCommentVenirs($stationCommentVenirUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la StationCommentVenir et StationCommentVenirUnifie
            foreach ($originalStationCommentVenirs as $stationCommentVenir) {
                if (!$stationCommentVenirUnifie->getStationCommentVenirs()->contains($stationCommentVenir)) {

                    //  suppression de la StationCommentVenir sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($stationCommentVenir->getSite()->getLibelle());
                    $entitySite = $emSite->find(StationCommentVenirUnifie::class, $stationCommentVenirUnifie->getId());
                    $stationCommentVenir = $entitySite->getStationCommentVenirs()->first();
                    $emSite->remove($stationCommentVenir);
                    $emSite->flush();
//                    dump($stationCommentVenir);
                    $stationCommentVenir->setStationCommentVenirUnifie(null);
                    $em->remove($stationCommentVenir);
                }
            }
            $em->persist($stationCommentVenirUnifie);
            $em->flush();


            $this->copieVersSites($stationCommentVenirUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La StationCommentVenir a bien été modifié.'
            );

            return $this->redirectToRoute('stationcommentvenir_edit', array('id' => $stationCommentVenirUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationcommentvenirunifie/edit.html.twig', array(
            'entity' => $stationCommentVenirUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a StationCommentVenirUnifie entity.
     *
     */
    public function deleteAction(Request $request, StationCommentVenirUnifie $stationCommentVenirUnifie)
    {
        $form = $this->createDeleteForm($stationCommentVenirUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $stationCommentVenirUnifieSite = $emSite->find(StationCommentVenirUnifie::class, $stationCommentVenirUnifie->getId());
                if (!empty($stationCommentVenirUnifieSite)) {
                    $emSite->remove($stationCommentVenirUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($stationCommentVenirUnifie);
            $em->flush();


            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La StationCommentVenir a été supprimé avec succès.'
            );

        }

        return $this->redirectToRoute('stationcommentvenir_index');
    }

}
