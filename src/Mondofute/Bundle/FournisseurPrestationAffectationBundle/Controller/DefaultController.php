<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteFournisseurPrestationAffectationBundle:Default:index.html.twig');
    }
}
