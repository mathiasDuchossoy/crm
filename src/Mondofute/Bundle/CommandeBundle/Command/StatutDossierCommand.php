<?php

namespace Mondofute\Bundle\CommandeBundle\Command;

use Mondofute\Bundle\CommandeBundle\Entity\GroupeStatutDossier;
use Mondofute\Bundle\CommandeBundle\Entity\GroupeStatutDossierTraduction;
use Mondofute\Bundle\CommandeBundle\Entity\StatutDossier;
use Mondofute\Bundle\CommandeBundle\Entity\StatutDossierTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatutDossierCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_commande_statut_dossier:init')
            ->setDescription('Initialisation des groupe statut dossier et des statuts');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emDefault = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
//        création du tableau des groupes avec leurs statuts
        $datas = array(
            'fr_FR' => array(
                'Pré Réservation' => array('active' => '#ff97e8', 'abandonnnée' => '', 'transformée' => ''),
                'Option' => array('attente fournisseur' => '#ffff00', 'ok fournisseur' => '#ffff00'),
                'Acompte' => array('attente fournisseur' => '#ffc000', 'ok fournisseur' => '#a4c2f4'),
                'Payé' => array(
                    'ok fournisseur' => '#538dd5',
                    'ok fournisseur - voucher envoyé' => '#00b050',
                    'attente fournisseur' => '#ffc000'
                ),
                'Annulation' => array('par fournisseur' => '#ff0000', 'par client' => '#ff0000', 'clôturée' => ''),
                'Client parti' => array(
                    'Fournisseur(s) non payé(s)' => '#808080',
                    'Fournisseur(s) partiellement payé(s)' => '#a6a6a6'
                ),
                '' => array('Dossier OK clôture - Fournisseur(s) payé(s)' => '#bfbfbf', 'Suppression Dossier' => '')
            )
        );
        $i = 0;
        $sites = $emDefault->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $em = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
            foreach ($datas as $langue => $groupes) {
                //        récupère la langue
                $l = $em->getRepository(Langue::class)->findBy(array('code' => $langue))[0];
                foreach ($groupes as $groupe => $statuts) {
                    if (!empty($groupe)) {
                        $output->write('génération du groupe :' . $groupe);
                        $g = new GroupeStatutDossier();
                        $g->setLibelle($groupe);
                        $gt = new GroupeStatutDossierTraduction();
                        $gt->setLibelle($groupe)
                            ->setGroupeStatutDossier($g)
                            ->setLangue($l);
                        try {
                            $em->persist($g);
                            $em->persist($gt);
                            $output->writeln('[OK]' . PHP_EOL);
                        } catch (Exception $e) {
                            $output->writeln('[ERREUR]');
                            die;
                        }
                    } else {
                        $g = null;
                    }

                    foreach ($statuts as $statut => $codeCouleur) {
                        $s = new StatutDossier();
                        $st = new StatutDossierTraduction();
                        $st->setLibelle($statut);
                        $st->setLangue($l);
                        $st->setStatutDossier($s);
                        $s->setGroupeStatutDossier($g);
                        $s->addTraduction($st);
                        if (!empty($codeCouleur)) {
                            $s->setCodeCouleur($codeCouleur);
                        } else {
                            $s->setCodeCouleur('#ffffff');
                        }
                        $em->persist($s);
                        $output->writeln($statut);
                    }
                }
            }
            $em->flush();
        }
    }
}
