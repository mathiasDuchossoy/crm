<?php

namespace Mondofute\Bundle\CoupDeCoeurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCoupDeCoeurBundle:Default:index.html.twig');
    }
}
