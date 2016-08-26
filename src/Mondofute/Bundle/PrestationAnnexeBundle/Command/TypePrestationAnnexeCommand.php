<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 17/03/2016
 * Time: 08:16
 */

namespace Mondofute\Bundle\PrestationAnnexeBundle\Command;


use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\TypePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\TypePrestationAnnexeTraduction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TypePrestationAnnexeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestationAnnexe:TypePrestationAnnexeInit')
            ->setDescription('Initialisation des types de prestation annexe');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Langue $langue */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $fr    = $em->find(Langue::class , 1);
        $en    = $em->find(Langue::class , 2);

        $typePrestationAnnexes              = new ArrayCollection();

        // *** RM (remontée mécanique) ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('remontée mécanique');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('ski lift');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin RM (remontée mécanique) ***
        // *** LM (location matériel de ski) ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Location matériel de ski');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('Snowboard rental');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin LM (location matériel de ski) ***
        // *** ESF (ecole ski française) ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Ecole ski française');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('French ski school');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin ESF (ecole ski française) ***
        // *** ASSURANCES ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Assurances');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('Insurance');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin ASSURANCES ***
        // *** SERVICES HOTELIERS ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Services hôteliers');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('Hotel services');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin SERVICES HOTELIERS ***
        // *** POUR VOS ENFANTS ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Vos enfants');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('Your children');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin POUR VOS ENFANTS ***
        // *** MASSAGES & BIEN ETRE ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Massages & bien être');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('Massage & Wellness');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin MASSAGES & BIEN ETRE ***
        // *** RESTAURATION ***
        $typePrestationAnnexe = new TypePrestationAnnexe();
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($fr);
        $typePrestationAnnexeTraduction->setLibelle('Restauration');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexeTraduction = new TypePrestationAnnexeTraduction();
        $typePrestationAnnexeTraduction->setLangue($en);
        $typePrestationAnnexeTraduction->setLibelle('Restoration');
        $typePrestationAnnexe->addTraduction($typePrestationAnnexeTraduction);
        $typePrestationAnnexes->add($typePrestationAnnexe);
        // *** Fin RESTAURATION ***

        // ***** ENREGISTREMENT *****
        $sites      = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
        foreach ($sites as $site) {
            $emSite         = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());

            /** @var TypePrestationAnnexe $typePrestationAnnexe */
            foreach ($typePrestationAnnexes as $typePrestationAnnexe){
                /** @var TypePrestationAnnexeTraduction $traduction */
                foreach ($typePrestationAnnexe->getTraductions() as $traduction){
                    $traduction->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                }
                $emSite->persist($typePrestationAnnexe);
            }
            $emSite->flush();
        }
        // ***** FIN ENREGISTREMENT *****

        echo PHP_EOL . "l'initialisation des types de prestation annexe est terminé.";
    }
}