<?php

namespace Mondofute\Bundle\UniteBundle\Repository;

/**
 * UniteTarifRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UniteTarifRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des niveau skieur crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('uniteTarif , traductions ')
            ->from('MondofuteUniteBundle:UniteTarif', 'uniteTarif')
            ->join('uniteTarif.traductions', 'traductions')
            ->join('traductions.langue', 'l')
            ->where("l.code = '$locale'");
//        $qb->orderBy('ns.id', 'ASC');

        return $qb;
    }
}