<?php

namespace Mondofute\Bundle\DecoteBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteHebergement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecoteHebergementCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_decote:decote_hebergement_command')
            ->addArgument('hebergementUnifieId', InputArgument::REQUIRED, 'L\'id de l\'hébergementUnifie.')
            ->setDescription('création decoteHebergement');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Decote $decote */
        /** @var Hebergement $hebergement */
        /** @var EntityManager $em */
        /** @var EntityManager $emSite */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $hebergementUnifieId = $input->getArgument('hebergementUnifieId');
//        $fournisseurId = $input->getArgument('fournisseurId');
//        $typeAffectation = TypeAffectation::logement;

        $sites = $em->getRepository(Site::class)->findAll();

        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $hebergementUnifie = $emSite->find(HebergementUnifie::class, $hebergementUnifieId);

            foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                /** @var FournisseurHebergement $fournisseur */
                foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
                    // récuperer les decote des decoteStation enf foncton du fournisseur et de la station
                    $decotes = $emSite->getRepository('MondofuteDecoteBundle:Decote')->findByDecoteStations($hebergement->getStation()->getId(), $fournisseur->getFournisseur()->getId());
                    // on parcourt les decotes
                    foreach ($decotes as $decote) {
                        // on essaye de récupérer la decoteHebergement en fonction du fournisseur et de l'hébergement
                        foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                            $decoteHebergement = $hebergement->getDecoteHebergements()->filter(function (DecoteHebergement $element) use ($decote, $hebergement, $fournisseur) {
                                return $element->getHebergement() == $hebergement and $element->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId() and $element->getDecote()->getDecoteUnifie() == $decote->getDecoteUnifie();
                            })->first();
                            if (false === $decoteHebergement) {
                                $decoteHebergement = new DecoteHebergement();
                                $hebergement->addDecoteHebergement($decoteHebergement);
                                $decoteHebergement
                                    ->setDecote($decote->getDecoteUnifie()->getDecotes()->filter(function (Decote $element) use ($hebergement) {
                                        return $hebergement->getSite() == $element->getSite();
                                    })->first())
                                    ->setFournisseur($fournisseur->getFournisseur());
                                $emSite->persist($decoteHebergement);
                            }
                        }
                    }
                }
            }
            $emSite->flush();
        }
    }
}
