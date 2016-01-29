<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentite;
use Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteUnifie;
use Mondofute\Bundle\GeographieBundle\Form\DomaineCarteIdentiteUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DomaineCarteIdentiteUnifie controller.
 *
 */
class DomaineCarteIdentiteUnifieController extends Controller
{
    /**
     * Lists all DomaineCarteIdentiteUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $domaineCarteIdentiteUnifies = $em->getRepository('MondofuteGeographieBundle:DomaineCarteIdentiteUnifie')->findAll();

        return $this->render('@MondofuteGeographie/domaineCarteIdentiteunifie/index.html.twig', array(
            'domaineCarteIdentiteUnifies' => $domaineCarteIdentiteUnifies,
        ));
    }

    /**
     * Creates a new DomaineCarteIdentiteUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = $request->get('sites');

        $domaineCarteIdentiteUnifie = new DomaineCarteIdentiteUnifie();

        $this->ajouterDomaineCarteIdentitesDansForm($domaineCarteIdentiteUnifie);
//        $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
        $this->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DomaineCarteIdentiteUnifieType', $domaineCarteIdentiteUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // dispacher les données communes
            $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);

            $this->supprimerDomaineCarteIdentites($domaineCarteIdentiteUnifie, $sitesAEnregistrer)
                ->ajouterCrm($domaineCarteIdentiteUnifie);

            $em = $this->getDoctrine()->getManager();
            $em->persist($domaineCarteIdentiteUnifie);
            $em->flush();

            $this->copieVersSites($domaineCarteIdentiteUnifie);
            return $this->redirectToRoute('geographie_domaineCarteIdentite_show', array('id' => $domaineCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/domaineCarteIdentiteunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $domaineCarteIdentiteUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les domaineCarteIdentites qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DomaineCarteIdentiteUnifie $entity
     */
    private function ajouterDomaineCarteIdentitesDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
                if ($domaineCarteIdentite->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaineCarteIdentite->getTraductions()->filter(function ($element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DomaineCarteIdentiteTraduction();
                            $traduction->setLangue($langue);
                            $domaineCarteIdentite->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $domaineCarteIdentite = new DomaineCarteIdentite();
                $domaineCarteIdentite->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DomaineCarteIdentiteTraduction();
                    $traduction->setLangue($langue);
                    $domaineCarteIdentite->addTraduction($traduction);
                }
                $entity->addDomaineCarteIdentite($domaineCarteIdentite);
            }
        }
    }


    /**
     * Classe les domaineCarteIdentites par classementAffichage
     * @param DomaineCarteIdentiteUnifie $entity
     */
    private function domaineCarteIdentitesSortByAffichage(DomaineCarteIdentiteUnifie $entity)
    {

        // Trier les domaineCarteIdentites en fonction de leurs ordre d'affichage
        $domaineCarteIdentites = $entity->getDomaineCarteIdentites(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $domaineCarteIdentites->getIterator();
        unset($domaineCarteIdentites);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $domaineCarteIdentites = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($domaineCarteIdentites);

        // remplacé les domaineCarteIdentites par ce nouveau tableau (une fonction 'set' a été créé dans DomaineCarteIdentite unifié)
        $entity->setDomaineCarteIdentites($domaineCarteIdentites);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $domaineCarteIdentites
     */
    private function traductionsSortByLangue($domaineCarteIdentites)
    {
        foreach ($domaineCarteIdentites as $domaineCarteIdentite) {
            $traductions = $domaineCarteIdentite->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function ($a, $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $domaineCarteIdentite->setTraductions($traductions);
        }
    }

    /**
     * dispacher les données communes dans chaque domaineCarteIdentites
     * @param DomaineCarteIdentiteUnifie $entity
     */
    private function dispacherDonneesCommune(DomaineCarteIdentiteUnifie $entity)
    {
        $domaineCarteIdentiteFirst = $entity->getDomaineCarteIdentites()->first();
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
//            $zoneTouristique = $domaineCarteIdentiteFirst->getZoneTouristique()->getZoneTouristiqueUnifie()->getZoneTouristiques()->filter(function ($element) use ($domaineCarteIdentite) {
//                return $element->getSite() == $domaineCarteIdentite->getSite();
//            })->first();
//            $domaineCarteIdentite->setZoneTouristique($zoneTouristique);
//            $domaineCarteIdentite->setCodePostal($domaineCarteIdentiteFirst->getCodePostal());
////            $domaineCarteIdentite->setMoisOuverture($domaineCarteIdentiteFirst->getMoisOuverture());
////            $domaineCarteIdentite->setJourOuverture($domaineCarteIdentiteFirst->getJourOuverture());
////            $domaineCarteIdentite->setMoisFermeture($domaineCarteIdentiteFirst->getMoisFermeture());
////            $domaineCarteIdentite->setJourFermeture($domaineCarteIdentiteFirst->getJourFermeture());
//            $domaineCarteIdentite->setLienMeteo($domaineCarteIdentiteFirst->getLienMeteo());
        }
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterCrm(DomaineCarteIdentiteUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $siteCrm = $em->getRepository(Site::class)->findOneBy(array('crm' => 1));
        $domaineCarteIdentiteCrm = null;
        $classementReferentTmp = 0;
        $i = 0;
        // parcourir toute les domaineCarteIdentites
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            //si i est égal à 0 et que le numéro de classement est inférieur au numéro de classement temporisé
            if ($i === 0 || $domaineCarteIdentite->getSite()->getClassementReferent() < $classementReferentTmp) {
                $domaineCarteIdentiteCrm = clone $domaineCarteIdentite;
                $domaineCarteIdentiteCrm->setSite($siteCrm);
                $domaineCarteIdentite->setAltitudeMini($domaineCarteIdentite->getAltitudeMini());
                $domaineCarteIdentite->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi());
                $domaineCarteIdentite->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin());
                $domaineCarteIdentite->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique());
                $classementReferentTmp = $domaineCarteIdentite->getSite()->getClassementReferent();
            }
            $i++;
        }

        if (!is_null($domaineCarteIdentiteCrm)) {
            $entity->addDomaineCarteIdentite($domaineCarteIdentiteCrm);
        }
        return $this;
    }

    /**
     * retirer de l'entité les domaineCarteIdentites qui ne doivent pas être enregistrer
     * @param DomaineCarteIdentiteUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDomaineCarteIdentites(DomaineCarteIdentiteUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if (!in_array($domaineCarteIdentite->getSite()->getId(), $sitesAEnregistrer)) {
                $domaineCarteIdentite->setDomaineCarteIdentiteUnifie(null);
                $entity->removeDomaineCarteIdentite($domaineCarteIdentite);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité domaineCarteIdentite
     * @param DomaineCarteIdentiteUnifie $entity
     */
    private function copieVersSites(DomaineCarteIdentiteUnifie $entity)
    {
//        Boucle sur les domaineCarteIdentites afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if ($domaineCarteIdentite->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($domaineCarteIdentite->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $domaineCarteIdentite->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(DomaineCarteIdentiteUnifie::class)->findOneById(array($entity->getId()))))) {
                    $entitySite = new DomaineCarteIdentiteUnifie();
                }

