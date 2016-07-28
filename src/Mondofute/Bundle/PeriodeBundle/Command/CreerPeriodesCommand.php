<?php

namespace Mondofute\Bundle\PeriodeBundle\Command;

use DateTime;
use Mondofute\Bundle\PeriodeBundle\Controller\PeriodeController;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreerPeriodesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('creer:periodes')
            ->setDescription('Création massive de périodes')
            ->addArgument('debut', InputArgument::REQUIRED, 'date du premier début de séjour')
            ->addArgument('fin', InputArgument::REQUIRED, 'date de fin de traitement')
            ->addArgument('type-periode', InputArgument::REQUIRED, 'identifiant du type de période')
            ->addArgument('nb-jour', InputArgument::OPTIONAL,
                'nombre de jour par période (prend la valeur de type période si non renseigner)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->valideArguments($input->getArguments())) {
            $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
            $sites = $em->getRepository(Site::class)->findAll();
            $debut = new \DateTime($input->getArgument('debut'));
            $fin = new \DateTime($input->getArgument('fin'));
            $nbJour = intval($input->getArgument('nb-jour'), 10);
            if (!empty($typePeriode = $em->getRepository(TypePeriode::class)->find(intval($input->getArgument('type-periode'),
                10)))
            ) {
                $periodeController = new PeriodeController();
                $periodeController->setContainer($this->getContainer());
                $periodes = $periodeController->creerPeriodes($debut, $fin, $typePeriode, $nbJour);
                if ($periodes->count() > 0) {
                    $periodeController->enregistrerPeriodesDansSites($periodes, $sites);
                }
            } else {
                $output->writeln('Le type de période spécifié n\'existe pas dans la base de données');
            }
        }
    }

    /**
     * Vérifie la validité des arguments donnés
     * @param array $arguments
     * @return bool
     */
    private function valideArguments($arguments)
    {
        $dateFormat = "Y-m-d";
        try {
            if (DateTime::createFromFormat($dateFormat, $arguments['debut'])) {
                if (DateTime::createFromFormat($dateFormat, $arguments['fin'])) {
                    if (intval($arguments['type-periode'], 10)) {
                        if (!empty($arguments['nb-jour'])) {
                            if (intval($arguments['nb-jour']) > 0) {
                            } else {
                                throw new \Exception('veuillez vérifier le nombre de jours');
                            }
                        }
                        return true;
                    } else {
                        throw new \Exception('le type de periode doit etre un entier');
                    }
                } else {
                    throw new \Exception('Veuillez vérifier le format de la date de fin YYYY-MM-DD');
                }
            } else {
                throw new \Exception('Veuillez vérifier le format de la date de début YYYY-MM-DD');
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
