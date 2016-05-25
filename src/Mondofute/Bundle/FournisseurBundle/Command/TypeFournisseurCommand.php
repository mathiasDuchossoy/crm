<?php

namespace Mondofute\Bundle\FournisseurBundle\Command;

use Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseurTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TypeFournisseurCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_fournisseur:type_fournisseur_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $sites = $em->getRepository(Site::class)->findAll();
        $langues = $em->getRepository(Langue::class)->findAll();
        $arrayTypeHebergement =
            array(
                array(
                    'fr_FR' => 'Hébergement',
                    'en_EN' => 'Accommodation',
                    'es_ES' => 'alojamiento',
                ),
                array(
                    'fr_FR' => 'Remontées Mécaniques',
                    'en_EN' => 'Lifts',
                    'es_ES' => 'Remontes',
                ),
                array(
                    'fr_FR' => 'Location Matériel de Ski',
                    'en_EN' => 'Ski Equipment Rental',
                    'es_ES' => 'Alquiler de equipamiento de esquí',
                ),
                array(
                    'fr_FR' => 'ESF',
                    'en_EN' => 'ESF',
                    'es_ES' => 'ESF',
                ),
                array(
                    'fr_FR' => 'Assurance',
                    'en_EN' => 'Insurance',
                    'es_ES' => 'Seguro',
                )
            );

        // parcourir chaque site
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $dataTypeFournisseurs = $emSite->getRepository(TypeFournisseur::class)->findAll();
            if (!empty($dataTypeFournisseurs)) {
                foreach ($dataTypeFournisseurs as $dataTypeFournisseur) {
                    $emSite->remove($dataTypeFournisseur);
                }
                $emSite->flush();
            }

            $connection = $emSite->getConnection();
            $connection->exec("SET foreign_key_checks = 0;");
            $connection->exec("DELETE FROM type_fournisseur");
            $connection->exec("ALTER TABLE type_fournisseur AUTO_INCREMENT = 1;");
            $connection->exec("SET foreign_key_checks = 1;");
            $connection->exec("SET foreign_key_checks = 0;");
            $connection->exec("DELETE FROM type_fournisseur_traduction");
            $connection->exec("ALTER TABLE type_fournisseur_traduction AUTO_INCREMENT = 1;");
            $connection->exec("SET foreign_key_checks = 1;");

            // parcourir tout les hebergement concernant la langue
            foreach ($arrayTypeHebergement as $typeHebergement) {
                $typeFournisseur = new TypeFournisseur();
                // parcourir chaque langue
                foreach ($langues as $langue) {
                    $typeFournisseurTraduction = new TypeFournisseurTraduction();
                    $typeFournisseurTraduction->setLangue($emSite->find(Langue::class, $langue));
                    $typeFournisseurTraduction->setLibelle($typeHebergement[$langue->getCode()]);
                    $typeFournisseur->addTraduction($typeFournisseurTraduction);
                }
                $emSite->persist($typeFournisseur);
            }
            $emSite->flush();
        }
    }
}
