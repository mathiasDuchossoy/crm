<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Controller;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeStockFournisseur;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FournisseurPrestationAnnexeStockFournisseurController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function chargerStockFournisseurAction($idFournisseurPrestationAnnexe, $idTypePeriode)
    {
        $em = $this->getDoctrine();
        $stocks = $em->getRepository(FournisseurPrestationAnnexeStockFournisseur::class)->charger($idFournisseurPrestationAnnexe,
            $idTypePeriode);
        return new JsonResponse(['stocks' => $stocks]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function enregistrerStockFournisseurAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $retour = array('valid' => true);
            try {
                $mbdd = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
                $em = $this->get('doctrine.orm.entity_manager');
//                $em->
                $stocks = $request->get('stocks');
                $sites = $em->getRepository(Site::class)->findAll();
                /** @var Site $site */
                foreach ($sites as $site) {
                    $table = 'fournisseur_prestation_annexe_stock_fournisseur';
                    $champs = array(
                        'fournisseur_prestation_annexe_id',
                        'periode_id',
                        'stock'
                    );
                    $duplicate = true;
                    $managers = array($site->getLibelle());
                    $mbdd->initInsertMassif($table, $champs, $duplicate, $managers);
                    $mbdd->initDeleteMassif($table, $managers);
                    foreach ($stocks as $stock) {
                        foreach ($stock['periodes'] as $periode) {
                            if (intval($periode['stock'], 10) > 0) {
                                $mbdd->addInsertLigne(array(
                                    $stock['fournisseurPrestationAnnexe'],
                                    $periode['id'],
                                    $periode['stock']
                                ));
                            } else {
                                $mbdd->addDeleteLigne(array(
                                    array('fournisseur_prestation_annexe_id', $stock['fournisseurPrestationAnnexe']),
                                    array('periode_id', $periode['id']),
                                ));
                            }
                        }
                    }
                    $mbdd->insertMassif();
                    $mbdd->deleteMassif();
                }
            } catch (\Exception $exception) {
                $retour['valid'] = false;
                return new JsonResponse($retour);
            }
            return new JsonResponse($retour);
        }
    }

    public function listeStocksFournisseurAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $typePeriodes = $em->getRepository(TypePeriode::class)->findAllFutur();
//        dump($request->get('idFournisseur'));
        $prestationAnnexes = $em->getRepository('MondofuteFournisseurPrestationAnnexeBundle:FournisseurPrestationAnnexe')->findByFamillePrestationAnnexe($request->get('idFournisseur'),
            $request->get('idFamillePrestationAnnexe'), $request->getLocale());
//        $prestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->findAll();
//        $prestationAnnexes = $em->getRepository(FournisseurPrestationAnnexe::class)->findBy(array('fournisseur'=>intval($request->get('idFournisseur',10)),'prestationAnnexe.famillePrestationAnnexe'=>intval($request->get('idFamillePrestationAnnexe'),10)));
//        dump($prestationAnnexes);
//        die;
        return $this->render('@MondofuteFournisseurPrestationAnnexe/template-fournisseur-prestation-annexe-stocks-fournisseur.html.twig',
            array('fournisseurPrestationAnnexes' => $prestationAnnexes, 'typePeriodes' => $typePeriodes)
        );
    }

}
