<?php

namespace Mondofute\Bundle\PeriodeBundle\Repository;

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
        $qb = $this->createQueryBuilder('type_periode_repository')
            ->select('type_periode_repository')
            ->addSelect('periodes')
            ->leftJoin('type_periode_repository.periodes', 'periodes')
        ->orderBy('periodes.debut','ASC')
        ->addOrderBy('periodes.fin','ASC');
        return $qb->getQuery()->getArrayResult();
    }
}
