<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\GeographieBundle\Entity\Domaine;
use Mondofute\Bundle\GeographieBundle\Entity\DomaineTraduction;
use Mondofute\Bundle\GeographieBundle\Entity\DomaineUnifie;
use Mondofute\Bundle\GeographieBundle\Form\DomaineUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DomaineUnifie controller.
 *
 */
class DomaineUnifieController extends Controller
{
    /**
     * Lists all DomaineUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $domaineUnifies = $em->getRepository('MondofuteGeographieBundle:DomaineUnifie')->findAll();
        return $this->render('@MondofuteGeographie/domaineunifie/index.html.twig', array(
            'domaineUnifies' => $domaineUnifies,
        ));
    }

    /**
     * Creates a new DomaineUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

        $sitesAEnregistrer = $request->get('sites');

        $domaineUnifie = new DomaineUnifie();

        $this->ajouterDomainesDansForm($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DomaineUnifieType', $domaineUnifie);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->supprimerDomaines($domaineUnifie, $sitesAEnregistrer)
                ->ajouterCrm($domaineUnifie);

//            $em = $this->getDoctrine()->getManager();
            $em->persist($domaineUnifie);
            $em->flush();

            $this->copieVersSites($domaineUnifie);
            return $this->redirectToRoute('geographie_domaine_show', array('id' => $domaineUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/domaineunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $domaineUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stations qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param DomaineUnifie $entity
     */
    private function ajouterDomainesDansForm(DomaineUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getDomaines() as $domaine) {
                if ($domaine->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($domaine->getTraductions()->filter(function ($element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new DomaineTraduction();
                            $traduction->setLangue($langue);
                            $domaine->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $domaine = new Domaine();
                $domaine->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new DomaineTraduction();
                    $traduction->setLangue($langue);
                    $domaine->addTraduction($traduction);
                }
                $entity->addDomaine($domaine);
            }
        }
    }

    /**
     * Classe les domaines par classementAffichage
     * @param DomaineUnifie $entity
     */
    private function domainesSortByAffichage(DomaineUnifie $entity)
    {

        // Trier les stations en fonction de leurs ordre d'affichage
        $domaines = $entity->getDomaines(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $domaines->getIterator();
        unset($domaines);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $domaines = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($domaines);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setDomaines($domaines);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $domaines
     */
    private function traductionsSortByLangue($domaines)
    {
        foreach ($domaines as $domaine) {
            $traductions = $domaine->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function ($a, $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $domaine->setTraductions($traductions);
        }
    }

    /**
     * @param DomaineUnifie $entity
     * @return $this
     */
    private function ajouterCrm(DomaineUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $siteCrm = $em->getRepository(Site::class)->findOneBy(array('crm' => 1));
        $domaineCrm = null;
        $classementReferentTmp = 0;
        $i = 0;
        // parcourir toute les domaines
        foreach ($entity->getDomaines() as $domaine) {
            //si i est égal à 0 et que le numéro de classement est inférieur au numéro de classement temporisé
            if ($i === 0 || $domaine->getSite()->getClassementReferent() < $classementReferentTmp) {
                $domaineCrm = clone $domaine;
                $domaineCrm->setSite($siteCrm);
                $classementReferentTmp = $domaine->getSite()->getClassementReferent();
            }
            $i++;
        }

        if (!is_null($domaineCrm)) {
            $entity->addDomaine($domaineCrm);
        }
        return $this;
    }

    /**
     * retirer de l'entité les domaines qui ne doivent pas être enregistrer
     * @param DomaineUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerDomaines(DomaineUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getDomaines() as $domaine) {
            if (!in_array($domaine->getSite()->getId(), $sitesAEnregistrer)) {
                $domaine->setDomaineUnifie(null);
                $entity->removeDomaine($domaine);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité station
     * @param DomaineUnifie $entity
     */
    public function copieVersSites(DomaineUnifie $entity)
    {
//        Boucle sur les stations afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDomaines() as $domaine) {
            if ($domaine->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($domaine->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $domaine->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(DomaineUnifie::class)->findOneById(array($entity->getId()))))) {
                    $entitySite = new DomaineUnifie();
                }

//            Récupération de la station sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineSite = $em->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $entitySite))))) {
                    $domaineSite = new Domaine();
                }

//            copie des données station
                $domaineSite
                    ->setSite($site)
                    ->setDomaineUnifie($entitySite);

//            Gestion des traductions
                foreach ($domaine->getTraductions() as $domaineTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $domaineTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($domaineTraducSite = $em->getRepository(DomaineTraduction::class)->findOneBy(array(
                        'domaine' => $domaineSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $domaineTraducSite = new DomaineTraduction();
                    }

//                copie des données traductions
                    $domaineTraducSite->setLangue($langue)
                        ->setLibelle($domaineTraduc->getLibelle())
                        ->setDomaine($domaineSite);

//                ajout a la collection de traduction de la station distante
                    $domaineSite->addTraduction($domaineTraducSite);
                }

                $entitySite->addDomaine($domaineSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterDomaineUnifieSiteDistant($entity->getId(), $entity->getDomaines());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de station a enregistrer
     * @param $idUnifie
     * @param $domaines
     */
    public function ajouterDomaineUnifieSiteDistant($idUnifie, $domaines)
    {
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($domaines->matching($criteres)) == 0 && (empty($emSite->getRepository(DomaineUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new DomaineUnifie();
                $emSite->persist($entity);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a DomaineUnifie entity.
     *
     */
    public function showAction(DomaineUnifie $domaineUnifie)
    {
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        return $this->render('@MondofuteGeographie/domaineunifie/show.html.twig', array(
            'domaineUnifie' => $domaineUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DomaineUnifie entity.
     *
     * @param DomaineUnifie $domaineUnifie The DomaineUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DomaineUnifie $domaineUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_domaine_delete', array('id' => $domaineUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing DomaineUnifie entity.
     *
     */
    public function editAction(Request $request, DomaineUnifie $domaineUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($domaineUnifie->getDomaines() as $domaine) {
                if (empty($domaine->getSite()->getCrm())) {
                    array_push($sitesAEnregistrer, $domaine->getSite()->getId());
                }
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $domaineCrm = $this->dissocierDomaineCrm($domaineUnifie);
        $originalDomaines = new ArrayCollection();
//          Créer un ArrayCollection des objets de stations courants dans la base de données
        foreach ($domaineUnifie->getDomaines() as $domaine) {
            $originalDomaines->add($domaine);
        }

        $this->ajouterDomainesDansForm($domaineUnifie);
//        $this->dispacherDonneesCommune($domaineUnifie);
        $this->domainesSortByAffichage($domaineUnifie);
        $deleteForm = $this->createDeleteForm($domaineUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\DomaineUnifieType', $domaineUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerDomaines($domaineUnifie, $sitesAEnregistrer);
            $this->mettreAJourDomaineCrm($domaineUnifie, $domaineCrm);
            $em->persist($domaineCrm);

            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalDomaines as $domaine) {
                if (!$domaineUnifie->getDomaines()->contains($domaine)) {

                    //  suppression de la station sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($domaine->getSite()->getLibelle());
                    $entitySite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                    $domaineSite = $entitySite->getDomaines()->first();
                    $emSite->remove($domaineSite);
                    $emSite->flush();
                    $domaine->setDomaineUnifie(null);
                    $em->remove($domaine);
                }
            }
            $em->persist($domaineUnifie);
            $em->flush();


            $this->copieVersSites($domaineUnifie);

//            dump($domaineUnifie);
//            dump($domaineCrm);
//            die;
            return $this->redirectToRoute('geographie_domaine_edit', array('id' => $domaineUnifie->getId()));
        }

        return $this->render('@MondofuteGeographie/domaineunifie/edit.html.twig', array(
            'entity' => $domaineUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * retirer la domaine crm
     * @param DomaineUnifie $entity
     *
     * @return mixed
     */
    private function dissocierDomaineCrm(DomaineUnifie $entity)
    {
        foreach ($entity->getDomaines() as $domaine) {
            if ($domaine->getSite()->getCrm() == 1) {
//                $station->setStationUnifie(null);
                $entity->removeDomaine($domaine);
                return $domaine;
            }
        }
    }

    /**
     * Mettre à jours ou créer une nouvelle stationCrm (si elle n'existe pas)
     * Permet aussi la gestion des traductions si elles n'existent pas (notament dans le cas d'un ajout de langue)
     * Retourne vrai si elle est seulement mise à jours
     * Retourne faux s'il s'agit d'une nouvelle
     * @param DomaineUnifie $domaineUnifie
     * @param Domaine $domaineCrm
     * @return bool
     */
    private function mettreAJourDomaineCrm(DomaineUnifie $domaineUnifie, Domaine $domaineCrm)
    {
        $em = $this->getDoctrine()->getManager();
        $tabClassementSiteReferent = array();

//        récupère les classementReferent pour chaque site dans un tableau
        foreach ($domaineUnifie->getDomaines() as $domaine) {
            $tabClassementSiteReferent[] = $domaine->getSite()->getClassementReferent();
        }

        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

        // Parcourir toutes les stations
        foreach ($domaineUnifie->getDomaines() as $domaine) {

            // Si la site de la station est égale au site de référence
            if ($domaine->getSite() == $siteReferent) {
//                dump($domaine);
//              ajouter les champs "communs"
                foreach ($langues as $langue) {
//                    dump($langue);
//                    recupere la traduction pour l'entite du site referent
                    $domaineTraduc = $domaine->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $domaineTraducCrm = $domaineCrm->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
//                    dump($domaineTraduc);


//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($domaineTraduc->getLibelle())) {
                        $domaineTraduc->setLibelle('');
                    }

//                    Si la traduction n'existe pas dans le crm on creer une nouvelle traduction
                    if (empty($domaineTraducCrm)) {
                        $domaineTraducCrm = new DomaineTraduction();
                        $domaineTraducCrm->setDomaine($domaineCrm);
                        $domaineTraducCrm->setLangue($langue);
//                        dump($domaineTraducCrm);
//                        dump($domaineTraduc);
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $domaineTraducCrm->setLibelle($domaineTraduc->getLibelle());
                        $domaineCrm->addTraduction($domaineTraducCrm);
                    } else {
                        //                    copie les attributs de traduction du site référent dans les traductions du crm
                        $domaineTraducCrm->setLibelle($domaineTraduc->getLibelle());
                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $domaineTraduc = $domaine->getTraductions()->filter(function ($element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs a vide
                    if (is_null($domaineTraduc->getLibelle())) {
                        $domaineTraduc->setLibelle('');
                    }
                }
            }
        }
    }

    /**
     * Deletes a DomaineUnifie entity.
     *
     */
    public function deleteAction(Request $request, DomaineUnifie $domaineUnifie)
    {
//        dump($domaineUnifie);die;
        $form = $this->createDeleteForm($domaineUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $domaineUnifieSite = $emSite->find(DomaineUnifie::class, $domaineUnifie->getId());
                if (!empty($domaineUnifieSite)) {
                    $emSite->remove($domaineUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($domaineUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('geographie_domaine_index');
    }
}
