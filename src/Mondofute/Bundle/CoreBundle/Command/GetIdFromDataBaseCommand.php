<?php

namespace Mondofute\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetIdFromDataBaseCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_core:get_id_from_data_base_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

//        $results = $em->createQuery("SHOW TABLES")->getResult();
//        $results = $em->getConnection()->fetchAll('SHOW TABLES');
//        $results = $em->getConnection()->setFetchMode('SHOW TABLES');
        $results = $em->getConnection()->query('SHOW TABLES');
//        $results = $em->getConnection()->query('SHOW TABLES')->fetchAll();

        // Paramétrage de l'écriture du futur fichier CSV
        $chemin = 'fichierIds.csv';
        $delimiteur = ';'; // Pour une tabulation, utiliser $delimiteur = "t";

// Création du fichier csv (le fichier est vide pour le moment)
// w+ : consulter http://php.net/manual/fr/function.fopen.php
        $fichier_csv = fopen($chemin, 'w+');

// Si votre fichier a vocation a être importé dans Excel,
// vous devez impérativement utiliser la ligne ci-dessous pour corriger
// les problèmes d'affichage des caractères internationaux (les accents par exemple)
        fprintf($fichier_csv, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Boucle foreach sur chaque ligne du tableau

//            $resultColumns = $em->getConnection()->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='zone_touristique_traduction' AND table_schema = 'crm_preprod_crm'");
//
//        var_dump($resultColumns->fetchAll());
//
//            while ($rowColumn = $resultColumns->fetch()) {
//                fputcsv($fichier_csv, $rowColumn, $delimiteur);
//            }
//        fputcsv($fichier_csv, array('Entité' , 'Element' , 'Obligatoire' , 'Personnalisable' , 'Cascade crm', 'Image obligatoire'), $delimiteur);
        while ($row = $results->fetch()) {
            // chaque ligne en cours de lecture est insérée dans le fichier
            // les valeurs présentes dans chaque ligne seront séparées par $delimiteur


            $resultColumns = $em->getConnection()->query("SHOW COLUMNS FROM " . $row['Tables_in_crm_128_crm'] . " Like 'id'");
//
            while ($rowColumn = $resultColumns->fetch()) {
//                fputcsv($fichier_csv, $rowColumn, $delimiteur);
//var_dump($rowColumn);
                if ($rowColumn['Type'] == 'int(11)') {
                    fputcsv($fichier_csv, $row, $delimiteur);
                    fputcsv($fichier_csv, array_merge(array(''), $rowColumn), $delimiteur);
                }
            }

        }

// fermeture du fichier csv
        fclose($fichier_csv);

    }
}
