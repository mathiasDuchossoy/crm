<?php

namespace Mondofute\Bundle\UniteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
//        foreach ($this->get('doctrine.orm.crm_entity_manager')->getClassMetadata(Unite::class)->subClasses as $key => $typeHebergement) {
//            $type = (new \ReflectionClass($typeHebergement))->getShortName();
//            print_r($type . PHP_EOL);
//        }
        return $this->render('MondofuteUniteBundle:Default:index.html.twig');
    }
}
