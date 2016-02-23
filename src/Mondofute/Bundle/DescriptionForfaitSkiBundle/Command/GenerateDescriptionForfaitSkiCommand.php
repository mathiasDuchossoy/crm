<?php
namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Command;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDescriptionForfaitSkiCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {

        $this
            ->setName('LDFS:generer')
            ->setDescription('Génère les descriptions forfait de ski')
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
//        die('ok');
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
//        foreach($sites as $site){
//            $output->writeln($site->getLibelle());
//        }
//        dump($sites);
//
//        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
//        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
////        $unitesAge = $em->getRepository('MondofuteUniteBundle:UniteAge')->findAll();
////        dump($unitesAge);
////        dump($sites);
//        $ligneCrm = array();
        foreach ($sites as $site) {
            $i = 1;
//            récupération de l'entity manager du site à enregistrer
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
//            récupération de la langue pour le site à traiter
            $langues = $emSite->getRepository(Langue::class)->findAll();
//            récupération du site pour le site à traiter
//            $siteSite = $emSite->getRepository('MondofuteSiteBundle:Site')->findOneById($site->getId());

            $idCategorie = 1;
//            Création de la ligne
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('1 jour Adulte');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('1 day Adult');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('1 dia mayor');
                        break;
                    default:
                        $ligneTraduction->setLibelle('1 jour Adulte');
                        break;
                }
                $ligneTraduction->setLangue($langue);
                $ligneTraduction->setTexteDur('');
                $ligneTraduction->setDescription('');
//                $ligneTraduction->setSite($siteSite);
                $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
                $emSite->persist($ligneTraduction);
                $ligne->addTraduction($ligneTraduction);

            }
            $emSite->persist($ligne);
            $emSite->flush();

//            if ($site->getCrm() == true) {
//                $ligneCrm[$i] = $ligne;
////                dump($uniteCrm);
//            } else {
//                $siteLigne = new SiteLigneDescriptionForfaitSki();
//                $siteLigne->setLigneDescriptionForfaitSkiCrm($ligneCrm[$i]);
//                $siteLigne->setLigneDescriptionForfaitSkiSiteId($ligne->getId());
//                $siteLigne->setSite($site);
//                $em->persist($siteLigne);
//                $em->flush();
//            }

            $i++;
////            Création de la ligne
//            $ligne = new LigneDescriptionForfaitSkiValeur();
////            $ligne->setTrancheAge(true);
//            $ligne->setClassement(2);
//            $ligne->setCategorie($emSite->getRepository('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSkiCategorie')->findOneById($idCategorie));
//            $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
//            $ligneTraduction->setLibelle('1 jour Enfant');
//            $ligneTraduction->setLangue($langue);
//            $ligneTraduction->setSite($siteSite);
//            $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
//            $emSite->persist($ligneTraduction);
//            $ligne->addTraduction($ligneTraduction);
//            $emSite->persist($ligne);
//            $emSite->flush();
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('1 jour Enfant');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('1 day children');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('1 dia menor');
                        break;
                    default:
                        $ligneTraduction->setLibelle('1 jour enfant');
                        break;
                }
                $ligneTraduction->setLangue($langue);
                $ligneTraduction->setTexteDur('');
                $ligneTraduction->setDescription('');
//                $ligneTraduction->setSite($siteSite);
                $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
                $emSite->persist($ligneTraduction);
                $ligne->addTraduction($ligneTraduction);

            }
            $emSite->persist($ligne);
            $emSite->flush();
            $i++;
////            Création de la ligne
//            $ligne = new LigneDescriptionForfaitSkiValeur();
////            $ligne->setTrancheAge(true);
//            $ligne->setClassement(2);
//            $ligne->setCategorie($emSite->getRepository('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSkiCategorie')->findOneById($idCategorie));
//            $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
//            $ligneTraduction->setLibelle('1 jour Enfant');
//            $ligneTraduction->setLangue($langue);
//            $ligneTraduction->setSite($siteSite);
//            $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
//            $emSite->persist($ligneTraduction);
//            $ligne->addTraduction($ligneTraduction);
//            $emSite->persist($ligne);
//            $emSite->flush();
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('1 jour Ado');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('1 day ado');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('1 dia ado');
                        break;
                    default:
                        $ligneTraduction->setLibelle('1 jour ado');
                        break;
                }
                $ligneTraduction->setLangue($langue);
                $ligneTraduction->setTexteDur('');
                $ligneTraduction->setDescription('');
//                $ligneTraduction->setSite($siteSite);
                $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
                $emSite->persist($ligneTraduction);
                $ligne->addTraduction($ligneTraduction);

            }
            $emSite->persist($ligne);
            $emSite->flush();
            $i++;
////            Création de la ligne
//            $ligne = new LigneDescriptionForfaitSkiValeur();
////            $ligne->setTrancheAge(true);
//            $ligne->setClassement(2);
//            $ligne->setCategorie($emSite->getRepository('MondofuteDescriptionForfaitSkiBundle:LigneDescriptionForfaitSkiCategorie')->findOneById($idCategorie));
//            $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
//            $ligneTraduction->setLibelle('1 jour Enfant');
//            $ligneTraduction->setLangue($langue);
//            $ligneTraduction->setSite($siteSite);
//            $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
//            $emSite->persist($ligneTraduction);
//            $ligne->addTraduction($ligneTraduction);
//            $emSite->persist($ligne);
//            $emSite->flush();
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('1 jour Ado');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('1 day ado');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('1 dia ado');
                        break;
                    default:
                        $ligneTraduction->setLibelle('1 jour ado');
                        break;
                }
                $ligneTraduction->setLangue($langue);
                $ligneTraduction->setTexteDur('');
                $ligneTraduction->setDescription('');
//                $ligneTraduction->setSite($siteSite);
                $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
                $emSite->persist($ligneTraduction);
                $ligne->addTraduction($ligneTraduction);

            }
            $emSite->persist($ligne);
            $emSite->flush();

        }
    }
}