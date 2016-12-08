<?php

namespace Mondofute\Bundle\CodePromoBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoHebergement;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoLogement;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCodePromoLogementByCodePromoUnifieCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('creer:codePromoLogementByCodePromoUnifie')
            ->setDescription('Création des codePromoLogement pour le logement')
            ->addArgument('codePromoUnifieId', InputArgument::REQUIRED, 'id de logement unifie.');
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

        $codePromoUnifieId = $input->getArgument('codePromoUnifieId');

        $codePromoUnifie = $em->find(CodePromoUnifie::class, $codePromoUnifieId);

        /** @var CodePromo $codePromo */
        foreach ($codePromoUnifie->getCodePromos() as $codePromo) {
            /** @var CodePromoHebergement $codePromoHebergement */
            foreach ($codePromo->getCodePromoHebergements() as $codePromoHebergement) {
                $hebergementUnifieId = $codePromoHebergement->getHebergement()->getHebergementUnifie()->getId();
                $logements = $em->getRepository(Logement::class)->findByFournisseurHebergement($codePromoHebergement->getFournisseur()->getId(), $hebergementUnifieId, $codePromo->getSite()->getId());
                foreach ($logements as $logement) {
                    if (false === $codePromo->getCodePromoLogements()->filter(function (CodePromoLogement $element) use ($logement) {
                            return $element->getLogement() == $logement;
                        })->first()
                    ) {
                        $codePromoLogement = new CodePromoLogement();
                        $codePromo
                            ->addCodePromoLogement($codePromoLogement);
                        $codePromoLogement->setLogement($logement);
                    }
                }
            }
        }
        $em->flush();

        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));

        // *** gestion code promo logement ***
        foreach ($sites as $site) {
            $emSite = $this->getContainer()->get('doctrine.orm.' . $site->getLibelle() . '_entity_manager');

            $entity = $codePromoUnifie->getCodePromos()->filter(function (CodePromo $element) use ($site) {
                return $element->getSite() == $site;
            })->first();
            $entitySite = $emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $codePromoUnifieId));
            if (!empty($entity->getCodePromoLogements()) && !$entity->getCodePromoLogements()->isEmpty()) {
                /** @var CodePromoLogement $codePromoLogement */
                foreach ($entity->getCodePromoLogements() as $codePromoLogement) {
                    $codePromoLogementSite = $entitySite->getCodePromoLogements()->filter(function (CodePromoLogement $element) use ($codePromoLogement) {
                        return $element->getId() == $codePromoLogement->getId();
                    })->first();
                    if (false === $codePromoLogementSite) {
                        $codePromoLogementSite = new CodePromoLogement();
//                        $emSite->persist($codePromoLogementSite);
                        $entitySite->addCodePromoLogement($codePromoLogementSite);
                        $codePromoLogementSite
                            ->setId($codePromoLogement->getId());

                        $metadata = $emSite->getClassMetadata(get_class($codePromoLogementSite));
                        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    }

                    $codePromoLogementSite
                        ->setLogement($emSite->getRepository(Logement::class)->findOneBy(array('logementUnifie' => $codePromoLogement->getLogement()->getLogementUnifie())));
                }
            }

            $emSite->flush();
        }
        // *** fin gestion code promo logement ***


    }
}
