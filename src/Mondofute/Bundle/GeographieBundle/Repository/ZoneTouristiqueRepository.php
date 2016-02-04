<?php

namespace Mondofute\Bundle\GeographieBundle\Repository;

/**
 * ZoneTouristiqueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ZoneTouristiqueRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des zone touristiques crm qui sont de la langue locale
    public function getTraductionsZoneTouristiquesCRMByLocale($locale, $siteZoneTouristique)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r , rt')
            ->from('MondofuteGeographieBundle:ZoneTouristique', 'r')
            ->join('r.traductions', 'rt')
            ->join('r.site', 's')
            ->join('rt.langue', 'l')
            ->where("l.code = '$locale'");
//        ->setParameter('code' , $locale)
        if (!empty($siteZoneTouristique)) {
            $qb->andWhere('s.id = :site')
                ->setParameter('site', $siteZoneTouristique->getId());
        } else {
//            $qb->andWhere('s.crm = :crm')
//                ->setParameter('crm', 1);
        }
        $qb->orderBy('r.id', 'ASC');

        return $qb;
    }
}
