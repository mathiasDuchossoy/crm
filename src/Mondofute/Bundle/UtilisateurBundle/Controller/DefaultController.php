<?php

namespace Mondofute\Bundle\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteUtilisateurBundle:Default:index.html.twig');
    }
}
