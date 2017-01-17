<?php

namespace Mondofute\Bundle\CommentaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteCommentaireBundle:Default:index.html.twig');
    }
}
