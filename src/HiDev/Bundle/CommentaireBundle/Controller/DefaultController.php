<?php

namespace HiDev\Bundle\CommentaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HiDevCommentaireBundle:Default:index.html.twig');
    }
}
