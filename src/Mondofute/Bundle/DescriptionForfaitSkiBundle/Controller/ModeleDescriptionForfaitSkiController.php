<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Entity\DomaineUnifie;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Age;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Mondofute\Bundle\UniteBundle\Entity\UniteAge;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * ModeleDescriptionForfaitSki controller.
 *
 */
class ModeleDescriptionForfaitSkiController extends Controller
{
    /**
     * Lists all ModeleDescriptionForfaitSkiUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();
        $count = $em
            ->getRepository('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'modeledescriptionforfaitski_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $entities = $this->getDoctrine()->getRepository('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/index.html.twig', array(
            'modeleDescriptionForfaitSkis' => $entities,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new ModeleDescriptionForfaitSki entity.
     *
     */
    public function newAction(Request $request)
    {
        /** @var LigneDescriptionForfaitSkiTraduction $ligneDescriptionForfaitSkiTraduction */
        $em = $this->getDoctrine()->getManager();
        $modeleDescriptionForfaitSki = new ModeleDescriptionForfaitSki();
        // Récupérer toutes les entitées LigneDescriptionForfaitSki
        $ligneDescriptionForfaitSkis = $em->getRepository('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki')->findAll();
        foreach ($ligneDescriptionForfaitSkis as $ligneDescriptionForfaitSki) {
            $descriptionForfaitSki = new DescriptionForfaitSki();
            $descriptionForfaitSki->setLigneDescriptionForfaitSki($ligneDescriptionForfaitSki);
            $descriptionForfaitSki->setQuantite($ligneDescriptionForfaitSki->getQuantite());
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMin())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMin()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMin()->getValeur());
                $age = clone  $ligneDescriptionForfaitSki->getAgeMin();
            }
            $descriptionForfaitSki->setAgeMin($age);
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMax())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMax()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMax()->getValeur());
                $age = clone $ligneDescriptionForfaitSki->getAgeMax();
            }
            $descriptionForfaitSki->setAgeMax($age);
            $descriptionForfaitSki->setClassement($ligneDescriptionForfaitSki->getClassement());
            $descriptionForfaitSki->setPresent($ligneDescriptionForfaitSki->getPresent());
            $prix = new Tarif();
            if (!empty($ligneDescriptionForfaitSki->getPrix())) {
                $prix = clone $ligneDescriptionForfaitSki->getPrix();
            }
            $descriptionForfaitSki->setPrix($prix);
            foreach ($ligneDescriptionForfaitSki->getTraductions() as $ligneDescriptionForfaitSkiTraduction) {
                $traduction = new DescriptionForfaitSkiTraduction();
                $traduction->setLangue($ligneDescriptionForfaitSkiTraduction->getLangue());
                $traduction->setDescription($ligneDescriptionForfaitSkiTraduction->getDescription());
                $traduction->setTexteDur($ligneDescriptionForfaitSkiTraduction->getTexteDur());
                $traduction->setLibelle($ligneDescriptionForfaitSkiTraduction->getLibelle());
                $descriptionForfaitSki->addTraduction($traduction);
                $this->traductionsSortByLangue($descriptionForfaitSki);
            }
            $modeleDescriptionForfaitSki->addDescriptionForfaitSki($descriptionForfaitSki);
        }
        $form = $this->createForm('Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType', $modeleDescriptionForfaitSki);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->copieVersSites($modeleDescriptionForfaitSki);

            $em->persist($modeleDescriptionForfaitSki);
            $em->flush();

            $this->addFlash('success', 'le ModeleDescriptionForfaitSki a bien été créée');
            return $this->redirectToRoute('modeledescriptionforfaitski_edit', array('id' => $modeleDescriptionForfaitSki->getId()));
        }

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/new.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'form' => $form->createView(),
        ));
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $descriptionForfaitSki
     */
    private function traductionsSortByLangue(DescriptionForfaitSki $descriptionForfaitSki)
    {
        /** @var ArrayIterator $iterator */
        $traductions = $descriptionForfaitSki->getTraductions();
        $iterator = $traductions->getIterator();
        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (DescriptionForfaitSkiTraduction $a, DescriptionForfaitSkiTraduction $b) {
            return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $traductions = new ArrayCollection(iterator_to_array($iterator));
        $descriptionForfaitSki->setTraductions($traductions);
    }

    public function copieVersSites(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $modeleDescriptionForfaitSkiSite = clone $modeleDescriptionForfaitSki;

            foreach ($modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis() as $descriptionForfaitSki) {
                foreach ($descriptionForfaitSki->getTraductions() as $traduction) {
                    $traduction->setLangue($emSite->find('MondofuteLangueBundle:Langue', $traduction->getLangue()->getId()));
                }
                if (!empty($descriptionForfaitSki->getPrix()->getUnite())) {
                    $descriptionForfaitSki->getPrix()->setUnite($emSite->find('MondofuteUniteBundle:UniteTarif', $descriptionForfaitSki->getPrix()->getUnite()->getId()));
                }
                if (!empty($descriptionForfaitSki->getAgeMin()->getUnite())) {
                    $descriptionForfaitSki->getAgeMin()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMin()->getUnite()->getId()));
                }
                if (!empty($descriptionForfaitSki->getAgeMax()->getUnite())) {
                    $descriptionForfaitSki->getAgeMax()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMax()->getUnite()->getId()));
                }
                if (!empty($descriptionForfaitSki->getLigneDescriptionForfaitSki())) {
                    $descriptionForfaitSki->setLigneDescriptionForfaitSki($emSite->find('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki', $descriptionForfaitSki->getLigneDescriptionForfaitSki()->getId()));
                }
                if (!empty($descriptionForfaitSki->getPresent())) {
                    $descriptionForfaitSki->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $descriptionForfaitSki->getPresent()->getId()));
                }
            }
            $emSite->persist($modeleDescriptionForfaitSkiSite);
            $emSite->flush();
        }
    }

    /**
     * @param DomaineUnifie $domaineUnifie
     * @param DomaineUnifie $domaineUnifieSite
     */
    public function copieToDomaineVersSites($domaineUnifie,  $domaineUnifieSite)
    {
        $domaineSite = $domaineUnifieSite->getDomaines()->first();
        $domaine = $domaineUnifie->getDomaines()->filter(function (Domaine $element) use ($domaineSite) {
            return $element->getSite()->getId() == $domaineSite->getSite()->getId();
        })->first();
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
        /** @var Domaine $domaineSite */
        /** @var Domaine $domaine */
//        foreach ($domaines as $domaine) {

            $emSite = $this->getDoctrine()->getManager($domaine->getSite()->getLibelle());
//            $domaineSite = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $domaineUnifie->getId()));

            $modeleDescriptionForfaitSki = $domaine->getModeleDescriptionForfaitSki();
            $modeleDescriptionForfaitSkiSite = $domaineSite->getModeleDescriptionForfaitSki();
            if(empty($modeleDescriptionForfaitSki))
            {
                $this->majDomaines($domaineUnifie);
            }

            if(empty($modeleDescriptionForfaitSkiSite))
            {
                $modeleDescriptionForfaitSkiSite = new ModeleDescriptionForfaitSki();
                $modeleDescriptionForfaitSkiSite->setId($modeleDescriptionForfaitSki->getId());
                $metadata = $emSite->getClassMetadata(get_class($modeleDescriptionForfaitSkiSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $domaineSite->setModeleDescriptionForfaitSki($modeleDescriptionForfaitSkiSite);
            }

            foreach ($modeleDescriptionForfaitSki->getDescriptionForfaitSkis() as $descriptionForfaitSki) {
                $descriptionForfaitSkiSite = $modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis()->filter(function (DescriptionForfaitSki $element) use ( $descriptionForfaitSki){
                    return $element->getLigneDescriptionForfaitSki()->getId() == $descriptionForfaitSki->getLigneDescriptionForfaitSki()->getId();
                })->first();
                if (false === $descriptionForfaitSkiSite)
                {
                    $descriptionForfaitSkiSite = new DescriptionForfaitSki();

                    $descriptionForfaitSkiSite->setId($descriptionForfaitSki->getId());
                    $metadata = $emSite->getClassMetadata(get_class($descriptionForfaitSkiSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                    $modeleDescriptionForfaitSkiSite->addDescriptionForfaitSki($descriptionForfaitSkiSite);
                    $descriptionForfaitSkiSite->setLigneDescriptionForfaitSki($emSite->find(LigneDescriptionForfaitSki::class , $descriptionForfaitSki->getLigneDescriptionForfaitSki()));
                }

                $descriptionForfaitSkiSite
                    ->setQuantite($descriptionForfaitSki->getQuantite())
                    ->setClassement($descriptionForfaitSki->getClassement())
                    ->setPresent($emSite->find(OuiNonNC::class , $descriptionForfaitSki->getPresent()))
                ;

                // *** gestion prix ***
                if(empty($prix = $descriptionForfaitSkiSite->getPrix()))
                {
                    $prix = new Tarif();
                    $descriptionForfaitSkiSite->setPrix($prix);
                }
                $prix
                    ->setValeur($descriptionForfaitSki->getPrix()->getValeur())
                    ->setUnite( !empty($descriptionForfaitSki->getPrix()->getUnite()) ? $emSite->find(UniteTarif::class, $descriptionForfaitSki->getPrix()->getUnite()) : null )
                ;
                // *** fin gestion prix ***

                // *** gestion ageMin ***
                if(empty($ageMin = $descriptionForfaitSkiSite->getAgeMin()))
                {
                    $ageMin = new Age();
                    $descriptionForfaitSkiSite->setAgeMin($ageMin);
                }
                $ageMin
                    ->setValeur($descriptionForfaitSki->getAgeMin()->getValeur())
                    ->setUnite( !empty($descriptionForfaitSki->getAgeMin()->getUnite()) ? $emSite->find(UniteAge::class, $descriptionForfaitSki->getAgeMin()->getUnite()) : null )
                ;
                // *** fin gestion ageMin ***

                // *** gestion ageMax ***
                if(empty($ageMax = $descriptionForfaitSkiSite->getAgeMax()))
                {
                    $ageMax = new Age();
                    $descriptionForfaitSkiSite->setAgeMax($ageMax);
                }
                $ageMax
                    ->setValeur($descriptionForfaitSki->getAgeMax()->getValeur())
                    ->setUnite( !empty($descriptionForfaitSki->getAgeMax()->getUnite()) ? $emSite->find(UniteAge::class, $descriptionForfaitSki->getAgeMax()->getUnite()) : null )
                ;
                // *** fin gestion ageMax ***

                // *** traduction ***
                foreach ($descriptionForfaitSki->getTraductions() as $traduction)
                {
                    $traductionSite = $descriptionForfaitSkiSite->getTraductions()->filter(function (DescriptionForfaitSkiTraduction $element) use ($traduction)
                    {
                        return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                    })->first();
                    if(false === $traductionSite)
                    {
                        $traductionSite = new DescriptionForfaitSkiTraduction();
                        $descriptionForfaitSkiSite->addTraduction($traductionSite);
                        $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                    }
                    $traductionSite
                        ->setLibelle($traduction->getLibelle())
                        ->setDescription($traduction->getDescription())
                        ->setTexteDur($traduction->getTexteDur())
                    ;
                }
                // *** fin traduction ***
            }
//        }
    }

    /**
     * @param DomaineUnifie $domaineUnifie
     */
    public function majDomaines($domaineUnifie){
        /** @var ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki */
        $modeleDescriptionForfaitSki = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getModeleDescriptionForfaitSki();
        $domaines = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
            return $element->getSite()->getCrm() == 0;
        });
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
//        $em = $this->getDoctrine()->getManager();
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        /** @var Domaine $domaine */
        foreach ($domaines as $domaine) {

            if(empty($modeleDescriptionForfaitSkiSite = $domaine->getModeleDescriptionForfaitSki()))
            {
                $modeleDescriptionForfaitSkiSite = new ModeleDescriptionForfaitSki();
                $domaine->setModeleDescriptionForfaitSki($modeleDescriptionForfaitSkiSite);
            }

            foreach ($modeleDescriptionForfaitSki->getDescriptionForfaitSkis() as $descriptionForfaitSki) {
                $descriptionForfaitSkiSite = $modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis()->filter(function (DescriptionForfaitSki $element) use ( $descriptionForfaitSki){
                    return $element->getLigneDescriptionForfaitSki() == $descriptionForfaitSki->getLigneDescriptionForfaitSki();
                })->first();
                if (false === $descriptionForfaitSkiSite)
                {
                    $descriptionForfaitSkiSite = new DescriptionForfaitSki();
                    $modeleDescriptionForfaitSkiSite->addDescriptionForfaitSki($descriptionForfaitSkiSite);
                    $descriptionForfaitSkiSite->setLigneDescriptionForfaitSki($descriptionForfaitSki->getLigneDescriptionForfaitSki());
                }

                $descriptionForfaitSkiSite
                    ->setQuantite($descriptionForfaitSki->getQuantite())
                    ->setClassement($descriptionForfaitSki->getClassement())
                    ->setPresent($descriptionForfaitSki->getPresent())
                ;

                // *** gestion prix ***
                if(empty($prix = $descriptionForfaitSkiSite->getPrix()))
                {
                    $prix = new Tarif();
                    $descriptionForfaitSkiSite->setPrix($prix);
                }
                $prix
                    ->setValeur($descriptionForfaitSki->getPrix()->getValeur())
                    ->setUnite($descriptionForfaitSki->getPrix()->getUnite())
                ;
                // *** fin gestion prix ***

                // *** gestion ageMin ***
                if(empty($ageMin = $descriptionForfaitSkiSite->getAgeMin()))
                {
                    $ageMin = new Age();
                    $descriptionForfaitSkiSite->setAgeMin($ageMin);
                }
                $ageMin
                    ->setValeur($descriptionForfaitSki->getAgeMin()->getValeur())
                    ->setUnite($descriptionForfaitSki->getAgeMin()->getUnite())
                ;
                // *** fin gestion ageMin ***

                // *** gestion ageMax ***
                if(empty($ageMax = $descriptionForfaitSkiSite->getAgeMax()))
                {
                    $ageMax = new Age();
                    $descriptionForfaitSkiSite->setAgeMax($ageMax);
                }
                $ageMax
                    ->setValeur($descriptionForfaitSki->getAgeMax()->getValeur())
                    ->setUnite($descriptionForfaitSki->getAgeMax()->getUnite())
                ;
                // *** fin gestion ageMax ***

                // *** traduction ***
                foreach ($descriptionForfaitSki->getTraductions() as $traduction)
                {
                    $traductionSite = $descriptionForfaitSkiSite->getTraductions()->filter(function (DescriptionForfaitSkiTraduction $element) use ($traduction)
                    {
                        return $element->getLangue() == $traduction->getLangue();
                    })->first();
                    if(false == $traductionSite)
                    {
                        $traductionSite = new DescriptionForfaitSkiTraduction();
                        $descriptionForfaitSkiSite->addTraduction($traductionSite);
                        $traductionSite->setLangue($traduction->getLangue());
                    }
                    $traductionSite
                        ->setLibelle($traduction->getLibelle())
                        ->setDescription($traduction->getDescription())
                        ->setTexteDur($traduction->getTexteDur())
                    ;
                }
                // *** fin traduction ***
            }
        }
    }

    /**
     * @param DomaineUnifie $domaineUnifie
     */
    public function addModeleDescriptionForfaitSkis($domaineUnifie)
    {
        /** @var Domaine $domaine */
        /** @var LigneDescriptionForfaitSkiTraduction $ligneDescriptionForfaitSkiTraduction */
        $em = $this->getDoctrine()->getManager();
        $domaineCrm = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
            return $element->getSite()->getCrm() == 1;
        })->first();
        $modeleDescriptionForfaitSki = new ModeleDescriptionForfaitSki();
        $domaineCrm->setModeleDescriptionForfaitSki($modeleDescriptionForfaitSki);
        // Récupérer toutes les entitées LigneDescriptionForfaitSki
        $ligneDescriptionForfaitSkis = $em->getRepository(LigneDescriptionForfaitSki::class)->findBy(array() , array('categorie' => 'ASC' ,'classement' => 'ASC'));
        foreach ($ligneDescriptionForfaitSkis as $ligneDescriptionForfaitSki) {
            $descriptionForfaitSki = new DescriptionForfaitSki();
            $descriptionForfaitSki->setLigneDescriptionForfaitSki($ligneDescriptionForfaitSki);
            $descriptionForfaitSki->setQuantite($ligneDescriptionForfaitSki->getQuantite());
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMin())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMin()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMin()->getValeur());
                $age = clone  $ligneDescriptionForfaitSki->getAgeMin();
            }
            $descriptionForfaitSki->setAgeMin($age);
            $age = new Age();
            if (!empty($ligneDescriptionForfaitSki->getAgeMax())) {
//                $age->setUnite($ligneDescriptionForfaitSki->getAgeMax()->getUnite());
//                $age->setValeur($ligneDescriptionForfaitSki->getAgeMax()->getValeur());
                $age = clone $ligneDescriptionForfaitSki->getAgeMax();
            }
            $descriptionForfaitSki->setAgeMax($age);
            $descriptionForfaitSki->setClassement($ligneDescriptionForfaitSki->getClassement());
            $present = $em->find(OuiNonNC::class,3);
            if(!empty($ligneDescriptionForfaitSki->getPresent()))
            {
                $present = $ligneDescriptionForfaitSki->getPresent();
            }
            $descriptionForfaitSki->setPresent($present);
            $prix = new Tarif();
            if (!empty($ligneDescriptionForfaitSki->getPrix())) {
                $prix = clone $ligneDescriptionForfaitSki->getPrix();
            }
            $descriptionForfaitSki->setPrix($prix);
            foreach ($ligneDescriptionForfaitSki->getTraductions() as $ligneDescriptionForfaitSkiTraduction) {
                $traduction = new DescriptionForfaitSkiTraduction();
                $traduction->setLangue($ligneDescriptionForfaitSkiTraduction->getLangue());
                $traduction->setDescription($ligneDescriptionForfaitSkiTraduction->getDescription());
                $traduction->setTexteDur($ligneDescriptionForfaitSkiTraduction->getTexteDur());
                $traduction->setLibelle($ligneDescriptionForfaitSkiTraduction->getLibelle());
                $descriptionForfaitSki->addTraduction($traduction);
                $this->traductionsSortByLangue($descriptionForfaitSki);
            }
            $modeleDescriptionForfaitSki->addDescriptionForfaitSki($descriptionForfaitSki);
        }
    }


    /**
<<<<<<< HEAD
>>>>>>> ba9625ab52772394c59e14bf831e9f5aa1a2ab57
||||||| parent of 5f4403d... resolu bug rebase 135 modeledescription forfait ski et hebergement
>>>>>>> CRM-135
=======
>>>>>>> 5f4403d... resolu bug rebase 135 modeledescription forfait ski et hebergement
     * Finds and displays a ModeleDescriptionForfaitSki entity.
     *
     */
    public function showAction(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $deleteForm = $this->createDeleteForm($modeleDescriptionForfaitSki);

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/show.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ModeleDescriptionForfaitSki entity.
     *
     * @param ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki The ModeleDescriptionForfaitSki entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modeledescriptionforfaitski_delete', array('id' => $modeleDescriptionForfaitSki->getId())))
            ->add('delete', SubmitType::class, array('label' => 'Supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ModeleDescriptionForfaitSki entity.
     *
     */
    public function editAction(Request $request, ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        $deleteForm = $this->createDeleteForm($modeleDescriptionForfaitSki);
        foreach ($modeleDescriptionForfaitSki->getDescriptionForfaitSkis() as $descriptionForfaitSki) {
            $this->traductionsSortByLangue($descriptionForfaitSki);
        }
        $editForm = $this->createForm('Mondofute\Bundle\DescriptionForfaitSkiBundle\Form\ModeleDescriptionForfaitSkiType', $modeleDescriptionForfaitSki)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->mettreAJourSites($modeleDescriptionForfaitSki);
            $em = $this->getDoctrine()->getManager();
            $em->persist($modeleDescriptionForfaitSki);
            $em->flush();

            $this->addFlash('success', 'le ModeleDescriptionForfaitSki a bien été modifiée');
            return $this->redirectToRoute('modeledescriptionforfaitski_edit', array('id' => $modeleDescriptionForfaitSki->getId()));
        }

        return $this->render('@MondofuteDescriptionForfaitSki/modeledescriptionforfaitski/edit.html.twig', array(
            'modeleDescriptionForfaitSki' => $modeleDescriptionForfaitSki,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function mettreAJourSites(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        /** @var DescriptionForfaitSkiTraduction $traductionSite */
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSkiSite */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());

            $modeleDescriptionForfaitSkiSite = $emSite->find('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki', $modeleDescriptionForfaitSki->getId());
            if (!empty($modeleDescriptionForfaitSkiSite)) {
                foreach ($modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis() as $descriptionForfaitSkiSite) {
                    $descriptionForfaitSki = $modeleDescriptionForfaitSki->getDescriptionForfaitSkis()->filter(function (DescriptionForfaitSki $element) use ($descriptionForfaitSkiSite) {
                        return $element->getLigneDescriptionForfaitSki()->getId() == $descriptionForfaitSkiSite->getLigneDescriptionForfaitSki()->getId();
                    })->first();
                    foreach ($descriptionForfaitSkiSite->getTraductions() as $traductionSite) {
                        $langue = $traductionSite->getLangue();
                        $traduction = $descriptionForfaitSki->getTraductions()->filter(function (DescriptionForfaitSkiTraduction $element) use ($langue) {
                            return $element->getLangue()->getId() == $langue->getId();
                        })->first();
                        $traductionSite->setDescription($traduction->getDescription());
                        $traductionSite->setTexteDur($traduction->getTexteDur());
                        $traductionSite->setLibelle($traduction->getLibelle());
                    }
                    $descriptionForfaitSkiSite->setClassement($descriptionForfaitSki->getClassement());
                    $descriptionForfaitSkiSite->setQuantite($descriptionForfaitSki->getQuantite());
                    $descriptionForfaitSkiSite->getPrix()->setValeur($descriptionForfaitSki->getPrix()->getValeur());
                    if (!empty($descriptionForfaitSki->getPrix()->getUnite())) {
//                        dump($descriptionForfaitSkiSite->getPrix()->getUnite());die;
                        $descriptionForfaitSkiSite->getPrix()->setUnite($emSite->find('MondofuteUniteBundle:UniteTarif', $descriptionForfaitSki->getPrix()->getUnite()->getId()));
                    }
                    $descriptionForfaitSki->getAgeMin()->setValeur($descriptionForfaitSki->getAgeMin()->getValeur());
                    if (!empty($descriptionForfaitSki->getAgeMin()->getUnite())) {
                        $descriptionForfaitSkiSite->getAgeMin()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMin()->getUnite()->getId()));
                    }
                    $descriptionForfaitSki->getAgeMax()->setValeur($descriptionForfaitSki->getAgeMax()->getValeur());
                    if (!empty($descriptionForfaitSki->getAgeMax()->getUnite())) {
                        $descriptionForfaitSkiSite->getAgeMax()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMax()->getUnite()->getId()));
                    }
                    if (!empty($descriptionForfaitSki->getLigneDescriptionForfaitSki())) {
                        $descriptionForfaitSkiSite->setLigneDescriptionForfaitSki($emSite->find('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki', $descriptionForfaitSki->getLigneDescriptionForfaitSki()->getId()));
                    }
                    if (!empty($descriptionForfaitSki->getPresent())) {
                        $descriptionForfaitSkiSite->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $descriptionForfaitSki->getPresent()->getId()));
                    }
                }
                $emSite->persist($modeleDescriptionForfaitSkiSite);
                $emSite->flush();
            } else {
//                $this->copieVersSites($modeleDescriptionForfaitSki);
            }
        }

    }

    /**
     * @param DomaineUnifie $domaineUnifie
     */
    public function mettreAJourToDomaineSites(DomaineUnifie $domaineUnifie)
    {
        /** @var DescriptionForfaitSkiTraduction $traductionSite */
        /** @var DescriptionForfaitSkiTraduction $traduction */
        /** @var DescriptionForfaitSki $descriptionForfaitSkiSite */
        /** @var DescriptionForfaitSki $descriptionForfaitSki */
        /** @var Site $site */
        /** @var ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki */
        $modeleDescriptionForfaitSki = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
            return $element->getSite()->getCrm() == 1;
        })->first()->getModeleDescriptionForfaitSki();
        $domaines = $domaineUnifie->getDomaines()->filter(function (Domaine $element) {
            return $element->getSite()->getCrm() == 0;
        });
