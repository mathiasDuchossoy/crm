<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteFournisseurPrestationAnnexeBundle:Default:index.html.twig');
    }
}
