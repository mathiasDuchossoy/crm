<?php

namespace Mondofute\Bundle\ChoixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteChoixBundle:Default:index.html.twig');
    }
}
