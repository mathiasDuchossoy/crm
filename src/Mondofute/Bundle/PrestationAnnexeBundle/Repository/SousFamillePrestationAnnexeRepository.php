<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Repository;

/**
 * SousFamillePrestationAnnexeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SousFamillePrestationAnnexeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des départements crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('entity , traductions')
            ->from('MondofutePrestationAnnexeBundle:SousFamillePrestationAnnexe', 'entity')
            ->join('entity.traductions', 'traductions')
            ->join('traductions.langue', 'langue')
            ->where('langue.code = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('traductions.libelle', 'ASC');

        return $qb;
    }
}
