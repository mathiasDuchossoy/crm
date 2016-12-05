<?php

namespace Mondofute\Bundle\HebergementBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * HebergementUnifieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HebergementUnifieRepository extends \Doctrine\ORM\EntityRepository
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
        $q = $this->createQueryBuilder('unifie')
            ->select('unifie')
            ->join('unifie.hebergements', 'entities')
            ->join('entities.traductions', 'traductions')
            ->join('traductions.langue', 'langue')
            ->where('entities.site = :site')
            ->setParameter('site', $site)
            ->andWhere('langue.code = :code')
            ->setParameter('code', $locale)
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        foreach ($sortbyArray as $key => $item) {
            $q
                ->orderBy($key, $item);
        }

        return new Paginator($q);
    }


    public function findByFournisseur($fournisseurId , $locale, $site = 1 , $stationId = null){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('hebergementUnifie.id hebergementUnifieId, hebergements.id hebergementId, traductions.nom, fournisseur.id fournisseurId, site.id siteId , stationUnifie.id stationUnifieId')
//        $qb->select('hebergementUnifie')
            ->from('MondofuteHebergementBundle:HebergementUnifie', 'hebergementUnifie')
            ->join('hebergementUnifie.fournisseurs' , 'fournisseurHebergements')
            ->join('fournisseurHebergements.fournisseur' , 'fournisseur')
            ->join('hebergementUnifie.hebergements' , 'hebergements')
            ->join('hebergements.traductions' , 'traductions')
            ->join('traductions.langue' , 'langue')
            ->join('hebergements.site' , 'site')
            ->join('hebergements.station' , 'station')
            ->join('station.stationUnifie' , 'stationUnifie')
            ->where('fournisseur.id = :fournisseurId')
            ->setParameter('fournisseurId', $fournisseurId)
            ->andWhere('langue.code = :langue')
            ->setParameter('langue', $locale)
            ->andWhere('site.id = :site')
            ->setParameter('site', $site)
        ;

        if (!empty($stationId))
        {
            $qb
                ->andWhere('stationUnifie.id = :stationId')
                ->setParameter('stationId' , $stationId)
            ;
        }

        $qb->orderBy('hebergementUnifie.id', 'ASC');

        $result = $qb->getQuery()->getResult();
//        dump($result);die;
        return $result;
    }

//    public function findByFournisseur($fournisseurId , $locale){
//        $qb = $this->getEntityManager()->createQueryBuilder();
//        $qb->select('hebergementUnifie , hebergements.id hebergementId, traductions.nom, fournisseur.id fournisseurId, site.id siteId')
////        $qb->select('hebergementUnifie')
//            ->from('MondofuteHebergementBundle:HebergementUnifie', 'hebergementUnifie')
//            ->join('hebergementUnifie.fournisseurs' , 'fournisseurHebergements')
//            ->join('fournisseurHebergements.fournisseur' , 'fournisseur')
//            ->join('hebergementUnifie.hebergements' , 'hebergements')
//            ->join('hebergements.traductions' , 'traductions')
//            ->join('traductions.langue' , 'langue')
//            ->join('hebergements.site' , 'site')
//            ->where('fournisseur.id = :fournisseurId')
//            ->setParameter('fournisseurId', $fournisseurId)
//            ->andWhere('langue.code = :langue')
//            ->setParameter('langue', $locale)
//        ;
//        $qb->orderBy('hebergementUnifie.id', 'ASC');
//
//        $result = $qb->getQuery()->getResult();
////        dump($result);die;
//        return $result;
//    }


    public function findHebergementUnifiesDuFournisseur($fournisseurId ){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('hebergementUnifie , hebergements')
//        $qb->select('hebergementUnifie')
            ->from('MondofuteHebergementBundle:HebergementUnifie', 'hebergementUnifie')
            ->join('hebergementUnifie.fournisseurs' , 'fournisseurHebergements')
            ->join('fournisseurHebergements.fournisseur' , 'fournisseur')
            ->join('hebergementUnifie.hebergements' , 'hebergements')
            ->join('hebergements.traductions' , 'traductions')
            ->join('traductions.langue' , 'langue')
            ->join('hebergements.site' , 'site')
            ->where('fournisseur.id = :fournisseurId')
            ->setParameter('fournisseurId', $fournisseurId)
//            ->andWhere('langue.code = :langue')
//            ->setParameter('langue', $locale)
//            ->andWhere('site.id = :site')
//            ->setParameter('site', $site)
        ;
        $qb->orderBy('hebergementUnifie.id', 'ASC');

        $result = $qb->getQuery()->getResult();
//        dump($result);die;
        return $result;
    }

    public function getFournisseurHebergements($fournisseurId, $locale, $site = 1)
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q
            ->from('MondofuteHebergementBundle:HebergementUnifie' , 'hebergementUnifie')
            ->select('hebergementUnifie.id  hebergementUnifieId, hebergements.id hebergementId, traductions.nom, stationTraductions.libelle stationLibelle, station.id stationId ')
            ->join('hebergementUnifie.fournisseurs' , 'fournisseurHebergements')
            ->join('fournisseurHebergements.fournisseur' , 'fournisseur')
            ->join('hebergementUnifie.hebergements' , 'hebergements')
            ->join('hebergements.traductions' , 'traductions')
            ->join('traductions.langue' , 'langue')
            ->join('hebergements.station', 'station')
            ->join('station.traductions', 'stationTraductions')
            ->join('stationTraductions.langue', 'stationTraductionlangue')
            ->where('fournisseur = :fournisseurId')
            ->setParameter('fournisseurId' , $fournisseurId)
            ->andWhere('langue.code = :locale')
            ->setParameter('locale' , $locale)
            ->andWhere('stationTraductionlangue.code = :localeStation')
            ->setParameter('localeStation', $locale)
            ->join('hebergements.site' , 'site')
            ->andWhere('site.id = :site')
            ->setParameter('site' , $site)
            ->orderBy('stationTraductions.libelle')
        ;

        $result = $q->getQuery()->getResult();

        return $result;
    }
}
