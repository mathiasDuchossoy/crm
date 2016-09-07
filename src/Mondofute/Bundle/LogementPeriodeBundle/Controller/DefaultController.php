<?php

namespace Mondofute\Bundle\LogementPeriodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteLogementPeriodeBundle:Default:index.html.twig');
    }
}
