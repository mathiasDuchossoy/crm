<?php

namespace Mondofute\Bundle\PromotionBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class promotionFamillePrestationAnnexeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_promotion:promotion_famille_prestation_command')
            ->setDescription('Créer les promotionFamillePrestationAnnexe à la création du fournisseur')
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
        $typeAffectationPrestationAnnexe = TypeAffectation::prestationAnnexe;

        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $connection = $emSite->getConnection();
//            $famillePrestationannexes = $emSite->getRepository(FamillePrestationAnnexe::class)->findAll();
            $fournisseur = $emSite->find(Fournisseur::class, $fournisseurId);
            foreach ($fournisseur->getTypes() as $type) {
                $typeId = $type->getId();
                $promotions = $emSite->getRepository('MondofutePromotionBundle:Promotion')->findByFournisseurAndAffectation($fournisseurId, $typeAffectationPrestationAnnexe);
                foreach ($promotions as $promotion) {
                    $promotionId = $promotion->getId();
                    $promotionFamillePrestationAnnexe = $promotion->getPromotionFamillePrestationAnnexes()->filter(function (PromotionFamillePrestationAnnexe $element) use ($typeId, $fournisseurId, $promotionId) {
                        return ($element->getPromotion()->getId() == $promotionId and $element->getFamillePrestationAnnexe()->getId() == $typeId and $element->getFournisseur()->getId() == $fournisseurId);
                    })->first();
                    if (false === $promotionFamillePrestationAnnexe) {
                        $promotionFamillePrestationAnnexe = new PromotionFamillePrestationAnnexe();
                        $promotion->addPromotionFamillePrestationAnnex($promotionFamillePrestationAnnexe);
                        $promotionFamillePrestationAnnexe->setFamillePrestationAnnexe($type)
                            ->setFournisseur($fournisseur);
                    }
                    $emSite->persist($promotionFamillePrestationAnnexe);
                }
            }
//            foreach ($famillePrestationannexes as $famillePrestationannex) {
//                if (!$fournisseur->getTypes()->contains($famillePrestationannex)) {
//                    $sql = 'DELETE FROM promotion_famille_prestation_annexe where type = ' . $typeAffectationPrestationAnnexe . ' and fournisseur_id = ' . $fournisseur->getId();
//                    $connection->executeQuery($sql);
//                }
//            }
            $emSite->flush();
        }
    }
}
