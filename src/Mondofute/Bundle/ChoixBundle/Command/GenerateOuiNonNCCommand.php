<?php
namespace Mondofute\Bundle\ChoixBundle\Command;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNCTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateOuiNonNCCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {

        $this
            ->setName('OUINONNC:generer')
            ->setDescription('Génère la liste de choix oui non Vide')
            ->addArgument(
                'sites',
                InputArgument::REQUIRED,
                "Veuillez renseigner les sites pour lesquels seront généré les ligne description forfait ski"
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return boolean
     * @
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        /** @var EntityManager $emSite */
//        Récupération des sites à enregistrer
        $sites = explode(',', $input->getArgument('sites'));
        $criteres = Criteria::create();
//        gestion de l'argument soit on passe l'identifiant soit on passe le libelle du site avec creation des conditions OU pour la requete
        foreach ($sites as $site) {
            if (is_numeric($site)) {
                $criteres->orWhere($criteres->expr()->eq('id', $site));
            } else {
                $criteres->orWhere($criteres->expr()->eq('libelle', $site));
            }
        }
//        Gestion obligatoire du crm
        $criteres->orWhere($criteres->expr()->eq('crm', 1));
//        Place le crm en premier pour avoir l'objet de reference avant de traiter les sites
        $criteres->orderBy(array('crm' => "DESC", "id" => "ASC"));

        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
//        Récupération des sites avec les criteres requis
        $sites = $em->getRepository(Site::class)->matching($criteres);
        foreach ($sites as $site) {
//            récupération de l'entity manager du site à enregistrer
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
//            récupération de la langue pour le site à traiter
            $langues = $emSite->getRepository(Langue::class)->findAll();
//            récupération du site pour le site à traiter
//            $siteSite = $emSite->getRepository('MondofuteSiteBundle:Site')->findOneById($site->getId());


            $choix = new OuiNonNC();
            $choix->setClassement(1);
            foreach ($langues as $langue) {
                $choixTraduction = new OuiNonNCTraduction();
                $choixTraduction->setLangue($langue);
                $choixTraduction->setOuiNonNC($choix);
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $choixTraduction->setLibelle('oui');
                        break;
                    case 'en_EN':
                        $choixTraduction->setLibelle('yes');
                        break;
                    case 'es_ES':
                        $choixTraduction->setLibelle('si');
                        break;
                    default:
                        $choixTraduction->setLibelle('oui');
                        break;
                }
                $choix->addTraduction($choixTraduction);
            }
            $emSite->persist($choix);

            $choix = new OuiNonNC();
            $choix->setClassement(2);
            foreach ($langues as $langue) {
                $choixTraduction = new OuiNonNCTraduction();
                $choixTraduction->setLangue($langue);
                $choixTraduction->setOuiNonNC($choix);
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $choixTraduction->setLibelle('non');
                        break;
                    case 'en_EN':
                        $choixTraduction->setLibelle('no');
                        break;
                    case 'es_ES':
                        $choixTraduction->setLibelle('no');
                        break;
                    default:
                        $choixTraduction->setLibelle('non');
                        break;
                }
                $choix->addTraduction($choixTraduction);
            }
            $emSite->persist($choix);
            $choix = new OuiNonNC();
            $choix->setClassement(3);
            foreach ($langues as $langue) {
                $choixTraduction = new OuiNonNCTraduction();
                $choixTraduction->setLangue($langue);
                $choixTraduction->setOuiNonNC($choix);
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $choixTraduction->setLibelle('NC');
                        break;
                    case 'en_EN':
                        $choixTraduction->setLibelle('NC');
                        break;
                    case 'es_ES':
                        $choixTraduction->setLibelle('NC');
                        break;
                    default:
                        $choixTraduction->setLibelle('NC');
                        break;
                }
                $choix->addTraduction($choixTraduction);
            }
            $emSite->persist($choix);

            $emSite->flush();
        }
    }
}