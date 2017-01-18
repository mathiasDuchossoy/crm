<?php

namespace Mondofute\Bundle\SaisonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteSaisonBundle:Default:index.html.twig');
    }
}
