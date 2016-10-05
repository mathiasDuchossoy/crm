<?php

namespace Mondofute\Bundle\LogementBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * LogementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogementRepository extends \Doctrine\ORM\EntityRepository
{
    private $connexion;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->connexion = $this->getEntityManager()->getConnection();
    }

    public function chargerPourStocks($idLogement)
    {
        $em = $this->getEntityManager();
        $site = $em->getRepository(Site::class)->findOneBy(array('crm' => true));
        if (isset($em)) {
            unset($em);
        }
        $sql = 'SELECT l.id, lu.id AS logementUnifieId FROM logement AS l JOIN logement_unifie AS lu ON lu.id=l.logement_unifie_id WHERE l.id=? AND l.site_id=?';
        $this->connexion->beginTransaction();
        $lStmt = $this->connexion->prepare($sql);
        if (!$lStmt) {

        } else {
            $retour = $lStmt->bindValue(1, intval($idLogement,10), Type::BIGINT);
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
                        }
                        if (isset($lResult)) {
                            unset($lResult);
                        }
                        if (isset($lStmt)) {
                            unset($lStmt);
                        }
//                        if (isset($logement)) {
//                            unset($logement);
//                        }
                        if (isset($logementUnifie)) {
                            unset($logementUnifie);
                        }

                    }
                }
            }
        }
        return $logement;
    }
}
