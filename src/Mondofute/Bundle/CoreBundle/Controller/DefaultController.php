<?php

namespace Mondofute\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCoreBundle:Default:index.html.twig');
    }
}
