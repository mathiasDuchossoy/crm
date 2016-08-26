<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * FamillePrestationAnnexeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FamillePrestationAnnexeRepository extends \Doctrine\ORM\EntityRepository
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
     * @param int $page
     * @param $maxperpage
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
            ->join('traductions.langue' , 'langue')
            ->where('langue.code = :locale')
            ->setParameter('locale', $locale)
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        foreach ($sortbyArray as $key => $item) {
            $q
                ->orderBy($key, $item);
        }

        return new Paginator($q);
    }


    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des départements crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('entity , traductions')
            ->from('MondofutePrestationAnnexeBundle:FamillePrestationAnnexe', 'entity')
            ->join('entity.traductions' , 'traductions')
            ->join('traductions.langue' , 'langue')
            ->where('langue.code = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('traductions.libelle', 'ASC')
        ;

        return $qb;
    }

//    public function getTraductionsByLocale($locale)
//    {
//
//        $qb = $this->getEntityManager()->createQueryBuilder();
//        $qb->select('r , rt')
//            ->from('MondofuteGeographieBundle:Departement', 'r')
//            ->join('r.traductions', 'rt')
//            ->join('r.site', 's')
//            ->join('rt.langue', 'l')
//            ->where("l.code = '$locale'");
////        ->setParameter('code' , $locale)
//        $qb->orderBy('r.id', 'ASC');
//
//        return $qb;
//    }

}
