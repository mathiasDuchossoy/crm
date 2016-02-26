<?php
namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Command;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorieTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDescriptionForfaitSkiCategorieCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {

        $this
            ->setName('DFSC:generer')
            ->setDescription('Génère les descriptions forfait de ski')
            ->addArgument(
                'sites',
                InputArgument::REQUIRED,
                "Veuillez renseigner les sites pour lesquels seront généré les catégorie"
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
//        foreach($sites as $site){
//            $output->writeln($site->getLibelle());
//        }
//        $categorieCrm = array();
        foreach ($sites as $site) {
//            récupération de l'entity manager du site à enregistrer
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
//            récupération de la langue pour le site à traiter
            $langues = $emSite->getRepository(Langue::class)->findAll();
//            récupération du site pour le site à traiter
//            $siteSite = $emSite->getRepository(Site::class)->findOneById($site->getId());
//            Boucle sur le nombre de catégorie a créer
            for ($i = 1; $i < 3; $i++) {
                $sortie = 0;
//                récupération du libelle et classement pour la catégorie à créer
                switch ($i) {
                    case 1:
//                        $libelle = 'tarifs';
                        $classement = 1;
                        break;
                    case 2:
//                        $libelle = 'informations pratiques';
                        $classement = 2;
                        break;
                    default:
                        $sortie = 1;
                        $classement = 1;
//                        $libelle = 'tarifs';
                        break;
                }
                if ($sortie === 1) {
                    break;
                }
//            création de la catégorie
                $categorie = new LigneDescriptionForfaitSkiCategorie();
                $categorie->setClassement($classement);

//            création de la traduction pour la catégorie
                foreach ($langues as $langue) {
                    switch ($langue->getCode()) {
                        case 'fr_FR':
                            switch ($i) {
                                case 1:
                                    $libelle = 'tarifs';
                                    break;
                                case 2:
                                    $libelle = 'informations pratiques';
                                    break;
                                default:
                                    $libelle = 'autre';
                                    break;
                            }
                            break;
                        case 'en_EN';
                            switch ($i) {
                                case 1:
                                    $libelle = 'prices';
                                    break;
                                case 2:
                                    $libelle = 'notices';
                                    break;
                                default:
                                    $libelle = 'other';
                                    break;
                            }
                            break;
                        case 'es_ES';
                            switch ($i) {
                                case 1:
                                    $libelle = 'pregos';
                                    break;
                                case 2:
                                    $libelle = 'informationes praticos';
                                    break;
                                default:
                                    $libelle = 'otro';
                                    break;
                            }
                            break;
                        default:
                            $libelle = '';
                            break;
                    }
                    $categorieTraduction = new LigneDescriptionForfaitSkiCategorieTraduction();
                    $categorieTraduction->setLangue($langue);
//                $categorieTraduction->setSite($siteSite);
                    $categorieTraduction->setLigneDescriptionForfaitSkiCategorie($categorie);
                    $categorieTraduction->setLibelle($libelle);


                    $categorie->addTraduction($categorieTraduction);
                    $emSite->persist($categorieTraduction);

                }

                $emSite->persist($categorie);
                $emSite->flush();
            }
        }
    }
}