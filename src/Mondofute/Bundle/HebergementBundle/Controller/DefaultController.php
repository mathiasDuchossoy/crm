<?php

namespace Mondofute\Bundle\HebergementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteHebergementBundle:Default:index.html.twig');
    }
}
