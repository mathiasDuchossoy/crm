<?php

namespace Mondofute\Bundle\LogementBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EditLogementPeriodeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_logement:edit_logement_periode_command')
            ->addArgument('logementUnifieId', InputArgument::REQUIRED)
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $today = new DateTime();
        /** @var EntityManager $em */
        $logementUnifieId = $input->getArgument('logementUnifieId');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();

        $sites = $connection->executeQuery('SELECT libelle FROM site')->fetchAll();

        foreach ($sites as $site) {
            $em = $this->getContainer()->get('doctrine')->getManager($site['libelle']);
            $connection = $em->getConnection();
            $typePeriodes = $connection->executeQuery('SELECT id FROM type_periode')->fetchAll();
            $logements = $connection->executeQuery('SELECT id FROM logement where logement_unifie_id = ' . $logementUnifieId)->fetchAll();
            foreach ($logements as $logement) {
                $logementTypePeriodes = $connection->executeQuery('SELECT type_periode_id id FROM logement_type_periode WHERE logement_id = ' . $logement['id'])->fetchAll();
                foreach ($typePeriodes as $typePeriode) {
                    if (!in_array($typePeriode, $logementTypePeriodes)) {
                        $requete = 'DELETE lp FROM logement_periode lp LEFT JOIN periode p ON lp.periode_id = p.id WHERE lp.logement_id = ' . $logement['id'] . ' AND p.type_id = ' . $typePeriode['id'];
                        $connection->executeQuery($requete);
                        $connection->executeQuery('DELETE lpl FROM logement_periode_locatif lpl LEFT JOIN periode p ON lpl.periode_id = p.id WHERE lpl.logement_id = ' . $logement['id'] . ' AND p.type_id = ' . $typePeriode['id']);
                    }
                }
                foreach ($logementTypePeriodes as $logementTypePeriode) {
                    $logementPeriodes = $connection->executeQuery('SELECT periode_id id FROM logement_periode lp LEFT JOIN periode p ON lp.periode_id = p.id WHERE lp.logement_id = ' . $logement['id'] . ' AND p.type_id = ' . $logementTypePeriode['id'] . ' AND p.debut >= ' . $today->format('Y-m-d'))->fetchAll();
                    $periodes = $connection->executeQuery('SELECT id FROM periode WHERE type_id = ' . $logementTypePeriode['id'] . ' AND debut >= ' . $today->format('Y-m-d'))->fetchAll();
                    $requeteLogementPeriode = 'INSERT INTO logement_periode (periode_id, logement_id, actif) VALUES ';
                    $requeteLogementPeriodeLocatif = 'INSERT INTO logement_periode_locatif (periode_id, logement_id) VALUES ';
                    $insertOk = false;
                    $sep = '';
                    foreach ($periodes as $periode) {
                        if (!in_array($periode, $logementPeriodes)) {
                            $requeteLogementPeriode .= $sep . '(' . $periode['id'] . ', ' . $logement['id'] . ', 1) ';
                            $requeteLogementPeriodeLocatif .= $sep . '(' . $periode['id'] . ', ' . $logement['id'] . ') ';
                            $sep = ', ';
                            $insertOk = true;
                        }
                    }
                    if ($insertOk) {
                        $connection->executeQuery($requeteLogementPeriode);
                        $connection->executeQuery($requeteLogementPeriodeLocatif);
                    }
                }
            }
        }
    }
}
