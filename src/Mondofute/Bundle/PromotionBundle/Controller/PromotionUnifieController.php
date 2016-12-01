<?php

namespace Mondofute\Bundle\PromotionBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use JMS\JobQueueBundle\Entity\Job;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseur;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionHebergement;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogement;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionTypeAffectation;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionUnifie;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * PromotionUnifie controller.
 *
 */
class PromotionUnifieController extends Controller
{
    const PromotionPeriodeValidite = "HiDev\\Bundle\\PromotionBundle\\Entity\\PromotionPeriodeValidite";

    /**
     * Lists all PromotionUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository(Site::class)->findBy(array('crm'=>0));
//        foreach ($sites as $site){
//            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $unifie = $emSite->find(PromotionUnifie::class, 1);
//            $emSite->remove($unifie);
//            $emSite->flush();
//        }

        $count = $em
            ->getRepository('MondofutePromotionBundle:PromotionUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'promotion_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofutePromotionBundle:PromotionUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofutePromotion/promotionunifie/index.html.twig', array(
            'promotionUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new PromotionUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $affectations = TypeAffectation::$libelles;

        $sitesAEnregistrer = $request->get('sites');

        $promotionUnifie = new PromotionUnifie();

        $this->ajouterPromotionsDansForm($promotionUnifie);
        $this->promotionsSortByAffichage($promotionUnifie);

        $form = $this->createForm('Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType', $promotionUnifie);
        $form->add('submit', SubmitType::class, array(
            'label' => $this->get('translator')->trans('Enregistrer'),
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Promotion $entity */
//            $this->addPromotionPeriode($promotionUnifie, 'Validite', PromotionPeriodeValidite::class);
//            $this->addPromotionPeriode($promotionUnifie, 'Sejour', PromotionPeriodeSejour::class);

            // *** gestion promotion typeAffectation ***
            $this->gestionPromotionTypeAffectation($promotionUnifie);
            // *** fin gestion promotion typeAffectation ***

            /** @var Promotion $promotion */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                if (false === in_array($promotion->getSite()->getId(), $sitesAEnregistrer)) {
                    $promotion->setActif(false);
                }
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($promotionUnifie);

