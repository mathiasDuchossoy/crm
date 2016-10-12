<?php

namespace Mondofute\Bundle\LogementPeriodeBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;


/**
 * LogementPeriodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogementPeriodeRepository extends \Doctrine\ORM\EntityRepository
{
    private $connexion;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->connexion = $this->getEntityManager()->getConnection();
    }

    /**
     * @param LogementPeriode $logementPeriode
     * @return mixed
     */
    public function chargerLocatif($logementPeriode)
    {
        $retour = true;
        $sql = 'SELECT lpl.stock, lpl.prix_public AS prixPublic , lpl.prix_fournisseur AS prixFournisseur, lpl.prix_achat AS prixAchat FROM logement_periode_locatif AS lpl WHERE lpl.logement_id=? AND lpl.periode_id=?';
        $this->connexion->beginTransaction();
        $stmt = $this->connexion->prepare($sql);
        if (!$stmt) {
            $retour = false;
        } else {
            $retour = $stmt->bindValue(1, $logementPeriode->getLogement()->getId(), Type::BIGINT);
            if ($retour) {
                $retour = $stmt->bindValue(2, $logementPeriode->getPeriode()->getId(), Type::BIGINT);
                if ($retour) {
                    $result = $stmt->execute();
                    if (!$result) {
                        $this->connexion->rollBack();
                        $retour = false;
                    } else {
                        $this->connexion->commit();
                        $result = $stmt->fetch();
                        if ($result) {
                            $locatif = new LogementPeriodeLocatif();
                            $locatif
                                ->setLogement($logementPeriode->getLogement())
                                ->setPeriode($logementPeriode->getPeriode())
                                ->setStock($result['stock'])
                                ->setPrixPublic($result['prixPublic'])
                                ->setPrixFournisseur($result['prixFournisseur'])
                                ->setPrixAchat($result['prixAchat'])
                            ;
                            $logementPeriode->setLocatif($locatif);
                        }
                    }
                }
            }
        }
        return $retour;
    }
}
