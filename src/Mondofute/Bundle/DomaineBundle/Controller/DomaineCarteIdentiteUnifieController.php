<?php

namespace Mondofute\Bundle\DomaineBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteUnifie;
use Mondofute\Bundle\DomaineBundle\Entity\Handiski;
use Mondofute\Bundle\DomaineBundle\Entity\HandiskiTraduction;
use Mondofute\Bundle\DomaineBundle\Entity\Snowpark;
use Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction;
use Mondofute\Bundle\DomaineBundle\Form\DomaineCarteIdentiteUnifieType;
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

        $domaineCarteIdentiteUnifies = $em->getRepository('MondofuteDomaineBundle:DomaineCarteIdentiteUnifie')->findAll();

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/index.html.twig', array(
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

        $this->ajouterDomaineCarteIdentitesDansForm($domaineCarteIdentiteUnifie)
            ->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);
        $this->ajouterSnowparksDansForm($domaineCarteIdentiteUnifie)
            ->ajouterHandiskiDansForm($domaineCarteIdentiteUnifie);
//        $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
//        $this->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);

        $form = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineCarteIdentiteUnifieType', $domaineCarteIdentiteUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // dispacher les données communes
//            $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);

            $this->supprimerDomaineCarteIdentites($domaineCarteIdentiteUnifie, $sitesAEnregistrer)
                ->ajouterCrm($domaineCarteIdentiteUnifie);

            $em = $this->getDoctrine()->getManager();
            $em->persist($domaineCarteIdentiteUnifie);
            $em->flush();

            $this->copieVersSites($domaineCarteIdentiteUnifie);

            // add flash messages
            $this->addFlash(
                'success',
                'Le carte d\'identité du domaine  a bien été créé.'
            );

            return $this->redirectToRoute('domaine_domaineCarteIdentite_edit', array('id' => $domaineCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $domaineCarteIdentiteUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Classe les domaineCarteIdentites par classementAffichage
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function domaineCarteIdentitesSortByAffichage(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les domaineCarteIdentites en fonction de leurs ordre d'affichage
        $domaineCarteIdentites = $entity->getDomaineCarteIdentites(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $domaineCarteIdentites->getIterator();
        unset($domaineCarteIdentites);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (DomaineCarteIdentite $a, DomaineCarteIdentite $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $domaineCarteIdentites = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($domaineCarteIdentites);

        // remplacé les domaineCarteIdentites par ce nouveau tableau (une fonction 'set' a été créé dans DomaineCarteIdentite unifié)
        $entity->setDomaineCarteIdentites($domaineCarteIdentites);

        return $this;
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $domaineCarteIdentites
     */
    private function traductionsSortByLangue($domaineCarteIdentites)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var ArrayIterator $iterator */
        foreach ($domaineCarteIdentites as $domaineCarteIdentite) {
            $traductions = $domaineCarteIdentite->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (DomaineCarteIdentiteTraduction $a, DomaineCarteIdentiteTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $domaineCarteIdentite->setTraductions($traductions);
        }
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
                        if ($domaineCarteIdentite->getTraductions()->filter(function (DomaineCarteIdentiteTraduction $element) use ($langue) {
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
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterHandiskiDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $traduction */
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $handiski = !empty($domaineCarteIdentite->getHandiski()) ? $domaineCarteIdentite->getHandiski() : new Handiski();
            foreach ($domaineCarteIdentite->getTraductions() as $traduction) {
                if (!empty($handiski->getTraductions())) {
                    $langue = $traduction->getLangue();
                    if (empty($handiski->getTraductions()->filter(function (HandiskiTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first())
                    ) {
                        $handiskiTraduction = new HandiskiTraduction();
                        $handiskiTraduction->setLangue($traduction->getLangue());
                        $handiski->addTraduction($handiskiTraduction);
                    }
                }
            }
            $domaineCarteIdentite->setHandiski($handiski);
        }
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterSnowparksDansForm(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $traduction */
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $snowpark = !empty($domaineCarteIdentite->getSnowpark()) ? $domaineCarteIdentite->getSnowpark() : new Snowpark();
            foreach ($domaineCarteIdentite->getTraductions() as $traduction) {
                if (!empty($snowpark->getTraductions())) {
                    $langue = $traduction->getLangue();
                    if (empty($snowpark->getTraductions()->filter(function (SnowparkTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first())
                    ) {
                        $snowparkTraduction = new SnowparkTraduction();
                        $snowparkTraduction->setLangue($traduction->getLangue());
                        $snowpark->addTraduction($snowparkTraduction);
                    }
                }
            }
            $domaineCarteIdentite->setSnowpark($snowpark);
        }
        return $this;
    }

    /**
     * @param DomaineCarteIdentiteUnifie $entity
     * @return $this
     */
    private function ajouterCrm(DomaineCarteIdentiteUnifie $entity)
    {
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
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
//                $domaineCarteIdentiteCrm->setAltitudeMini($domaineCarteIdentite->getAltitudeMini());
//                $domaineCarteIdentiteCrm->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi());
//                $domaineCarteIdentiteCrm->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin());
//                $domaineCarteIdentiteCrm->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique());
//                $domaineCarteIdentiteCrm->setRemonteeMecanique($domaineCarteIdentite->getRemonteeMecanique());
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
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
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
        /** @var SnowparkTraduction $snowparkTraductionSite */
        /** @var HandiskiTraduction $handiskiTraductionSite */
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $domaineCarteIdentiteTraduc */
//        Boucle sur les domaineCarteIdentites afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if ($domaineCarteIdentite->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($domaineCarteIdentite->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $domaineCarteIdentite->getSite()->getId()));

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
//                if (is_null(($entitySite = $em->getRepository(DomaineCarteIdentiteUnifie::class)->findOneById(array($entity->getId()))))) {
//                    $entitySite = new DomaineCarteIdentiteUnifie();
//                }
                if (is_null(($entitySite = $em->find(DomaineCarteIdentiteUnifie::class, $entity->getId())))) {
                    $entitySite = new DomaineCarteIdentiteUnifie();
                }

//            Récupération de la domaineCarteIdentite sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($domaineCarteIdentiteSite = $em->getRepository(DomaineCarteIdentite::class)->findOneBy(array('domaineCarteIdentiteUnifie' => $entitySite))))) {
                    $domaineCarteIdentiteSite = new DomaineCarteIdentite();
                }

//            copie des données domaineCarteIdentite
                $snowparkSite = !empty($domaineCarteIdentiteSite->getSnowpark()) ? $domaineCarteIdentiteSite->getSnowpark() : clone $domaineCarteIdentite->getSnowpark();
                foreach ($snowparkSite->getTraductions() as $snowparkTraductionSite) {
                    /** @var SnowparkTraduction $snowparkTraduction */
                    $snowparkTraduction = $domaineCarteIdentite->getSnowpark()->getTraductions()->filter(function (SnowparkTraduction $element) use ($snowparkTraductionSite) {
                        return $element->getLangue()->getId() == $snowparkTraductionSite->getLangue()->getId();
                    })->first();
                    $snowparkTraductionSite->setDescription($snowparkTraduction->getDescription());
                    $snowparkTraductionSite->setLangue($em->find(Langue::class, $snowparkTraductionSite->getLangue()));
                }
                $handiskiSite = !empty($domaineCarteIdentiteSite->getHandiski()) ? $domaineCarteIdentiteSite->getHandiski() : clone $domaineCarteIdentite->getHandiski();
                foreach ($handiskiSite->getTraductions() as $handiskiTraductionSite) {
                    /** @var HandiskiTraduction $handiskiTraduction */
                    $handiskiTraduction = $domaineCarteIdentite->getHandiski()->getTraductions()->filter(function (HandiskiTraduction $element) use ($handiskiTraductionSite) {
                        return $element->getLangue()->getId() == $handiskiTraductionSite->getLangue()->getId();
                    })->first();
                    $handiskiTraductionSite->setDescription($handiskiTraduction->getDescription());
                    $handiskiTraductionSite->setLangue($em->find(Langue::class, $handiskiTraductionSite->getLangue()));
                }
                $remonteeMecaniqueSite = !empty($domaineCarteIdentiteSite->getRemonteeMecanique()) ? $domaineCarteIdentiteSite->getRemonteeMecanique() : clone $domaineCarteIdentite->getRemonteeMecanique();
                $remonteeMecaniqueSite->setNombre($domaineCarteIdentite->getRemonteeMecanique()->getNombre());
                $domaineCarteIdentiteSite
                    ->setSite($site)
                    ->setDomaineCarteIdentiteUnifie($entitySite)
                    ->setAltitudeMini($domaineCarteIdentite->getAltitudeMini())
                    ->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi())
                    ->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin())
                    ->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique())
                    ->setSnowpark($snowparkSite)
                    ->setHandiski($handiskiSite)
                    ->setRemonteeMecanique($remonteeMecaniqueSite);

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
     * @param $idUnifie
     * @param Collection $domaineCarteIdentites
     */
    private function ajouterDomaineCarteIdentiteUnifieSiteDistant($idUnifie, Collection $domaineCarteIdentites)
    {
        /** @var Site $site */
        /** @var ArrayCollection $domaineCarteIdentites */
        $em = $this->getDoctrine()->getManager();
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

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/show.html.twig', array(
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
            ->setAction($this->generateUrl('domaine_domaineCarteIdentite_delete', array('id' => $domaineCarteIdentiteUnifie->getId())))
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

        $this->ajouterDomaineCarteIdentitesDansForm($domaineCarteIdentiteUnifie)
//        $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
            ->domaineCarteIdentitesSortByAffichage($domaineCarteIdentiteUnifie);
        $this->ajouterSnowparksDansForm($domaineCarteIdentiteUnifie)
            ->ajouterHandiskiDansForm($domaineCarteIdentiteUnifie);

        $deleteForm = $this->createDeleteForm($domaineCarteIdentiteUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\DomaineBundle\Form\DomaineCarteIdentiteUnifieType',
            $domaineCarteIdentiteUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $this->dispacherDonneesCommune($domaineCarteIdentiteUnifie);
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

            // add flash messages
            $this->addFlash(
                'success',
                'La carte d\'identité du domaine a bien été modifié.'
            );

            return $this->redirectToRoute('domaine_domaineCarteIdentite_edit', array('id' => $domaineCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteDomaine/domainecarteidentiteunifie/edit.html.twig', array(
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
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        foreach ($entity->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            if ($domaineCarteIdentite->getSite()->getCrm() == 1) {
//                $domaineCarteIdentite->setDomaineCarteIdentiteUnifie(null);
                $entity->removeDomaineCarteIdentite($domaineCarteIdentite);
                return $domaineCarteIdentite;
            }
        }
        return false;
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
        /** @var SnowparkTraduction $snowparkTraduction */
        /** @var SnowparkTraduction $snowparkTraductionCrm */
        /** @var HandiskiTraduction $handiskiTraduction */
        /** @var HandiskiTraduction $handiskiTraductionCrm */
        /** @var DomaineCarteIdentite $domaineCarteIdentite */
        /** @var DomaineCarteIdentiteTraduction $domaineCarteIdentiteTraduc */
        /** @var DomaineCarteIdentiteTraduction $domaineCarteIdentiteTraducCrm */
        /** @var Langue $langue */
        $em = $this->getDoctrine()->getManager();
        $tabClassementSiteReferent = [];

//        récupère les classementReferent pour chaque site dans un tableau
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {
            $tabClassementSiteReferent[] = $domaineCarteIdentite->getSite()->getClassementReferent();
        }

        // Récupèrer le site référent dans la base
        $siteReferent = $em->getRepository(Site::class)->findOneBy(array('classementReferent' => min($tabClassementSiteReferent)));

        $langues = $em->getRepository(Langue::class)->findAll();

//         Parcourir toutes les domaineCarteIdentites
        foreach ($domaineCarteIdentiteUnifie->getDomaineCarteIdentites() as $domaineCarteIdentite) {

            // Si la site de la domaineCarteIdentite est égale au site de référence
            if ($domaineCarteIdentite->getSite() == $siteReferent) {

                foreach ($domaineCarteIdentite->getSnowpark()->getTraductions() as $snowparkTraduction) {
                    $langue = $snowparkTraduction->getLangue();
                    $snowparkTraductionCrm = $domaineCarteIdentiteCrm->getSnowpark()->getTraductions()->filter(function (SnowparkTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
                    $snowparkTraductionCrm->setDescription($snowparkTraduction->getDescription());
                }
                foreach ($domaineCarteIdentite->getHandiski()->getTraductions() as $handiskiTraduction) {
                    $langue = $handiskiTraduction->getLangue();
                    $handiskiTraductionCrm = $domaineCarteIdentiteCrm->getHandiski()->getTraductions()->filter(function (HandiskiTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();
                    $handiskiTraductionCrm->setDescription($handiskiTraduction->getDescription());
                }
//           ajouter les champs "communs"
                $domaineCarteIdentiteCrm
                    ->setAltitudeMini($domaineCarteIdentite->getAltitudeMini())
                    ->setAltitudeMaxi($domaineCarteIdentite->getAltitudeMaxi())
                    ->setKmPistesSkiAlpin($domaineCarteIdentite->getKmPistesSkiAlpin())
                    ->setKmPistesSkiNordique($domaineCarteIdentite->getKmPistesSkiNordique())
                    ->getRemonteeMecanique()->setNombre($domaineCarteIdentite->getRemonteeMecanique()->getNombre());

                foreach ($langues as $langue) {
//                    recupere la traduction pour l'entite du site referent
                    $domaineCarteIdentiteTraduc = $domaineCarteIdentite->getTraductions()->filter(function (DomaineCarteIdentiteTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    récupère la traductin dans le crm
                    $domaineCarteIdentiteTraducCrm = $domaineCarteIdentiteCrm->getTraductions()->filter(function (DomaineCarteIdentiteTraduction $element) use ($langue
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

                        // copie les attributs de traduction du site référent dans les traductions du crm
                        $domaineCarteIdentiteTraducCrm->setAccroche($domaineCarteIdentiteTraduc->getAccroche());
                        $domaineCarteIdentiteTraducCrm->setDescription($domaineCarteIdentiteTraduc->getDescription());
                        $domaineCarteIdentiteCrm->addTraduction($domaineCarteIdentiteTraducCrm);
                    } else {
                        // copie les attributs de traduction du site référent dans les traductions du crm
                        $domaineCarteIdentiteTraducCrm->setAccroche($domaineCarteIdentiteTraduc->getAccroche());
                        $domaineCarteIdentiteTraducCrm->setDescription($domaineCarteIdentiteTraduc->getDescription());
                    }

                }
            } else {

//                permet de vérifier si la langue existe pour les sites non referents si elle n'existe pas on la rajoute
                foreach ($langues as $langue) {

//                    recupere la traduction pour la langue $langue
                    $domaineCarteIdentiteTraduc = $domaineCarteIdentite->getTraductions()->filter(
                        function (DomaineCarteIdentiteTraduction $element) use ($langue) {
                        return $element->getLangue() == $langue;
                    })->first();

//                    null est interdit, si la traduction n'existe pas on passe les attributs à vide
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
        /** @var Site $siteDistant */
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

            // add flash messages
            $this->addFlash('success', 'Le carte d\'identité du domaine a été supprimé avec succès.');
        }

        return $this->redirectToRoute('domaine_domaineCarteIdentite_index');
    }

}