            try {
                $em->flush();

//                $this->copieVersSites($promotionUnifie);

                $this->addFlash('success', 'La promotion a bien été créé.');

                return $this->redirectToRoute('promotion_edit', array('id' => $promotionUnifie->getId()));
            } catch (Exception $e) {
//                switch ($e->getCode()){
//                    case 0:
//                        $this->addFlash('error', "Le code " . $promotionUnifie->getCode() . " existe déjà.");
//                        break;
//                    default:
//                        $this->addFlash('error', "Add not done: " . $e->getMessage());
//                        break;
//                }

                $this->addFlash('error', "Add not done: " . $e->getMessage());
            }

        }

        return $this->render('@MondofutePromotion/promotionunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'entity' => $promotionUnifie,
            'form' => $form->createView(),
            'affectations' => $affectations,
            'fournisseursTypeHebergement' => new ArrayCollection(),
            'fournisseursPrestationAnnexe' => new ArrayCollection(),
        ));
    }

    /**
     * Ajouter les promotions qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param PromotionUnifie $entityUnifie
     */
    private function ajouterPromotionsDansForm(PromotionUnifie $entityUnifie)
    {
        /** @var Promotion $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $entity = $entityUnifie->getPromotions()->filter(function (Promotion $element) use ($site) {
                return $element->getSite() == $site;
            })->first();
            if (false === $entity) {
                $entity = new Promotion();
                $entityUnifie->addPromotion($entity);
                $entity->setSite($site);
            }
        }
    }

    /**
     * Classe les promotions par classementAffichage
     * @param PromotionUnifie $entity
     */
    private function promotionsSortByAffichage(PromotionUnifie $entity)
    {
        // Trier les promotions en fonction de leurs ordre d'affichage
        $promotions = $entity->getPromotions(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $promotions->getIterator();
        unset($promotions);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (Promotion $a, Promotion $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $promotions = new ArrayCollection(iterator_to_array($iterator));

        // remplacé les promotions par ce nouveau tableau (une fonction 'set' a été créé dans Promotion unifié)
        $entity->setPromotions($promotions);
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionTypeAffectation($promotionUnifie)
    {
        /** @var PromotionTypeAffectation $promotionTypeAffectationCrm */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if (false === $promotion->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::logement;
                })->first()
            ) {
//                $promotion->getPromotionHebergements()->clear();
//                $promotion->getPromotionLogements()->clear();
//                $promotionFournisseurs = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element){
//                    return $element->getType() == Type::hebergement;
//                });
//                foreach ($promotionFournisseurs as $promotionFournisseur)
//                {
//                    $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
//                }
            }
            if (false === $promotion->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) {
                    return $element->getTypeAffectation() == TypeAffectation::prestationAnnexe;
                })->first()
            ) {
//                $promotion->getPromotionFamillePrestationAnnexes()->clear();
//                $promotion->getPromotionFournisseurPrestationAnnexes()->clear();
//                $promotionFournisseurs = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element){
//                    return $element->getType() == Type::fournisseurPrestationAnnexe;
//                });
//                foreach ($promotionFournisseurs as $promotionFournisseur)
//                {
//                    $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
//                }
            }
            foreach ($promotion->getPromotionTypeAffectations() as $typeAffectation) {
                $typeAffectation->setPromotion($promotion);
            }
        }
        $promotionTypeAffectationCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionTypeAffectations();
        $typeAffectations = new ArrayCollection();
        foreach ($promotionTypeAffectationCrms as $promotionTypeAffectationCrm) {
            $typeAffectations->add($promotionTypeAffectationCrm->getTypeAffectation());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $typeAffectationSites = new ArrayCollection();
                foreach ($promotion->getPromotionTypeAffectations() as $promotionTypeAffectationSite) {
                    $typeAffectationSites->add($promotionTypeAffectationSite->getTypeAffectation());
                }
                foreach ($typeAffectations as $typeAffectation) {
                    if (false === $typeAffectationSites->contains($typeAffectation)) {
                        $newTypeAffectation = new PromotionTypeAffectation();
                        $promotion->addPromotionTypeAffectation($newTypeAffectation);
                        $newTypeAffectation->setTypeAffectation($typeAffectation);
                    }
                }
            }
        }
    }

    /**
     * Finds and displays a PromotionUnifie entity.
     *
     */
    public function showAction(PromotionUnifie $promotionUnifie)
    {
        $deleteForm = $this->createDeleteForm($promotionUnifie);

        return $this->render('@MondofutePromotion/promotionunifie/show.html.twig', array(
            'promotionUnifie' => $promotionUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a PromotionUnifie entity.
     *
     * @param PromotionUnifie $promotionUnifie The PromotionUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PromotionUnifie $promotionUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('promotion_delete', array('id' => $promotionUnifie->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing PromotionUnifie entity.
     *
     */
    public function editAction(Request $request, PromotionUnifie $promotionUnifie)
    {
        /** @var Promotion $promotion */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));

        // *** gestion promotion typeAffectation ***
        $affectations = TypeAffectation::$libelles;

        $originalPromotionTypeAffectations = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionTypeAffectations->set($promotion->getSite()->getId(), new ArrayCollection());
            foreach ($promotion->getPromotionTypeAffectations() as $typeAffectation) {
                $originalPromotionTypeAffectations->get($promotion->getSite()->getId())->add($typeAffectation);
            }
        }
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        // *** fin gestion promotion typeAffectation ***

        // *** gestion promotion fournisseur ***
        $originalPromotionFournisseurs = new ArrayCollection();

        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionFournisseurs->set($promotion->getSite()->getId(), new ArrayCollection());
            foreach ($promotion->getPromotionFournisseurs() as $promotionFournisseur) {
                $originalPromotionFournisseurs->get($promotion->getSite()->getId())->add($promotionFournisseur);
            }
        }
        // *** fin gestion promotion fournisseur ***

        // *** gestion promotion hebergement ***
        $originalPromotionHebergements = new ArrayCollection();
//        $fournisseurHebergements = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
//            $fournisseurHebergements->set($promotion->getId(), new ArrayCollection());
            $originalPromotionHebergements->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionHebergement $promotionHebergement */
            foreach ($promotion->getPromotionHebergements() as $promotionHebergement) {
                $originalPromotionHebergements->get($promotion->getSite()->getId())->add($promotionHebergement);
//                if (empty($fournisseurHebergements->get($promotion->getId())->get($promotionHebergement->getFournisseur()->getId()))) {
//                    $fournisseurHebergements->get($promotion->getId())->set($promotionHebergement->getFournisseur()->getId(), new ArrayCollection());
//                }
            }
        }
        // *** fin gestion promotion hebergement ***

        // *** gestion promotion fournisseurPrestationAnnexe ***
        $originalPromotionFournisseurPrestationAnnexes = new ArrayCollection();
//        $fournisseurFournisseurPrestationAnnexes = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
//            $fournisseurFournisseurPrestationAnnexes->set($promotion->getId(), new ArrayCollection());
            $originalPromotionFournisseurPrestationAnnexes->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexe */
            foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexe) {
                $originalPromotionFournisseurPrestationAnnexes->get($promotion->getSite()->getId())->add($promotionFournisseurPrestationAnnexe);
//                if (empty($fournisseurFournisseurPrestationAnnexes->get($promotion->getId())->get($promotionFournisseurPrestationAnnexe->getFournisseur()->getId()))) {
//                    $fournisseurFournisseurPrestationAnnexes->get($promotion->getId())->set($promotionFournisseurPrestationAnnexe->getFournisseur()->getId(), new ArrayCollection());
//                }
            }
        }
        // *** fin gestion promotion fournisseurPrestationAnnexe ***


        // *** gestion promotion famillePrestationAnnexe ***
        $originalPromotionFamillePrestationAnnexes = new ArrayCollection();
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            $originalPromotionFamillePrestationAnnexes->set($promotion->getSite()->getId(), new ArrayCollection());
            /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex */
            foreach ($promotion->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnex) {
                $originalPromotionFamillePrestationAnnexes->get($promotion->getSite()->getId())->add($promotionFamillePrestationAnnex);
            }
        }
        // *** fin gestion promotion fournisseurPrestationAnnexe ***

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Promotion $promotion */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                if ($promotion->getActif()) {
                    array_push($sitesAEnregistrer, $promotion->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $this->ajouterPromotionsDansForm($promotionUnifie);

        $this->promotionsSortByAffichage($promotionUnifie);
        $deleteForm = $this->createDeleteForm($promotionUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType',
            $promotionUnifie)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                if (false === in_array($promotion->getSite()->getId(), $sitesAEnregistrer)) {
                    $promotion->setActif(false);
                } else {
                    $promotion->setActif(true);
                }
            }

            // *** gestion promotion typeAffectation ***
            $this->gestionPromotionTypeAffectation($promotionUnifie);

            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionTypeAffectationSites = $originalPromotionTypeAffectations->get($promotion->getSite()->getId());
                foreach ($originalPromotionTypeAffectationSites as $originalPromotionTypeAffectation) {
                    if (false === $promotion->getPromotionTypeAffectations()->contains($originalPromotionTypeAffectation)) {
                        $em->remove($originalPromotionTypeAffectation);
                    }
                }
            }
            // *** fin gestion promotion typeAffectation ***

            // *** gestion promotion fournisseur ***
            $this->gestionPromotionFournisseur($promotionUnifie);

            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionFournisseurSites = $originalPromotionFournisseurs->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionFournisseurs() as $promotionFournisseur) {
                    /** @var ArrayCollection $originalPromotionFournisseurSites */
                    /** @var PromotionFournisseur $promotionFournisseur */
                    $originalPromotionFournisseur = $originalPromotionFournisseurSites->filter(function (PromotionFournisseur $element) use ($promotionFournisseur) {
                        return ($element->getFournisseur() == $promotionFournisseur->getFournisseur()
                            and $element->getType() == $promotionFournisseur->getType()
                            and $element->getPromotion() == $promotionFournisseur->getPromotion());
                    })->first();
                    if (!empty($originalPromotionFournisseur)) {
                        $promotion->getPromotionFournisseurs()->removeElement($promotionFournisseur);
                        $promotion->addPromotionFournisseur($originalPromotionFournisseur);
                    }
                }
                foreach ($originalPromotionFournisseurSites as $originalPromotionFournisseur) {
                    if (false === $promotion->getPromotionFournisseurs()->contains($originalPromotionFournisseur)) {
                        $em->remove($originalPromotionFournisseur);
                    }
                }
            }
            // *** fin gestion promotion fournisseur ***

            // *** gestion promotion hebergement ***
            $this->gestionPromotionHebergement($promotionUnifie);

            /** @var PromotionHebergement $originalPromotionHebergement */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionHebergementSites = $originalPromotionHebergements->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionHebergements() as $promotionHebergement) {
                    /** @var ArrayCollection $originalPromotionHebergementSites */
                    /** @var PromotionHebergement $promotionHebergement */
                    $originalPromotionHebergement = $originalPromotionHebergementSites->filter(function (PromotionHebergement $element) use ($promotionHebergement) {
                        return ($element->getHebergement() == $promotionHebergement->getHebergement()
                            and $element->getFournisseur() == $promotionHebergement->getFournisseur()
                            and $element->getPromotion() == $promotionHebergement->getPromotion());
                    })->first();
                    if (!empty($originalPromotionHebergement)) {
                        $promotion->getPromotionHebergements()->removeElement($promotionHebergement);
                        $promotion->addPromotionHebergement($originalPromotionHebergement);
                    }
                }
                foreach ($originalPromotionHebergementSites as $originalPromotionHebergement) {
                    if (false === $promotion->getPromotionHebergements()->contains($originalPromotionHebergement)) {
//                        $em->detach($originalPromotionHebergement);
//                        $originalPromotionHebergement = $em->find(PromotionHebergement::class, $originalPromotionHebergement);
//                        $hebergementUnifieId = $originalPromotionHebergement->getHebergement()->getHebergementUnifie()->getId();
//                        $em->merge($originalPromotionHebergement);
//                        $logements = new ArrayCollection($em->getRepository(Logement::class)->findByFournisseurHebergement($originalPromotionHebergement->getFournisseur()->getId(), $hebergementUnifieId, $promotion->getSite()->getId()));
//                        /** @var PromotionLogement $promotionLogement */
//                        foreach ($promotion->getPromotionLogements() as $promotionLogement) {
//                            if ($logements->contains($promotionLogement->getLogement())) {
//                                $promotion->getPromotionLogements()->removeElement($promotionLogement);
//                                $em->remove($promotionLogement);
//                            }
//                        }

                        $promotionLogements = $promotion->getPromotionLogements()->filter(function (PromotionLogement $element) use ($originalPromotionHebergement) {
                            return ($element->getLogement()->getFournisseurHebergement()->getHebergement() == $originalPromotionHebergement->getHebergement()->getHebergementUnifie()
//                            and $element->getLogement()->getFournisseurHebergement()->getFournisseur() == $originalPromotionHebergement->getHebergement()->getHebergementUnifie()->get
                            );
                        });
                        foreach ($promotionLogements as $promotionLogement) {
                            $promotion->getPromotionLogements()->removeElement($promotionLogement);
                            $em->remove($promotionLogement);
                        }
                        $promotion->getPromotionHebergements()->removeElement($originalPromotionHebergement);
                        $em->remove($originalPromotionHebergement);
                    }
                }
            }
            // *** fin gestion promotion hebergement ***

            // *** gestion promotion fournisseurPrestationAnnexe ***
            $this->gestionPromotionFournisseurPrestationAnnexe($promotionUnifie);

            /** @var PromotionFournisseurPrestationAnnexe $originalPromotionFournisseurPrestationAnnexe */
            /** @var PromotionFournisseurPrestationAnnexe $originalPromotionFournisseurPrestationAnnexe */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionFournisseurPrestationAnnexeSites = $originalPromotionFournisseurPrestationAnnexes->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnex) {
                    /** @var ArrayCollection $originalPromotionFournisseurPrestationAnnexeSites */
                    /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex */
                    $originalPromotionFournisseurPrestationAnnexe = $originalPromotionFournisseurPrestationAnnexeSites->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($promotionFournisseurPrestationAnnex) {
                        return ($element->getFournisseurPrestationAnnexe() == $promotionFournisseurPrestationAnnex->getFournisseurPrestationAnnexe()
                            and $element->getFournisseur() == $promotionFournisseurPrestationAnnex->getFournisseur()
                            and $element->getPromotion() == $promotionFournisseurPrestationAnnex->getPromotion());
                    })->first();
                    if (!empty($originalPromotionFournisseurPrestationAnnexe)) {
                        $promotion->getPromotionFournisseurPrestationAnnexes()->removeElement($promotionFournisseurPrestationAnnex);
                        $promotion->addPromotionFournisseurPrestationAnnex($originalPromotionFournisseurPrestationAnnexe);
                    }
                }
                foreach ($originalPromotionFournisseurPrestationAnnexeSites as $originalPromotionFournisseurPrestationAnnexe) {
                    if (false === $promotion->getPromotionFournisseurPrestationAnnexes()->contains($originalPromotionFournisseurPrestationAnnexe)) {
                        $promotion->getPromotionFournisseurPrestationAnnexes()->removeElement($originalPromotionFournisseurPrestationAnnexe);
                        $em->remove($originalPromotionFournisseurPrestationAnnexe);
                    }
                }
            }
            // *** fin gestion promotion fournisseurPrestationAnnexe ***

            // *** gestion promotion famillePrestationAnnexe ***
            $this->gestionPromotionFamillePrestationAnnexe($promotionUnifie);

            /** @var PromotionFamillePrestationAnnexe $originalPromotionFamillePrestationAnnexe */
            /** @var PromotionFamillePrestationAnnexe $originalPromotionFamillePrestationAnnexe */
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                $originalPromotionFamillePrestationAnnexeSites = $originalPromotionFamillePrestationAnnexes->get($promotion->getSite()->getId());
                foreach ($promotion->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnex) {
                    /** @var ArrayCollection $originalPromotionFamillePrestationAnnexeSites */
                    /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex */
                    $originalPromotionFamillePrestationAnnexe = $originalPromotionFamillePrestationAnnexeSites->filter(function (PromotionFamillePrestationAnnexe $element) use ($promotionFamillePrestationAnnex) {
                        return ($element->getFamillePrestationAnnexe() == $promotionFamillePrestationAnnex->getFamillePrestationAnnexe()
                            and $element->getFournisseur() == $promotionFamillePrestationAnnex->getFournisseur()
                            and $element->getPromotion() == $promotionFamillePrestationAnnex->getPromotion());
                    })->first();
                    if (!empty($originalPromotionFamillePrestationAnnexe)) {
                        $promotion->getPromotionFamillePrestationAnnexes()->removeElement($promotionFamillePrestationAnnex);
                        $promotion->addPromotionFamillePrestationAnnex($originalPromotionFamillePrestationAnnexe);
                    }
                }
                foreach ($originalPromotionFamillePrestationAnnexeSites as $originalPromotionFamillePrestationAnnexe) {
                    if (false === $promotion->getPromotionFamillePrestationAnnexes()->contains($originalPromotionFamillePrestationAnnexe)) {
                        $promotion->getPromotionFamillePrestationAnnexes()->removeElement($originalPromotionFamillePrestationAnnexe);
                        $em->remove($originalPromotionFamillePrestationAnnexe);
                    }
                }
            }
            // *** fin gestion promotion famillePrestationAnnexe ***

            $em->persist($promotionUnifie);
            $em->flush();

