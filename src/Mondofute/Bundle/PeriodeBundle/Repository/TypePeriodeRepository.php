<?php

namespace Mondofute\Bundle\PeriodeBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;

/**
 * TypePeriodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TypePeriodeRepository extends \Doctrine\ORM\EntityRepository
{
    private $connexion;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->connexion = $this->getEntityManager()->getConnection();
    }

    public function chargerParDates()
    {
        $qb = $this->createQueryBuilder('type_periode_repository');
        $qb->from('MondofutePeriodeBundle:TypePeriode', 'tp')
            ->leftJoin('tp.periodes', 'p')
            ->addOrderBy('type_periode_repository.id', 'ASC')
            ->addOrderBy('p.debut', 'ASC')
            ->addOrderBy('p.fin', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Get the paginated list of published secteurs
     *
     * @param int $page
     * @param int $maxperpage
     * @param array $sortbyArray
     * @return Paginator
     */
    public function getList($page = 1, $maxperpage, $sortbyArray = array(), TypePeriode $typePeriode = null)
    {
        $q = $this->createQueryBuilder('entity')
            ->select('entity')
            ->leftJoin('entity.periodes', 'periodes')
            ->where('periodes.type = :type')
            ->setParameter('type', $typePeriode)
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        $pag = new Paginator($q, true);
        return $pag;
    }

    public function findAllArray()
    {
        $sql = 'SELECT tp.id FROM type_periode AS tp';
        $this->connexion->beginTransaction();
        $tpStmt = $this->connexion->prepare($sql);
        if (!$tpStmt) {

        } else {
            $result = $tpStmt->execute();
            if (!$result) {
                $this->connexion->rollBack();
                $retour = false;
            } else {
                while ($tpResult = $tpStmt->fetch()) {
                    $typePeriode = new \stdClass();
                    $typePeriode->id = $tpResult['id'];
                    unset($tpResult);
//                                            recupération des traductions
                    $sql = 'SELECT p.id, p.debut, p.fin FROM periode AS p WHERE p.type_id=?';
                    $pStmt = $this->connexion->prepare($sql);
                    if (!$pStmt) {

                    } else {
                        $retour = $pStmt->bindValue(1, $typePeriode->id, Type::BIGINT);
                        if ($retour) {
                            $result = $pStmt->execute();
                            if (!$result) {
                                $this->connexion->rollBack();
                                return false;
                            } else {
                                while ($pResult = $pStmt->fetch()) {
                                    $periode = new \stdClass();
                                    $periode->id = $pResult['id'];
                                    $periode->debut = new \DateTime($pResult['debut']);
                                    $periode->fin = new \DateTime($pResult['fin']);
                                    $typePeriode->periodes[] = $periode;
                                }
                            }
                        }
                    }
                    $typePeriodes[] = $typePeriode;
                }
            }
        }
        return $typePeriodes;
    }

    public function findAllFutur()
    {
        $today = new \DateTime();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('tp')
            ->from('MondofutePeriodeBundle:TypePeriode', 'tp')
            ->join('tp.periodes', 'periodes')
            ->where('periodes.debut >= :today')
            ->setParameter('today', $today->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }
}
