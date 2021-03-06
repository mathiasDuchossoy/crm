<?php

namespace Mondofute\Bundle\CodePromoBundle\Repository;

/**
 * CodePromoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CodePromoRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByLike($like, $site)
    {
        $q = $this->createQueryBuilder('codePromo')
            ->select('codePromo.id, concat(codePromoUnifie.code, \' - \',codePromo.libelle) text')
            ->join('codePromo.codePromoUnifie', 'codePromoUnifie')
            ->where('codePromo.libelle LIKE :val or codePromoUnifie.code LIKE :val')
            ->setParameter('val', '%' . $like . '%')
            ->andWhere('codePromo.site = :site')
            ->setParameter('site', $site);

        return $q->getQuery()->getResult();
    }
}