//            $this->copieVersSites($promotionUnifie);

            // *** gestion promotion logement ***
            $this->gestionPromotionLogement($promotionUnifie);
            // *** fin gestion promotion logement ***

            // add flash messages
            /** @var Session $session */
            $this->addFlash('success', 'La promotion a bien été modifié.');

            return $this->redirectToRoute('promotion_edit', array('id' => $promotionUnifie->getId()));
        }

        return $this->render('@MondofutePromotion/promotionunifie/edit.html.twig', array(
            'entity' => $promotionUnifie,
            'sites' => $sites,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
//            'promotionClients' => $originalPromotionClients,
            'affectations' => $affectations,
            'panelPromotion' => true,
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
//            'fournisseurHebergements' => $fournisseurHebergements,
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
//            'fournisseurFournisseurPrestationAnnexes' => $fournisseurFournisseurPrestationAnnexes,
        ));
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionFournisseur($promotionUnifie)
    {
        /** @var PromotionFournisseur $promotionFournisseurSite */
        /** @var PromotionFournisseur $promotionFournisseurCrm */
        /** @var PromotionFournisseur $fournisseur */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
                    $promotion->getPromotionFournisseurs()->removeElement($fournisseur);
                } else {
                    $fournisseur->setPromotion($promotion);
                }
            }
        }
        $promotionFournisseurCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionFournisseurs();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionFournisseurCrms as $promotionFournisseurCrm) {
            $fournisseurs->add($promotionFournisseurCrm);
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                foreach ($promotionFournisseurCrms as $key => $fournisseur) {
                    $fournisseurSite = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($fournisseur) {
                        return ($element->getFournisseur() == $fournisseur->getFournisseur() and $element->getType() == $fournisseur->getType());
                    })->first();
                    if (false === $fournisseurSite) {
                        $newFournisseur = new PromotionFournisseur();
                        $promotion->addPromotionFournisseur($newFournisseur);
                        $newFournisseur
                            ->setFournisseur($fournisseur->getFournisseur())
                            ->setType($fournisseur->getType());
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionHebergement($promotionUnifie)
    {
        /** @var Hebergement $hebergement */
        /** @var PromotionHebergement $promotionHebergementCrm */
        /** @var PromotionHebergement $promotionHebergement */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionHebergements() as $promotionHebergement) {
                if (empty($promotionHebergement->getHebergement())) {
                    $promotion->getPromotionHebergements()->removeElement($promotionHebergement);
                } else {
                    $promotionHebergement->setPromotion($promotion);
                }
            }
        }
        $promotionHebergementCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionHebergements();
        $hebergements = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionHebergementCrms as $promotionHebergementCrm) {
            $hebergements->add($promotionHebergementCrm->getHebergement());
            $fournisseurs->add($promotionHebergementCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $hebergementSites = new ArrayCollection();
                foreach ($promotion->getPromotionHebergements() as $promotionHebergementSite) {
                    $hebergementSites->add($promotionHebergementSite->getHebergement());
                }

                foreach ($hebergements as $key => $hebergement) {
                    $hebergementSite = $hebergement->getHebergementUnifie()->getHebergements()->filter(function (Hebergement $element) use ($promotion) {
                        return $element->getSite() == $promotion->getSite();
                    })->first();
                    if (false === $hebergementSites->contains($hebergementSite)) {
                        $newHebergement = new PromotionHebergement();
                        $promotion->addPromotionHebergement($newHebergement);
                        $newHebergement
                            ->setHebergement($hebergementSite)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionFournisseurPrestationAnnexe($promotionUnifie)
    {
        /** @var PromotionFournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
        /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexeCrm */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $fournisseurPrestationAnnexe) {
                if (empty($fournisseurPrestationAnnexe->getFournisseurPrestationAnnexe())) {
                    $promotion->getPromotionFournisseurPrestationAnnexes()->removeElement($fournisseurPrestationAnnexe);
                } else {
                    $fournisseurPrestationAnnexe->setPromotion($promotion);
                }
            }
        }
        $promotionFournisseurPrestationAnnexeCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionFournisseurPrestationAnnexes();
        $fournisseurPrestationAnnexes = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionFournisseurPrestationAnnexeCrms as $promotionFournisseurPrestationAnnexeCrm) {
            $fournisseurPrestationAnnexes->add($promotionFournisseurPrestationAnnexeCrm->getFournisseurPrestationAnnexe());
            $fournisseurs->add($promotionFournisseurPrestationAnnexeCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $fournisseurPrestationAnnexeSites = new ArrayCollection();
                foreach ($promotion->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexeSite) {
                    $fournisseurPrestationAnnexeSites->add($promotionFournisseurPrestationAnnexeSite->getFournisseurPrestationAnnexe());
                }
                foreach ($fournisseurPrestationAnnexes as $key => $fournisseurPrestationAnnexe) {
                    if (false === $fournisseurPrestationAnnexeSites->contains($fournisseurPrestationAnnexe)) {
                        $newFournisseurPrestationAnnexe = new PromotionFournisseurPrestationAnnexe();
                        $promotion->addPromotionFournisseurPrestationAnnex($newFournisseurPrestationAnnexe);
                        $newFournisseurPrestationAnnexe
                            ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionFamillePrestationAnnexe($promotionUnifie)
    {
        /** @var PromotionFamillePrestationAnnexe $famillePrestationAnnexe */
        /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnexeCrm */
        /** @var Promotion $promotion */
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            foreach ($promotion->getPromotionFamillePrestationAnnexes() as $famillePrestationAnnexe) {
                if (empty($famillePrestationAnnexe->getFamillePrestationAnnexe())) {
                    $promotion->getPromotionFamillePrestationAnnexes()->removeElement($famillePrestationAnnexe);
                } else {
                    $famillePrestationAnnexe->setPromotion($promotion);
                }
            }
        }
        $promotionFamillePrestationAnnexeCrms = $promotionUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getPromotionFamillePrestationAnnexes();
        $famillePrestationAnnexes = new ArrayCollection();
        $fournisseurs = new ArrayCollection();
        foreach ($promotionFamillePrestationAnnexeCrms as $promotionFamillePrestationAnnexeCrm) {
            $famillePrestationAnnexes->add($promotionFamillePrestationAnnexeCrm->getFamillePrestationAnnexe());
            $fournisseurs->add($promotionFamillePrestationAnnexeCrm->getFournisseur());
        }
        foreach ($promotionUnifie->getPromotions() as $promotion) {
            if ($promotion->getSite()->getCrm() == 0) {
                $famillePrestationAnnexeSites = new ArrayCollection();
                foreach ($promotion->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnexeSite) {
                    $famillePrestationAnnexeSites->add($promotionFamillePrestationAnnexeSite->getFamillePrestationAnnexe());
                }
                foreach ($famillePrestationAnnexes as $key => $famillePrestationAnnexe) {
                    if (false === $famillePrestationAnnexeSites->contains($famillePrestationAnnexe)) {
                        $newFamillePrestationAnnexe = new PromotionFamillePrestationAnnexe();
                        $promotion->addPromotionFamillePrestationAnnex($newFamillePrestationAnnexe);
                        $newFamillePrestationAnnexe
                            ->setFamillePrestationAnnexe($famillePrestationAnnexe)
                            ->setFournisseur($fournisseurs->get($key));
                    }
                }
            }
        }
    }

    /**
     * @param PromotionUnifie $promotionUnifie
     */
    private function gestionPromotionLogement($promotionUnifie)
    {
        $em = $this->getDoctrine()->getManager();

        $job = new Job('creer:promotionLogementByPromotionUnifie',
            array(
                'promotionUnifieId' => $promotionUnifie->getId()
            ), true, 'promotionLogementByPromotionUnifie');
        $em->persist($job);
        $em->flush();
    }

    public function getFournisseurHebergementsAction($promotionId, $fournisseurId, $siteId)
    {
        $em = $this->getDoctrine()->getManager();
        $hebergements = $em->getRepository(HebergementUnifie::class)->getFournisseurHebergements($fournisseurId, $this->container->getParameter('locale'), $siteId);

        $promotionHebergements = $em->getRepository(PromotionHebergement::class)->findBy(array('promotion' => $promotionId, 'fournisseur' => $fournisseurId));

        $promotionUnifie = new PromotionUnifie();
        $promotion = new Promotion();
        $promotionUnifie->addPromotion($promotion);
        foreach ($promotionHebergements as $promotionHebergement) {
            $promotion->addPromotionHebergement($promotionHebergement);
        }

        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

//        dump($form->children['promotions'][0]);die;

        return $this->render('@MondofutePromotion/promotionunifie/get-code-promo-fournisseur-hebergements.html.twig', array(
            'hebergements' => $hebergements,
            'promotionId' => $promotionId,
            'fournisseurId' => $fournisseurId,
            'keyPromotion' => '__keyPromotion__',
            'promotion' => $form->children['promotions'][0],
        ));
    }

    public function getFournisseurPrestationAnnexesAction($promotionId, $fournisseurId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurPrestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->getFournisseurPrestationAnnexes($fournisseurId, $this->container->getParameter('locale'));
//        $prestationAnnexes = new ArrayCollection();

        $promotionFournisseurPrestationAnnexes = $em->getRepository(PromotionFournisseurPrestationAnnexe::class)->findBy(array('promotion' => $promotionId, 'fournisseur' => $fournisseurId));
        $promotionFamillePrestationAnnexes = $em->getRepository(PromotionFamillePrestationAnnexe::class)->findBy(array('promotion' => $promotionId, 'fournisseur' => $fournisseurId));
        $promotionUnifie = new PromotionUnifie();
        $promotion = new Promotion();
        $promotionUnifie->addPromotion($promotion);
        foreach ($promotionFournisseurPrestationAnnexes as $promotionFournisseurPrestationAnnex) {
            $promotion->addPromotionFournisseurPrestationAnnex($promotionFournisseurPrestationAnnex);
        }
        foreach ($promotionFamillePrestationAnnexes as $promotionFamillePrestationAnnex) {
            $promotion->addPromotionFamillePrestationAnnex($promotionFamillePrestationAnnex);
        }

        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

        return $this->render('@MondofutePromotion/promotionunifie/get-code-promo-fournisseur-prestation-annexes.html.twig', array(
            'fournisseurPrestationAnnexes' => $fournisseurPrestationAnnexes,
            'promotionId' => $promotionId,
            'fournisseurId' => $fournisseurId,
            'keyPromotion' => '__keyPromotion__',
            'promotion' => $form->children['promotions'][0],
        ));
    }

    /**
     * Deletes a PromotionUnifie entity.
     *
     */
    public function deleteAction(Request $request, PromotionUnifie $promotionUnifie)
    {
        /** @var Promotion $promotionSite */
        /** @var Promotion $promotion */
        $form = $this->createDeleteForm($promotionUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $promotionUnifieSite = $emSite->find(PromotionUnifie::class, $promotionUnifie);
                if (!empty($promotionUnifieSite)) {
                    $emSite->remove($promotionUnifieSite);
                    $emSite->flush();
                }
            }

            $em->remove($promotionUnifie);
            $em->flush();

            $this->addFlash('success', 'La prestation annexe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('promotion_index');
    }

    public function getClientsAction($clientName, $promotionId, $siteId)
    {
        $em = $this->getDoctrine()->getManager();
        $entityUnifie = new PromotionUnifie();

        $this->ajouterPromotionsDansForm($entityUnifie);
        $this->promotionsSortByAffichage($entityUnifie);

        $promotionClients = new ArrayCollection();
        if (!empty($promotionId)) {
            $promotion = $em->find(Promotion::class, $promotionId);
            $promotionClients = $promotion->getPromotionClients();
        }

//        /** @var Promotion $promotion */
        $clients = $em->getRepository(Client::class)->getClients($clientName);
        $clientForm = new ArrayCollection();
        foreach ($clients as $client) {

            $clientExist = $promotionClients->filter(function (PromotionClient $element) use ($client) {
                return $element->getClient()->getId() == $client->getId();
            })->first();
            if (false === $clientExist) {
                $clientForm->add($client);
            }
        }

//        $famillePrestationAnnexe    = $em->find(FamillePrestationAnnexe::class, $clientName);
        $form = $this->createForm('Mondofute\Bundle\PromotionBundle\Form\PromotionUnifieType', $entityUnifie,
            array(
                'clients' => $clients
            )
        );

        return $this->render('@MondofutePromotion/promotionunifie/client.html.twig', array(
            'form' => $form->createView(),
            'siteId' => $siteId,
            'clients' => $clientForm
        ));
    }

    public function getPanelHebergementAction($promotionId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseursTypeHebergement = $em->getRepository(Fournisseur::class)->rechercherTypeHebergement()->getQuery()->getResult();
        $promotionUnifie = new PromotionUnifie();
        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionUnifie->addPromotion($promotion);
        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();

        return $this->render('@MondofutePromotion/promotionunifie/panel-hebergement.html.twig', array(
            'fournisseursTypeHebergement' => $fournisseursTypeHebergement,
            'promotion' => $form->children['promotions'][0],
            'keyPromotion' => '_keyPromotion_'
        ));
    }

    public function getPrestationAnnexeAction($promotionId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseursPrestationAnnexe = $em->getRepository(Fournisseur::class)->findWithPrestationAnnexes();
        $promotionUnifie = new PromotionUnifie();
        $promotion = $em->find(Promotion::class, $promotionId);
        $promotionUnifie->addPromotion($promotion);
        $form = $this->createForm(PromotionUnifieType::class, $promotionUnifie)->createView();


        return $this->render('@MondofutePromotion/promotionunifie/panel-prestation-annexe.html.twig', array(
            'fournisseursPrestationAnnexe' => $fournisseursPrestationAnnexe,
            'promotion' => $form->children['promotions'][0],
            'keyPromotion' => '_keyPromotion_'
        ));
    }

    private function addPromotionPeriode(PromotionUnifie $entityUnifie, $spec, $PromotionPeriode)
    {
//    private function addPromotionPeriode(PromotionUnifie $entityUnifie , $spec,  ){
        $getPromotionPeriodes = "getPromotionPeriode" . $spec . "s";
        $addPromotionPeriode = "addPromotionPeriode" . $spec;
        $entityCrm = $entityUnifie->getPromotions()->filter(function (Promotion $element) {
            return $element->getSite()->getCrm() == true;
        })->first();

        if (
            !empty($entityCrm->$getPromotionPeriodes()) &&
            !$entityCrm->$getPromotionPeriodes()->isEmpty()
        ) {
            foreach ($entityUnifie->getPromotions() as $promotion) {
                if ($promotion->getSite()->getCrm() == false &&
                    (empty($promotion->$getPromotionPeriodes()) ||
                        $promotion->$getPromotionPeriodes()->isEmpty())
                ) {
//                    /** @var PromotionPeriode $promotionPeriodeValidite */
                    foreach ($entityCrm->$getPromotionPeriodes() as $promotionPeriode) {
                        $promotionPeriodeSite = new $PromotionPeriode();
                        $promotion->$addPromotionPeriode($promotionPeriodeSite);
                        $promotionPeriodeSite
                            ->setDateDebut($promotionPeriode->getDateDebut())
                            ->setDateFin($promotionPeriode->getDateFin());
                    }
                }
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité promotion
     * @param PromotionUnifie $entityUnifie
     */
    private function copieVersSites(PromotionUnifie $entityUnifie)
    {
        /** @var EntityManager $emSite */
        /** @var Promotion $entity */
        /** @var Promotion $entityCrm */
//        Boucle sur les promotions afin de savoir sur quel site nous devons l'enregistrer
        foreach ($entityUnifie->getPromotions() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());
                $site = $emSite->find(Site::class, $entity->getSite());

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (empty($entityUnifieSite = $emSite->find(PromotionUnifie::class, $entityUnifie))) {
                    $entityUnifieSite = new PromotionUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                }

                //  Récupération de la promotion sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty($entitySite = $emSite->getRepository(Promotion::class)->findOneBy(array('promotionUnifie' => $entityUnifieSite)))) {
                    $entitySite = new Promotion();
                    $entitySite
                        ->setSite($site)
                        ->setPromotionUnifie($entityUnifieSite);

                    $entityUnifieSite->addPromotion($entitySite);
                }

                // *** gestion promotion client ***
                if (!empty($entity->getPromotionClients()) && !$entity->getPromotionClients()->isEmpty()) {
                    /** @var PromotionClient $promotionClient */
                    foreach ($entity->getPromotionClients() as $promotionClient) {
                        $promotionClientSite = $entitySite->getPromotionClients()->filter(function (PromotionClient $element) use ($promotionClient) {
                            return $element->getId() == $promotionClient->getId();
                        })->first();
                        if (false === $promotionClientSite) {

                            $promotionClientSite = new PromotionClient();
                            $entitySite->addPromotionClient($promotionClientSite);
                            $promotionClientSite
                                ->setId($promotionClient->getId());
                            $metadata = $emSite->getClassMetadata(get_class($promotionClientSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $promotionPeriodeValidite = null;
                        if (!empty($promotionClient->getPromotionPeriodeValidite())) {
                            $promotionPeriodeValidite = $emSite->find(PromotionPeriodeValidite::class, $promotionClient->getPromotionPeriodeValidite());
                        }
                        $promotionClientSite
                            ->setPromotionPeriodeValidite($promotionPeriodeValidite)
                            ->setClient($emSite->find(Client::class, $promotionClient->getClient()))
                            ->setUtilise($promotionClient->getUtilise());
                    }
                }

                if (!empty($entitySite->getPromotionClients()) && !$entitySite->getPromotionClients()->isEmpty()) {
                    /** @var PromotionClient $promotionClient */
                    /** @var PromotionClient $promotionClientSite */
                    foreach ($entitySite->getPromotionClients() as $promotionClientSite) {
                        $promotionClient = $entity->getPromotionClients()->filter(function (PromotionClient $element) use ($promotionClientSite) {
                            return $element->getId() == $promotionClientSite->getId();
                        })->first();
                        if (false === $promotionClient) {
//                            $entitySite->removePromotionClient($promotionClientSite);
                            $emSite->remove($promotionClientSite);
                        }
                    }
                }
                // *** fin gestion promotion client ***

//                 *** gestion promotion periode validité ***
                if (!empty($entity->getPromotionPeriodeValidites()) && !$entity->getPromotionPeriodeValidites()->isEmpty()) {
                    /** @var PromotionPeriodeValidite $promotionPeriodeValidite */
                    foreach ($entity->getPromotionPeriodeValidites() as $promotionPeriodeValidite) {
                        $promotionPeriodeValiditeSite = $entitySite->getPromotionPeriodeValidites()->filter(function (PromotionPeriodeValidite $element) use ($promotionPeriodeValidite) {
                            return $element->getId() == $promotionPeriodeValidite->getId();
                        })->first();
                        if (false === $promotionPeriodeValiditeSite) {
                            $promotionPeriodeValiditeSite = new PromotionPeriodeValidite();
                            $entitySite->addPromotionPeriodeValidite($promotionPeriodeValiditeSite);
                            $promotionPeriodeValiditeSite
                                ->setId($promotionPeriodeValidite->getId());
                            $metadata = $emSite->getClassMetadata(get_class($promotionPeriodeValiditeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $promotionPeriodeValiditeSite
                            ->setDateDebut($promotionPeriodeValidite->getDateDebut())
                            ->setDateFin($promotionPeriodeValidite->getDateFin());
                    }
                }

                if (!empty($entitySite->getPromotionPeriodeValidites()) && !$entitySite->getPromotionPeriodeValidites()->isEmpty()) {
                    /** @var PromotionPeriodeValidite $promotionPeriodeValidite */
                    /** @var PromotionPeriodeValidite $promotionPeriodeValiditeSite */
                    foreach ($entitySite->getPromotionPeriodeValidites() as $promotionPeriodeValiditeSite) {
                        $promotionPeriodeValidite = $entity->getPromotionPeriodeValidites()->filter(function (PromotionPeriodeValidite $element) use ($promotionPeriodeValiditeSite) {
                            return $element->getId() == $promotionPeriodeValiditeSite->getId();
                        })->first();
                        if (false === $promotionPeriodeValidite) {
                            $promotionClientSite = $entitySite->getPromotionClients()->filter(function (PromotionClient $element) use ($promotionPeriodeValiditeSite) {
                                return $element->getPromotionPeriodeValidite() == $promotionPeriodeValiditeSite;
                            })->first();
                            if (!empty($promotionClientSite)) {
                                $promotionClientSite->setPromotionPeriodeValidite(null);
                                $emSite->remove($promotionClientSite);
                            }
//                            $entitySite->removePromotionPeriodeValidite($promotionPeriodeValiditeSite);
                            $emSite->remove($promotionPeriodeValiditeSite);
                        }
                    }
                }
//                 *** fin promotion periode validité ***

                // *** gestion promotion periode séjour ***
                if (!empty($entity->getPromotionPeriodeSejours()) && !$entity->getPromotionPeriodeSejours()->isEmpty()) {
                    /** @var PromotionPeriodeSejour $promotionPeriodeSejour */
                    foreach ($entity->getPromotionPeriodeSejours() as $promotionPeriodeSejour) {
                        $promotionPeriodeSejourSite = $entitySite->getPromotionPeriodeSejours()->filter(function (PromotionPeriodeSejour $element) use ($promotionPeriodeSejour) {
                            return $element->getId() == $promotionPeriodeSejour->getId();
                        })->first();
                        if (false === $promotionPeriodeSejourSite) {
                            $promotionPeriodeSejourSite = new PromotionPeriodeSejour();
                            $entitySite->addPromotionPeriodeSejour($promotionPeriodeSejourSite);
                            $promotionPeriodeSejourSite
                                ->setId($promotionPeriodeSejour->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionPeriodeSejourSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $promotionPeriodeSejourSite
                            ->setDateDebut($promotionPeriodeSejour->getDateDebut())
                            ->setDateFin($promotionPeriodeSejour->getDateFin());
                    }
                }

                if (!empty($entitySite->getPromotionPeriodeSejours()) && !$entitySite->getPromotionPeriodeSejours()->isEmpty()) {
                    /** @var PromotionPeriodeSejour $promotionPeriodeSejour */
                    foreach ($entitySite->getPromotionPeriodeSejours() as $promotionPeriodeSejourSite) {
                        $promotionPeriodeSejour = $entity->getPromotionPeriodeSejours()->filter(function (PromotionPeriodeSejour $element) use ($promotionPeriodeSejourSite) {
                            return $element->getId() == $promotionPeriodeSejourSite->getId();
                        })->first();
                        if (false === $promotionPeriodeSejour) {
//                            $entitySite->removePromotionPeriodeSejour($promotionPeriodeSejourSite);
                            $emSite->remove($promotionPeriodeSejourSite);
                        }
                    }
                }
                // *** fin gestion promotion periode séjour ***

                // *** gestion promotion typeAffectation ***
                if (!empty($entity->getPromotionTypeAffectations()) && !$entity->getPromotionTypeAffectations()->isEmpty()) {
                    /** @var PromotionTypeAffectation $promotionTypeAffectation */
                    foreach ($entity->getPromotionTypeAffectations() as $promotionTypeAffectation) {
                        $promotionTypeAffectationSite = $entitySite->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) use ($promotionTypeAffectation) {
                            return $element->getId() == $promotionTypeAffectation->getId();
                        })->first();
                        if (false === $promotionTypeAffectationSite) {
                            $promotionTypeAffectationSite = new PromotionTypeAffectation();
                            $entitySite->addPromotionTypeAffectation($promotionTypeAffectationSite);
                            $promotionTypeAffectationSite
                                ->setId($promotionTypeAffectation->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionTypeAffectationSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $promotionTypeAffectationSite
                            ->setTypeAffectation($promotionTypeAffectation->getTypeAffectation());
                    }
                }

                if (!empty($entitySite->getPromotionTypeAffectations()) && !$entitySite->getPromotionTypeAffectations()->isEmpty()) {
                    /** @var PromotionTypeAffectation $promotionTypeAffectation */
                    foreach ($entitySite->getPromotionTypeAffectations() as $promotionTypeAffectationSite) {
                        $promotionTypeAffectation = $entity->getPromotionTypeAffectations()->filter(function (PromotionTypeAffectation $element) use ($promotionTypeAffectationSite) {
                            return $element->getId() == $promotionTypeAffectationSite->getId();
                        })->first();
                        if (false === $promotionTypeAffectation) {
//                            $entitySite->removePromotionTypeAffectation($promotionTypeAffectationSite);
                            $emSite->remove($promotionTypeAffectationSite);
                        }
                    }
                }
                // *** fin gestion promotion typeAffectation ***

                // *** gestion promotion fournisseur ***
                if (!empty($entity->getPromotionFournisseurs()) && !$entity->getPromotionFournisseurs()->isEmpty()) {
                    /** @var PromotionFournisseur $promotionFournisseur */
                    foreach ($entity->getPromotionFournisseurs() as $promotionFournisseur) {
                        $promotionFournisseurSite = $entitySite->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($promotionFournisseur) {
                            return $element->getId() == $promotionFournisseur->getId();
                        })->first();
                        if (false === $promotionFournisseurSite) {
                            $promotionFournisseurSite = new PromotionFournisseur();
                            $entitySite->addPromotionFournisseur($promotionFournisseurSite);
                            $promotionFournisseurSite
                                ->setId($promotionFournisseur->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionFournisseurSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $promotionFournisseurSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionFournisseur->getFournisseur()))
                            ->setType($promotionFournisseur->getType());
                    }
                }

                if (!empty($entitySite->getPromotionFournisseurs()) && !$entitySite->getPromotionFournisseurs()->isEmpty()) {
                    /** @var PromotionFournisseur $promotionFournisseur */
                    foreach ($entitySite->getPromotionFournisseurs() as $promotionFournisseurSite) {
                        $promotionFournisseur = $entity->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($promotionFournisseurSite) {
                            return $element->getId() == $promotionFournisseurSite->getId();
                        })->first();
                        if (false === $promotionFournisseur) {
//                            $entitySite->removePromotionFournisseur($promotionFournisseurSite);
                            $emSite->remove($promotionFournisseurSite);
                        }
                    }
                }
                // *** fin gestion promotion fournisseur ***

                // *** gestion promotion hebergement ***
                if (!empty($entity->getPromotionHebergements()) && !$entity->getPromotionHebergements()->isEmpty()) {
                    /** @var PromotionHebergement $promotionHebergement */
                    foreach ($entity->getPromotionHebergements() as $promotionHebergement) {
                        $promotionHebergementSite = $entitySite->getPromotionHebergements()->filter(function (PromotionHebergement $element) use ($promotionHebergement) {
                            return $element->getId() == $promotionHebergement->getId();
                        })->first();
                        if (false === $promotionHebergementSite) {
                            $promotionHebergementSite = new PromotionHebergement();
                            $entitySite->addPromotionHebergement($promotionHebergementSite);
                            $promotionHebergementSite
                                ->setId($promotionHebergement->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionHebergementSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionHebergementSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionHebergement->getFournisseur()))
                            ->setHebergement($emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $promotionHebergement->getHebergement()->getHebergementUnifie())));
                    }
                }

                if (!empty($entitySite->getPromotionHebergements()) && !$entitySite->getPromotionHebergements()->isEmpty()) {
                    /** @var PromotionHebergement $promotionHebergement */
                    foreach ($entitySite->getPromotionHebergements() as $promotionHebergementSite) {
                        $promotionHebergement = $entity->getPromotionHebergements()->filter(function (PromotionHebergement $element) use ($promotionHebergementSite) {
                            return $element->getId() == $promotionHebergementSite->getId();
                        })->first();
                        if (false === $promotionHebergement) {
//                            $entitySite->removePromotionHebergement($promotionHebergementSite);
                            $emSite->remove($promotionHebergementSite);
                        }
                    }
                }
                // *** fin gestion promotion hebergement ***

                // *** gestion promotion fournisseurPrestationAnnexe ***
                if (!empty($entity->getPromotionFournisseurPrestationAnnexes()) && !$entity->getPromotionFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexe */
                    foreach ($entity->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexe) {
                        $promotionFournisseurPrestationAnnexeSite = $entitySite->getPromotionFournisseurPrestationAnnexes()->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($promotionFournisseurPrestationAnnexe) {
                            return $element->getId() == $promotionFournisseurPrestationAnnexe->getId();
                        })->first();
                        if (false === $promotionFournisseurPrestationAnnexeSite) {
                            $promotionFournisseurPrestationAnnexeSite = new PromotionFournisseurPrestationAnnexe();
                            $entitySite->addPromotionFournisseurPrestationAnnex($promotionFournisseurPrestationAnnexeSite);
                            $promotionFournisseurPrestationAnnexeSite
                                ->setId($promotionFournisseurPrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionFournisseurPrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionFournisseurPrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionFournisseurPrestationAnnexe->getFournisseur()))
                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class, $promotionFournisseurPrestationAnnexe->getFournisseurPrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getPromotionFournisseurPrestationAnnexes()) && !$entitySite->getPromotionFournisseurPrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnexe */
                    foreach ($entitySite->getPromotionFournisseurPrestationAnnexes() as $promotionFournisseurPrestationAnnexeSite) {
                        $promotionFournisseurPrestationAnnexe = $entity->getPromotionFournisseurPrestationAnnexes()->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($promotionFournisseurPrestationAnnexeSite) {
                            return $element->getId() == $promotionFournisseurPrestationAnnexeSite->getId();
                        })->first();
                        if (false === $promotionFournisseurPrestationAnnexe) {
//                            $entitySite->removePromotionFournisseurPrestationAnnexe($promotionFournisseurPrestationAnnexeSite);
                            $emSite->remove($promotionFournisseurPrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion promotion fournisseurPrestationAnnexe ***

                // *** gestion promotion famillePrestationAnnexe ***
                if (!empty($entity->getPromotionFamillePrestationAnnexes()) && !$entity->getPromotionFamillePrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnexe */
                    foreach ($entity->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnexe) {
                        $promotionFamillePrestationAnnexeSite = $entitySite->getPromotionFamillePrestationAnnexes()->filter(function (PromotionFamillePrestationAnnexe $element) use ($promotionFamillePrestationAnnexe) {
                            return $element->getId() == $promotionFamillePrestationAnnexe->getId();
                        })->first();
                        if (false === $promotionFamillePrestationAnnexeSite) {
                            $promotionFamillePrestationAnnexeSite = new PromotionFamillePrestationAnnexe();
                            $entitySite->addPromotionFamillePrestationAnnex($promotionFamillePrestationAnnexeSite);
                            $promotionFamillePrestationAnnexeSite
                                ->setId($promotionFamillePrestationAnnexe->getId());

                            $metadata = $emSite->getClassMetadata(get_class($promotionFamillePrestationAnnexeSite));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }

                        $promotionFamillePrestationAnnexeSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $promotionFamillePrestationAnnexe->getFournisseur()))
                            ->setFamillePrestationAnnexe($emSite->find(FamillePrestationAnnexe::class, $promotionFamillePrestationAnnexe->getFamillePrestationAnnexe()));
                    }
                }

                if (!empty($entitySite->getPromotionFamillePrestationAnnexes()) && !$entitySite->getPromotionFamillePrestationAnnexes()->isEmpty()) {
                    /** @var PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnexe */
                    foreach ($entitySite->getPromotionFamillePrestationAnnexes() as $promotionFamillePrestationAnnexeSite) {
                        $promotionFamillePrestationAnnexe = $entity->getPromotionFamillePrestationAnnexes()->filter(function (PromotionFamillePrestationAnnexe $element) use ($promotionFamillePrestationAnnexeSite) {
                            return $element->getId() == $promotionFamillePrestationAnnexeSite->getId();
                        })->first();
                        if (false === $promotionFamillePrestationAnnexe) {
//                            $entitySite->removePromotionFamillePrestationAnnexe($promotionFamillePrestationAnnexeSite);
                            $emSite->remove($promotionFamillePrestationAnnexeSite);
                        }
                    }
                }
                // *** fin gestion promotion famillePrestationAnnexe ***

                // *** gestion promotion logement ***
                if (!empty($entitySite->getPromotionLogements()) && !$entitySite->getPromotionLogements()->isEmpty()) {
                    /** @var PromotionLogement $promotionLogement */
                    foreach ($entitySite->getPromotionLogements() as $promotionLogementSite) {
                        $promotionLogement = $entity->getPromotionLogements()->filter(function (PromotionLogement $element) use ($promotionLogementSite) {
                            return $element->getId() == $promotionLogementSite->getId();
                        })->first();
                        if (false === $promotionLogement) {
//                            $entitySite->removePromotionLogement($promotionLogementSite);
                            $emSite->remove($promotionLogementSite);
                        }
                    }
                }
                // *** fin gestion promotion logement ***

                $entityUnifieSite
                    ->setCode($entityUnifie->getCode());
                //  copie des données promotion
                $entitySite
                    ->setActifSite($entity->getActifSite())
                    ->setLibelle($entity->getLibelle())
                    ->setValeurRemise($entity->getValeurRemise())
                    ->setPrixMini($entity->getPrixMini())
                    ->setActif($entity->getActif())
                    ->setClientAffectation($entity->getClientAffectation())
                    ->setTypeRemise($entity->getTypeRemise())
                    ->setUsagePromotion($entity->getUsagePromotion());

                $emSite->persist($entityUnifieSite);

                $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $emSite->flush();
            }
        }
    }

}
