<?php

namespace Mondofute\Bundle\FournisseurBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\FournisseurBundle\Entity\ConditionAnnulationDescription;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class initConditionAnnulationDescriptionCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_fournisseur:init_condition_annulation_description_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();

        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());
            $conditionAnnulationDescription = new ConditionAnnulationDescription();
            $conditionAnnulationDescription->setId(1);
            $conditionAnnulationDescription->setStandard(true);

            $metadata = $emSite->getClassMetadata(get_class($conditionAnnulationDescription));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            $description = 'Possibilité de relouer une annulation' . PHP_EOL . 'J-21 à J-15 = 25% du prix net' . PHP_EOL . 'J-14 à J-  8 = 50% du prix net' . PHP_EOL . 'J-  7 à J-  1 = 75% du prix net' . PHP_EOL . 'non présentation = 100% du prix net';
            $conditionAnnulationDescription->setDescription($description);

            $emSite->persist($conditionAnnulationDescription);
            $emSite->flush();
        }
    }
}
