<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteDescriptionForfaitSkiBundle:Default:index.html.twig');
    }
}
