<?php

namespace Mondofute\Bundle\PeriodeBundle\Controller;

use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TypePeriodeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function listeTypePeriodesAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $listeTypePeriodes = $em->getRepository(TypePeriode::class)->findAllArray();
            return new JsonResponse($listeTypePeriodes);
        }
    }

    public function chargerOngletsTypePeriodesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listeTypePeriodes = $em->getRepository(TypePeriode::class)->findAll();
//        $typePeriodes = $request->get('typePeriodes');
//        dump($typePeriodes);
        return $this->render('@MondofuteHebergement/hebergementunifie/onglets_type_periode.html.twig',
            array('typePeriodes' => $listeTypePeriodes, 'conteneur' => $request->get('idConteneur')));
    }
}
