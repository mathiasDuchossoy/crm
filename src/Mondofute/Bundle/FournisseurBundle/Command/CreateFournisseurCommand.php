<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 17/03/2016
 * Time: 08:16
 */

namespace Mondofute\Bundle\FournisseurBundle\Command;


use DateTime;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class CreateFournisseurCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fournisseur:createFournisseur')
            ->setDescription('Création d\'un fournisseur');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Fournisseur $fournisseurParent */
        $locale = 'fr_FR';
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $helper = $this->getHelper('question');

        $qEnseigne = new Question("<question>Enseigne: [Pierre et vacances]</question>\n", 'Mathias');
        $rEnseigne = $helper->ask($input, $output, $qEnseigne);

        // ***** FOURNISSEUR PARENT *****
        $fournisseurParents = $em->getRepository('MondofuteFournisseurBundle:Fournisseur')->getFournisseurDeFournisseur();

        $textQFournisseurParent = "<question>";
        foreach ($fournisseurParents as $fournisseurParent) {
            $textQFournisseurParent .= $fournisseurParent->getId() . " => " . $fournisseurParent->getEnseigne() . ", ";
        }
        $textQFournisseurParent .= "\nFournisseurParent: [null]</question>\n";

        $qFournisseurParent = new Question($textQFournisseurParent, 'null');
        $rFournisseurParent = $helper->ask($input, $output, $qFournisseurParent);
        // ***** FIN FOURNISSEUR PARENT *****

        // ***** TYPE FOURNISSEUR *****
        $typeFournisseurs = $em->getRepository('MondofuteFournisseurBundle:TypeFournisseur')->findAll();

        $textQTypeFournisseur = "<question>";
        foreach ($typeFournisseurs as $typeFournisseur) {
            $textQTypeFournisseur .= $typeFournisseur->getId() . " => " . $typeFournisseur->getLibelle() . ", ";
        }
        $textQTypeFournisseur .= "\nTypeFournisseur: [1]</question>\n";

        $qTypeFournisseur = new Question($textQTypeFournisseur, '1');
        $rTypeFournisseur = $helper->ask($input, $output, $qTypeFournisseur);
        // ***** FIN TYPE FOURNISSEUR *****

        $qContient = new Question("<question> 1 => Produit, 2 => Fournisseurs\nContient: [1]</question>\n", '1');
        $rContient = $helper->ask($input, $output, $qContient);

        switch ($rContient) {
            case '1':
                $rContient = FournisseurContient::PRODUIT;
                break;
            case '2':
                $rContient = FournisseurContient::FOURNISSEUR;
                break;
            default:
                $rContient = FournisseurContient::PRODUIT;
                break;
        }


        $qActif = new Question("<question>Yes / No\nActif: [Yes]</question>\n", 'Yes');
        $rActif = $helper->ask($input, $output, $qActif);

        switch ($rActif) {
            case 'Yes':
                $rActif = true;
                break;
            case 'No':
                $rActif = false;
                break;
            default:
                $rActif = true;
                break;
        }

//         ***** ENREGISTREMENT *****
        $dateCreation = new DateTime();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());

            $fournisseur = new Fournisseur();
            $fournisseur->setDateCreation($dateCreation)
                ->setDateModification($dateCreation);

            $fournisseur->setEnseigne($rEnseigne);
            if (!empty($rFournisseurParent)) {
                $fournisseur->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur',
                    $rFournisseurParent));
            }
            $fournisseur->setType($emSite->find('MondofuteFournisseurBundle:TypeFournisseur', $rTypeFournisseur));
            $fournisseur->setActif($rActif);
            $fournisseur->setContient($rContient);

            $emSite->persist($fournisseur);
            $emSite->flush();

        }
//         ***** FIN ENREGISTREMENT *****


    }
}