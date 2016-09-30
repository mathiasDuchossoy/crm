<?php

namespace Mondofute\Bundle\LogementBundle\Repository;

/**
 * LogementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogementRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByFournisseurHebergement($fournisseurId, $hebergementUnifieId, $siteId )
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q
            ->from('MondofuteLogementBundle:Logement' , 'logement')
            ->select('logement')
            ->join('logement.fournisseurHebergement' , 'fournisseurHebergement')
            ->join('logement.site' , 'site')
            ->where('site.id = :siteId')
            ->andWhere('fournisseurHebergement.fournisseur = :fournisseurId')
            ->andWhere('fournisseurHebergement.hebergement = :hebergementUnifieId')
            ->setParameters(
                array(
                    'fournisseurId'         => $fournisseurId,
                    'hebergementUnifieId'   => $hebergementUnifieId,
                    'siteId'   => $siteId
                )
            )
        ;

        $result = $q->getQuery()->getResult();

        return $result;
    }
}
