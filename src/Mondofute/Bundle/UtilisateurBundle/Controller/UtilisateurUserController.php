<?php

namespace Mondofute\Bundle\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UtilisateurUserController extends Controller
{
    public function registerAction()
    {
        return $this->container
            ->get('pugx_multi_user.registration_manager')
            ->register('Mondofute\Bundle\UtilisateurBundle\Entity\UtilisateurUser');
//        return $this->render('MondofuteUtilisateurBundle:UtilisateurUser:register.html.twig', array(
//            // ...
//        ));
    }

}
