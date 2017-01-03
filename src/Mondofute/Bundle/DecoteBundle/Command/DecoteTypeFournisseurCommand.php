<?php

namespace Mondofute\Bundle\DecoteBundle\Command;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseur;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteTypeAffectation;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteUnifie;
use Mondofute\Bundle\DecoteBundle\Entity\TypeAffectation;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecoteTypeFournisseurCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_decote:decote_type_fournisseur_command')
            ->setDescription('Créer les decoteFournisseurs en fonction des typeFournisseurs')
            ->addArgument('decoteUnifieId', InputArgument::REQUIRED, 'L\'id du decoteUnifie.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var DecoteTypeAffectation $typeAffectation */
        /** @var Site $site */
        /** @var EntityManager $emSite */
        /** @var FamillePrestationAnnexe $typeFournisseur */
        /** @var Decote $decote */
        $typeType = TypeAffectation::type;
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $decoteUnifieId = $input->getArgument('decoteUnifieId');
        $famillePrestationannexes = $em->getRepository(FamillePrestationAnnexe::class)->findAll();

        $sites = $em->getRepository(Site::class)->findAll();

        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());
            $connection = $em->getConnection();
            $decoteUnifie = $emSite->find(DecoteUnifie::class, $decoteUnifieId);
            foreach ($decoteUnifie->getDecotes() as $decote) {
                foreach ($decote->getDecoteTypeAffectations() as $typeAffectation) {
                    if ($typeAffectation->getTypeAffectation() == $typeType) {
                        // on parcourt les famillle de prestation annexe du fournisseur
                        foreach ($decote->getTypeFournisseurs() as $typeFournisseur) {

                            $fournisseurs = $emSite->getRepository(Fournisseur::class)->findByFamillePrestationAnnexe($typeFournisseur->getId());
                            foreach ($fournisseurs as $fournisseur) {
                                $decoteFournisseur = $emSite->getRepository(DecoteFournisseur::class)->findOneBy(array('fournisseur' => $fournisseur, 'decote' => $decote, 'type' => $typeType));
                                if (empty($decoteFournisseur)) {
                                    $decoteFournisseur = new DecoteFournisseur();
                                    $decoteFournisseur
                                        ->setFournisseur($fournisseur)
                                        ->setType($typeType)
                                        ->setDecote($decote);
                                    $decoteFournisserExists = $decote->getDecoteFournisseurs()->filter(function (DecoteFournisseur $element) use ($decoteFournisseur) {
                                        return ($element->getDecote() == $decoteFournisseur->getDecote()
                                            and $element->getFournisseur() == $decoteFournisseur->getFournisseur()
                                            and $element->getType() == $decoteFournisseur->getType());
                                    })->first();
                                    if (false === $decoteFournisserExists) {
                                        $decote->addDecoteFournisseur($decoteFournisseur);
                                    }
                                }
                            }
                        }
                    }
                }
//                foreach ($famillePrestationannexes as $famillePrestationannex) {
//                    $typeFournisseur = $decote->getTypeFournisseurs()->filter(function (FamillePrestationAnnexe $element) use ($famillePrestationannex) {
//                        return $famillePrestationannex->getId() == $element->getId();
//                    })->first();
//                    if (false === $typeFournisseur and !$decote->getDecoteTypeAffectations()->contains(TypeAffectation::prestationAnnexe)) {
//                        $sql = 'DELETE FROM decote_fournisseur where type = ' . $typeType . ' and decote_id = ' . $decote->getId();
//                        $connection->executeQuery($sql);
//                    }
//                }
            }
            $emSite->persist($decoteUnifie);
            $emSite->flush();
        }
    }
}
