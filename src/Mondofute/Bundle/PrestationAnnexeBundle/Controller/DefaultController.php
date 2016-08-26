<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofutePrestationAnnexeBundle:Default:index.html.twig');
    }
}
