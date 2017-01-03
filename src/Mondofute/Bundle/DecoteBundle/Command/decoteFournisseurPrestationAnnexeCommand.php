<?php

namespace Mondofute\Bundle\DecoteBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseurPrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class decoteFournisseurPrestationAnnexeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_decote:decote_fournisseur_prestation_annexe_command')
            ->setDescription('Créer les decoteFournisseurPrestationAnnexe à l\'édition du fournisseur')
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
        /** @var Decote $decote */
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
            $decotes = $emSite->getRepository('MondofuteDecoteBundle:Decote')->findByFournisseurAndAffectationAndFamille($fournisseurId, $typeAffectationPrestationAnnexe, $typeId);
            foreach ($decotes as $decote) {
                $decoteId = $decote->getId();
                $decoteFournisseurPrestationAnnexe = $decote->getDecoteFournisseurPrestationAnnexes()->filter(function (DecoteFournisseurPrestationAnnexe $element) use ($fournisseurPrestationAnnexeId, $fournisseurId, $decoteId) {
                    return ($element->getDecote()->getId() == $decoteId and $element->getFournisseurPrestationAnnexe()->getId() == $fournisseurPrestationAnnexeId and $element->getFournisseur()->getId() == $fournisseurId);
                })->first();
                if (false === $decoteFournisseurPrestationAnnexe) {
                    $decoteFournisseurPrestationAnnexe = new DecoteFournisseurPrestationAnnexe();
                    $decote->addDecoteFournisseurPrestationAnnex($decoteFournisseurPrestationAnnexe);
                    $decoteFournisseurPrestationAnnexe
                        ->setFournisseurPrestationAnnexe($fournisseurPrestationAnnexe)
                        ->setFournisseur($fournisseur);
                }
                $emSite->persist($decoteFournisseurPrestationAnnexe);
            }
//            }
//            foreach ($fournisseurPrestationannexes as $fournisseurPrestationannex) {
//                if (!$fournisseur->getTypes()->contains($fournisseurPrestationannex)) {
//                    $sql = 'DELETE FROM decote_fournisseur_prestation_annexe where type = ' . $typeAffectationPrestationAnnexe . ' and fournisseur_id = ' . $fournisseur->getId();
//                    $connection->executeQuery($sql);
//                }
//            }
            $emSite->flush();
        }
    }
}
