<?php

namespace Mondofute\Bundle\MotClefBundle\Controller;

use Mondofute\Bundle\MotClefBundle\Entity\MotClefTraduction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MotClefController extends Controller
{
    public function getmotclefslikeAction(Request $request)
    {
        $like = $request->get('q');
        $langue = $request->get('langue');
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(MotClefTraduction::class)->findByLike($like, $langue);

        return new Response(json_encode($data));
    }
}
