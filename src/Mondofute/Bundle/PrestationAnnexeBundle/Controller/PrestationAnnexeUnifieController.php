<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexeTraduction;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexeUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * PrestationAnnexeUnifie controller.
 *
 */
class PrestationAnnexeUnifieController extends Controller
{
    /**
     * Lists all PrestationAnnexeUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofutePrestationAnnexeBundle:PrestationAnnexeUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'prestationAnnexe_prestationAnnexe_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofutePrestationAnnexeBundle:PrestationAnnexeUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofutePrestationAnnexe/prestationannexeunifie/index.html.twig', array(
            'prestationAnnexeUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new PrestationAnnexeUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $prestationAnnexeUnifie = new PrestationAnnexeUnifie();

        $this->ajouterPrestationAnnexesDansForm($prestationAnnexeUnifie);
        $this->prestationAnnexesSortByAffichage($prestationAnnexeUnifie);

        $form = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeUnifieType', $prestationAnnexeUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            // affilier les entités liés
//            $this->affilierEntities($prestationAnnexeUnifie);

            $this->supprimerPrestationAnnexes($prestationAnnexeUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($prestationAnnexeUnifie);
            $em->flush();

            $this->copieVersSites($prestationAnnexeUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La prestationAnnexe a bien été créé.'
            );

            return $this->redirectToRoute('prestationannexe_edit', array('id' => $prestationAnnexeUnifie->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/prestationannexeunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $prestationAnnexeUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les prestationAnnexes qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param PrestationAnnexeUnifie $entity
     */
    private function ajouterPrestationAnnexesDansForm(PrestationAnnexeUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getPrestationAnnexes() as $prestationAnnexe) {
                if ($prestationAnnexe->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($prestationAnnexe->getTraductions()->filter(function (PrestationAnnexeTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new PrestationAnnexeTraduction();
                            $traduction->setLangue($langue);
                            $prestationAnnexe->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $prestationAnnexe = new PrestationAnnexe();
                $prestationAnnexe->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new PrestationAnnexeTraduction();
                    $traduction->setLangue($langue);
                    $prestationAnnexe->addTraduction($traduction);
                }
                $entity->addPrestationAnnexe($prestationAnnexe);
            }
        }
    }


    /**
     * Classe les prestationAnnexes par classementAffichage
     * @param PrestationAnnexeUnifie $entity
     */
    private function prestationAnnexesSortByAffichage(PrestationAnnexeUnifie $entity)
    {

        // Trier les prestationAnnexes en fonction de leurs ordre d'affichage
        $prestationAnnexes = $entity->getPrestationAnnexes(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $prestationAnnexes->getIterator();
        unset($prestationAnnexes);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (PrestationAnnexe $a, PrestationAnnexe $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $prestationAnnexes = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($prestationAnnexes);

        // remplacé les prestationAnnexes par ce nouveau tableau (une fonction 'set' a été créé dans PrestationAnnexe unifié)
        $entity->setPrestationAnnexes($prestationAnnexes);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $prestationAnnexes
     */
    private function traductionsSortByLangue($prestationAnnexes)
    {
        /** @var ArrayIterator $iterator */
        /** @var PrestationAnnexe $prestationAnnexe */
        foreach ($prestationAnnexes as $prestationAnnexe) {
            $traductions = $prestationAnnexe->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (PrestationAnnexeTraduction $a, PrestationAnnexeTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $prestationAnnexe->setTraductions($traductions);
        }
    }

    /**
     * retirer de l'entité les prestationAnnexes qui ne doivent pas être enregistrer
     * @param PrestationAnnexeUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerPrestationAnnexes(PrestationAnnexeUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getPrestationAnnexes() as $prestationAnnexe) {
            if (!in_array($prestationAnnexe->getSite()->getId(), $sitesAEnregistrer)) {
                $prestationAnnexe->setPrestationAnnexeUnifie(null);
                $entity->removePrestationAnnexe($prestationAnnexe);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité prestationAnnexe
     * @param PrestationAnnexeUnifie $entity
     */
    private function copieVersSites(PrestationAnnexeUnifie $entity)
    {
        /** @var PrestationAnnexeTraduction $prestationAnnexeTraduc */
//        Boucle sur les prestationAnnexes afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getPrestationAnnexes() as $prestationAnnexe) {
            if ($prestationAnnexe->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $em = $this->getDoctrine()->getManager($prestationAnnexe->getSite()->getLibelle());
                $site = $em->getRepository(Site::class)->findOneBy(array('id' => $prestationAnnexe->getSite()->getId()));
                if (!empty($prestationAnnexe->getZoneTouristique())) {
                    $zoneTouristique = $em->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $prestationAnnexe->getZoneTouristique()->getZoneTouristiqueUnifie()));
                } else {
                    $zoneTouristique = null;
                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null(($entitySite = $em->getRepository(PrestationAnnexeUnifie::class)->find($entity->getId())))) {
                    $entitySite = new PrestationAnnexeUnifie();
                }
//                if (is_null(($entitySite = $em->getRepository('MondofutePrestationAnnexeBundle:PrestationAnnexeUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new PrestationAnnexeUnifie();
//                }


//            Récupération de la prestationAnnexe sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($prestationAnnexeSite = $em->getRepository(PrestationAnnexe::class)->findOneBy(array('prestationAnnexeUnifie' => $entitySite))))) {
                    $prestationAnnexeSite = new PrestationAnnexe();
                }

//            copie des données prestationAnnexe
                $prestationAnnexeSite
                    ->setSite($site)
                    ->setPrestationAnnexeUnifie($entitySite)
                    ->setZoneTouristique($zoneTouristique);

//            Gestion des traductions
                foreach ($prestationAnnexe->getTraductions() as $prestationAnnexeTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $em->getRepository(Langue::class)->findOneBy(array('id' => $prestationAnnexeTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($prestationAnnexeTraducSite = $em->getRepository(PrestationAnnexeTraduction::class)->findOneBy(array(
                        'prestationAnnexe' => $prestationAnnexeSite,
                        'langue' => $langue
                    ))))
                    ) {
                        $prestationAnnexeTraducSite = new PrestationAnnexeTraduction();
                    }

//                copie des données traductions
                    $prestationAnnexeTraducSite->setLangue($langue)
                        ->setLibelle($prestationAnnexeTraduc->getLibelle())
                        ->setPrestationAnnexe($prestationAnnexeSite);

//                ajout a la collection de traduction de la prestationAnnexe distante
                    $prestationAnnexeSite->addTraduction($prestationAnnexeTraducSite);
                }

                $entitySite->addPrestationAnnexe($prestationAnnexeSite);
                $em->persist($entitySite);
                $em->flush();
            }
        }
        $this->ajouterPrestationAnnexeUnifieSiteDistant($entity->getId(), $entity->getPrestationAnnexes());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de prestationAnnexe a enregistrer
     * @param $idUnifie
     * @param $prestationAnnexes
     */
    private function ajouterPrestationAnnexeUnifieSiteDistant($idUnifie, $prestationAnnexes)
    {
        /** @var ArrayCollection $prestationAnnexes */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($prestationAnnexes->matching($criteres)) == 0 && (empty($emSite->getRepository(PrestationAnnexeUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new PrestationAnnexeUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a PrestationAnnexeUnifie entity.
     *
     */
    public function showAction(PrestationAnnexeUnifie $prestationAnnexeUnifie)
    {
        $deleteForm = $this->createDeleteForm($prestationAnnexeUnifie);

        return $this->render('@MondofutePrestationAnnexe/prestationAnnexeunifie/show.html.twig', array(
            'prestationAnnexeUnifie' => $prestationAnnexeUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a PrestationAnnexeUnifie entity.
     *
     * @param PrestationAnnexeUnifie $prestationAnnexeUnifie The PrestationAnnexeUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PrestationAnnexeUnifie $prestationAnnexeUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('prestationannexe_delete', array('id' => $prestationAnnexeUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing PrestationAnnexeUnifie entity.
     *
     */
    public function editAction(Request $request, PrestationAnnexeUnifie $prestationAnnexeUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($prestationAnnexeUnifie->getPrestationAnnexes() as $prestationAnnexe) {
                array_push($sitesAEnregistrer, $prestationAnnexe->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalPrestationAnnexes = new ArrayCollection();
//          Créer un ArrayCollection des objets de prestationAnnexes courants dans la base de données
        foreach ($prestationAnnexeUnifie->getPrestationAnnexes() as $prestationAnnexe) {
            $originalPrestationAnnexes->add($prestationAnnexe);
        }

        $this->ajouterPrestationAnnexesDansForm($prestationAnnexeUnifie);
//        $this->affilierEntities($prestationAnnexeUnifie);

        $this->prestationAnnexesSortByAffichage($prestationAnnexeUnifie);
        $deleteForm = $this->createDeleteForm($prestationAnnexeUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeUnifieType',
            $prestationAnnexeUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

//        dump($editForm);die;

        $editForm->handleRequest($request);
//        dump($prestationAnnexeUnifie);die;

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerPrestationAnnexes($prestationAnnexeUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la prestationAnnexe et prestationAnnexeUnifie
            foreach ($originalPrestationAnnexes as $prestationAnnexe) {
                if (!$prestationAnnexeUnifie->getPrestationAnnexes()->contains($prestationAnnexe)) {

                    //  suppression de la prestationAnnexe sur le site
                    $emSite = $this->getDoctrine()->getManager($prestationAnnexe->getSite()->getLibelle());
                    $entitySite = $emSite->find(PrestationAnnexeUnifie::class, $prestationAnnexeUnifie->getId());
                    $prestationAnnexeSite = $entitySite->getPrestationAnnexes()->first();
                    $emSite->remove($prestationAnnexeSite);
                    $emSite->flush();
//                    dump($prestationAnnexe);
                    $prestationAnnexe->setPrestationAnnexeUnifie(null);
                    $em->remove($prestationAnnexe);
                }
            }
            $em->persist($prestationAnnexeUnifie);
            $em->flush();


            $this->copieVersSites($prestationAnnexeUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La prestationAnnexe a bien été modifié.'
            );

            return $this->redirectToRoute('prestationannexe_edit', array('id' => $prestationAnnexeUnifie->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/prestationannexeunifie/edit.html.twig', array(
            'entity' => $prestationAnnexeUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PrestationAnnexeUnifie entity.
     *
     */
    public function deleteAction(Request $request, PrestationAnnexeUnifie $prestationAnnexeUnifie)
    {
        $form = $this->createDeleteForm($prestationAnnexeUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var PrestationAnnexe $prestationAnnexe */
            $erreurHebergement = false;
            foreach ($prestationAnnexeUnifie->getPrestationAnnexes() as $prestationAnnexe) {
                if (!$prestationAnnexe->getHebergements()->isEmpty() && !$erreurHebergement) {
                    $erreurHebergement = true;
                    $this->addFlash('error', 'La prestationAnnexe est lié à un hébergement.');
                }
            }
            if (!$erreurHebergement) {
                $em = $this->getDoctrine()->getManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $prestationAnnexeUnifieSite = $emSite->find(PrestationAnnexeUnifie::class, $prestationAnnexeUnifie->getId());
                    if (!empty($prestationAnnexeUnifieSite)) {
                        $emSite->remove($prestationAnnexeUnifieSite);
                        $emSite->flush();
                    }
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($prestationAnnexeUnifie);
                $em->flush();

                $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
            }
        }

        return $this->redirectToRoute('prestationannexe_index');
    }

}
