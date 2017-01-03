<?php

namespace Mondofute\Bundle\PromotionBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseur;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionTypeAffectation;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionUnifie;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PromotionTypeFournisseurCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_promotion:promotion_type_fournisseur_command')
            ->setDescription('Créer les promotionFournisseurs en fonction des typeFournisseurs')
            ->addArgument('promotionUnifieId', InputArgument::REQUIRED, 'L\'id du promotionUnifie.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PromotionTypeAffectation $typeAffectation */
        /** @var Site $site */
        /** @var EntityManager $emSite */
        /** @var FamillePrestationAnnexe $typeFournisseur */
        /** @var Promotion $promotion */
        $typeType = TypeAffectation::type;
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $promotionUnifieId = $input->getArgument('promotionUnifieId');
        $famillePrestationannexes = $em->getRepository(FamillePrestationAnnexe::class)->findAll();

        $sites = $em->getRepository(Site::class)->findAll();

        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());
            $connection = $em->getConnection();
            $promotionUnifie = $emSite->find(PromotionUnifie::class, $promotionUnifieId);
            foreach ($promotionUnifie->getPromotions() as $promotion) {
                foreach ($promotion->getPromotionTypeAffectations() as $typeAffectation) {
                    if ($typeAffectation->getTypeAffectation() == $typeType) {
                        // on parcourt les famillle de prestation annexe du fournisseur
                        foreach ($promotion->getTypeFournisseurs() as $typeFournisseur) {

                            $fournisseurs = $emSite->getRepository(Fournisseur::class)->findByFamillePrestationAnnexe($typeFournisseur->getId());
                            foreach ($fournisseurs as $fournisseur) {
                                $promotionFournisseur = $emSite->getRepository(PromotionFournisseur::class)->findOneBy(array('fournisseur' => $fournisseur, 'promotion' => $promotion, 'type' => $typeType));
                                if (empty($promotionFournisseur)) {
                                    $promotionFournisseur = new PromotionFournisseur();
                                    $promotionFournisseur
                                        ->setFournisseur($fournisseur)
                                        ->setType($typeType)
                                        ->setPromotion($promotion);
                                    $promotionFournisserExists = $promotion->getPromotionFournisseurs()->filter(function (PromotionFournisseur $element) use ($promotionFournisseur) {
                                        return ($element->getPromotion() == $promotionFournisseur->getPromotion()
                                            and $element->getFournisseur() == $promotionFournisseur->getFournisseur()
                                            and $element->getType() == $promotionFournisseur->getType());
                                    })->first();
                                    if (false === $promotionFournisserExists) {
                                        $promotion->addPromotionFournisseur($promotionFournisseur);
                                    }
                                }
                            }
                        }
                    }
                }
//                foreach ($famillePrestationannexes as $famillePrestationannex) {
//                    $typeFournisseur = $promotion->getTypeFournisseurs()->filter(function (FamillePrestationAnnexe $element) use ($famillePrestationannex) {
//                        return $famillePrestationannex->getId() == $element->getId();
//                    })->first();
//                    if (false === $typeFournisseur and !$promotion->getPromotionTypeAffectations()->contains(TypeAffectation::prestationAnnexe)) {
//                        $sql = 'DELETE FROM promotion_fournisseur where type = ' . $typeType . ' and promotion_id = ' . $promotion->getId();
//                        $connection->executeQuery($sql);
//                    }
//                }
            }
            $emSite->persist($promotionUnifie);
            $emSite->flush();
        }
    }
}
