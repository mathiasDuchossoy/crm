<?php

namespace Mondofute\Bundle\AccueilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        dump($em->getRepository('MondofuteSiteBundle:Site')->findAll());die;
        return $this->render('MondofuteAccueilBundle:Default:index.html.twig');
    }
}
