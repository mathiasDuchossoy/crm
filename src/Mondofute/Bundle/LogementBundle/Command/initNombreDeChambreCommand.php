<?php

namespace Mondofute\Bundle\LogementBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\NombreDeChambre;
use Mondofute\Bundle\LogementBundle\Entity\NombreDeChambreTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class initNombreDeChambreCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_logement:init_nombre_de_chambre_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $emSite */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $sites = $em->getRepository(Site::class)->findAll();
        /** @var Site $site */
        foreach ($sites as $site){
            $emSite = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());
            $langues = $emSite->getRepository(Langue::class)->findAll();

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(1);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('1 séjour-chambre (Studio)')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(2);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(3);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('1 chambre')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(4);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('1 chambre + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(5);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('2 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(6);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('2 chambres + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(7);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('3 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(8);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('3 chambres + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(9);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('4 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(10);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('4 chambres + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(11);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('5 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(12);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('5 chambres + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(13);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('6 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(14);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('6 chambres + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(15);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('7 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(16);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('7 chambres + 1 chambre-cabine')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(17);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('8 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(18);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('9 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(19);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('10 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(20);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('11 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $nombreDeChambre = new NombreDeChambre();
            $nombreDeChambre->setId(21);

            $metadata = $emSite->getClassMetadata(get_class($nombreDeChambre));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            foreach ($langues as $langue){
                $traduction = new NombreDeChambreTraduction();
                $nombreDeChambre->addTraduction($traduction);
                $traduction
                    ->setLangue($langue)
                    ->setLibelle('12 chambres')
                ;
            }

            $emSite->persist($nombreDeChambre);

            $emSite->flush();

        }

    }
}
