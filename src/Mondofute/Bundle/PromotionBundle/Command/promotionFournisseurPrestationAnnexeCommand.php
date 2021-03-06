<?php

namespace Mondofute\Bundle\PromotionBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class promotionFournisseurPrestationAnnexeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_promotion:promotion_fournisseur_prestation_annexe_command')
            ->setDescription('Créer les promotionFournisseurPrestationAnnexe à l\'édition du fournisseur')
            ->addArgument('fournisseurId', InputArgument::REQUIRED, 'L\'id du fournisseur.')
            ->addArgument('fournisseurPrestationAnnexeId', InputArgument::REQUIRED, 'L\'id du fournisseur prestation annexe.');
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
        $fournisseurPrestationAnnexeId = $input->getArgument('fournisseurPrestationAnnexeId');
        $typeAffectationPrestationAnnexe = TypeAffectation::prestationAnnexe;

        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
//            $connection = $emSite->getConnection();
            $fournisseurPrestationAnnexe = $emSite->find(FournisseurPrestationAnnexe::class, $fournisseurPrestationAnnexeId);
            $fournisseur = $emSite->find(Fournisseur::class, $fournisseurId);
            /** @var FournisseurPrestationAnnexe $prestationAnnex */
//            foreach ($fournisseur->getPrestationAnnexes() as $prestationAnnex) {
            $typeId = $fournisseurPrestationAnnexe->getPrestationAnnexe()->getFamillePrestationAnnexe()->getId();
            $fournisseurPrestationAnnexeId = $fournisseurPrestationAnnexe->getId();
            $promotions = $emSite->getRepository('MondofutePromotionBundle:Promotion')->findByFournisseurAndAffectationAndFamille($fournisseurId, $typeAffectationPrestationAnnexe, $typeId);
            foreach ($promotions as $promotion) {
                $promotionId = $promotion->getId();
                $promotionFournisseurPrestationAnnexe = $promotion->getPromotionFournisseurPrestationAnnexes()->filter(function (PromotionFournisseurPrestationAnnexe $element) use ($fournisseurPrestationAnnexeId, $fournisseurId, $promotionId) {
                    return ($element->getPromotion()->getId() == $promotionId and $element->getFournisseurPrestationAnnexe()->getId() == $fournisseurPrestationAnnexeId and $element->getFournisseur()->getId() == $fournisseurId);
                })->first();
                if (false === $promotionFournisseurPrestationAnnexe) {
                    $promotionFournisseurPrestationAnnexe = new PromotionFournisseurPrestationAnnexe();
                    $promotion->addPromotionFournisseurPrestationAnnex($promotionFournisseurPrestationAnnexe);
                    $promotionFournisseurPrestationAnnexe
                        ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe)
                        ->setFournisseur($fournisseur);
                }
                $emSite->persist($promotionFournisseurPrestationAnnexe);
            }
//            }
//            foreach ($fournisseurPrestationannexes as $fournisseurPrestationannex) {
//                if (!$fournisseur->getTypes()->contains($fournisseurPrestationannex)) {
//                    $sql = 'DELETE FROM promotion_fournisseur_prestation_annexe where type = ' . $typeAffectationPrestationAnnexe . ' and fournisseur_id = ' . $fournisseur->getId();
//                    $connection->executeQuery($sql);
//                }
//            }
            $emSite->flush();
        }
    }
}
