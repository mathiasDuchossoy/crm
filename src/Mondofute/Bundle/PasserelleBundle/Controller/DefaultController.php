<?php

namespace Mondofute\Bundle\PasserelleBundle\Controller;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PasserelleBundle\Entity\Anite;
use Mondofute\Bundle\PasserelleBundle\Entity\Pass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(Fournisseur $fournisseur)
    {
        $passerelleFactory = $this->container->get('mondofute_passerelle_factory');
        $em = $this->getDoctrine()->getManager();

        $param = new Anite();
        $param->setParam1('666');
        $param->setParam2('999');
        $fournisseur->setPasserelle($param);
        $em->persist($fournisseur);
        $em->flush();

        $em = $this->getDoctrine()->getManager();
        $passerelles = $em->getRepository(Pass::class)->findAll();
//        dump($passerelles);die;
//        dump($passerelleFactory);die;
        $passerelleFactory::init($fournisseur);
        return $this->render('MondofutePasserelleBundle:Default:index.html.twig',
            array('passerelles' => $passerelles, 'fournisseur' => $fournisseur));
    }
}
