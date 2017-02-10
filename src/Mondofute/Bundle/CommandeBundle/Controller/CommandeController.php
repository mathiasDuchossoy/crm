<?php

namespace Mondofute\Bundle\CommandeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\CommandeBundle\Entity\Commande;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLigne;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLignePrestationAnnexe;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLigneSejour;
use Mondofute\Bundle\CommandeBundle\Entity\SejourNuite;
use Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commande controller.
 *
 */
class CommandeController extends Controller
{
    /**
     * Lists all Commande entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteCommandeBundle:Commande')
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'commande_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $unifies = $this->getDoctrine()->getRepository('MondofuteCommandeBundle:Commande')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteCommande/commande/index.html.twig', array(
            'commandes' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new commande entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $commande = new Commande();

        $form = $this->createForm('Mondofute\Bundle\CommandeBundle\Form\CommandeType', $commande);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commande);
            $em->flush();

            $this->copieVersSites($commande);

            $this->addFlash('success', 'Commande créé avec succès.');
            return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
        }

        return $this->render('@MondofuteCommande/commande/new.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param Commande $commande
     */
    function copieVersSites($commande)
    {
        /** @var EntityManager $emSite */
        $emSite = $this->getDoctrine()->getManager($commande->getSite()->getLibelle());
        $commandeSite = $emSite->find(Commande::class, $commande->getId());
        if (empty($commandeSite)) {
            $commandeSite = new Commande();
            $commandeSite->setId($commande->getId());
            $metadata = $emSite->getClassMetadata(get_class($commandeSite));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        }
        $commandeSite
            ->setSite($emSite->find(Site::class, $commande->getSite()))
            ->setDateCommande($commande->getDateCommande())
            ->setNumCommande($commande->getNumCommande());

        // /* *** gestion clients ***
        $this->gestionClientSite($commande, $commandeSite, $emSite);
        // *** fin gestion clients *** */

        // /* *** gestion commande ligne ***
        $this->gestionCommandeLigneSite($commande, $commandeSite, $emSite);
        // *** fin gestion commande ligne *** */

        // /* *** gestion commande ligne prestation annexe sejour ***
        $this->gestionCommandeLignePrestationAnnexeSejourSite($commande, $commandeSite, $emSite);
        // *** gestion commande ligne prestation annexe sejour *** */

        $emSite->persist($commandeSite);
        $emSite->flush();
    }

    /**
     * @param Commande $commande
     * @param Commande $commandeSite
     * @param EntityManager $emSite
     */
    private function gestionClientSite($commande, $commandeSite, $emSite)
    {
        /** @var Client $clientSite */
        /** @var Client $client */
        // suppression clients
        foreach ($commandeSite->getClients() as $clientSite) {
            $client = $commande->getClients()->filter(function (Client $element) use ($clientSite) {
                return $element->getId() == $clientSite->getId();
            })->first();
            if (false === $client) {
                $commandeSite->removeClient($clientSite);
            }
        }
        // ajout clients
        foreach ($commande->getClients() as $client) {
            $clientSite = $commandeSite->getClients()->filter(function (Client $element) use ($client) {
                return $element->getId() == $client->getId();
            })->first();
            if (false === $clientSite) {
                $commandeSite->addClient($emSite->find(Client::class, $client));
            }
        }
    }

