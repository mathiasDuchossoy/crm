<?php

namespace Mondofute\Bundle\DecoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteDecoteBundle:Default:index.html.twig');
    }
}
