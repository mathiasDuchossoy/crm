<?php

namespace Mondofute\Bundle\PromotionBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionStation;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PromotionStationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_promotion:promotion_station_command')
            ->addArgument('hebergementUnifieId', InputArgument::REQUIRED, 'L\'id de l\'hébergementUnifie.')
            ->addArgument('fournisseurId', InputArgument::REQUIRED, 'L\'id du fournisseur.')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Promotion $promotion */
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
            // récuperer les promotion des promotionfournisseur de type logement
            $promotions = $emSite->getRepository('MondofutePromotionBundle:Promotion')->findByFournisseurAndAffectation($fournisseurId, $typeAffectation);
            // on parcourt les promotions
            foreach ($promotions as $promotion) {
                // on essaye de récupérer la promotionStation en fonction du fournisseur et de la station de chaque hébergement
                foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                    $station = $hebergement->getStation();
                    $promotionStation = $station->getPromotionStations()->filter(function (PromotionStation $element) use ($promotion, $station, $fournisseurId) {
                        return $element->getStation() == $station and $element->getFournisseur()->getId() == $fournisseurId and $element->getPromotion() == $promotion;
                    })->first();
                    if (false === $promotionStation) {
                        echo 'création';
                        $promotionStation = new PromotionStation();
                        $promotion->addPromotionStation($promotionStation);
                        $promotionStation
                            ->setStation($station)
                            ->setFournisseur($fournisseur);
                        $emSite->persist($promotionStation);
                    }
                }
            }
            $emSite->flush();
        }
    }
}