    /**
     * @param Commande $commande
     * @param Commande $commandeSite
     * @param EntityManager $emSite
     */
    private function gestionCommandeLigneSite($commande, $commandeSite, $emSite)
    {
        /** @var CommandeLigne $commandeLigneSite */
        /** @var CommandeLigne $commandeLigne */
        // suppression commandeLignes
        foreach ($commandeSite->getCommandeLignes() as $commandeLigneSite) {
            $commandeLigne = $commande->getCommandeLignes()->filter(function (CommandeLigne $element) use ($commandeLigneSite) {
                return $element->getId() == $commandeLigneSite->getId();
            })->first();
            if (false === $commandeLigne) {
                $commandeSite->removeCommandeLigne($commandeLigneSite);
                $emSite->remove($commandeLigneSite);
            }
        }
        // ajout commandeLignes
        foreach ($commande->getCommandeLignes() as $commandeLigne) {
            $commandeLigneSite = $commandeSite->getCommandeLignes()->filter(function (CommandeLigne $element) use ($commandeLigne) {
                return $element->getId() == $commandeLigne->getId();
            })->first();
            if (false === $commandeLigneSite) {
                $oReflectionClass = new ReflectionClass($commandeLigne);
                $ClassParent = $oReflectionClass->getParentClass()->getName();
                if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                    $oReflectionClassParent = new ReflectionClass($ClassParent);
                    $metadata = $emSite->getClassMetadata($oReflectionClassParent->getParentClass()->getName());
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }
                $Class = get_class($commandeLigne);
                $commandeLigneSite = new $Class();
                $commandeLigneSite->setId($commandeLigne->getId());
                $metadata = $emSite->getClassMetadata($ClassParent);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata = $emSite->getClassMetadata($Class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $commandeSite->addCommandeLigne($commandeLigneSite);
            }
//            $commandeLigneSite->setMontant($commandeLigne->getMontant());
            $oReflectionClass = new ReflectionClass($commandeLigneSite);
            switch ($oReflectionClass->getShortName()) {
                case 'SejourPeriode':
                    /** @var SejourPeriode $commandeLigneSite */
                    /** @var SejourPeriode $commandeLigne */
                    $commandeLigneSite
                        ->setPeriode($emSite->find(Periode::class, $commandeLigne->getPeriode()))
                        ->setNbParticipants($commandeLigne->getNbParticipants())
                        ->setLogement($emSite->getRepository(Logement::class)->findOneBy(['logementUnifie' => $commandeLigne->getLogement()->getLogementUnifie()]));
                    break;
                case 'SejourNuite':
                    /** @var SejourNuite $commandeLigneSite */
                    /** @var SejourNuite $commandeLigne */
                    $commandeLigneSite
                        ->setLogement($emSite->getRepository(Logement::class)->findOneBy(['logementUnifie' => $commandeLigne->getLogement()->getLogementUnifie()]));
                    break;
                case 'CommandeLignePrestationAnnexe':
                    /** @var CommandeLignePrestationAnnexe $commandeLigneSite */
                    /** @var CommandeLignePrestationAnnexe $commandeLigne */
                    $commandeLigneSite
                        ->setDateDebut($commandeLigne->getDateDebut())
                        ->setDateFin($commandeLigne->getDateFin())
                        ->setFournisseurPrestationAnnexeParam($emSite->find(FournisseurPrestationAnnexeParam::class, $commandeLigne->getFournisseurPrestationAnnexeParam()));
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param Commande $commande
     * @param Commande $commandeSite
     * @param EntityManager $emSite
     */
    private function gestionCommandeLignePrestationAnnexeSejourSite($commande, $commandeSite, $emSite)
    {
        /** @var CommandeLigne $commandeLigneSite */
        /** @var CommandeLigne $commandeLigne */
        $commandeLignePrestationAnnexeSejourSites = new ArrayCollection();
        $commandeLignePrestationAnnexeSejours = new ArrayCollection();
        foreach ($commandeSite->getCommandeLignes() as $commandeLigneSite) {
            $oReflectionClass = new ReflectionClass($commandeLigneSite);
            if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                /** @var CommandeLigneSejour $commandeLigneSite */
                foreach ($commandeLigneSite->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                    $commandeLignePrestationAnnexeSejourSites->add($commandeLignePrestationAnnex);
                }
            }
        }
        foreach ($commande->getCommandeLignes() as $commandeLigne) {
            $oReflectionClass = new ReflectionClass($commandeLigne);
            if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                /** @var CommandeLigneSejour $commandeLigne */
                foreach ($commandeLigne->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                    $commandeLignePrestationAnnexeSejours->add($commandeLignePrestationAnnex);
                }
            }
        }

        /** @var CommandeLignePrestationAnnexe $commandeLigne */
        /** @var CommandeLignePrestationAnnexe $commandeLigneSite */
        // suppression commandeLignePrestationAnnexeSejours
        foreach ($commandeLignePrestationAnnexeSejourSites as $commandeLigneSite) {
            $commandeLigne = $commandeLignePrestationAnnexeSejours->filter(function (CommandeLignePrestationAnnexe $element) use ($commandeLigneSite) {
                return $element->getId() == $commandeLigneSite->getId();
            })->first();
            if (false === $commandeLigne) {
                $commandeLigneSite->getCommandeLigneSejour()->removeCommandeLignePrestationAnnex($commandeLigneSite);
                $emSite->remove($commandeLigneSite);
            }
        }
        // ajout commandeLignePrestationAnnexeSejours
        foreach ($commandeLignePrestationAnnexeSejours as $commandeLigne) {
            $commandeLigneSite = $commandeLignePrestationAnnexeSejourSites->filter(function (CommandeLignePrestationAnnexe $element) use ($commandeLigne) {
                return $element->getId() == $commandeLigne->getId();
            })->first();
            if (false === $commandeLigneSite) {
                /** @var CommandeLigneSejour $commandeLigneSejourSite */
                $commandeLigneSejourSite = $commandeSite->getCommandeLignes()->filter(function (CommandeLigne $element) use ($commandeLigne) {
                    return $element->getId() == $commandeLigne->getCommandeLigneSejour()->getId();
                })->first();
                $oReflectionClass = new ReflectionClass($commandeLigne);
                $ClassParent = $oReflectionClass->getParentClass()->getName();
                $Class = get_class($commandeLigne);
                $commandeLigneSite = new $Class();
                $commandeLigneSite->setId($commandeLigne->getId());
                $metadata = $emSite->getClassMetadata($ClassParent);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata = $emSite->getClassMetadata($Class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $commandeLigneSejourSite->addCommandeLignePrestationAnnex($commandeLigneSite);
                $commandeLigneSite
                    ->setDateDebut($commandeLigne->getDateDebut())
                    ->setDateFin($commandeLigne->getDateFin());
                dump($commandeLigne->getDateDebut());
                dump($commandeLigne->getDateFin());
                die;
            }
        }
    }

    public function getPeriodesByLogementAction($id)
    {
        /** @var LogementPeriodeLocatif $logementPeriodeLocatif */
        $em = $this->getDoctrine()->getManager();
        $logement = $em->find(Logement::class, $id);
        $logementPeriodeLocatifs = $logement->getLogementPeriodeLocatifs()->filter(function (LogementPeriodeLocatif $element) {
            return $element->getPrixPublic() > 0;
        });
        $periodes = new ArrayCollection();
        foreach ($logementPeriodeLocatifs as $logementPeriodeLocatif) {
            $periodes->add($logementPeriodeLocatif->getPeriode());
        }

        return $this->render('@MondofuteCommande/commande/options_logement_periodes.html.twig', array(
            'periodes' => $periodes,
            'logementPeriodeLocatifsStockNotEmpty' => $logement->getLogementPeriodeLocatifsStockNotEmpty()
        ));
    }

    public function getPrestationAnnexeExterneAction($dateDebut, $dateFin, $fournisseurId, $typeId)
    {
        $em = $this->getDoctrine()->getManager();
        $prestationAnnexeExternes = $em->getRepository(FournisseurPrestationAnnexeParam::class)->findPrestationAnnexeExterne($dateDebut, $dateFin, $fournisseurId, $typeId);

        return $this->render('@MondofuteCommande/commande/options_prestation_annexe_externe.html.twig', array(
            'prestationAnnexeExternes' => $prestationAnnexeExternes
        ));
    }

    public function getFournisseurPrestationAnnexeExterneAction($dateDebut, $dateFin, $stationId, $typeId)
    {
        $em = $this->getDoctrine()->getManager();
        $fournisseurForPrestationAnnexeExternes = $em->getRepository(Fournisseur::class)->findFournisseurForPrestationAnnexeExterne($dateDebut, $dateFin, $stationId, $typeId);

        return $this->render('@MondofuteCommande/commande/options_fournisseur_prestation_annexe.html.twig', array(
            'fournisseurForPrestationAnnexeExternes' => $fournisseurForPrestationAnnexeExternes
        ));
    }

    /**
     * Finds and displays a commande entity.
     *
     */
    public function showAction(Commande $commande)
    {
        $deleteForm = $this->createDeleteForm($commande);

        return $this->render('@MondofuteCommande/commande/show.html.twig', array(
            'commande' => $commande,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a commande entity.
     *
     * @param Commande $commande The commande entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commande $commande)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commande_delete', array('id' => $commande->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     */
    public function editAction(Request $request, Commande $commande)
    {
        $deleteForm = $this->createDeleteForm($commande);
        $form = $this->createForm('Mondofute\Bundle\CommandeBundle\Form\CommandeType', $commande)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $originalCommandeLignes = new ArrayCollection();
        $originalCommandeLignePrestationAnnexeSejours = new ArrayCollection();
        /** @var CommandeLigne $commandeLigne */
        foreach ($commande->getCommandeLignes() as $commandeLigne) {
            $originalCommandeLignes->add($commandeLigne);
            $oReflectionClass = new ReflectionClass($commandeLigne);
            if ($oReflectionClass->getParentClass()->getShortName() == 'CommandeLigneSejour') {
                /** @var CommandeLigneSejour $commandeLigne */
                /** @var CommandeLignePrestationAnnexe $commandeLignePrestationAnnex */
                foreach ($commandeLigne->getCommandeLignePrestationAnnexes() as $commandeLignePrestationAnnex) {
                    if (empty($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId()))) {
                        $originalCommandeLignePrestationAnnexeSejours->set($commandeLigne->getId(), new ArrayCollection());
                    }
                    $originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId())->add($commandeLignePrestationAnnex);
                }
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($originalCommandeLignes as $originalCommandeLigne) {
                if (false === $commande->getCommandeLignes()->contains($originalCommandeLigne)) {
                    $em->remove($originalCommandeLigne);
                } else {
                    foreach ($commande->getCommandeLignes() as $commandeLigne) {
                        if (!empty($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId()))) {
                            foreach ($originalCommandeLignePrestationAnnexeSejours->get($commandeLigne->getId()) as $originalCommandeLignePrestationAnnexeSejour) {
                                if (false === $commandeLigne->getCommandeLignePrestationAnnexes()->contains($originalCommandeLignePrestationAnnexeSejour)) {
                                    $em->remove($originalCommandeLignePrestationAnnexeSejour);
                                }
                            }
                        }
                    }
                }
            }

            $em->flush();

            $this->copieVersSites($commande);

            $this->addFlash('success', 'Commande modifiée avec succès.');

            return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
        }

        return $this->render('@MondofuteCommande/commande/edit.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a commande entity.
     *
     */
    public function deleteAction(Request $request, Commande $commande)
    {
        /** @var Site $site */
        $form = $this->createDeleteForm($commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $commandeSite = $emSite->find(Commande::class, $commande->getId());
                    if (!empty($commandeSite)) {
                        $emSite->remove($commandeSite);
                        $emSite->flush();
                    }
                }

                $em->remove($commande);
                $em->flush();

                $this->addFlash('success', 'Commande supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'la commande est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('commande_index');
    }
}
