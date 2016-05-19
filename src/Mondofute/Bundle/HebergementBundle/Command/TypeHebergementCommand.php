<?php

namespace Mondofute\Bundle\HebergementBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergementUnifie;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LangueBundle\Entity\LangueTraduction;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class TypeHebergementCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('typeHebergement:ajouter')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Site $site */
//        récupération de l'entity manager
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');

        $output->setDecorated(true);
//        récupère les sites pour lesquels nous souhaitons ajouter un type d'hébergement
        $sites = $this->getSites($input, $output);

        $typeHebergementUnifie = new TypeHebergementUnifie();
//        boucle sur les sites pour lesquels nous souhaitons enregistrer les types hébergements
        foreach ($sites as $site) {
            $output->writeln('<info>' . strtoupper('SITE : ' . $site->getLibelle()) . '</info>');
//            gestion de la traduction
            $traductions = $this->getTraductions($input, $output);
            $individuel = $this->getIndividuel($input, $output);
            $typeHebergement = new TypeHebergement();
            $typeHebergement->setIndividuel($individuel);
            $typeHebergement->setSite($site);
            foreach ($traductions as $traduction) {
                $typeHebergement->addTraduction($traduction);
                $em->persist($traduction);
            }
            $typeHebergementUnifie->addTypeHebergement($typeHebergement);
            $em->persist($typeHebergement);
        }
        $em->persist($typeHebergementUnifie);
        $em->flush();

        $this->copieVersSites($sites, $typeHebergementUnifie);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ArrayCollection
     */
    public function getSites(InputInterface $input, OutputInterface $output)
    {
        $tabSites = array();
        $retourSites = new ArrayCollection();
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
//        charger les sites
        $sites = $em->getRepository(Site::class)->findAll();
//        création de la question
        $questionDialog = $this->getHelper('question');
        $question = 'Choisir un site à ajouter : ' . PHP_EOL;
        $question .= '\'a\' pour annuler et sortir du programme ' . PHP_EOL;
        $question .= '\'q\' pour sortir et/ou valider vos choix' . PHP_EOL;
        foreach ($sites as $site) {
            $tabSites[$site->getId()] = $site;
            $question .= '- ' . $site->getId() . ' : ' . $site->getLibelle() . PHP_EOL;
        }
        $question .= '\'all\' pour ajouter tous les sites' . PHP_EOL;
        $question = new Question($question);
//        pose la question tant que l'utilisateur n'a pas demandé à sortir
        do {
            $reponse = $questionDialog->ask($input, $output, $question);
//            gestion de la réponse de l'utilisateur
//            test que la réponse soit numérique
            if (!is_numeric($reponse)) {
//                cas d'une réponse non numérique
                if ($reponse == 'q') {
//                    demande de sortir
                    $sortir = true;
                } elseif ($reponse == 'a') {
//                    annulation, vide les sites à traiter et demande de sortir
                    $output->writeln('<info>vous avez annulé la création du type d\'hébergement</info>');
                    exit();
                } elseif ($reponse == 'all') {
//                    balaye tous les sites et les ajoute aux sites a enregistrer
                    foreach ($sites as $site) {
                        $retourSites->add($site);

                    }
//                    sors de la fonction
                    $sortir = true;
                } else {
//                    mauvais choix de l'utilisateur
                    $output->writeln('Vous devez choisir un des choix proposé ou \'q\' pour quitter)');
                    $sortir = false;
                }
            } else {
//                reponse numérique 
                if (!empty($tabSites[$reponse])) {
//                    réponse de l'utilisateur acceptable
                    if (!$retourSites->contains($tabSites[$reponse])) {
//                        ajoute aux sites à retourner si le site n'est pas déjà présent
                        $retourSites->add($tabSites[$reponse]);
                    }
                    $sortir = false;
                } else {
//                    mauvais choix de l'utilisateur
                    $output->writeln('Vous devez choisir un des choix proposé ou \'q\' pour quitter)');
                    $sortir = false;
                }
            }
        } while ($sortir == false);
        return $retourSites;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ArrayCollection
     */
    public function getTraductions(InputInterface $input, OutputInterface $output)
    {
        /** @var Langue $langue */
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $traductions = new ArrayCollection();
//        récupère les langues du système
        $langues = $em->getRepository(Langue::class)->findAll();
//        boucle sur les langues
        foreach ($langues as $langue) {
//            recuperation du libelle pour la langue
            $libelle = $this->getLibelle($input, $output, $langue);
            if ($libelle != false) {
//            création de l'objet traduction
                $traduction = new TypeHebergementTraduction();
                $traduction->setLangue($langue);
                $traduction->setLibelle($libelle);
                $traductions->add($traduction);
            }
        }
        return $traductions;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Langue $langue
     * @return mixed
     */
    public function getLibelle(InputInterface $input, OutputInterface $output, Langue $langue)
    {
        /** @var LangueTraduction $traduction */
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');

        $questionDialog = $this->getHelper('question');
//        récupération de la langue FR
        $langueFR = $em->getRepository(Langue::class)->findOneBy(array('code' => 'fr_FR'));
        $question = 'Veuiller entrer le libelle pour la langue [';
//        recherche du libelle en français de la langue à renseigner
        foreach ($langue->getTraductions() as $traduction) {
            if ($traduction->getLangueTraduction() == $langueFR) {
                $question .= $traduction->getLibelle();
                break;
            }
        }
        $question .= '] (\'a\' pour annuler et sortir) : ';
        $question = new Question($question);
//        récupération du libelle
        $libelle = $questionDialog->ask($input, $output, $question);
        if ($libelle == 'a') {
//            l'utilisateur a annulé sors du programme
            $output->writeln('<info>vous avez annulé la création du type d\'hébergement</info>');
            exit();
        }
        return $libelle;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function getIndividuel(InputInterface $input, OutputInterface $output)
    {
        $sortir = false;
        $questionDialog = $this->getHelper('question');
        $question = 'Votre type d\'hébergement est individuel? ' . PHP_EOL . '\'o\' = oui  ' . PHP_EOL . '\'n\' =non ' . PHP_EOL . '\'a\' = annuler et sortir du programme' . PHP_EOL;
        $question = new Question($question);
        do {
            $reponse = $questionDialog->ask($input, $output, $question);
            switch ($reponse) {
                case 'o':
                    $reponse = true;
                    $sortir = true;
                    break;
                case 'n':
                    $reponse = false;
                    $sortir = true;
                    break;
                case 'a':
//                    l'utilisateur a annulé sors du programme
                    $output->writeln('<info>vous avez annulé la création du type d\'hébergement</info>');
                    exit();
                    break;
                default:
                    $output->writeln('<info>Votre réponse n\'est pas acceptable, veuillez saisir à nouveau votre choix</info>');
                    break;
            }
        } while ($sortir == false);
        return $reponse;
    }

    /**
     * @param ArrayCollection $sites
     * @param TypeHebergementUnifie $typeHebergementUnifie
     */
    public function copieVersSites(ArrayCollection $sites, TypeHebergementUnifie $typeHebergementUnifie)
    {

        /** @var Site $site */
        /** @var TypeHebergement $typeHebergement */
        /** @var TypeHebergementTraduction $traduction */
        /** @var EntityManager $emSite */
        foreach ($sites as $site) {
            if ($site->getCrm() == false) {
                $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
                $typeHebergementUnifieSite = new TypeHebergementUnifie();
                foreach ($typeHebergementUnifie->getTypeHebergements() as $typeHebergement) {
                    if ($typeHebergement->getSite() == $site) {
                        $siteSite = $emSite->getRepository(Site::class)->findOneBy(array('id' => $site->getId()));
                        $typeHebergementSite = new TypeHebergement();
                        $typeHebergementSite->setIndividuel($typeHebergement->getIndividuel());
                        $typeHebergementSite->setSite($siteSite);
//                $emSite->persist($emSite);
                        foreach ($typeHebergement->getTraductions() as $traduction) {
                            $traductionSite = new TypeHebergementTraduction();
                            $langueSite = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $traduction->getLangue()->getId()));
                            $traductionSite->setLangue($langueSite);
                            $traductionSite->setLibelle($traduction->getLibelle());
                            $typeHebergementSite->addTraduction($traductionSite);
                            $emSite->persist($traductionSite);
                        }
                        $typeHebergementUnifieSite->addTypeHebergement($typeHebergementSite);
                        $emSite->persist($typeHebergementSite);
                    }

                }
                $emSite->persist($typeHebergementUnifieSite);
                $emSite->flush();

            }
        }
    }
}
