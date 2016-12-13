<?php

namespace Mondofute\Bundle\CodePromoBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoHebergement;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoLogement;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCodePromoLogementCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('creer:codePromoLogement')
            ->setDescription('Création des codePromoLogement pour le logement')
            ->addArgument('logementUnifieId', InputArgument::REQUIRED, 'id de logement unifie.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $emSite */
        /** @var CodePromoLogement $codePromoLogement */
        /** @var CodePromoHebergement $codePromoHebergement */
        /** @var Logement $logement */

        $em = $this->getContainer()->get('doctrine.orm.crm_entity_manager');

        $logementUnifieId = $input->getArgument('logementUnifieId');

        $logementUnifie = $em->find(LogementUnifie::class, $logementUnifieId);

        $arrayCodePromoLogements = new ArrayCollection();
        foreach ($logementUnifie->getLogements() as $logement) {
            $codePromoHebergements = new ArrayCollection($em->getRepository(CodePromoHebergement::class)->findBy(array(
                'fournisseur' => $logement->getFournisseurHebergement()->getFournisseur(),
                'hebergement' => $logement->getFournisseurHebergement()->getHebergement()->getHebergements()->filter(function (
                    Hebergement $element
                ) use ($logement) {
                    return $element->getSite() == $logement->getSite();
                })->first()
            ))
            );
            foreach ($codePromoHebergements as $codePromoHebergement) {
                $codePromoLogement = new CodePromoLogement();
                $em->persist($codePromoLogement);
                $codePromoLogement
                    ->setCodePromo($codePromoHebergement->getCodePromo())
                    ->setLogement($logement);
                $arrayCodePromoLogements->add($codePromoLogement);
            }
        }
        $em->flush();

        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');
            foreach ($arrayCodePromoLogements as $codePromoLogement) {
                if ($codePromoLogement->getCodePromo()->getSite() == $site) {
                    $codePromoLogementSite = new CodePromoLogement();
                    $emSite->persist($codePromoLogementSite);
                    $codePromoLogementSite->setId($codePromoLogement->getId());
                    $metadata = $emSite->getClassMetadata(get_class($codePromoLogementSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                    $codePromoLogementSite
                        ->setCodePromo($emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $codePromoLogement->getCodePromo()->getCodePromoUnifie())))
                        ->setLogement($emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $logementUnifieId)));
                }
            }
            $emSite->flush();
        }
    }
}
