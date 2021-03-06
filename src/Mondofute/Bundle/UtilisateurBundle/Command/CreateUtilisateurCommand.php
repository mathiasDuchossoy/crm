<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 17/03/2016
 * Time: 08:16
 */

namespace Mondofute\Bundle\UtilisateurBundle\Command;


use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Mondofute\Bundle\UtilisateurBundle\Entity\UtilisateurUser;
use Nucleus\MoyenComBundle\Entity\Email;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class CreateUtilisateurCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('utilisateur:createUtilisateur')
            ->setDescription('Création d\'un interlocteur');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();


        $helper = $this->getHelper('question');
        $qPrenom = new Question("<question>Prenom: [Admin]</question>\n", 'Admin');
        $rPrenom = $helper->ask($input, $output, $qPrenom);

        $qNom = new Question("<question>Nom: [Admin]</question>\n", 'Admin');
        $rNom = $helper->ask($input, $output, $qNom);

        do {
            $qMail = new Question("<question>Mail / Login: [admin@mondofute.com]</question>\n", 'admin@mondofute.com');
            $rMail = $helper->ask($input, $output, $qMail);
        } while (!empty($em->getRepository(UtilisateurUser::class)->findOneBy(array('email' => $rMail))));

        $qPwd = new Question("<question>Mot de passe: [pass]</question>\n", 'pass');
        $rPwd = $helper->ask($input, $output, $qPwd);

        // ***** ENREGISTREMENT CRM*****
        /** @var Utilisateur $utilisateur */
        $utilisateur = new Utilisateur();
        $utilisateurUser = new UtilisateurUser();
        $mail = new Email();

        $mail->setAdresse($rMail);

        $utilisateur
            ->setPrenom($rPrenom)
            ->setNom($rNom)
            ->addMoyenCom($mail);

        $utilisateurUser
            ->setUsername($rMail)
            ->setEmail($rMail)
            ->setPlainPassword($rPwd)
            ->setUtilisateur($utilisateur)
            ->setEnabled(true)
            ->addRole($utilisateurUser::ROLE_SUPER_ADMIN);

        $em->persist($utilisateur);
        $em->persist($utilisateurUser);
        $em->flush();
        // ***** FIN ENREGISTREMENT CRM*****

        // ***** ENREGISTREMENT *****
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array('crm' => 0));
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine')->getEntityManager($site->getLibelle());

            /** @var Utilisateur $utilisateur */
            $utilisateurSite = new Utilisateur();
            $utilisateurUserSite = new UtilisateurUser();
            $mail = new Email();

            $utilisateurSite->setId($utilisateur->getId());
            $metadata = $emSite->getClassMetadata(get_class($utilisateurSite));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);


            $utilisateurUserSite->setId($utilisateurUser->getId());
            $metadata = $emSite->getClassMetadata(get_class($utilisateurUserSite));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            $mail->setAdresse($rMail);

            $utilisateurSite
                ->setPrenom($rPrenom)
                ->setNom($rNom)
                ->addMoyenCom($mail);

            $utilisateurUserSite
                ->setUsername($rMail)
                ->setEmail($rMail)
                ->setPlainPassword($rPwd)
                ->setUtilisateur($utilisateurSite)
                ->setEnabled(true)
                ->addRole($utilisateurUserSite::ROLE_SUPER_ADMIN);

            $emSite->persist($utilisateurSite);
            $emSite->persist($utilisateurUserSite);
            $emSite->flush();
        }
        // ***** FIN ENREGISTREMENT *****

        echo PHP_EOL . "L'utilisateur a bien été créé.";
    }
}