<?php

namespace HiDev\Bundle\CodePromoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HiDevCodePromoBundle:Default:index.html.twig');
    }
}
