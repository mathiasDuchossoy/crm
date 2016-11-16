<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Controller;

use Buzz\Message\Request;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class FournisseurPrestationAnnexeStockHebergementController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function chargerStockHebergementAction($idPrestationAnnexe,$idHebergement,$idTypePeriode){
        return new JsonResponse();
    }
}
