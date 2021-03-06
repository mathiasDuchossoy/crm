<?php

namespace Mondofute\Bundle\UniteBundle\Repository;

/**
 * UniteDistanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UniteDistanceRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('uniteDistance , traductions ')
            ->from('MondofuteUniteBundle:UniteDistance', 'uniteDistance')
            ->join('uniteDistance.traductions', 'traductions')
            ->join('traductions.langue', 'l')
            ->where("l.code = '$locale'");
//        $qb->orderBy('ns.id', 'ASC');

        return $qb;
    }
}
