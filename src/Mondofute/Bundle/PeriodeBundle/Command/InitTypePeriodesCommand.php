<?php

namespace Mondofute\Bundle\PeriodeBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitTypePeriodesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('init:type_periodes')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
         * Initialisation des type de periodes
         */
        $typePeriodes = new ArrayCollection();
        $typePeriode = new TypePeriode();
        $typePeriode->setCourt(1)
            ->setNbJourDefaut(2);
        $typePeriodes->add($typePeriode);
        $typePeriode = new TypePeriode();
        $typePeriode->setCourt(0)
            ->setNbJourDefaut(7);
        $typePeriodes->add($typePeriode);
        $typePeriode = new TypePeriode();
        $typePeriode->setCourt(0)
            ->setNbJourDefaut(14);
        $typePeriodes->add($typePeriode);
        $typePeriode = new TypePeriode();
        $typePeriode->setCourt(0)
            ->setNbJourDefaut(21);
        $typePeriodes->add($typePeriode);

        $this->enregistrer($typePeriodes);

    }

    protected function enregistrer($typePeriodes)
    {
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $sites = $em->getRepository(Site::class)->findAll();
//        dump($sites);
        /** @var Site $site */
        foreach ($sites as $site) {
            /** @var EntityManagerInterface $emSite */
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
            foreach ($typePeriodes as $typePeriode) {
                $emSite->persist($typePeriode);
            }
            $emSite->flush();
        }
    }
}