//            Récupération de la domaineCarteIdentite sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineCarteIdentiteSite = $em->getRepository(DomaineCarteIdentite::class)->findOneBy(array('domaineCarteIdentiteUnifie' => $entitySite))))) {
                    $domaineCarteIdentiteSite = new DomaineCarteIdentite();
                }

//            copie des données domaineCarteIdentite
                $domaineCarteIdentiteSite
                    ->setSite($site)
                    ->setDomaineCarteIdentiteUnifie($entitySite)
                    ->setAltitudeMini($domaineCarteIdentite->getAltitudeMini())
                    ->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi())
                    ->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin())
                    ->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique());

//            Gestion des traductions
                foreach ($domaineCarteIdentite->getTraductions() as $domaineCarteIdentiteTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $domaineCarteIdentiteTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($domaineCarteIdentiteTraducSite = $em->getRepository(DomaineCarteIdentiteTraduction::class)->findOneBy(array(
                        'domaineCarteIdentite' => $domaineCarteIdentiteSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $domaineCarteIdentiteTraducSite = new DomaineCarteIdentiteTraduction();
                    }

//                copie des données traductions
                    $domaineCarteIdentiteTraducSite->setLangue($langue)
                        ->setAccroche($domaineCarteIdentiteTraduc->getAccroche())
                        ->setDescription($domaineCarteIdentiteTraduc->getDescription())
                        ->setDomaineCarteIdentite($domaineCarteIdentiteSite);

//                ajout a la collection de traduction de la domaineCarteIdentite distante
                    $domaineCarteIdentiteSite->addTraduction($domaineCarteIdentiteTraducSite);
                }

                $entitySite->addDomaineCarteIdentite($domaineCarteIdentiteSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterDomaineCarteIdentiteUnifieSiteDistant($entity->getId(), $entity->getDomaineCarteIdentites());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de domaineCarteIdentite a enregistrer
     * @param $idUnifie
     * @param $domaineCarteIdentites
     */
    private function ajouterDomaineCarteIdentiteUnifieSiteDistant($idUnifie, $domaineCarteIdentites)
    {
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($domaineCarteIdentites->matching($criteres)) == 0 && (empty($emSite->getRepository(DomaineCarteIdentiteUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DomaineCarteIdentiteUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a DomaineCarteIdentiteUnifie entity.
     *
     */
    public function showAction(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        $deleteForm = $this->createDeleteForm($domaineCarteIdentiteUnifie);

        return $this->render('@MondofuteGeographie/domaineCarteIdentiteunifie/show.html.twig', array(
            'domaineCarteIdentiteUnifie' => $domaineCarteIdentiteUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DomaineCarteIdentiteUnifie entity.
     *
     * @param DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie The DomaineCarteIdentiteUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_domaineCarteIdentite_delete', array('id' => $domaineCarteIdentiteUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DomaineCarteIdentiteUnifie entity.
     *
     */
    public function editAction(Request $request, DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {
                if (empty($domaineCarteIdentite->getSite()->getCrm())) {
                    array_push($sitesAEnregistrer, $domaineCarteIdentite->getSite()->getId());
                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $domaineCarteIdentiteCrm = $this->dissocierDomaineCarteIdentiteCrm($domaineCarteIdentiteUnifie);
        $originalDomaineCarteIdentites = new ArrayCollection();
//          Créer un ArrayCollection des objets de domaineCarteIdentites courants dans la base de données
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $originalDomaineCarteIdentites->add($domaineCarteIdentite);
        }

        $this->ajouterDomaineCarteIdentitesDansForm($domaineCarteIdentiteUnifie);
        $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
        $this->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);
        $deleteForm = $this->createDeleteForm($domaineCarteIdentiteUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DomaineCarteIdentiteUnifieType',
            $domaineCarteIdentiteUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
            $this->supprimerDomaineCarteIdentites($domaineCarteIdentiteUnifie, $sitesAEnregistrer);
            $this->mettreAJourDomaineCarteIdentiteCrm($domaineCarteIdentiteUnifie, $domaineCarteIdentiteCrm);
            $em->persist($domaineCarteIdentiteCrm);

            // Supprimer la relation entre la domaineCarteIdentite et domaineCarteIdentiteUnifie
            foreach ($originalDomaineCarteIdentites as $domaineCarteIdentite) {
                if (!$domaineCarteIdentiteUnifie->getDomaineCarteIdentites()->contains($domaineCarteIdentite)) {

                    //  suppression de la domaineCarteIdentite sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($domaineCarteIdentite->getSite()->getLibelle());
                    $entitySite = $emSite->find(DomaineCarteIdentiteUnifie::class, $domaineCarteIdentiteUnifie->getId());
                    $domaineCarteIdentiteSite = $entitySite->getDomaineCarteIdentites()->first();
                    $emSite->remove($domaineCarteIdentiteSite);
                    $emSite->flush();
//                    dump($domaineCarteIdentite);
                    $domaineCarteIdentite->setDomaineCarteIdentiteUnifie(null);
                    $em->remove($domaineCarteIdentite);
                }
            }
            $em->persist($domaineCarteIdentiteUnifie);
            $em->flush();


            $this->copieVersSites($domaineCarteIdentiteUnifie);

//            dump($domaineCarteIdentiteUnifie);
//            dump($domaineCarteIdentiteCrm);
//            die;
            return $this->redirectToRoute('geographie_domaineCarteIdentite_edit', array('id' => $domaineCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/domaineCarteIdentiteunifie/edit.html.twig', array(
            'entity' => $domaineCarteIdentiteUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * retirer la domaineCarteIdentite crm
     * @param DomaineCarteIdentiteUnifie $entity
     *
     * @return mixed
     */
    private function dissocierDomaineCarteIdentiteCrm(DomaineCarteIdentiteUnifie $entity)
    {
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if ($domaineCarteIdentite->getSite()->getCrm() == 1) {
//                $domaineCarteIdentite->setDomaineCarteIdentiteUnifie(null);
                $entity->removeDomaineCarteIdentite($domaineCarteIdentite);
                return $domaineCarteIdentite;
            }
        }
    }

    /**
     * Mettre à jours ou créer une nouvelle domaineCarteIdentiteCrm (si elle n'existe pas)
     * Permet aussi la gestion des traductions si elles n'existent pas (notament dans le cas d'un ajout de langue)
     * Retourne vrai si elle est seulement mise à jours
     * Retourne faux s'il s'agit d'une nouvelle
     * @param DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie
     * @param DomaineCarteIdentite $domaineCarteIdentiteCrm
     * @return bool
     */
    private function mettreAJourDomaineCarteIdentiteCrm(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie, DomaineCarteIdentite $domaineCarteIdentiteCrm)
    {
        $em = $this->getDoctrine()->getManager();
        $tabClassementSiteReferent = array();

//        récupère les classementReferent pour chaque site dans un tableau
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $tabClassementSiteReferent[] = $domaineCarteIdentite->getSite()->getClassementReferent();
        }

        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

        // Parcourir toutes les domaineCarteIdentites
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {

            // Si la site de la domaineCarteIdentite est égale au site de référence
            if ($domaineCarteIdentite->getSite() == $siteReferent) {
//                dump($domaineCarteIdentite);
//           ajouter les champs "communs"

                $domaineCarteIdentiteCrm
                    ->setAltitudeMini($domaineCarteIdentite->getAltitudeMini())
                    ->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi())
                    ->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin())
                    ->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique());

                foreach ($langues as $langue) {
//                    dump($langue);
//                    recupere la traduction pour l'entite du site referent
                    $domaineCarteIdentiteTraduc = $domaineCarteIdentite->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $domaineCarteIdentiteTraducCrm = $domaineCarteIdentiteCrm->getTraductions()->filter(function ($element) use ($langue
                    ) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($domaineCarteIdentiteTraduc);

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($domaineCarteIdentiteTraduc->getAccroche())) {
                        $domaineCarteIdentiteTraduc->setAccroche('');
                    }
                    if (is_null($domaineCarteIdentiteTraduc->getDescription())) {
                        $domaineCarteIdentiteTraduc->setDescription('');
                    }


//                    Si la traduction n'existe pas dans le crm on creer une nouvelle traduction
                    if (empty($domaineCarteIdentiteTraducCrm)) {
                        $domaineCarteIdentiteTraducCrm = new DomaineCarteIdentiteTraduction();
                        $domaineCarteIdentiteTraducCrm->setDomaineCarteIdentite($domaineCarteIdentiteCrm);
                        $domaineCarteIdentiteTraducCrm->setLangue($langue);
//                        dump($domaineCarteIdentiteTraducCrm);
//                        dump($domaineCarteIdentiteTraduc);
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $domaineCarteIdentiteTraducCrm->setAccroche($domaineCarteIdentiteTraduc->getAccroche());
                        $domaineCarteIdentiteTraducCrm->setDescription($domaineCarteIdentiteTraduc->getDescription());
                        $domaineCarteIdentiteCrm->addTraduction($domaineCarteIdentiteTraducCrm);
                    } else {
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $domaineCarteIdentiteTraducCrm->setAccroche($domaineCarteIdentiteTraduc->getAccroche());
                        $domaineCarteIdentiteTraducCrm->setDescription($domaineCarteIdentiteTraduc->getDescription());

                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $domaineCarteIdentiteTraduc = $domaineCarteIdentite->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($domaineCarteIdentiteTraduc->getAccroche())) {
                        $domaineCarteIdentiteTraduc->setAccroche('');
                    }
                    if (is_null($domaineCarteIdentiteTraduc->getDescription())) {
                        $domaineCarteIdentiteTraduc->setDescription('');
                    }

                }
            }
        }
//die;
    }

    /**
     * Deletes a DomaineCarteIdentiteUnifie entity.
     *
     */
    public function deleteAction(Request $request, DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie)
    {
        $form = $this->createDeleteForm($domaineCarteIdentiteUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $domaineCarteIdentiteUnifieSite = $emSite->find(DomaineCarteIdentiteUnifie::class, $domaineCarteIdentiteUnifie->getId());
                if (!empty($domaineCarteIdentiteUnifieSite)) {
                    $emSite->remove($domaineCarteIdentiteUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($domaineCarteIdentiteUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('geographie_domaineCarteIdentite_index');
    }

}
