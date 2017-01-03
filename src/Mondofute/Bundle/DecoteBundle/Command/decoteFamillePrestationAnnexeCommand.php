<?php

namespace Mondofute\Bundle\DecoteBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFamillePrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class decoteFamillePrestationAnnexeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_decote:decote_famille_prestation_command')
            ->setDescription('Créer les decoteFamillePrestationAnnexe à la création du fournisseur')
            ->addArgument('fournisseurId', InputArgument::REQUIRED, 'L\'id du fournisseur.');
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
        $typeAffectationPrestationAnnexe = TypeAffectation::prestationAnnexe;

        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $connection = $emSite->getConnection();
//            $famillePrestationannexes = $emSite->getRepository(FamillePrestationAnnexe::class)->findAll();
            $fournisseur = $emSite->find(Fournisseur::class, $fournisseurId);
            foreach ($fournisseur->getTypes() as $type) {
                $typeId = $type->getId();
                $decotes = $emSite->getRepository('MondofuteDecoteBundle:Decote')->findByFournisseurAndAffectation($fournisseurId, $typeAffectationPrestationAnnexe);
                foreach ($decotes as $decote) {
                    $decoteId = $decote->getId();
                    $decoteFamillePrestationAnnexe = $decote->getDecoteFamillePrestationAnnexes()->filter(function (DecoteFamillePrestationAnnexe $element) use ($typeId, $fournisseurId, $decoteId) {
                        return ($element->getDecote()->getId() == $decoteId and $element->getFamillePrestationAnnexe()->getId() == $typeId and $element->getFournisseur()->getId() == $fournisseurId);
                    })->first();
                    if (false === $decoteFamillePrestationAnnexe) {
                        $decoteFamillePrestationAnnexe = new DecoteFamillePrestationAnnexe();
                        $decote->addDecoteFamillePrestationAnnex($decoteFamillePrestationAnnexe);
                        $decoteFamillePrestationAnnexe->setFamillePrestationAnnexe($type)
                            ->setFournisseur($fournisseur);
                    }
                    $emSite->persist($decoteFamillePrestationAnnexe);
                }
            }
//            foreach ($famillePrestationannexes as $famillePrestationannex) {
//                if (!$fournisseur->getTypes()->contains($famillePrestationannex)) {
//                    $sql = 'DELETE FROM decote_famille_prestation_annexe where type = ' . $typeAffectationPrestationAnnexe . ' and fournisseur_id = ' . $fournisseur->getId();
//                    $connection->executeQuery($sql);
//                }
//            }
            $emSite->flush();
        }
    }
}
