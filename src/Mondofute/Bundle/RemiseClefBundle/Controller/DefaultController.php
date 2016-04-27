<?php

namespace Mondofute\Bundle\RemiseClefBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteRemiseClefBundle:Default:index.html.twig');
    }
}
