<?php

namespace Mondofute\Bundle\UniteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteUniteBundle:Default:index.html.twig');
    }
}
