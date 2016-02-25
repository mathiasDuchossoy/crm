<?php
namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Command;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Age;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
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
//            1 JOUR ADULTE
            $ligne = new LigneDescriptionForfaitSki();

            $ageMin = new Age();
            $ageMin->setValeur(18);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(59);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(15);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

            $ligne->setAgeMin($ageMin);
            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

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
                $ligneTraduction->setLigneDescriptionForfaitSki($ligne);
                $emSite->persist($ligneTraduction);
                $ligne->addTraduction($ligneTraduction);

            }
            $emSite->persist($ligne);
//            $emSite->flush();
            $i++;

//            1 JOUR ENFANT
            $ligne = new LigneDescriptionForfaitSki();

            $ageMax = new Age();
            $ageMax->setValeur(12);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(7);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

//            $ligne->setAgeMin($ageMin);
            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

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
//            $emSite->flush();
            $i++;

//            1 JOUR ADO
            $ligne = new LigneDescriptionForfaitSki();

            $ageMin = new Age();
            $ageMin->setValeur(13);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(17);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(13);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

            $ligne->setAgeMin($ageMin);
            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

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
//            $emSite->flush();
            $i++;

//            1 JOUR ETUDIANT
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

            $ageMin = new Age();
            $ageMin->setValeur(13);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(17);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(14);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('1 jour Etudiant');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('1 day student');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('1 dia estudiante');
                        break;
                    default:
                        $ligneTraduction->setLibelle('1 jour etudiant');
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
//            $emSite->flush();
            $i++;

//            1 JOUR SENIOR
            $ligne = new LigneDescriptionForfaitSki();

            $ageMin = new Age();
            $ageMin->setValeur(60);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(17);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(13.5);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));


            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('1 jour senior');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('1 day older');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('1 dia abuelo');
                        break;
                    default:
                        $ligneTraduction->setLibelle('1 jour senior');
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
//            $emSite->flush();
            $i++;

//            6 JOURS ADULTE
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);
            $ageMin = new Age();
            $ageMin->setValeur(18);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(59);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(85);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));


            $ligne->setAgeMin($ageMin);
            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('6 jours Adulte');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('6 days Adult');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('6 dias mayor');
                        break;
                    default:
                        $ligneTraduction->setLibelle('6 jours Adulte');
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
//            $emSite->flush();
            $i++;

//            6 JOURS ENFANT
            $ligne = new LigneDescriptionForfaitSki();

//            $ageMin = new Age();
//            $ageMin->setValeur(12);
//            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
            $ageMax = new Age();
            $ageMax->setValeur(12);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(40);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

//            $ligne->setAgeMin($ageMin);
            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('6 jours Enfant');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('6 days children');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('6 dias menor');
                        break;
                    default:
                        $ligneTraduction->setLibelle('6 jours enfant');
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
//            $emSite->flush();
            $i++;

//            6 JOURS ADO
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

            $ageMin = new Age();
            $ageMin->setValeur(13);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(17);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(75);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

            $ligne->setAgeMin($ageMin);
            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('6 jour Ado');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('6 day ado');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('6 dia ado');
                        break;
                    default:
                        $ligneTraduction->setLibelle('6 jour ado');
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
//            $emSite->flush();
            $i++;

//            6 JOURS ETUDIANT
            $ligne = new LigneDescriptionForfaitSki();

            $ageMin = new Age();
            $ageMin->setValeur(13);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(17);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(82);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('6 jours Etudiant');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('6 days student');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('6 dias estudiante');
                        break;
                    default:
                        $ligneTraduction->setLibelle('6 jours etudiant');
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
//            $emSite->flush();
            $i++;

//              6 JOURS SENIOR
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

            $ageMin = new Age();
            $ageMin->setValeur(60);
            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $ageMax = new Age();
            $ageMax->setValeur(17);
            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 2)));
            $tarif = new Tarif();
            $tarif->setValeur(77);
            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id' => 3)));

            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
            $ligne->setPrix($tarif);

            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('6 jours senior');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('6 days older');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('6 dias abuelo');
                        break;
                    default:
                        $ligneTraduction->setLibelle('6 jours senior');
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
            $i++;

