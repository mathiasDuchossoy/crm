<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexeTraduction;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * PrestationAnnexe controller.
 *
 */
class PrestationAnnexeController extends Controller
{
    /**
     * Lists all PrestationAnnexe entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(PrestationAnnexe::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofutePrestationAnnexeBundle:PrestationAnnexe')
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

        $entities = $this->getDoctrine()->getRepository('MondofutePrestationAnnexeBundle:PrestationAnnexe')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofutePrestationAnnexe/prestationannexe/index.html.twig', array(
            'prestationAnnexes' => $entities,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new PrestationAnnexe entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $prestationAnnexe = new PrestationAnnexe();

        foreach ($langues as $langue) {
            $prestationAnnexeTraduction = $prestationAnnexe->getTraductions()->filter(function (
                PrestationAnnexeTraduction $element
            ) use ($langue) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $prestationAnnexeTraduction) {
                $prestationAnnexeTraduction = new PrestationAnnexeTraduction();
                $prestationAnnexeTraduction->setLangue($langue);
                $prestationAnnexe->addTraduction($prestationAnnexeTraduction);
            }
        }

        $form = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeType',
            $prestationAnnexe, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($prestationAnnexe);
            $em->flush();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            $this->copieVersSites($prestationAnnexe, $sites);

            $this->addFlash('success', 'La prestation annexe a bien été créé.');

            return $this->redirectToRoute('prestationannexe_edit', array('id' => $prestationAnnexe->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/prestationannexe/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $prestationAnnexe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Copie dans la base de données site l'entité prestationAnnexe
     * @param PrestationAnnexe $entityUnifie
     */
    private function copieVersSites(PrestationAnnexe $entity, $sites)
    {
        /** @var EntityManager $emSite */
        /** @var PrestationAnnexeTraduction $entityTraduc */
        /** @var Site $site */
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            //  Récupération de la prestationAnnexe sur le site distant si elle existe sinon créer une nouvelle entité
            if (empty($entitySite = $emSite->find(PrestationAnnexe::class, $entity))) {
                $entitySite = new PrestationAnnexe();
                $entitySite->setId($entity->getId());
                $metadata = $emSite->getClassMetadata(get_class($entitySite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }

            // *** gestion famille prestation annexe ***
            if (!empty($entity->getFamillePrestationAnnexe())) {
                $famille = $emSite->find(FamillePrestationAnnexe::class, $entity->getFamillePrestationAnnexe());
            } else {
                $famille = null;
            }
            // *** fin gestion famille prestation annexe ***

            // *** gestion sous-famille prestation annexe ***
            if (!empty($entity->getSousFamillePrestationAnnexe())) {
                $sousFamille = $emSite->find(SousFamillePrestationAnnexe::class,
                    $entity->getSousFamillePrestationAnnexe());
            } else {
                $sousFamille = null;
            }
            // *** fin gestion sous-famille prestation annexe ***

            //  copie des données prestationAnnexe
            $entitySite
                ->setFamillePrestationAnnexe($famille)
                ->setSousFamillePrestationAnnexe($sousFamille);

            //  Gestion des traductions
            foreach ($entity->getTraductions() as $entityTraduc) {

                //  récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                if (empty(($entityTraducSite = $emSite->getRepository(PrestationAnnexeTraduction::class)->findOneBy(array(
                    'prestationAnnexe' => $entitySite,
                    'langue' => $entityTraduc->getLangue()
                ))))
                ) {
                    //  récupération de la langue sur le site distant
                    $langue = $emSite->find(Langue::class, $entityTraduc->getLangue());
                    $entityTraducSite = new PrestationAnnexeTraduction();
                    $entitySite->addTraduction($entityTraducSite);
                    $entityTraducSite
                        ->setLangue($langue);
                }

                //  copie des données traductions
                $entityTraducSite
                    ->setLibelle($entityTraduc->getLibelle());
            }

            $emSite->persist($entitySite);
            $emSite->flush();

        }
    }

    public function stocksHebergementAction(Request $request,$idFournisseurHebergement)
    {
        $em = $this->getDoctrine();
        /** @var FournisseurHebergement $fournisseurHebergement */
        $fournisseurHebergement = $em->getRepository(FournisseurHebergement::class)->find($idFournisseurHebergement);
        /** @var Hebergement $hebergement */
        $hebergement = $fournisseurHebergement->getHebergement()->getHebergements()->filter(
            function($hebergement){
                /** @var Hebergement $hebergement */
                return $hebergement->getSite()->getCrm() == true;
        })->first();
//        dump($hebergement->getPrestationAnnexes()->first()->getFournisseurPrestationAnnexe()->getPrestationAnnexe()->getId());die;
        return $this->render('@MondofutePrestationAnnexe/prestationannexe/popup-prestation-annexe-stocks-hebergement.html.twig',array(
            'maxInputVars' => ini_get('max_input_vars'),
            'fournisseurHebergement' => $fournisseurHebergement
        ));
    }

    /**
     * Finds and displays a PrestationAnnexe entity.
     *
     */
    public function showAction(PrestationAnnexe $prestationAnnexe)
    {
        $deleteForm = $this->createDeleteForm($prestationAnnexe);

        return $this->render('@MondofutePrestationAnnexe/prestationannexe/show.html.twig', array(
            'prestationAnnexe' => $prestationAnnexe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a PrestationAnnexe entity.
     *
     * @param PrestationAnnexe $prestationAnnexe The PrestationAnnexe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PrestationAnnexe $prestationAnnexe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('prestationannexe_delete', array('id' => $prestationAnnexe->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing PrestationAnnexe entity.
     *
     */
    public function editAction(Request $request, PrestationAnnexe $prestationAnnexe)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $deleteForm = $this->createDeleteForm($prestationAnnexe);

        $editForm = $this->createForm('Mondofute\Bundle\PrestationAnnexeBundle\Form\PrestationAnnexeType',
            $prestationAnnexe, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'Mettre à jour',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em->persist($prestationAnnexe);
            $em->flush();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            $this->copieVersSites($prestationAnnexe, $sites);

            // add flash messages
            /** @var Session $session */
            $this->addFlash('success', 'La prestationAnnexe a bien été modifié.');

            return $this->redirectToRoute('prestationannexe_edit', array('id' => $prestationAnnexe->getId()));
        }

        return $this->render('@MondofutePrestationAnnexe/prestationannexe/edit.html.twig', array(
            'entity' => $prestationAnnexe,
            'langues' => $langues,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PrestationAnnexe entity.
     *
     */
    public function deleteAction(Request $request, PrestationAnnexe $prestationAnnexe)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($prestationAnnexe);
        $form->handleRequest($request);

        $fournisseurPrestationAnnexe = $em->getRepository(FournisseurPrestationAnnexe::class)->findOneBy(array('prestationAnnexe' => $prestationAnnexe));
        $errorFournisseurPrestationAnnexe = false;
        if (!empty($fournisseurPrestationAnnexe)) {
            $errorFournisseurPrestationAnnexe = true;
            $this->addFlash('error', 'La prestation annexe est utilisé par un fournisseur.');
        }
        if ($form->isSubmitted() && $form->isValid() && !$errorFournisseurPrestationAnnexe) {

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $prestationAnnexeSite = $emSite->find(PrestationAnnexe::class, $prestationAnnexe);
                if (!empty($prestationAnnexeSite)) {
                    $emSite->remove($prestationAnnexeSite);
                    $emSite->flush();
                }
            }

            $em->remove($prestationAnnexe);
            $em->flush();

            $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('prestationannexe_index');
    }

}
