<?php

namespace Mondofute\Bundle\PromotionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofutePromotionBundle:Default:index.html.twig');
    }
}