//        $em = $this->getDoctrine()->getManager();
        foreach ($domaines as $domaine) {
            $emSite = $this->getDoctrine()->getManager($domaine->getSite()->getLibelle());
            $domaineSite = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $domaineUnifie));

            $modeleDescriptionForfaitSkiSite = $emSite->find('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki', $domaineSite->getModeleDescriptionForfaitSki()->getId());
            if (!empty($modeleDescriptionForfaitSkiSite)) {
                foreach ($modeleDescriptionForfaitSkiSite->getDescriptionForfaitSkis() as $descriptionForfaitSkiSite) {
                    $descriptionForfaitSki = $modeleDescriptionForfaitSki->getDescriptionForfaitSkis()->filter(function (DescriptionForfaitSki $element) use ($descriptionForfaitSkiSite) {
                        return $element->getLigneDescriptionForfaitSki()->getId() == $descriptionForfaitSkiSite->getLigneDescriptionForfaitSki()->getId();
                    })->first();
                    foreach ($descriptionForfaitSkiSite->getTraductions() as $traductionSite) {
                        $langue = $traductionSite->getLangue();
                        $traduction = $descriptionForfaitSki->getTraductions()->filter(function (DescriptionForfaitSkiTraduction $element) use ($langue) {
                            return $element->getLangue()->getId() == $langue->getId();
                        })->first();
                        $traductionSite->setDescription($traduction->getDescription());
                        $traductionSite->setTexteDur($traduction->getTexteDur());
                        $traductionSite->setLibelle($traduction->getLibelle());
                    }
                    $descriptionForfaitSkiSite->setClassement($descriptionForfaitSki->getClassement());
                    $descriptionForfaitSkiSite->setQuantite($descriptionForfaitSki->getQuantite());
                    $descriptionForfaitSkiSite->getPrix()->setValeur($descriptionForfaitSki->getPrix()->getValeur());
                    if (!empty($descriptionForfaitSki->getPrix()->getUnite())) {
                        $descriptionForfaitSkiSite->getPrix()->setUnite($emSite->find('MondofuteUniteBundle:UniteTarif', $descriptionForfaitSki->getPrix()->getUnite()->getId()));
                    }
                    $descriptionForfaitSki->getAgeMin()->setValeur($descriptionForfaitSki->getAgeMin()->getValeur());
                    if (!empty($descriptionForfaitSki->getAgeMin()->getUnite())) {
                        $descriptionForfaitSkiSite->getAgeMin()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMin()->getUnite()->getId()));
                    }
                    $descriptionForfaitSki->getAgeMax()->setValeur($descriptionForfaitSki->getAgeMax()->getValeur());
                    if (!empty($descriptionForfaitSki->getAgeMax()->getUnite())) {
                        $descriptionForfaitSkiSite->getAgeMax()->setUnite($emSite->find('MondofuteUniteBundle:UniteAge', $descriptionForfaitSki->getAgeMax()->getUnite()->getId()));
                    }
                    if (!empty($descriptionForfaitSki->getLigneDescriptionForfaitSki())) {
                        $descriptionForfaitSkiSite->setLigneDescriptionForfaitSki($emSite->find('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSki', $descriptionForfaitSki->getLigneDescriptionForfaitSki()->getId()));
                    }
                    if (!empty($descriptionForfaitSki->getPresent())) {
                        $descriptionForfaitSkiSite->setPresent($emSite->find('MondofuteChoixBundle:OuiNonNC', $descriptionForfaitSki->getPresent()->getId()));
                    }
                }
                $emSite->persist($modeleDescriptionForfaitSkiSite);
                $emSite->flush();
            } else {
//                $this->copieVersSites($modeleDescriptionForfaitSki);
            }
        }

    }

    /**
     * Deletes a ModeleDescriptionForfaitSki entity.
     *
     */
    public function deleteAction(Request $request, ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki)
    {
        /** @var Site $site */
        $form = $this->createDeleteForm($modeleDescriptionForfaitSki);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                $modeleDescriptionForfaitSkiSite = $emSite->find('MondofuteDescriptionForfaitSkiBundle:ModeleDescriptionForfaitSki', $modeleDescriptionForfaitSki->getId());
                $emSite->remove($modeleDescriptionForfaitSkiSite);
                $emSite->flush();
            }
            $em->remove($modeleDescriptionForfaitSki);
            $em->flush();
        }

        $this->addFlash('success', 'le ModeleDescriptionForfaitSki a bien été supprimée');
        return $this->redirectToRoute('modeledescriptionforfaitski_index');
    }

}
