<?php

namespace Mondofute\Bundle\DecoteBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseur;
use Mondofute\Bundle\DecoteBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class decoteFournisseurCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_decote:decote_fournisseur_command')
            ->setDescription('Créer les decoteFournisseurs à la création du fournisseur')
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
        $typeType = TypeAffectation::type;

        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $connection = $emSite->getConnection();
            $fournisseur = $emSite->find(Fournisseur::class, $fournisseurId);
            $delete = true;
            foreach ($fournisseur->getTypes() as $type) {
                $decotes = $emSite->getRepository('MondofuteDecoteBundle:Decote')->findByTypeFournisseur($type->getId());
                foreach ($decotes as $decote) {
                    $decoteId = $decote->getId();
                    $decoteFournisseur = $decote->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) use ($typeType, $fournisseurId, $decoteId) {
                        return ($element->getDecote()->getId() == $decoteId and $element->getType() == $typeType and $element->getFournisseur()->getId() == $fournisseurId);
                    })->first();
                    if (false === $decoteFournisseur) {
                        $decoteFournisseur = new DecoteFournisseur();
                        $decote->addDecoteFournisseur($decoteFournisseur);
                        $decoteFournisseur->setType(TypeAffectation::type)
                            ->setFournisseur($fournisseur);
                    }
                    $emSite->persist($decote);
                }
                if (!empty($decotes)) {
                    $delete = false;
                }
            }
            // si il y a aucune decote pour aucun type du fournisseur alors on supprime le decoteFournisseur
            if ($delete) {
                $sql = 'DELETE FROM decote_fournisseur where type = ' . $typeType . ' and fournisseur_id = ' . $fournisseur->getId();
                $connection->executeQuery($sql);
            }
            $emSite->flush();
        }
    }
}
