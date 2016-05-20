<?php

namespace Mondofute\Bundle\AccueilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
//        dump($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN'));
////        if ($user->hasRole('ROLE_ADMIN')) {
////            $this->authenticateUser($user, $response);
////        } else {
////            throw new AccessDeniedException ('Oups !!! Access denied ' ) ;
////        }
//        die;
        return $this->render('MondofuteAccueilBundle:Default:index.html.twig');
    }
}
