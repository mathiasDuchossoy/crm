<?php

namespace Mondofute\Bundle\LangueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteLangueBundle:Default:index.html.twig');
    }
}
