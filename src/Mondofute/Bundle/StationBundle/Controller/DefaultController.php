<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteStationBundle:Default:index.html.twig');
    }
}
