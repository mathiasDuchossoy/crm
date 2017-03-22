<?php

namespace Mondofute\Bundle\PasserelleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofutePasserelleBundle:Default:index.html.twig');
    }
}
