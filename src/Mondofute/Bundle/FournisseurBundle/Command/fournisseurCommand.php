<?php

namespace Mondofute\Bundle\FournisseurBundle\Command;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\Type;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
use Mondofute\Bundle\UniteBundle\Entity\UniteClassementHebergement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class fournisseurCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mondofute_fournisseur:fournisseur_command')
            ->setDescription('Hello PhpStorm');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Fournisseur $fournisseurParent */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $sites      = $em->getRepository('MondofuteSiteBundle:Site')->findAll();
        $langues    = $em->getRepository(Langue::class)->findAll();


//        $unifies = $em->getRepository(HebergementUnifie::class)->findAll();
//        foreach ($unifies as $entity){
//            $em->remove($entity);
//            $em->flush();
//        }
//        $unifies = $em->getRepository(Fournisseur::class)->findAll();
//        foreach ($unifies as $entity){
//            $em->remove($entity);
//            $em->flush();
//        }
//        die;


        for($i=1;$i<=300;$i++){
            $fournisseur = new Fournisseur();
            $fournisseur->setEnseigne('Fournisseur '.$i);
            $fournisseur->setContient(FournisseurContient::PRODUIT);
            $em->persist($fournisseur);
            $em->flush();
        }

        for($i=1;$i<=1000;$i++){
            $fournisseurIndex  = mt_rand (1, 300);
            $hebergementUnifie = new HebergementUnifie();

            $fournisseur = $em->find(Fournisseur::class, $fournisseurIndex);
            $fournisseurHebergement = new FournisseurHebergement();
            $fournisseurHebergement->setHebergement($hebergementUnifie);
            $fournisseurHebergement->setFournisseur($fournisseur);
            $fournisseur->addHebergement($fournisseurHebergement);

            foreach ($sites as $site){

                $hebergement = new Hebergement();
                $hebergement->setSite($site);
                $hebergementUnifie->addHebergement($hebergement);
                /** @var Fournisseur $fournisseur */
                $tab = array('1','4');
                $typeHebergementId = array_rand($tab);
                $hebergement->setTypeHebergement($em->find(TypeHebergement::class, $typeHebergementId));
                $tab = array('1','4');
                $stationId = array_rand($tab);
                $hebergement->setStation($em->find(Station::class, $stationId));
                $tab = array('7','8');
                $classementId = array_rand($tab);
                $classementhebergement = new ClassementHebergement();
                $classementhebergement->setUnite($em->find(UniteClassementHebergement::class, $classementId));
                $hebergement->setClassement($classementhebergement);
                foreach ($langues as $langue){
                    $trad = new HebergementTraduction();
                    $hebergement->addTraduction($trad);
                    $trad->setLangue($langue);
                    $trad->setNom('Hebergement ' . $i);
                }
                $em->persist($fournisseur);

                $em->persist($hebergement);

                $em->persist($fournisseurHebergement);
            }
            $em->persist($hebergementUnifie);
            $em->flush();
        }


//
//
//        foreach ($sites as $site) {
//            $emSite = $this->getContainer()->get('doctrine')->getManager($site->getLibelle());
//
//            $fournisseur = new Fournisseur();
//            $fournisseur->setDateCreation($dateCreation)
//                ->setDateModification($dateCreation);
//
//            $fournisseur->setEnseigne($rEnseigne);
//            if (!empty($rFournisseurParent)) {
//                $fournisseur->setFournisseurParent($emSite->find('MondofuteFournisseurBundle:Fournisseur', $rFournisseurParent));
//            }
//            $fournisseur->setType($emSite->find('MondofuteFournisseurBundle:TypeFournisseur', $rTypeFournisseur));
//            $fournisseur->setActif($rActif);
//            $fournisseur->setContient($rContient);
//
//            $emSite->persist($fournisseur);
//            $emSite->flush();
//
//        }
    }
}
