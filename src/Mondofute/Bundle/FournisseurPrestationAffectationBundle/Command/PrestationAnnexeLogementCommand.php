<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogementUnifie;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class PrestationAnnexeLogementCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('creer:prestationAnnexeLogement')
            ->setDescription('Création des prestation annexe pour les logements')
            ->addArgument('logementUnifieId', InputArgument::REQUIRED, 'id de logement unifie.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        $logementUnifieId = $input->getArgument('logementUnifieId');
        $logementUnifie = $em->find(LogementUnifie::class, $logementUnifieId);
        $fournisseurId = $em->getRepository(LogementUnifie::class)->getFournisseur($logementUnifieId);

        $prestationAnnexeHebergementUnifies = $em->getRepository(PrestationAnnexeHebergementUnifie::class)->findByLogementUnifieId($logementUnifieId , $fournisseurId);
//        dump($prestationAnnexeHebergementUnifies);
        $prestationAnnexeLogementUnifies = new ArrayCollection();
        foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie)
        {
            $prestationAnnexeLogementUnifie = new PrestationAnnexeLogementUnifie();
            $prestationAnnexeLogementUnifies->add($prestationAnnexeLogementUnifie);
            $em->persist($prestationAnnexeLogementUnifie);
            foreach ($prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement)
            {
                $logement = $logementUnifie->getLogements()->filter(function (Logement $element) use ($prestationAnnexeHebergement){
                    return $element->getSite() == $prestationAnnexeHebergement->getSite();
                })->first();
                $prestationAnnexeLogement = new PrestationAnnexeLogement();
                $prestationAnnexeLogementUnifie->addPrestationAnnexeLogement($prestationAnnexeLogement);
                $prestationAnnexeLogement
                    ->setLogement($logement)
                    ->setFournisseurPrestationAnnexe($prestationAnnexeHebergement->getFournisseurPrestationAnnexe())
                    ->setActif($prestationAnnexeHebergement->getActif())
                    ->setSite($prestationAnnexeHebergement->getSite())
                ;
            }


//            dump($prestationAnnexeLogementUnifie->getId());
        }

        $em->flush();

        /** @var PrestationAnnexeLogementUnifie $prestationAnnexeLogementUnifie */
        /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
        foreach ($sites as $site)
        {
            /** @var EntityManager $emSite */
            $emSite = $this->getContainer()->get('doctrine.orm.'.$site->getLibelle().'_entity_manager');
            foreach ($prestationAnnexeLogementUnifies as $prestationAnnexeLogementUnifie)
            {
                $prestationAnnexeLogementUnifieSite = new PrestationAnnexeLogementUnifie();
                $emSite->persist($prestationAnnexeLogementUnifieSite);
                $prestationAnnexeLogementUnifieSite->setId($prestationAnnexeLogementUnifie->getId());
                $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeLogementUnifieSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $prestationAnnexeLogement = $prestationAnnexeLogementUnifie->getPrestationAnnexeLogements()->filter(function (PrestationAnnexeLogement $element) use ($site){
                    return $element->getSite() == $site;
                })->first();

                $logement = $emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $logementUnifieId));
                $prestationAnnexeLogementSite = new PrestationAnnexeLogement();
                $prestationAnnexeLogementUnifieSite->addPrestationAnnexeLogement($prestationAnnexeLogementSite);
                $prestationAnnexeLogementSite
                    ->setLogement($logement)
                    ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class ,$prestationAnnexeLogement->getFournisseurPrestationAnnexe() ))
                    ->setSite($emSite->find(Site::class, $site))
                    ->setActif($prestationAnnexeLogement->getActif())
                ;
            }
            $emSite->flush();
        }



    }
}
