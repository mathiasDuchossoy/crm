<?php

namespace Mondofute\Bundle\MotClefBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteMotClefBundle:Default:index.html.twig');
    }
}
