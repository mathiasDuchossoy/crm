<?php

namespace Mondofute\Bundle\DecoteBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteStation;
use Mondofute\Bundle\DecoteBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecoteStationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_decote:decote_station_command')
            ->addArgument('hebergementUnifieId', InputArgument::REQUIRED, 'L\'id de l\'hébergementUnifie.')
            ->addArgument('fournisseurId', InputArgument::REQUIRED, 'L\'id du fournisseur.')
            ->setDescription('Hello PhpStorm');
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
        $fournisseurId = $input->getArgument('fournisseurId');
        $typeAffectation = TypeAffectation::logement;

        $sites = $em->getRepository(Site::class)->findAll();

        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $hebergementUnifie = $emSite->find(HebergementUnifie::class, $hebergementUnifieId);
            $fournisseur = $emSite->find(Fournisseur::class, $fournisseurId);
            // récuperer les decote des decotefournisseur de type logement
            $decotes = $emSite->getRepository('MondofuteDecoteBundle:Decote')->findByFournisseurAndAffectation($fournisseurId, $typeAffectation);
            // on parcourt les decotes
            foreach ($decotes as $decote) {
                // on essaye de récupérer la decoteStation en fonction du fournisseur et de la station de chaque hébergement
                foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                    $station = $hebergement->getStation();
                    $decoteStation = $station->getDecoteStations()->filter(function (DecoteStation $element) use ($decote, $station, $fournisseurId) {
                        return $element->getStation() == $station and $element->getFournisseur()->getId() == $fournisseurId and $element->getDecote() == $decote;
                    })->first();
                    if (false === $decoteStation) {
                        echo 'création';
                        $decoteStation = new DecoteStation();
                        $decote->addDecoteStation($decoteStation);
                        $decoteStation
                            ->setStation($station)
                            ->setFournisseur($fournisseur);
                        $emSite->persist($decoteStation);
                    }
                }
            }
            $emSite->flush();
        }
    }
}
