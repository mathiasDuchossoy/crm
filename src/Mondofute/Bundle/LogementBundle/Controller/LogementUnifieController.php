<?php

namespace Mondofute\Bundle\LogementBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementBundle\Form\LogementUnifieType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * LogementUnifie controller.
 *
 */
class LogementUnifieController extends Controller
{
    /**
     * Lists all LogementUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $logementUnifies = $em->getRepository('MondofuteLogementBundle:LogementUnifie')->findAll();

        return $this->render('@MondofuteLogement/logementunifie/index.html.twig', array(
            'logementUnifies' => $logementUnifies,
        ));
    }

    /**
     * Creates a new LogementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $logementUnifie = new LogementUnifie();

        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);

        $form = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($logementUnifie);
            $em->flush();

            return $this->redirectToRoute('logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'logementUnifie' => $logementUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les logements qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param LogementUnifie $entity
     */
    private function ajouterLogementsDansForm(LogementUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entity->getLogements() as $logement) {
                if ($logement->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($logement->getTraductions()->filter(function (LogementTraduction $element) use ($langue) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new LogementsTraduction();
                            $traduction->setLangue($langue);
                            $logement->addTraduction($traduction);
                        }
                    }
                }
            }
            if (!$siteExiste) {
                $logement = new Logement();
                $logement->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new LogementTraduction();
                    $traduction->setLangue($langue);
                    $logement->addTraduction($traduction);
                }
                $entity->addLogement($logement);
            }
        }
    }

    /**
     * Classe les departements par classementAffichage
     * @param LogementUnifie $entity
     */
    private function logementsSortByAffichage(LogementUnifie $entity)
    {
        /** @var ArrayIterator $iterator */

        // Trier les stations en fonction de leurs ordre d'affichage
        $logements = $entity->getLogements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $logements->getIterator();
        unset($departements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Logement $a, Logement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $logements = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($logements);

        // remplacé les stations par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entity->setLogements($logements);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $logements
     */
    private function traductionsSortByLangue($logements)
    {
        /** @var ArrayIterator $iterator */
        /** @var Logement $logement */
        foreach ($logements as $logement) {
            $traductions = $logement->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (LogementTraduction $a, LogementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $logement->setTraductions($traductions);
        }
    }

    /**
     * Finds and displays a LogementUnifie entity.
     *
     */
    public function showAction(LogementUnifie $logementUnifie)
    {
        $deleteForm = $this->createDeleteForm($logementUnifie);

        return $this->render('@MondofuteLogement/logementunifie/show.html.twig', array(
            'logementUnifie' => $logementUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a LogementUnifie entity.
     *
     * @param LogementUnifie $logementUnifie The LogementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LogementUnifie $logementUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('logement_logement_delete', array('id' => $logementUnifie->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing LogementUnifie entity.
     *
     */
    public function editAction(Request $request, LogementUnifie $logementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        //        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($logementUnifie->getLogements() as $logement) {
                array_push($sitesAEnregistrer, $logement->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }
        $originalLogements = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        foreach ($logementUnifie->getLogements() as $logement) {
            $originalLogements->add($logement);
        }
        $this->ajouterLogementsDansForm($logementUnifie);
        $this->logementsSortByAffichage($logementUnifie);

        $deleteForm = $this->createDeleteForm($logementUnifie);
        $editForm = $this->createForm('Mondofute\Bundle\LogementBundle\Form\LogementUnifieType', $logementUnifie,
            array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->supprimerLogements($logementUnifie, $sitesAEnregistrer);
            // Supprimer la relation entre la station et stationUnifie
            foreach ($originalLogements as $logement) {
                if (!$logementUnifie->getLogements()->contains($logement)) {

//                    //  suppression de la station sur le site
//                    $emSite = $this->getDoctrine()->getEntityManager($logement->getSite()->getLibelle());
//                    $entitySite = $emSite->find(DepartementUnifie::class, $logementUnifie->getId());
//                    $departementSite = $entitySite->getDepartements()->first();
//                    $emSite->remove($departementSite);
//
//                    $emSite->flush();
////                    dump($departement);
//                    $departement->setDepartementUnifie(null);
                    $em->remove($logement);
                }
            }
            $em->persist($logementUnifie);
            $em->flush();

            return $this->redirectToRoute('logement_logement_edit', array('id' => $logementUnifie->getId()));
        }

        return $this->render('@MondofuteLogement/logementunifie/edit.html.twig', array(
            'logementUnifie' => $logementUnifie,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
        ));
    }

    /**
     * retirer de l'entité les departements qui ne doivent pas être enregistrer
     * @param LogementUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerLogements(LogementUnifie $entity, array $sitesAEnregistrer)
    {
        /** @var Logement $logement */
        foreach ($entity->getLogements() as $logement) {
            if (!in_array($logement->getSite()->getId(), $sitesAEnregistrer)) {
                $entity->removeLogement($logement);
            }
        }
        return $this;
    }

    /**
     * Deletes a LogementUnifie entity.
     *
     */
    public function deleteAction(Request $request, LogementUnifie $logementUnifie)
    {
        $form = $this->createDeleteForm($logementUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($logementUnifie);
            $em->flush();
        }

        return $this->redirectToRoute('logement_logement_index');
    }
}
