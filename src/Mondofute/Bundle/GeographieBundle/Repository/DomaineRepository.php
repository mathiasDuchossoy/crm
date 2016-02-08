<?php

namespace Mondofute\Bundle\GeographieBundle\Repository;

/**
 * DomaineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DomaineRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des domaines crm qui sont de la langue locale
    public function getTraductionsDomainesByLocale($locale, $domaineUnifieId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d , dt ')
            ->from('MondofuteGeographieBundle:Domaine', 'd')
            ->join('d.traductions', 'dt')
            ->join('d.domaineUnifie', 'du')
            ->join('d.site', 's')
            ->join('dt.langue', 'l')
            ->where("l.code = '$locale'");
//        ->setParameter('code' , $locale)
        if (!empty($domaineUnifieId))
        {
            $qb->andWhere('du.id != :domaineUnifieId')
                ->setParameter('domaineUnifieId' , $domaineUnifieId);
        }
        $qb->andWhere('d.domaineParent IS NULL');
        $qb->orderBy('d.id', 'ASC');

        return $qb;
    }
    
}
