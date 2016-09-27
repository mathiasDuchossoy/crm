<?php

namespace Mondofute\Bundle\HebergementBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * FournisseurHebergementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurHebergementRepository extends \Doctrine\ORM\EntityRepository
{
    public function chargerPourStocks($idHebergementUnifie)
    {
        $em = $this->getEntityManager();
        $site = $em->getRepository(Site::class)->findOneBy(array('crm' => true));
//        dump($site);die;
        $fournisseurHebergements = new ArrayCollection();
//        $em = $this->getEntityManager();
//        $conn = $em->getConnection();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fh.id, f.id fournisseurId')
            ->from('MondofuteHebergementBundle:FournisseurHebergement', 'fh')
            ->leftJoin('fh.fournisseur', 'f')
            ->where('fh.hebergement = :idHebergement')
            ->setParameter('idHebergement', $idHebergementUnifie);
        $fournisseurHebergementsResult = $qb->getQuery()->getResult();
        unset($qb);
//        print_r($fournisseurHebergementsResult);
//        die;
        foreach ($fournisseurHebergementsResult as $fournisseurHebergementResult) {
            $fournisseurHebergement = new FournisseurHebergement();
            $fournisseurHebergement->setId($fournisseurHebergementResult['id']);
            $fournisseur = new Fournisseur();
            $fournisseur->setId($fournisseurHebergementResult['fournisseurId']);
            $qbLogements = $this->getEntityManager()->createQueryBuilder();
            $qbLogements->select('l.id, lu.id logementUnifieId')
                ->from('MondofuteLogementBundle:Logement', 'l')
                ->innerJoin('l.fournisseurHebergement', 'fh')
                ->innerJoin('l.site', 's')
                ->innerJoin('l.logementUnifie', 'lu')
                ->where('fh.id = :idFournisseurHebergement')
                ->andWhere('s.id = :idSite')
                ->setParameter('idSite', $site->getId())
                ->setParameter('idFournisseurHebergement', $fournisseurHebergement->getId());
            $logementsResult = $qbLogements->getQuery()->getResult();
            unset($qbLogements);
            foreach ($logementsResult as $logementResult) {
                $idLogement = $logementResult['id'];
                $logement = new Logement();
                $logementUnifie = new LogementUnifie();
                $logementUnifie->setId($logementResult['logementUnifieId']);
                $logement->setLogementUnifie($logementUnifie)
                    ->setSite($site);

//                gestion des traductions
                $qbLogementTraduction = $this->getEntityManager()->createQueryBuilder();
                $qbLogementTraduction->select('lt.nom, lang.code')
                    ->from('MondofuteLogementBundle:LogementTraduction', 'lt')
                    ->innerJoin('lt.logement', 'l')
                    ->innerJoin('lt.langue', 'lang')
                    ->where('l.id = :idlogement')
                    ->setParameter('idlogement', $idLogement);
                $logementTraductionsResult = $qbLogementTraduction->getQuery()->getResult();
                unset($qbLogementTraduction);
                foreach ($logementTraductionsResult as $logementTraductionResult) {
                    $logementTraduction = new LogementTraduction();
                    $logementTraduction->setNom($logementTraductionResult['nom']);
                    $langue = new Langue();
                    $langue->setCode($logementTraductionResult['code']);
                    $logementTraduction->setLangue($langue);
                    $logement->addTraduction($logementTraduction);
                }
                unset($logementTraductionsResult);

//                gestion des périodes
                $qbLogementPeriodes = $this->getEntityManager()->createQueryBuilder();
                $qbLogementPeriodes->select('p.id periodeId')
                    ->from('MondofuteLogementPeriodeBundle:LogementPeriode', 'lp')
                    ->join('lp.logement', 'l')
                    ->join('lp.periode', 'p')
                    ->where('l.id = :idLogement')
                    ->setParameter('idLogement', $idLogement);
                $logementPeriodesResult = $qbLogementPeriodes->getQuery()->getResult();
                unset($qbLogementPeriodes);
                foreach ($logementPeriodesResult as $logementPeriodeResult) {
                    $logementPeriode = new LogementPeriode();
                    $periode = new Periode();
                    $periode->setId($logementPeriodeResult['periodeId']);
                    $logementPeriode->setLogement($logement)
                        ->setPeriode($periode);

//                    $qbLogementPeriodesLocatif = $this->getEntityManager()->createQueryBuilder();
//                    $qbLogementPeriodesLocatif->select('lpl.stock')
//                        ->from('MondofuteCatalogueBundle:LogementPeriodeLocatif','lpl')
//                        ->join('lpl.logement','l')
//                        ->join('lpl.periode','p')
//                        ->where('l.id = :idLogement')
//                        ->andWhere('p.id = :idPeriode')
//                        ->setParameter('idLogement',$idLogement)
//                        ->setParameter('idPeriode',$periode->getId());
//                    $lplResults = $qbLogementPeriodesLocatif->getQuery()->getResult();
//                    foreach ($lplResults as $lplResult){
//                        $lpl = new LogementPeriodeLocatif();
//                        $lpl->setStock($lplResult['stock'])
//                            ->setLogement($logement)
//                            ->setPeriode($periode);
//                        $logementPeriode->setLocatif($lpl);
//                    }

                    $logement->addPeriode($logementPeriode);
                }
                unset($logementPeriodesResult);

//                ajout du logement au fournisseurHebergement
                $fournisseurHebergement->addLogement($logement);
            }
            unset($logementsResult);
            $fournisseurHebergement->setFournisseur($fournisseur);

            $fournisseurHebergements->add($fournisseurHebergement);
        }
//        dump($fournisseurHebergements);
//        print_r(memory_get_usage());
//        die;
//        SELECT fh.id FROM fournisseurHebergement WHERE idHebergement=x;

//        $qb = $this->getEntityManager()->createQueryBuilder();
//        $qb->select('fh')
//            ->addSelect('h.id')
//            ->addSelect('f.id')
//            ->addSelect('logements')
//            ->from('MondofuteHebergementBundle:FournisseurHebergement', 'fh')
//            ->join('fh.fournisseur', 'f')
//            ->join('fh.hebergement', 'h')
//            ->leftJoin('fh.logements','logements')
//        ->where('h.id = :idHebergementUnifie')
//        ->setParameter('idHebergementUnifie',$idHebergementUnifie);
//        $result = $qb->getQuery()->getResult();
//        dump(new ArrayCollection($result));
//        die;
//        $qb->select('fh')
//            ->addSelect('fh.fournisseur')
//            ->addSelect('fh.logements')
//            ->addSelect('lt.langue')
//            ->addSelect('lt.nom')
//            ->from('MondofuteHebergementBundle:FournisseurHebergement','fh')
//            ->leftJoin('fh.fournisseur','f')
//            ->leftJoin('fh.logements','l')
//        ->leftJoin('l.site','site')
//        ->leftJoin('l.traductions','lt')
//        ->leftJoin('lt.langue','ltl')
//        ->where('fh.hebergement.id = :idHebergement')
//        ->setParameter('idHebergement',$idHebergementUnifie);
//        echo memory_get_usage();
//        die;
        return $fournisseurHebergements;
    }
}
