<?php

namespace Mondofute\Bundle\CatalogueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCatalogueBundle:Default:index.html.twig');
    }
}
