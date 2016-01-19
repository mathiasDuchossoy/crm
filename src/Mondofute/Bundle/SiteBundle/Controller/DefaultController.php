<?php

namespace Mondofute\Bundle\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteSiteBundle:Default:index.html.twig');
    }
}
