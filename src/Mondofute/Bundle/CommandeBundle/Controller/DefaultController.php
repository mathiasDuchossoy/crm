<?php

namespace Mondofute\Bundle\CommandeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCommandeBundle:Default:index.html.twig');
    }
}
