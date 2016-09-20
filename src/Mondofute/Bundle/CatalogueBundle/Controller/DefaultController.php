<?php

namespace Mondofute\Bundle\CatalogueBundle\Controller;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCatalogueBundle:Default:index.html.twig');
    }
    public function exception(){
        throw new \Exception('oups');
    }
    public function enregistrerStocksLocatifAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $retour = array('valid'=>true);
            try{
                $this->exception();
                $mbdd = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
                $em = $this->get('doctrine.orm.entity_manager');
                $stocks = $request->get('stocks');
                $sites = $em->getRepository(Site::class)->findAll();
                foreach ($sites as $site) {
                    $table = 'logement_periode_locatif';
                    $champs = array('logement_id','periode_id','stock');
                    $duplicate = true;
                    $managers = array($site->getLibelle());
                    $mbdd->initInsertMassif($table,$champs,$duplicate,$managers);
                    foreach ($stocks as $idLogementUnifie => $periodes) {
                        /** @var EntityManager $emSite */
                        $emSite = $this->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
                        $logements = $emSite->getRepository(Logement::class)->findBy(array('logementUnifie' => $idLogementUnifie));
                        foreach ($logements as $logement) {
                            foreach ($periodes as $idPeriode => $stock){
                                $mbdd->addInsertLigne(array($logement->getId(),$idPeriode,$stock));
                            }
                        }
                    }
                    $mbdd->insertMassif();
                }
            }catch (\Exception $exception){
                $retour['valid'] = false;
                return new JsonResponse($retour);
            }

            return new JsonResponse($retour);
        } else {
            return new Response();
        }
    }
}
