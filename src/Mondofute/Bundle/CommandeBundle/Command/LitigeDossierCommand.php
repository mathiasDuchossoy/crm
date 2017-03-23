<?php

namespace Mondofute\Bundle\CommandeBundle\Command;

use Mondofute\Bundle\CommandeBundle\Entity\LitigeDossier;
use Mondofute\Bundle\CommandeBundle\Entity\LitigeDossierTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LitigeDossierCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_commande_litige_dossier:init')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emDefault = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $datas = [
            'fr_FR' => ['à traiter' => '#ffff00', 'en cours' => '#ff0000', 'clôture' => '#808080']
        ];
        $sites = $emDefault->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $em = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
            foreach ($datas as $langue => $litiges) {
                $lang = $em->getRepository(Langue::class)->findBy(array('code' => $langue))[0];
                foreach ($litiges as $litige => $codeCouleur) {
                    $l = new LitigeDossier();
                    $l->setCodeCouleur($codeCouleur);
                    $lt = new LitigeDossierTraduction();
                    $lt->setLibelle($litige)->setLangue($lang)->setLitigeDossier($l);
                    $l->addTraduction($lt);
                    $em->persist($l);
                }
            }
            $em->flush();
        }
    }
}
