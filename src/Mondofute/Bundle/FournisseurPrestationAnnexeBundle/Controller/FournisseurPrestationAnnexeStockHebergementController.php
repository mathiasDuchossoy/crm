<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Controller;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeStockHebergement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FournisseurPrestationAnnexeStockHebergementController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function chargerStockHebergementAction($idPrestationAnnexe, $idFournisseurHebergement, $idTypePeriode)
    {
        $em = $this->getDoctrine();
        $stocks = $em->getRepository(FournisseurPrestationAnnexeStockHebergement::class)->charger($idFournisseurHebergement,
            $idPrestationAnnexe, $idTypePeriode);
        return new JsonResponse(['stocks' => $stocks]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function enregistrerStockHebergementAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $retour = array('valid' => true);
            try {
                $mbdd = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
                $em = $this->get('doctrine.orm.entity_manager');
//                $em->
                $stocks = $request->get('stocks');
                $sites = $em->getRepository(Site::class)->findAll();
                foreach ($sites as $site) {
                    $table = 'fournisseur_prestation_annexe_stock_hebergement';
                    $champs = array(
                        'fournisseur_prestation_annexe_id',
                        'periode_id',
                        'fournisseur_hebergement_id',
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
                                    $stock['fournisseurHebergement'],
                                    $periode['stock']
                                ));
                            } else {
                                $mbdd->addDeleteLigne(array(
                                    array(
                                        'fournisseur_prestation_annexe_id',
                                        $stock['fournisseurPrestationAnnexe']
                                    ),
                                    array('periode_id', $periode['id']),
                                    array('fournisseur_hebergement_id', $stock['fournisseurHebergement'])
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
}
