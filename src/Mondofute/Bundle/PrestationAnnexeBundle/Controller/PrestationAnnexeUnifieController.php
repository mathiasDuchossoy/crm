<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexeTraduction;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexeUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe;
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
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(PrestationAnnexeUnifie::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofutePrestationAnnexeBundle:PrestationAnnexeUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'prestationannexe_index',
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
            /** @var PrestationAnnexe $prestationAnnexe */
            foreach ($prestationAnnexeUnifie->getPrestationAnnexes() as $prestationAnnexe){
                if(false === in_array($prestationAnnexe->getSite()->getId(),$sitesAEnregistrer)){
                    $prestationAnnexe->setActif(false);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($prestationAnnexeUnifie);
            $em->flush();

            $this->copieVersSites($prestationAnnexeUnifie);

            $this->addFlash('success','La prestation annexe a bien été créé.');

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
     * Copie dans la base de données site l'entité prestationAnnexe
     * @param PrestationAnnexeUnifie $entity
     */
    private function copieVersSites(PrestationAnnexeUnifie $entity)
    {
        /** @var PrestationAnnexe $prestationAnnexe */
        /** @var PrestationAnnexeTraduction $prestationAnnexeTraduc */
//        Boucle sur les prestationAnnexes afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entity->getPrestationAnnexes() as $prestationAnnexe) {
            if ($prestationAnnexe->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($prestationAnnexe->getSite()->getLibelle());
                $site = $emSite->find(Site::class , $prestationAnnexe->getSite());

                // *** gestion du type ***
                if (!empty($prestationAnnexe->getType())) {
                    $type = $prestationAnnexe->getType();
                } else {
                    $type = null;
                }
                // *** fin gestion du type ***

                // *** gestion famille prestation annexe ***
                if (!empty($prestationAnnexe->getFamillePrestationAnnexe())){
                    $famille    = $emSite->find(FamillePrestationAnnexe::class,$prestationAnnexe->getFamillePrestationAnnexe());
                }else{
                    $famille    = null;
                }
                // *** fin gestion famille prestation annexe ***

                // *** gestion sous-famille prestation annexe ***
                $sousFamilleCollection    = new ArrayCollection();
                if (!empty($prestationAnnexe->getSousFamillePrestationAnnexes())){
                    foreach ($prestationAnnexe->getSousFamillePrestationAnnexes() as $sousFamillePrestationAnnexe){
                        $sousFamilleCollection->add($emSite->find(SousFamillePrestationAnnexe::class,$sousFamillePrestationAnnexe));
                    }
                }
                // *** fin gestion sous-famille prestation annexe ***

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (empty(($entitySite = $emSite->find(PrestationAnnexeUnifie::class , $entity)))) {
                    $entitySite = new PrestationAnnexeUnifie();
                }

                //  Récupération de la prestationAnnexe sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($prestationAnnexeSite = $emSite->getRepository(PrestationAnnexe::class)->findOneBy(array('prestationAnnexeUnifie' => $entitySite))))) {
                    $prestationAnnexeSite = new PrestationAnnexe();
                    $entitySite->addPrestationAnnexe($prestationAnnexeSite);
                }

                //  copie des données prestationAnnexe
                $prestationAnnexeSite
                    ->setSite($site)
                    ->setPrestationAnnexeUnifie($entitySite)
                    ->setType($type)
                    ->setFamillePrestationAnnexe($famille)
                    ->setSousFamillePrestationAnnexes($sousFamilleCollection)
                    ->setActif($prestationAnnexe->getActif())
                ;

                //  Gestion des traductions
                foreach ($prestationAnnexe->getTraductions() as $prestationAnnexeTraduc) {

                    //  récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($prestationAnnexeTraducSite = $emSite->getRepository(PrestationAnnexeTraduction::class)->findOneBy(array(
                        'prestationAnnexe' => $prestationAnnexeSite,
                        'langue' => $prestationAnnexeTraduc->getLangue()
                    ))))
                    ) {
                        //  récupération de la langue sur le site distant
                        $langue = $emSite->find(Langue::class , $prestationAnnexeTraduc->getLangue());
                        $prestationAnnexeTraducSite = new PrestationAnnexeTraduction();
                        $prestationAnnexeSite->addTraduction($prestationAnnexeTraducSite);
                        $prestationAnnexeTraducSite
                            ->setLangue($langue)
                        ;
                    }

                    //  copie des données traductions
                    $prestationAnnexeTraducSite
                        ->setLibelle($prestationAnnexeTraduc->getLibelle())
                    ;
                }

                $emSite->persist($entitySite);
                $emSite->flush();
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
            ->add('Supprimer', SubmitType::class)
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
            /** @var PrestationAnnexe $prestationAnnexe */
            foreach ($prestationAnnexeUnifie->getPrestationAnnexes() as $prestationAnnexe) {
                if ($prestationAnnexe->getActif()){
                    array_push($sitesAEnregistrer, $prestationAnnexe->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $this->ajouterPrestationAnnexesDansForm($prestationAnnexeUnifie);

        $this->prestationAnnexesSortByAffichage($prestationAnnexeUnifie);
        $deleteForm = $this->createDeleteForm($prestationAnnexeUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeUnifieType',
            $prestationAnnexeUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($prestationAnnexeUnifie->getPrestationAnnexes() as $prestationAnnexe){
                if(false === in_array($prestationAnnexe->getSite()->getId(),$sitesAEnregistrer)){
                    $prestationAnnexe->setActif(false);
                }else{
                    $prestationAnnexe->setActif(true);
                }
            }

            $em->persist($prestationAnnexeUnifie);
            $em->flush();

            $this->copieVersSites($prestationAnnexeUnifie);

            // add flash messages
            /** @var Session $session */
            $this->addFlash('success','La prestationAnnexe a bien été modifié.');

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
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $prestationAnnexeUnifieSite = $emSite->find(PrestationAnnexeUnifie::class, $prestationAnnexeUnifie);
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

        return $this->redirectToRoute('prestationannexe_index');
    }

}
