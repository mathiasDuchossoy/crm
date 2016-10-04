<?php

namespace Mondofute\Bundle\HebergementBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * FournisseurHebergementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurHebergementRepository extends \Doctrine\ORM\EntityRepository
{
    private $connexion;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->connexion = $this->getEntityManager()->getConnection();
    }
//    public function chargerPourStocks($idHebergementUnifie)
//    {
//        $em = $this->getEntityManager();
//        $site = $em->getRepository(Site::class)->findOneBy(array('crm' => true));
////        dump($site);die;
//        $fournisseurHebergements = new ArrayCollection();
////        $em = $this->getEntityManager();
////        $conn = $em->getConnection();
//        $qb = $this->getEntityManager()->createQueryBuilder();
//        $qb->select('fh.id, f.id fournisseurId')
//            ->from('MondofuteHebergementBundle:FournisseurHebergement', 'fh')
//            ->leftJoin('fh.fournisseur', 'f')
//            ->where('fh.hebergement = :idHebergement')
//            ->setParameter('idHebergement', $idHebergementUnifie);
//        $fournisseurHebergementsResult = $qb->getQuery()->getResult();
//        unset($qb);
////        print_r($fournisseurHebergementsResult);
////        die;
//        foreach ($fournisseurHebergementsResult as $fournisseurHebergementResult) {
//            $fournisseurHebergement = new FournisseurHebergement();
//            $fournisseurHebergement->setId($fournisseurHebergementResult['id']);
//            $fournisseur = new Fournisseur();
//            $fournisseur->setId($fournisseurHebergementResult['fournisseurId']);
//            $qbLogements = $this->getEntityManager()->createQueryBuilder();
//            $qbLogements->select('l.id, lu.id logementUnifieId')
//                ->from('MondofuteLogementBundle:Logement', 'l')
//                ->innerJoin('l.fournisseurHebergement', 'fh')
//                ->innerJoin('l.site', 's')
//                ->innerJoin('l.logementUnifie', 'lu')
//                ->where('fh.id = :idFournisseurHebergement')
//                ->andWhere('s.id = :idSite')
//                ->setParameter('idSite', $site->getId())
//                ->setParameter('idFournisseurHebergement', $fournisseurHebergement->getId());
//            $logementsResult = $qbLogements->getQuery()->getResult();
//            unset($qbLogements);
//            foreach ($logementsResult as $logementResult) {
//                $idLogement = $logementResult['id'];
//                $logement = new Logement();
//                $logementUnifie = new LogementUnifie();
//                $logementUnifie->setId($logementResult['logementUnifieId']);
//                $logement->setLogementUnifie($logementUnifie)
//                    ->setSite($site);
//
////                gestion des traductions
//                $qbLogementTraduction = $this->getEntityManager()->createQueryBuilder();
//                $qbLogementTraduction->select('lt.nom, lang.code')
//                    ->from('MondofuteLogementBundle:LogementTraduction', 'lt')
//                    ->innerJoin('lt.logement', 'l')
//                    ->innerJoin('lt.langue', 'lang')
//                    ->where('l.id = :idlogement')
//                    ->setParameter('idlogement', $idLogement);
//                $logementTraductionsResult = $qbLogementTraduction->getQuery()->getResult();
//                unset($qbLogementTraduction);
//                foreach ($logementTraductionsResult as $logementTraductionResult) {
//                    $logementTraduction = new LogementTraduction();
//                    $logementTraduction->setNom($logementTraductionResult['nom']);
//                    $langue = new Langue();
//                    $langue->setCode($logementTraductionResult['code']);
//                    $logementTraduction->setLangue($langue);
//                    $logement->addTraduction($logementTraduction);
//                }
//                unset($logementTraductionsResult);
//
////                gestion des périodes
//                $qbLogementPeriodes = $this->getEntityManager()->createQueryBuilder();
//                $qbLogementPeriodes->select('p.id periodeId')
//                    ->from('MondofuteLogementPeriodeBundle:LogementPeriode', 'lp')
//                    ->join('lp.logement', 'l')
//                    ->join('lp.periode', 'p')
//                    ->where('l.id = :idLogement')
//                    ->setParameter('idLogement', $idLogement);
//                $logementPeriodesResult = $qbLogementPeriodes->getQuery()->getResult();
//                unset($qbLogementPeriodes);
//                foreach ($logementPeriodesResult as $logementPeriodeResult) {
//                    $logementPeriode = new LogementPeriode();
//                    $periode = new Periode();
//                    $periode->setId($logementPeriodeResult['periodeId']);
//                    $logementPeriode->setLogement($logement)
//                        ->setPeriode($periode);
//
////                    $qbLogementPeriodesLocatif = $this->getEntityManager()->createQueryBuilder();
////                    $qbLogementPeriodesLocatif->select('lpl.stock')
////                        ->from('MondofuteCatalogueBundle:LogementPeriodeLocatif','lpl')
////                        ->join('lpl.logement','l')
////                        ->join('lpl.periode','p')
////                        ->where('l.id = :idLogement')
////                        ->andWhere('p.id = :idPeriode')
////                        ->setParameter('idLogement',$idLogement)
////                        ->setParameter('idPeriode',$periode->getId());
////                    $lplResults = $qbLogementPeriodesLocatif->getQuery()->getResult();
////                    unset($qbLogementPeriodesLocatif);
////                    foreach ($lplResults as $lplResult){
////                        $lpl = new LogementPeriodeLocatif();
////                        $lpl->setStock($lplResult['stock'])
////                            ->setLogement($logement)
////                            ->setPeriode($periode);
////                        $logementPeriode->setLocatif($lpl);
////                    }
////                    unset($lplResults);
//                    $logement->addPeriode($logementPeriode);
//                }
//                unset($logementPeriodesResult);
//
////                ajout du logement au fournisseurHebergement
//                $fournisseurHebergement->addLogement($logement);
//            }
//            unset($logementsResult);
//            $fournisseurHebergement->setFournisseur($fournisseur);
//
//            $fournisseurHebergements->add($fournisseurHebergement);
//        }
////        dump($fournisseurHebergements);
////        print_r(memory_get_usage());
////        die;
////        SELECT fh.id FROM fournisseurHebergement WHERE idHebergement=x;
//
////        $qb = $this->getEntityManager()->createQueryBuilder();
////        $qb->select('fh')
////            ->addSelect('h.id')
////            ->addSelect('f.id')
////            ->addSelect('logements')
////            ->from('MondofuteHebergementBundle:FournisseurHebergement', 'fh')
////            ->join('fh.fournisseur', 'f')
////            ->join('fh.hebergement', 'h')
////            ->leftJoin('fh.logements','logements')
////        ->where('h.id = :idHebergementUnifie')
////        ->setParameter('idHebergementUnifie',$idHebergementUnifie);
////        $result = $qb->getQuery()->getResult();
////        dump(new ArrayCollection($result));
////        die;
////        $qb->select('fh')
////            ->addSelect('fh.fournisseur')
////            ->addSelect('fh.logements')
////            ->addSelect('lt.langue')
////            ->addSelect('lt.nom')
////            ->from('MondofuteHebergementBundle:FournisseurHebergement','fh')
////            ->leftJoin('fh.fournisseur','f')
////            ->leftJoin('fh.logements','l')
////        ->leftJoin('l.site','site')
////        ->leftJoin('l.traductions','lt')
////        ->leftJoin('lt.langue','ltl')
////        ->where('fh.hebergement.id = :idHebergement')
////        ->setParameter('idHebergement',$idHebergementUnifie);
////        echo memory_get_usage();
////        die;
//        return $fournisseurHebergements;
//    }
    public function chargerPourStocks($idHebergementUnifie)
    {
        echo memory_get_usage().PHP_EOL;
        $em = $this->getEntityManager();
        echo memory_get_usage().PHP_EOL;
        $site = $em->getRepository(Site::class)->findOneBy(array('crm' => true));
        echo memory_get_usage().PHP_EOL;
        if (isset($em)) {
            unset($em);
        }
        echo memory_get_usage().PHP_EOL;

        $fournisseurHebergements = new ArrayCollection();
        $sql = 'SELECT fh.id, f.id AS fournisseurId, f.enseigne FROM fournisseur_hebergement AS fh LEFT JOIN fournisseur AS f ON f.id=fh.fournisseur_id WHERE fh.hebergement_id=?';
        $this->connexion->beginTransaction();
        echo memory_get_usage().PHP_EOL;
        $fhStmt = $this->connexion->prepare($sql);
        echo memory_get_usage().PHP_EOL;
        if (!$fhStmt) {

        } else {
            $retour = $fhStmt->bindValue(1, $idHebergementUnifie, Type::BIGINT);
            if ($retour) {
                $result = $fhStmt->execute();
                if (!$result) {
                    $this->connexion->rollBack();
                    $retour = false;
                } else {
                    while ($fhResult = $fhStmt->fetch()) {
//                        création du fournisseur hébergement
                        $fh = new FournisseurHebergement();
                        $fh->setId($fhResult['id']);
//                        création du fournisseur
                        $f = new Fournisseur();
                        $f->setEnseigne($fhResult['enseigne'])
                            ->setId($fhResult['fournisseurId']);
//                        association du fournisseur au fournisseur hébergement
                        $fh->setFournisseur($f);
                        unset($fhResult);
                        $fournisseurHebergements->add($fh);
                    }
                    if(isset($fhResult)) {
                        unset($fhResult);
                    }
                }
            }
        }
        die;
                        $sql = 'SELECT l.id, lu.id AS logementUnifieId FROM logement AS l JOIN logement_unifie AS lu ON lu.id=l.logement_unifie_id WHERE l.fournisseur_hebergement_id=? AND l.site_id=?';
//                        $this->connexion->beginTransaction();
                        $lStmt = $this->connexion->prepare($sql);
                        if (!$lStmt) {

                        } else {
                            $retour = $lStmt->bindValue(1, $fh->getId(), Type::BIGINT);
                            if ($retour) {
                                $retour = $lStmt->bindValue(2, 1, Type::BIGINT);
                                if ($retour) {
                                    $result = $lStmt->execute();
                                    if (!$result) {
                                        $this->connexion->rollBack();
                                        $retour = false;
                                    } else {
//                                        $this->connexion->commit();
//                    $result = $stmt->fetch();
                                        while ($lResult = $lStmt->fetch()) {
                                            $idLogement = $lResult['id'];
                                            $logement = new Logement();
                                            $logementUnifie = new LogementUnifie();
                                            $logementUnifie->setId($lResult['logementUnifieId']);
                                            $logement->setLogementUnifie($logementUnifie)->setSite($site);
                                            unset($lResult);
//                                            recupération des traductions
                                            $sql = 'SELECT lt.nom, l.code FROM logement_traduction AS lt LEFT JOIN langue AS l ON lt.langue_id=l.id WHERE lt.logement_id=?';
                                            $ltStmt = $this->connexion->prepare($sql);
                                            if (!$ltStmt) {

                                            } else {
                                                $retour = $ltStmt->bindValue(1, $idLogement, Type::BIGINT);
                                                if ($retour) {
                                                    $result = $ltStmt->execute();
                                                    if (!$result) {
                                                        $this->connexion->rollBack();
                                                        return false;
                                                    } else {
                                                        while ($ltResult = $ltStmt->fetch()) {
                                                            $logementTraduction = new LogementTraduction();
                                                            $langue = new Langue();
                                                            $langue->setCode($ltResult['code']);
                                                            $logementTraduction->setLangue($langue)
                                                                ->setNom($ltResult['nom']);
                                                            $logement->addTraduction($logementTraduction);

                                                        }
                                                        if (isset($ltResult)) {
                                                            unset($ltResult);
                                                        }
                                                        if (isset($ltStmt)) {
                                                            unset($ltStmt);
                                                        }
                                                        if (isset($logementTraduction)) {
                                                            unset($logementTraduction);
                                                        }
                                                        if (isset($langue)) {
                                                            unset($langue);
                                                        }
                                                    }
                                                }
                                            }
//                                            fin de la récupération des traductions
//                                            récupération des périodes logement
                                            $sql = 'SELECT lp.periode_id, lplocatif.stock, p.type_id, p.debut,p.fin FROM logement_periode AS lp LEFT JOIN logement_periode_locatif AS lplocatif ON lp.periode_id=lplocatif.periode_id AND lp.logement_id=lplocatif.logement_id LEFT JOIN periode AS p ON p.id=lp.periode_id WHERE lp.logement_id=?';
                                            $lpStmt = $this->connexion->prepare($sql);
                                            if (!$lpStmt) {

                                            } else {
                                                $retour = $lpStmt->bindValue(1, $idLogement, Type::BIGINT);
                                                if ($retour) {
                                                    $result = $lpStmt->execute();
                                                    if (!$result) {
                                                        $this->connexion->rollBack();
                                                        return false;
                                                    } else {
                                                        while ($lpResult = $lpStmt->fetch()) {
                                                            $logementPeriode = new LogementPeriode();
                                                            $logementPeriodeLocatif = new LogementPeriodeLocatif();
                                                            $periode = new Periode();
                                                            $typePeriode = new TypePeriode();
                                                            $typePeriode->setId((int)$lpResult['type_id']);
//                                                            $periode->setId();
                                                            $periode
                                                                ->setDebut(new \DateTime($lpResult['debut']))
                                                                ->setFin(new \DateTime($lpResult['fin']))
                                                                ->setType($typePeriode)->setId($lpResult['periode_id']);

                                                            $logementPeriodeLocatif->setLogement($logement)
                                                                ->setPeriode($periode)
                                                                ->setStock($lpResult['stock']);
                                                            $logementPeriode->setLogement($logement)->setPeriode($periode)->setLocatif($logementPeriodeLocatif);
                                                            $logement->addPeriode($logementPeriode);
                                                        }
                                                        if (isset($lpStmt)) {
                                                            unset($lpStmt);
                                                        }
                                                        if (isset($lpResult)) {
                                                            unset($lpResult);
                                                        }
                                                        if (isset($logementPeriode)) {
                                                            unset($logementPeriode);
                                                        }
                                                        if (isset($periode)) {
                                                            unset($periode);
                                                        }
                                                        if (isset($logementPeriodeLocatif)) {
                                                            unset($logementPeriodeLocatif);
                                                        }
                                                    }
                                                }
                                            }
//                                            fin de récupération des périodes logements
                                            $fh->addLogement($logement);
                                        }
                                        if (isset($lResult)) {
                                            unset($lResult);
                                        }
                                        if (isset($lStmt)) {
                                            unset($lStmt);
                                        }
                                        if (isset($logement)) {
                                            unset($logement);
                                        }
                                        if (isset($logementUnifie)) {
                                            unset($logementUnifie);
                                        }

                                    }
                                }
                            }
                        }
                    }
                    if (isset($fhResult)) {
                        unset($fhResult);
                    }
                    if (isset($fhStmt)) {
                        unset($fhStmt);
                    }
                    if (isset($fh)) {
                        unset($fh);
                    }

                }
            }
        }
//        dump($fournisseurHebergements);
//        echo memory_get_peak_usage();
//        die;
        return $fournisseurHebergements;
    }
}
