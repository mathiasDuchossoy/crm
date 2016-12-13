<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogementUnifie;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EditPrestationAnnexeLogementCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('edit:prestationAnnexeLogement')
            ->setDescription('Mise à jours des prestation annexe pour les logements')
            ->addArgument('logementUnifieId', InputArgument::REQUIRED, 'id de logement unifie.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Logement $logement */
        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        $logementUnifieId = $input->getArgument('logementUnifieId');
        $logementUnifie = $em->find(LogementUnifie::class, $logementUnifieId);
        $fournisseurId = $em->getRepository(LogementUnifie::class)->getFournisseur($logementUnifieId);

        /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
        /** @var PrestationAnnexeLogementUnifie $prestationAnnexeLogementUnifie */

        $prestationAnnexeHebergementUnifies = $em->getRepository(PrestationAnnexeHebergementUnifie::class)->findByLogementUnifieId($logementUnifieId,
            $fournisseurId);
        $prestationAnnexeLogementUnifies = new ArrayCollection();
        foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
            $paramId = $prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements()->first()->getParam()->getId();
            $prestationAnnexeLogementUnifie = $em->getRepository(PrestationAnnexeLogementUnifie::class)->findByCriteria($paramId,
                $logementUnifieId);
            $prestationAnnexeLogementUnifies->add($prestationAnnexeLogementUnifie);
            $em->persist($prestationAnnexeLogementUnifie);
            foreach ($prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                $prestationAnnexeLogement = $prestationAnnexeLogementUnifie->getPrestationAnnexeLogements()->filter(function (
                    PrestationAnnexeLogement $element
                ) use ($prestationAnnexeHebergement) {
                    return $element->getSite() == $prestationAnnexeHebergement->getSite();
                })->first();

                $capacite = $prestationAnnexeHebergement->getParam()->getCapacite();
                $actif = false;
                if (empty($capacite) or (!empty($capacite) and $capacite->getMin() <= $prestationAnnexeLogement->getLogement()->getCapacite() and $prestationAnnexeLogement->getLogement()->getCapacite() <= $capacite->getMax())) {
                    $actif = $prestationAnnexeHebergement->getActif();
                }
                $prestationAnnexeLogement
                    ->setActif($actif);
            }
        }

        $em->flush();

        /** @var PrestationAnnexeLogementUnifie $prestationAnnexeLogementUnifie */
        /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
        foreach ($sites as $site) {
            /** @var EntityManager $emSite */
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
            foreach ($prestationAnnexeLogementUnifies as $prestationAnnexeLogementUnifie) {
                $prestationAnnexeLogementUnifieSite = $emSite->find(PrestationAnnexeLogementUnifie::class,
                    $prestationAnnexeLogementUnifie);
                $emSite->persist($prestationAnnexeLogementUnifieSite);

                $prestationAnnexeLogement = $prestationAnnexeLogementUnifie->getPrestationAnnexeLogements()->filter(function (
                    PrestationAnnexeLogement $element
                ) use ($site) {
                    return $element->getSite() == $site;
                })->first();

                $prestationAnnexeLogementSite = $prestationAnnexeLogementUnifieSite->getPrestationAnnexeLogements()->first();
                $prestationAnnexeLogementSite
                    ->setActif($prestationAnnexeLogement->getActif());
            }
            $emSite->flush();
        }

    }
}
