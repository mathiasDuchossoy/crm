<?php

namespace HiDev\Bundle\AuteurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HiDevAuteurBundle:Default:index.html.twig');
    }
}
