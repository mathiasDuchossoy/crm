<?php

namespace Mondofute\Bundle\LogementPeriodeBundle\Command;

use Doctrine\DBAL\Connection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class testsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('periode:tests')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $sites = $em->getRepository(Site::class)->findAll();
        $i = 0;
        $mbdd = $this->getContainer()->get('nucleus_manager_bdd.entity.manager_bdd');
        $mbdd2 = $this->getContainer()->get('nucleus_manager_bdd.entity.manager_bdd');
        /** @var Site $site */
        foreach ($sites as $site) {
            $i++;
//            if ($i != 3) {
//                continue;
//            }
            dump($site->getLibelle());
            /** @var Connection $emSite */
//            $emSite = $this->getContainer()->get('doctrine')->getConnection($site->getLibelle());
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
//            $emSite-
            $logements = $emSite->createQuery('SELECT l.id FROM Mondofute\Bundle\LogementBundle\Entity\Logement l')->getArrayResult();
//        dump($logements);
//            $periodes = $emSite->createQuery('SELECT p.id FROM Mondofute\Bundle\PeriodeBundle\Entity\Periode p WHERE p.debut >= CURDATE()')->getArrayResult();
            $periodes = $emSite->createQuery('SELECT p.id FROM Mondofute\Bundle\PeriodeBundle\Entity\Periode p')->getArrayResult();
//        dump($periodes);


//            $mbdd->initInsertMassif('logement_periode_locatif',
//                array('periode_id', 'logement_id', 'prix_public', 'stock'), true,
            $mbdd->initInsertMassif('logement_periode', array('periode_id', 'logement_id', 'actif'), true,
                array($site->getLibelle()));
            $mbdd2->initInsertMassif('logement_periode_locatif',
                array('periode_id', 'logement_id', 'prix_public', 'stock'), true,
//            $mbdd->initInsertMassif('logement_periode', array('periode_id', 'logement_id', 'actif'), true,
                array($site->getLibelle()));
            echo PHP_EOL . count($logements) . PHP_EOL;
            echo PHP_EOL . count($periodes) . PHP_EOL;
            foreach ($logements as $logement) {
                foreach ($periodes as $periode) {
//                dump(array($logement['id'],$periode['id'],1));
                    $mbdd->addInsertLigne(array($periode['id'], $logement['id'], true));
                    $mbdd2->addInsertLigne(array($periode['id'], $logement['id'], 0, 1));
//                $mbdd->addInsertLigne(array($logement['id'],$periode['id'],1));
                }
            }
            $mbdd->insertMassif();
            $mbdd2->insertMassif();
            $emSite->close();
        }
//        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
////        $logements = $em->getRepository(LogementPeriode::class)->findAll();
//        $logements = $em->getRepository(Logement::class)->findAll();
//        /** @var Logement $logement */
//        foreach ($logements as $logement) {
//            echo PHP_EOL . PHP_EOL . PHP_EOL . $logement->getId() . PHP_EOL;
//            $periodes = $logement->getPeriodes();
////            echo count($logement->getPeriodes()).PHP_EOL;
//            /** @var LogementPeriode $periode */
//            foreach ($periodes as $periode) {
//                echo $this->getContainer()->get('translator')->trans('type_periode.' . $periode->getPeriode()->getType()->getId()) . ' du ' . $periode->getPeriode()->getDebut()->format('Y-m-d') . ' au ' . $periode->getPeriode()->getFin()->format('Y-m-d') . PHP_EOL;
////                echo get_class($periode->getLocatif());
//            }
//        }
    }
}
