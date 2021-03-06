<?php

namespace Mondofute\Bundle\StationBundle\Repository;

/**
 * StationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StationRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductions des stations crm qui sont de la langue locale
    public function getTraductionsByLocale($locale, $stationUnifieId = null, $siteId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('station , traductions ')
            ->from('MondofuteStationBundle:Station', 'station')
            ->join('station.traductions', 'traductions')
            ->join('station.stationUnifie', 'stationUnifie')
            ->join('station.site', 'site')
            ->join('traductions.langue', 'langue')
            ->where("langue.code = '$locale'");
//        ->setParameter('code' , $locale)
        if (!empty($stationUnifieId)) {
            $qb->andWhere('stationUnifie.id != :stationUnifieId')
                ->setParameter('stationUnifieId', $stationUnifieId);
        }
        if (!empty($siteId)) {
            $qb->andWhere('site.id = :siteId')
                ->setParameter('siteId', $siteId);
        }
        $qb->andWhere('station.stationMere IS NULL');
        $qb->orderBy('station.id', 'ASC');

        return $qb;
    }
}