//              FORFAIT FAMILLE
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

//            $ageMin = new Age();
//            $ageMin->setValeur(60);
//            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $ageMax = new Age();
//            $ageMax->setValeur(17);
//            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $tarif = new Tarif();
//            $tarif->setValeur(77);
//            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
//            $ligne->setPrix($tarif);
            $ligne->setPresent($emSite->getRepository(OuiNonNC::class)->findOneBy(array('id' => 3)));
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('forfait famille');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('family pass');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('forfeto familial');
                        break;
                    default:
                        $ligneTraduction->setLibelle('forfait famille');
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
            $i++;


            $idCategorie = 2;
//              FORFAIT SKI GRATUIT
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

//            $ageMin = new Age();
//            $ageMin->setValeur(60);
//            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $ageMax = new Age();
//            $ageMax->setValeur(17);
//            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $tarif = new Tarif();
//            $tarif->setValeur(77);
//            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
//            $ligne->setPrix($tarif);
            $ligne->setPresent($emSite->getRepository(OuiNonNC::class)->findOneBy(array('id' => 3)));
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('forfait ski gratuit');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('free ski pass');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('forfeto ski gratis');
                        break;
                    default:
                        $ligneTraduction->setLibelle('forfait ski gratuit');
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
            $i++;

//              REMONTEES MECANIQUE GRATUITES
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

//            $ageMin = new Age();
//            $ageMin->setValeur(60);
//            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $ageMax = new Age();
//            $ageMax->setValeur(17);
//            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $tarif = new Tarif();
//            $tarif->setValeur(77);
//            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
//            $ligne->setPrix($tarif);
            $ligne->setPresent($emSite->getRepository(OuiNonNC::class)->findOneBy(array('id' => 3)));
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('remontées mécaniques gratuites');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('free RM');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('RM gratis');
                        break;
                    default:
                        $ligneTraduction->setLibelle('remontées mécaniques gratuites');
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
            $i++;

//              SUPPORT MAINS LIBRES
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

//            $ageMin = new Age();
//            $ageMin->setValeur(60);
//            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $ageMax = new Age();
//            $ageMax->setValeur(17);
//            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $tarif = new Tarif();
//            $tarif->setValeur(77);
//            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
//            $ligne->setPrix($tarif);
            $ligne->setPresent($emSite->getRepository(OuiNonNC::class)->findOneBy(array('id' => 3)));
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('support Mains Libres');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('Free hands support');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('manos libres');
                        break;
                    default:
                        $ligneTraduction->setLibelle('support Mains Libres');
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
            $i++;

//              PIECE D IDENTITE
            $ligne = new LigneDescriptionForfaitSki();
//            $ligne->setTrancheAge(true);

//            $ageMin = new Age();
//            $ageMin->setValeur(60);
//            $ageMin->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $ageMax = new Age();
//            $ageMax->setValeur(17);
//            $ageMax->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>2)));
//            $tarif = new Tarif();
//            $tarif->setValeur(77);
//            $tarif->setUnite($emSite->getRepository(Unite::class)->findOneBy(array('id'=>3)));

//            $ligne->setAgeMin($ageMin);
//            $ligne->setAgeMax($ageMax);
//            $ligne->setPrix($tarif);
            $ligne->setPresent($emSite->getRepository(OuiNonNC::class)->findOneBy(array('id' => 3)));
            $ligne->setClassement($i);
            $ligne->setQuantite(0);
            $ligne->setCategorie($emSite->find(LigneDescriptionForfaitSkiCategorie::class, $idCategorie));
            foreach ($langues as $langue) {
                $ligneTraduction = new LigneDescriptionForfaitSkiTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $ligneTraduction->setLibelle('pièce d\'identité');
                        break;
                    case 'en_EN':
                        $ligneTraduction->setLibelle('identity pieces');
                        break;
                    case 'es_ES':
                        $ligneTraduction->setLibelle('identidad');
                        break;
                    default:
                        $ligneTraduction->setLibelle('pièce d\'identité');
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