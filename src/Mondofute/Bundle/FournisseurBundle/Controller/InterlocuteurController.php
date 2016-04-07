<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InterlocuteurController extends Controller
{
    public function newAction()
    {

//        return $this->render('MondofuteFournisseurBundle:Interlocuteur:new.html.twig', array(
//            // ...
//        ));
    }

    public function editAction()
    {
        return $this->render('MondofuteFournisseurBundle:Interlocuteur:edit.html.twig', array(// ...
        ));
    }

}
