<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteGeographieBundle:Default:index.html.twig');
    }
}
