<?php

namespace Mondofute\Bundle\CatalogueBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TarifLocatifController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function enregistrerTarifsLocatifAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $retour = array('valid' => false);
            try {
                $logementUnifieId = intval($request->get('logementUnifieId'), 10);
                if ($logementUnifieId > 0) {
                    $tarifs = $request->get('tarifs');
                    if (count($tarifs) > 0) {
                        $em = $this->getDoctrine()->getManager();
                        $sites = $em->getRepository(Site::class)->findAll();

                        $champs = array(
                            'logement_id',
                            'periode_id',
                            'stock',
                            'prix_catalogue',
                            'prix_public',
                            'com_mondofute',
                            'prix_achat'
                        );
                        $table = 'logement_periode_locatif';
                        $duplicate = true;
                        foreach ($sites as $site) {
                            $manager = array($site->getLibelle());
                            $mbdd = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
                            $mbdd->initInsertMassif('logement_periode_locatif', $champs, $duplicate, $manager);
                            $em = $this->getDoctrine()->getManager($site->getLibelle());
                            $logements = $em->getRepository(Logement::class)->findBy(array('logementUnifie' => $logementUnifieId));
                            foreach ($logements as $logement) {
                                foreach ($tarifs as $tarif) {
                                    if ($tarif != '') {
//                                        dump($tarif['prixPublic']);die;
                                        $mbdd->addInsertLigne(array(
                                            $logement->getId(),
                                            $tarif['periodeId'],
                                            $tarif['stock'],
                                            $tarif['prixCatalogue'],
                                            $tarif['prixPublic'],
                                            $tarif['comMondofute'],
                                            $tarif['prixAchat']
                                        ));
                                    }
                                }
                            }
                            $mbdd->insertMassif();
                        }
                        $retour['valid'] = true;
                    }
                } else {
                    $retour['message'] = 'erreur sur l\'identifiant du logement' . $logementUnifieId;
                }
                return new JsonResponse($retour);
            } catch (\Exception $except) {
                return new JsonResponse($retour);
            }


        }
    }

    public function getByDatesAction($logementId, $dateDebut, $dateFin)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $logementPeriodeLocatifs = $em->getRepository(LogementPeriodeLocatif::class)->getByDates($logementId, $dateDebut, $dateFin);

        return $this->render('@MondofuteCatalogue/logementperiodelocatif/options-logement-periode-locatif.html.twig', array(
            'logementPeriodeLocatifs' => $logementPeriodeLocatifs,
            'dateDebut' => new DateTime(date($dateDebut)),
            'dateFin' => new DateTime(date($dateFin)),
        ));
    }


    public function getForCommandeLigneSejourAction($logementId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $periodes = $em->getRepository(LogementPeriodeLocatif::class)->getForCommandeLigneSejour($logementId);

        return $this->render('@MondofuteCatalogue/logementperiodelocatif/option-periodes-for-commande-ligne-sejour.html.twig', array(
            'periodes' => $periodes
        ));
    }


}
