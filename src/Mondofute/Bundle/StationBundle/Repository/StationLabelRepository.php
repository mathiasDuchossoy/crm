<?php

namespace Mondofute\Bundle\StationBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * StationLabelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StationLabelRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return mixed
     */
    public function countTotal()
    {
        return $this->createQueryBuilder('entity')
            ->select('COUNT(entity)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get the paginated list of published secteurs
     *
     * @param int $page
     * @param int $maxperpage
     * @param $locale
     * @param array $sortbyArray
     * @param int $site
     * @return Paginator
     */
    public function getList($page = 1, $maxperpage, $locale, $sortbyArray = array(), $site = 1)
    {
        $q = $this->createQueryBuilder('entity')
            ->select('entity')
            ->join('entity.traductions' , 'traductions')
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        foreach ($sortbyArray as $key => $item) {
            $q
                ->orderBy($key, $item);
        }

        return new Paginator($q);
    }
}
