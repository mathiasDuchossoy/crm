<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 17/03/2016
 * Time: 08:16
 */

namespace Mondofute\Bundle\FournisseurBundle\Command;


use commun\moyencommunicationBundle\Entity\Fixe;
use commun\moyencommunicationBundle\Entity\Mobile;
use commun\moyencommunicationBundle\Entity\MoyenCommunication;
use commun\moyencommunicationBundle\moyenCommunicationBundle;
use DateTime;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class UpdateInterlocuteurCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fournisseur:updateInterlocuteur')
            ->setDescription('Mis à jour d\'un interlocteur');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var MoyenCommunication $moyenCommunication */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $locale = 'fr_FR';

        $interlocuteur = $em->find('MondofuteFournisseurBundle:Interlocuteur', 50);


        $helper = $this->getHelper('question');

        // ***** MOYEN COMMUNICATION  *****
        $moyenCommunications = $interlocuteur->getMoyenCommunications();

        $textQMoyenComm = "<question>";
        foreach ($moyenCommunications as $moyenCommunication) {
            $typeComm = (new ReflectionClass($moyenCommunication))->getShortName();
            $textQMoyenComm .= '[' . $moyenCommunication->getId() . "] $typeComm => ";
            switch ($typeComm) {
                case 'Adresse':
                    $textQMoyenComm .= $moyenCommunication->getCodePostal() . "\n";
                    break;
                case 'Email':
                    $textQMoyenComm .= $moyenCommunication->getAdresse() . "\n";
                    break;
                case 'Telecopie':
                    $textQMoyenComm .= $moyenCommunication->getNumero() . "\n";
                    break;
                case 'Fixe':
                    $textQMoyenComm .= $moyenCommunication->getNumero() . "\n";
                    break;
                case 'Mobile':
                    $textQMoyenComm .= $moyenCommunication->getNumero() . "\n";
                    break;
                default:
                    break;
            }

        }
        $textQMoyenComm .= "Choix:</question>\n";

        $qMoyenComm = new Question($textQMoyenComm, null);
        $rMoyenComm = $helper->ask($input, $output, $qMoyenComm);

        $moyenCommunication = $interlocuteur->getMoyenCommunications()->filter(function (MoyenCommunication $element
        ) use ($rMoyenComm) {
            return $element->getId() == $rMoyenComm;
        })->first();

        $typeComm = (new ReflectionClass($moyenCommunication))->getShortName();

        $textQMoyenComm = "<question>";
        switch ($typeComm) {
            case 'Adresse':
                $textQMoyenComm .= "Entrer le nouveau code postal\n";
                break;
            case 'Email':
                $textQMoyenComm .= "Entrer la nouvelle adresse\n";
                break;
            case 'Telecopie':
                $textQMoyenComm .= "Entrer le nouveau numéro\n";
                break;
            case 'Fixe':
                $textQMoyenComm .= "Entrer le nouveau numéro\n";
                break;
            case 'Mobile':
                $textQMoyenComm .= "Entrer le nouveau numéro\n";
                break;
            default:
                break;
        }
        $textQMoyenComm .= "</question>\n";

        $qMoyenComm = new Question($textQMoyenComm, null);
        $rMoyenComm = $helper->ask($input, $output, $qMoyenComm);

        switch ($typeComm) {
            case 'Adresse':
                $moyenCommunication->setCodePOstal($rMoyenComm);
                break;
            case 'Email':
                $moyenCommunication->setAdresse($rMoyenComm);
                break;
            case 'Telecopie':
                $moyenCommunication->setNumero($rMoyenComm);
                break;
            case 'Fixe':
                $moyenCommunication->setNumero($rMoyenComm);
                break;
            case 'Mobile':
                $moyenCommunication->setNumero($rMoyenComm);
                break;
            default:
                break;
        }
        // ***** FIN MOYEN COMMUNICATION *****


        // ***** ENREGISTREMENT *****
        $dateModification = new DateTime();

        $interlocuteur->setDateModification($dateModification);

        $em->persist($interlocuteur);
        $em->flush();

//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
//        foreach($sites as $site){
//            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());
//            $interlocuteur->setDateModification($dateModification);
//
//            $emSite->persist($interlocuteur);
//            $emSite->flush();
//
//        }
        // ***** FIN ENREGISTREMENT *****


    }
}