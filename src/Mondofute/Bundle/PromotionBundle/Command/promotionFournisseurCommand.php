<?php

namespace Mondofute\Bundle\PromotionBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseur;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class promotionFournisseurCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_promotion:promotion_fournisseur_command')
            ->setDescription('Créer les promotionFournisseurs à la création du fournisseur')
            ->addArgument('fournisseurId', InputArgument::REQUIRED, 'L\'id du fournisseur.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $emSite */
        /** @var Site $site */
        /** @var Promotion $promotion */
        /** @var Fournisseur $fournisseur */
        /** @var FamillePrestationAnnexe $type */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $fournisseurId = $input->getArgument('fournisseurId');
        $typeType = TypeAffectation::type;

        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $connection = $emSite->getConnection();
            $fournisseur = $emSite->find(Fournisseur::class, $fournisseurId);
            $delete = true;
            foreach ($fournisseur->getTypes() as $type) {
                $promotions = $emSite->getRepository('MondofutePromotionBundle:Promotion')->findByTypeFournisseur($type->getId());
                foreach ($promotions as $promotion) {
                    $promotionId = $promotion->getId();
                    $promotionFournisseur = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($typeType, $fournisseurId, $promotionId) {
                        return ($element->getPromotion()->getId() == $promotionId and $element->getType() == $typeType and $element->getFournisseur()->getId() == $fournisseurId);
                    })->first();
                    if (false === $promotionFournisseur) {
                        $promotionFournisseur = new PromotionFournisseur();
                        $promotion->addPromotionFournisseur($promotionFournisseur);
                        $promotionFournisseur->setType(TypeAffectation::type)
                            ->setFournisseur($fournisseur);
                    }
                    $emSite->persist($promotion);
                }
                if (!empty($promotions)) {
                    $delete = false;
                }
            }
            // si il y a aucune promotion pour aucun type du fournisseur alors on supprime le promotionFournisseur
            if ($delete) {
                $sql = 'DELETE FROM promotion_fournisseur where type = ' . $typeType . ' and fournisseur_id = ' . $fournisseur->getId();
                $connection->executeQuery($sql);
            }
            $emSite->flush();
        }
    }
}
