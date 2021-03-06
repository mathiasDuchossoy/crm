<?php

namespace Mondofute\Bundle\GeographieBundle\Repository;

/**
 * ProfilRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProfilRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des départements crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('profil , traductions')
            ->from('MondofuteGeographieBundle:Profil', 'profil')
            ->join('profil.traductions', 'traductions')
            ->join('profil.site', 'site')
            ->join('traductions.langue', 'langue')
            ->where("langue.code = '$locale'");
//        ->setParameter('code' , $locale)
        $qb->orderBy('profil.profilUnifie', 'ASC');

        return $qb;
    }
}
