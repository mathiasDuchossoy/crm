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
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexeTraduction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class FamillePrestationAnnexeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestationAnnexe:FamillePrestationAnnexeInit')
            ->setDescription('Initialisation des familles de prestation annexe');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Langue $langue */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $fr    = $em->find(Langue::class , 1);
        $en    = $em->find(Langue::class , 2);

        $famillePrestationAnnexes              = new ArrayCollection();

        // *** RM (remontée mécanique) ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('remontée mécanique');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('ski lift');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin RM (remontée mécanique) ***
        // *** LM (location matériel de ski) ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Location matériel de ski');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Snowboard rental');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin LM (location matériel de ski) ***
        // *** ESF (ecole ski française) ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Ecole ski française');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('French ski school');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin ESF (ecole ski française) ***
        // *** ASSURANCES ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Assurances');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Insurance');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin ASSURANCES ***
        // *** SERVICES HOTELIERS ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Services hôteliers');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Hotel services');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin SERVICES HOTELIERS ***
        // *** POUR VOS ENFANTS ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Vos enfants');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Your children');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin POUR VOS ENFANTS ***
        // *** MASSAGES & BIEN ETRE ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Massages & bien être');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Massage & Wellness');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin MASSAGES & BIEN ETRE ***
        // *** RESTAURATION ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Restauration');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Restoration');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin RESTAURATION ***
        // *** HEBERGEMENT ***
        $famillePrestationAnnexe = new FamillePrestationAnnexe();
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($fr);
        $famillePrestationAnnexeTraduction->setLibelle('Hébergement');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexeTraduction = new FamillePrestationAnnexeTraduction();
        $famillePrestationAnnexeTraduction->setLangue($en);
        $famillePrestationAnnexeTraduction->setLibelle('Accommodation');
        $famillePrestationAnnexe->addTraduction($famillePrestationAnnexeTraduction);
        $famillePrestationAnnexes->add($famillePrestationAnnexe);
        // *** Fin HEBERGEMENT ***

        // ***** ENREGISTREMENT *****
        $sites      = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
        foreach ($sites as $site) {
            $emSite         = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());

            /** @var FamillePrestationAnnexe $famillePrestationAnnexe */
            foreach ($famillePrestationAnnexes as $famillePrestationAnnexe){
                /** @var FamillePrestationAnnexeTraduction $traduction */
                foreach ($famillePrestationAnnexe->getTraductions() as $traduction){
                    $traduction->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
                }
                $emSite->persist($famillePrestationAnnexe);
            }
            $emSite->flush();
        }
        // ***** FIN ENREGISTREMENT *****

        echo PHP_EOL . "l'initialisation des familles de prestation annexe est terminé.";
    }
}