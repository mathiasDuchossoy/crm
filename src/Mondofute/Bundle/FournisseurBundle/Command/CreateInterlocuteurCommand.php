<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 17/03/2016
 * Time: 08:16
 */

namespace Mondofute\Bundle\FournisseurBundle\Command;


use DateTime;
use Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class CreateInterlocuteurCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fournisseur:createInterlocuteur')
            ->setDescription('Création d\'un interlocteur');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = 'fr_FR';

        $helper = $this->getHelper('question');
        $qPrenom = new Question("<question>Prenom: [Mathias]</question>\n", 'Mathias');
        $rPrenom = $helper->ask($input, $output, $qPrenom);

        $qNom = new Question("<question>Nom: [Duch]</question>\n", 'Duch');
        $rNom = $helper->ask($input, $output, $qNom);

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        // ***** FONCTION *****
        $fonctions = $em->getRepository('MondofuteFournisseurBundle:InterlocuteurFonction')->findAll();

        $textQFonction = "<question>";
        foreach ($fonctions as $key => $fonction) {
            $fonctionTraduction = $fonction->getTraductions()->filter(function (
                \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonctionTraduction $element
            ) use ($locale) {
                return $element->getLangue()->getCode() == $locale;
            })->first();
            $textQFonction .= $fonction->getId() . " => " . $fonctionTraduction->getLibelle() . ", ";
        }
        $textQFonction .= "\nFonction: [1]</question>\n";

        $qFonction = new Question($textQFonction, '1');
        $rFonction = $helper->ask($input, $output, $qFonction);
        // ***** FIN FONCTION *****

        // ***** SERVICE *****
        $services = $em->getRepository('MondofuteFournisseurBundle:ServiceInterlocuteur')->findAll();

        $textQService = "<question>";
        foreach ($services as $key => $service) {
            $serviceTraduction = $service->getTraductions()->filter(function (
                \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteurTraduction $element
            ) use ($locale) {
                return $element->getLangue()->getCode() == $locale;
            })->first();
            $textQService .= $service->getId() . " => " . $serviceTraduction->getLibelle() . ", ";
        }
        $textQService .= "\nService: [1]</question>\n";

        $qService = new Question($textQService, '1');
        $rService = $helper->ask($input, $output, $qService);
        // ***** FIN SERVICE *****

        $qMail = new Question("<question>Mail: [mathias@mondofute.com]</question>\n", 'mathias@mondofute.com');
        $rMail = $helper->ask($input, $output, $qMail);

        $qTelephone1 = new Question("<question>Telephone1: [0123456789]</question>\n", '0123456789');
        $rTelephone1 = $helper->ask($input, $output, $qTelephone1);

        $qTelephone2 = new Question("<question>Telephone2: [0123456789]</question>\n", '0123456989');
        $rTelephone2 = $helper->ask($input, $output, $qTelephone2);

        $qPortable = new Question("<question>Portable: [0623456789]</question>\n", '0623456789');
        $rPortable = $helper->ask($input, $output, $qPortable);

        // ***** ENREGISTREMENT *****
        $dateCreation = new DateTime();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
            $interlocuteur = new Interlocuteur();
            $interlocuteur->setDateCreation($dateCreation)
                ->setDateModification($dateCreation);
            $interlocuteur->setDateNaissance($dateCreation)
                ->setPrenom($rPrenom)
                ->setNom($rNom)
                ->setActif(true);

            $telephone1 = new Fixe();
            $telephone1->setNumero($rTelephone1);
            $interlocuteur->addMoyenCommunication($telephone1);

            $telephone2 = new Fixe();
            $telephone2->setNumero($rTelephone2);
            $interlocuteur->addMoyenCommunication($telephone2);

            $portable = new Mobile();
            $portable->setSmsing(true)
                ->setNumero($rPortable);
            $interlocuteur->addMoyenCommunication($portable);

            $interlocuteur->setFonction($emSite->find('MondofuteFournisseurBundle:InterlocuteurFonction', $rFonction));
            $interlocuteur->setService($emSite->find('MondofuteFournisseurBundle:ServiceInterlocuteur', $rService));

            $emSite->persist($interlocuteur);
            $emSite->flush();

        }
        // ***** FIN ENREGISTREMENT *****


    }
}