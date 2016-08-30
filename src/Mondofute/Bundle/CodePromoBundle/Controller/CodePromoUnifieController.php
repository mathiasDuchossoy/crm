<?php

namespace Mondofute\Bundle\CodePromoBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * CodePromoUnifie controller.
 *
 */
class CodePromoUnifieController extends Controller
{
    /**
     * Lists all CodePromoUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(CodePromoUnifie::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofuteCodePromoBundle:CodePromoUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'codePromo_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteCodePromoBundle:CodePromoUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteCodePromo/codepromounifie/index.html.twig', array(
            'codePromoUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new CodePromoUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));

        $sitesAEnregistrer = $request->get('sites');

        $codePromoUnifie = new CodePromoUnifie();

        $this->ajouterCodePromosDansForm($codePromoUnifie);
        $this->codePromosSortByAffichage($codePromoUnifie);

        $form = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType', $codePromoUnifie);
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')))
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CodePromo $codePromo */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo){
                if(false === in_array($codePromo->getSite()->getId(),$sitesAEnregistrer)){
                    $codePromo->setActif(false);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($codePromoUnifie);
            $em->flush();

            $this->copieVersSites($codePromoUnifie);

            $this->addFlash('success','Le code promo a bien été créé.');

            return $this->redirectToRoute('codepromo_edit', array('id' => $codePromoUnifie->getId()));
        }

        return $this->render('@MondofuteCodePromo/codepromounifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $codePromoUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les codePromos qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param CodePromoUnifie $entityUnifie
     */
    private function ajouterCodePromosDansForm(CodePromoUnifie $entityUnifie)
    {
        /** @var CodePromo $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $entity = $entityUnifie->getCodePromos()->filter(function (CodePromo $element)use($site){
                return $element->getSite() == $site;
            })->first();
            if (false === $entity){
                $entity = new CodePromo();
                $entityUnifie->addCodePromo($entity);
                $entity->setSite($site);
            }
        }
    }

    /**
     * Classe les codePromos par classementAffichage
     * @param CodePromoUnifie $entity
     */
    private function codePromosSortByAffichage(CodePromoUnifie $entity)
    {
        // Trier les codePromos en fonction de leurs ordre d'affichage
        $codePromos = $entity->getCodePromos(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $codePromos->getIterator();
        unset($codePromos);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (CodePromo $a, CodePromo $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $codePromos = new ArrayCollection(iterator_to_array($iterator));

        // remplacé les codePromos par ce nouveau tableau (une fonction 'set' a été créé dans CodePromo unifié)
        $entity->setCodePromos($codePromos);
    }

    /**
     * Copie dans la base de données site l'entité codePromo
     * @param CodePromoUnifie $entityUnifie
     */
    private function copieVersSites(CodePromoUnifie $entityUnifie)
    {
        /** @var EntityManager $emSite */
        /** @var CodePromo $entity */
//        Boucle sur les codePromos afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entityUnifie->getCodePromos() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());
                $site = $emSite->find(Site::class , $entity->getSite());

                // *** gestion du type ***
                if (!empty($entity->getType())) {
                    $type = $entity->getType();
                } else {
                    $type = null;
                }
                // *** fin gestion du type ***

                // *** gestion famille prestation annexe ***
                if (!empty($entity->getFamilleCodePromo())){
                    $famille    = $emSite->find(FamilleCodePromo::class,$entity->getFamilleCodePromo());
                }else{
                    $famille    = null;
                }
                // *** fin gestion famille prestation annexe ***

                // *** gestion sous-famille prestation annexe ***
                if (!empty($entity->getSousFamilleCodePromo())){
                    $sousFamille    = $emSite->find(SousFamilleCodePromo::class,$entity->getSousFamilleCodePromo());
                }else{
                    $sousFamille    = null;
                }
                // *** fin gestion sous-famille prestation annexe ***

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (empty($entityUnifieSite = $emSite->find(CodePromoUnifie::class , $entityUnifie))) {
                    $entityUnifieSite = new CodePromoUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                }

                //  Récupération de la codePromo sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty($entitySite = $emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $entityUnifieSite)))) {
                    $entitySite = new CodePromo();
                    $entitySite
                        ->setSite($site)
                        ->setCodePromoUnifie($entityUnifieSite)
                    ;

                    $entityUnifieSite->addCodePromo($entitySite);
                }

                //  copie des données codePromo
                $entitySite
                    ->setType($type)
                    ->setFamilleCodePromo($famille)
                    ->setSousFamilleCodePromo($sousFamille)
                    ->setActif($entity->getActif())
                ;

                $emSite->persist($entityUnifieSite);

                $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a CodePromoUnifie entity.
     *
     */
    public function showAction(CodePromoUnifie $codePromoUnifie)
    {
        $deleteForm = $this->createDeleteForm($codePromoUnifie);

        return $this->render('@MondofuteCodePromo/codePromounifie/show.html.twig', array(
            'codePromoUnifie' => $codePromoUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a CodePromoUnifie entity.
     *
     * @param CodePromoUnifie $codePromoUnifie The CodePromoUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CodePromoUnifie $codePromoUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('codepromo_delete', array('id' => $codePromoUnifie->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing CodePromoUnifie entity.
     *
     */
    public function editAction(Request $request, CodePromoUnifie $codePromoUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var CodePromo $codePromo */
            foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
                if ($codePromo->getActif()){
                    array_push($sitesAEnregistrer, $codePromo->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $this->ajouterCodePromosDansForm($codePromoUnifie);

        $this->codePromosSortByAffichage($codePromoUnifie);
        $deleteForm = $this->createDeleteForm($codePromoUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\CodePromoBundle\Form\CodePromoUnifieType',
            $codePromoUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($codePromoUnifie->getCodePromos() as $codePromo){
                if(false === in_array($codePromo->getSite()->getId(),$sitesAEnregistrer)){
                    $codePromo->setActif(false);
                }else{
                    $codePromo->setActif(true);
                }
            }

            $em->persist($codePromoUnifie);
            $em->flush();

            $this->copieVersSites($codePromoUnifie);

            // add flash messages
            /** @var Session $session */
            $this->addFlash('success','La codePromo a bien été modifié.');

            return $this->redirectToRoute('codepromo_edit', array('id' => $codePromoUnifie->getId()));
        }

        return $this->render('@MondofuteCodePromo/codePromounifie/edit.html.twig', array(
            'entity' => $codePromoUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CodePromoUnifie entity.
     *
     */
    public function deleteAction(Request $request, CodePromoUnifie $codePromoUnifie)
    {
        $form = $this->createDeleteForm($codePromoUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $codePromoUnifieSite = $emSite->find(CodePromoUnifie::class, $codePromoUnifie);
                if (!empty($codePromoUnifieSite)) {
                    $emSite->remove($codePromoUnifieSite);
                    $emSite->flush();
                }
            }

            $em->remove($codePromoUnifie);
            $em->flush();

            $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('codepromo_index');
    }

}
