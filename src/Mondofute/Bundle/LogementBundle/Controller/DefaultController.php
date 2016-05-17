<?php

namespace Mondofute\Bundle\LogementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteLogementBundle:Default:index.html.twig');
    }
}
