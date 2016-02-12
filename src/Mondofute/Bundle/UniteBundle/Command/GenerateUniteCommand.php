<?php
namespace Mondofute\Bundle\UniteBundle\Command;


//use Mondofute\Bundle\SiteBundle\Entity\SiteUnite;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\UniteAge;
use Mondofute\Bundle\UniteBundle\Entity\UniteDistance;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Mondofute\Bundle\UniteBundle\Entity\UniteTraduction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateUniteCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {

        $this
            ->setName('unite:generer')
            ->setDescription('Génère les unités');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return boolean
     * @
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Langue $langue */
        /** @var EntityManager $em */
        /** @var EntityManager $emSite */

        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $sites = $em->getRepository(Site::class)->findAll();
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
            $langues = $emSite->getRepository(Langue::class)->findAll();
            // generer les unites ages
//        MOIS
            $unite = new UniteAge();
            $unite->setMultiplicateurReference(1);
            $unite->setReference(null);
            foreach ($langues as $langue) {
                $uniteTraduction = new UniteTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $uniteTraduction->setLibelle('mois');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                    case 'en_EN':
                        $uniteTraduction->setLibelle('month');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                    case 'es_ES':
                        $uniteTraduction->setLibelle('mès');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                    default:
                        $uniteTraduction->setLibelle('');
                        $uniteTraduction->setLibelleCourt('');
                        break;
                }
                $uniteTraduction->setLangue($langue);
                $uniteTraduction->setUnite($unite);
                $unite->addTraduction($uniteTraduction);
                $emSite->persist($uniteTraduction);
            }
            $uniteReference = $unite;
            $emSite->persist($unite);
            $emSite->flush();
//        ANNEES
            $unite = new UniteAge();
            $unite->setMultiplicateurReference(12);
            $unite->setReference($uniteReference);
            foreach ($langues as $langue) {
                $uniteTraduction = new UniteTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $uniteTraduction->setLibelle('années');
                        $uniteTraduction->setLibelleCourt('ans');
                        break;
                    case 'en_EN':
                        $uniteTraduction->setLibelle('year');
                        $uniteTraduction->setLibelleCourt('y');
                        break;
                    case 'es_ES':
                        $uniteTraduction->setLibelle('año');
                        $uniteTraduction->setLibelleCourt('a');
                        break;
                    default:
                        $uniteTraduction->setLibelle('');
                        $uniteTraduction->setLibelleCourt('');
                        break;
                }
                $uniteTraduction->setLangue($langue);
                $uniteTraduction->setUnite($unite);
                $unite->addTraduction($uniteTraduction);
                $emSite->persist($uniteTraduction);
            }
            $uniteReference = null;
            $emSite->persist($unite);
            $emSite->flush();

            // generer les unites tarifs
//        EUROS
            $unite = new UniteTarif();
            $unite->setMultiplicateurReference(1);
            $unite->setReference(null);
            foreach ($langues as $langue) {
                $uniteTraduction = new UniteTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $uniteTraduction->setLibelle('euros');
                        $uniteTraduction->setLibelleCourt('€');
                        break;
//                    case 'en_EN':
//                        $uniteTraduction->setLibelle('mois');
//                        $uniteTraduction->setLibelleCourt('m');
//                        break;
//                    case 'es_ES':
//                        $uniteTraduction->setLibelle('mois');
//                        $uniteTraduction->setLibelleCourt('m');
//                        break;
                    default:
                        $uniteTraduction->setLibelle('euros');
                        $uniteTraduction->setLibelleCourt('€');
                        break;
                }
                $uniteTraduction->setLangue($langue);
                $uniteTraduction->setUnite($unite);
                $unite->addTraduction($uniteTraduction);
                $emSite->persist($uniteTraduction);
            }
            $uniteReference = $unite;
            $emSite->persist($unite);
            $emSite->flush();
//        CENTS
            $unite = new UniteTarif();
            $unite->setMultiplicateurReference(0.01);
            $unite->setReference($uniteReference);
            foreach ($langues as $langue) {
                $uniteTraduction = new UniteTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $uniteTraduction->setLibelle('cents');
                        $uniteTraduction->setLibelleCourt('cts');
                        break;
//                    case 'en_EN':
//                        break;
//                    case 'es_ES':
//                        break;
                    default:
                        $uniteTraduction->setLibelle('cents');
                        $uniteTraduction->setLibelleCourt('cts');
                        break;
                }
                $uniteTraduction->setLangue($langue);
                $uniteTraduction->setUnite($unite);
                $unite->addTraduction($uniteTraduction);
                $emSite->persist($uniteTraduction);
            }
            $uniteReference = null;
            $emSite->persist($unite);
            $emSite->flush();

            // generer les unites distance
//        KILOMETRE
            $unite = new UniteDistance();
            $unite->setMultiplicateurReference(1);
            $unite->setReference(null);
            foreach ($langues as $langue) {
                $uniteTraduction = new UniteTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $uniteTraduction->setLibelle('kilomètre');
                        $uniteTraduction->setLibelleCourt('km');
                        break;
                    case 'en_EN':
                        $uniteTraduction->setLibelle('kilometer');
                        $uniteTraduction->setLibelleCourt('km');
                        break;
                    case 'es_ES':
                        $uniteTraduction->setLibelle('kilometro');
                        $uniteTraduction->setLibelleCourt('km');
                        break;
                    default:
                        $uniteTraduction->setLibelle('kilomètre');
                        $uniteTraduction->setLibelleCourt('km');
                        break;
                }
                $uniteTraduction->setLangue($langue);
                $uniteTraduction->setUnite($unite);
                $unite->addTraduction($uniteTraduction);
                $emSite->persist($uniteTraduction);
            }
            $uniteReference = $unite;
            $emSite->persist($unite);
            $emSite->flush();
//        METRE
            $unite = new UniteDistance();
            $unite->setMultiplicateurReference(0.001);
            $unite->setReference($uniteReference);
            foreach ($langues as $langue) {
                $uniteTraduction = new UniteTraduction();
                switch ($langue->getCode()) {
                    case 'fr_FR':
                        $uniteTraduction->setLibelle('mètre');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                    case 'en_EN':
                        $uniteTraduction->setLibelle('meter');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                    case 'es_ES':
                        $uniteTraduction->setLibelle('metro');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                    default:
                        $uniteTraduction->setLibelle('mètre');
                        $uniteTraduction->setLibelleCourt('m');
                        break;
                }
                $uniteTraduction->setLangue($langue);
                $uniteTraduction->setUnite($unite);
                $unite->addTraduction($uniteTraduction);
                $emSite->persist($uniteTraduction);
            }
            $uniteReference = null;
            $emSite->persist($unite);
            $emSite->flush();

        }
    }
}