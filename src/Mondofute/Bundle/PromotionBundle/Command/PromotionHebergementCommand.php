<?php

namespace Mondofute\Bundle\PromotionBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionHebergement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PromotionHebergementCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_promotion:promotion_hebergement_command')
            ->addArgument('hebergementUnifieId', InputArgument::REQUIRED, 'L\'id de l\'hébergementUnifie.')
            ->setDescription('création promotionHebergement');
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
//        $fournisseurId = $input->getArgument('fournisseurId');
//        $typeAffectation = TypeAffectation::logement;

        $sites = $em->getRepository(Site::class)->findAll();

        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $hebergementUnifie = $emSite->find(HebergementUnifie::class, $hebergementUnifieId);

            foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                /** @var FournisseurHebergement $fournisseur */
                foreach ($hebergementUnifie->getFournisseurs() as $fournisseur) {
                    // récuperer les promotion des promotionStation enf foncton du fournisseur et de la station
                    $promotions = $emSite->getRepository('MondofutePromotionBundle:Promotion')->findByPromotionStations($hebergement->getStation()->getId(), $fournisseur->getFournisseur()->getId());
                    // on parcourt les promotions
                    foreach ($promotions as $promotion) {
                        // on essaye de récupérer la promotionHebergement en fonction du fournisseur et de l'hébergement
                        foreach ($hebergementUnifie->getHebergements() as $hebergement) {
                            $promotionHebergement = $hebergement->getPromotionHebergements()->filter(function (PromotionHebergement $element) use ($promotion, $hebergement, $fournisseur) {
                                return $element->getHebergement() == $hebergement and $element->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId() and $element->getPromotion()->getPromotionUnifie() == $promotion->getPromotionUnifie();
                            })->first();
                            if (false === $promotionHebergement) {
                                $promotionHebergement = new PromotionHebergement();
                                $hebergement->addPromotionHebergement($promotionHebergement);
                                $promotionHebergement
                                    ->setPromotion($promotion->getPromotionUnifie()->getPromotions()->filter(function (Promotion $element) use ($hebergement) {
                                        return $hebergement->getSite() == $element->getSite();
                                    })->first())
                                    ->setFournisseur($fournisseur->getFournisseur());
                                $emSite->persist($promotionHebergement);
                            }
                        }
                    }
                }
            }
            $emSite->flush();
        }
    }
}
