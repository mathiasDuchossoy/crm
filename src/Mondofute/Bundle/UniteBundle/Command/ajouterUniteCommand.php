<?php
namespace Mondofute\Bundle\UniteBundle\Command;


//use Mondofute\Bundle\SiteBundle\Entity\SiteUnite;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
use Mondofute\Bundle\UniteBundle\Entity\UniteAge;
use Mondofute\Bundle\UniteBundle\Entity\UniteClassementHebergement;
use Mondofute\Bundle\UniteBundle\Entity\UniteDistance;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Mondofute\Bundle\UniteBundle\Entity\UniteTraduction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AjouterUniteCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {

        $this
            ->setName('unite:ajouter')
            ->setDescription('ajouter une unité');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return boolean
     * @
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setDecorated(true);
        /** @var Langue $langue */
        /** @var EntityManager $em */
        /** @var EntityManager $emSite */
        $discriminators = array();
        $discriminators[1] = new \stdClass();
        $discriminators[1]->classe = UniteAge::class;
        $discriminators[1]->choix = '1: Unité d\'âge';
        $discriminators[2] = new \stdClass();
        $discriminators[2]->id = 2;
        $discriminators[2]->classe = UniteTarif::class;
        $discriminators[2]->choix = '2: Unité de tarif';
        $discriminators[3] = new \stdClass();
        $discriminators[3]->classe = UniteDistance::class;
        $discriminators[3]->choix = '3: Unité de distance';
        $discriminators[4] = new \stdClass();
        $discriminators[4]->classe = UniteClassementHebergement::class;
        $discriminators[4]->choix = '4: Unité de classement d\'hébergement';

        /** @var QuestionHelper $dialog */
        $dialog = $this->getHelper('question');
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $sites = $em->getRepository(Site::class)->findAll();
        $langues = $em->getRepository(Langue::class)->findAll();
//        $output->writeln('veuillez choisir le type d\'unité :');
//        $output->writeln('1: Unité d\'âge');
//        $output->writeln('2: Unité de tarifs');
//        $output->writeln('3: Unité de distance');
//        $output->writeln('4: Unité de classement d\'hébergement');
        $qChoixUnite = '<question>veuillez choisir le type d\'unité : ' . PHP_EOL;
        foreach ($discriminators as $each) {
            $qChoixUnite .= $each->choix . PHP_EOL;
        }
        $qChoixUnite .= '</question>';
        $qChoixUnite = new Question(PHP_EOL . $qChoixUnite . PHP_EOL, null);
        $quitter = false;
        $valide = false;
        do {
            $discriminator = $dialog->ask($input, $output,
                $qChoixUnite);
            if ($discriminator == 'q') {
                $quitter = true;
            } elseif (!empty($discriminators[$discriminator])) {
                $valide = true;
            } else {
                $valide = false;
                $quitter = false;
            }
            if ($quitter !== true && $valide !== true) {
                $output->writeln('<error>le choix sélectionné n\'est pas valide veullez saisir à nouveau votre choix</error>');
            }
        } while ($quitter !== true && $valide !== true);
        if ($quitter === true) {
            $output->writeln('<info>Vous avez quitté l\'application</info>');
        } elseif ($valide === true) {
            $output->writeln('<info>paramétrage de l\'unité à créer</info>');
            $libelle = array();
            $libelleCourt = array();
            foreach ($langues as $langue) {
                $qTraduction = '<question>Veuillez choisir le nom pour la langue : ' . $langue->getCode() . '</question>' . PHP_EOL;
                $qTraduction = new Question($qTraduction, null);
                $libelle[$langue->getCode()] = $dialog->ask($input, $output, $qTraduction);
                $qTraduction = '<question>Veuillez choisir le nom court pour la langue : ' . $langue->getCode() . '</question>' . PHP_EOL;
                $qTraduction = new Question($qTraduction, null);
                $libelleCourt[$langue->getCode()] = $dialog->ask($input, $output, $qTraduction);
            }
            foreach ($sites as $site) {
                $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
                $languesSite = $emSite->getRepository(Langue::class)->findAll();
                // generer les unites
                /** @var Unite $unite */
                $unite = new $discriminators[$discriminator]->classe;
                $unite->setMultiplicateurReference(1);
                $unite->setReference(null);
                foreach ($languesSite as $langue) {

                    /** @var UniteTraduction $uniteTraduction */
                    $uniteTraduction = new UniteTraduction();
                    $uniteTraduction->setLibelle($libelle[$langue->getCode()]);
                    $uniteTraduction->setLibelleCourt($libelleCourt[$langue->getCode()]);
                    $uniteTraduction->setLangue($langue);
                    $uniteTraduction->setUnite($unite);
                    $unite->addTraduction($uniteTraduction);
                    $emSite->persist($uniteTraduction);
                }
//                $uniteReference = $unite;
                $emSite->persist($unite);
                $emSite->flush();
//                dump($unite);
            }
        }
    }
}