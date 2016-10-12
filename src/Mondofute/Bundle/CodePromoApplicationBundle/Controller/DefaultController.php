<?php

namespace Mondofute\Bundle\CodePromoApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCodePromoApplicationBundle:Default:index.html.twig');
    }
}
