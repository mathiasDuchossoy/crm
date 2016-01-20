<?php

namespace Mondofute\Bundle\AccueilBundle\Controller;

use Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie;
use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteAccueilBundle:Default:index.html.twig');
    }
}
