<?php

namespace Mondofute\Bundle\MotClefBundle\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
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
        $data = $em->getRepository(MotClef::class)->findByLike($like, $langue);

        return new Response(json_encode($data));
    }

    public function ajaxNewAction(Request $request)
    {
        $motClefText = $request->get('value');
        $langue = $request->get('langue');
        $em = $this->getDoctrine()->getManager();

        $motClef = new MotClef();
        $motClef
            ->setLibelle($motClefText)
            ->setLangue($em->find(Langue::class, $langue))
        ;
        $em->persist($motClef);
        $em->flush();

        $sites = $em->getRepository(Site::class)->findBy(['crm' => 0]);
        foreach ($sites as $site)
        {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $motClefSite = new MotClef();
            $motClefSite
                ->setId($motClef->getId())
                ->setLibelle($motClefText)
                ->setLangue($emSite->find(Langue::class, $langue))
            ;

            $metadata = $emSite->getClassMetadata(get_class($motClefSite));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $emSite->persist($motClefSite);

            $emSite->flush();
        }

        return new Response($motClef->getId());
    }

}
