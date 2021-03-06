<?php

namespace Mondofute\Bundle\LogementBundle\Command;

use Mondofute\Bundle\LogementPeriodeBundle\Controller\LogementPeriodeController;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssocierPeriodesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_logement:associer_periodes_command')
            ->addArgument('id-logement', InputArgument::REQUIRED, 'identifiant du logement à associer')
            ->addArgument('id-site', InputArgument::REQUIRED,
                'identifiant du site dans lequel nous allons effectuer les enregistrements')
            ->setDescription('permet d\'associer les périodes futures au logement défini par id-logement sur le site défini par id-site');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->valideArguments($input->getArguments())) {
            try {
                $idLogement = intval($input->getArgument('id-logement'), 10);
                $idSite = intval($input->getArgument('id-site'), 10);
                $em = $this->getContainer()->get('doctrine.orm.entity_manager');
                $site = $em->getRepository(Site::class)->find($idSite);
                $logementPeriodeController = new LogementPeriodeController();
                $logementPeriodeController->setContainer($this->getContainer());
                $logementPeriodeController->associerPeriodes($idLogement, $site);

            } catch (\Exception $exception) {
                echo $exception->getMessage();
                echo $exception->getLine();
            }
        }
    }

    private function valideArguments($arguments)
    {
        if (!is_numeric($arguments['id-logement'])) {
            return false;
        }
        if (!is_numeric($arguments['id-site'])) {
            return false;
        }
        return true;
    }
}
