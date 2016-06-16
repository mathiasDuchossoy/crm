<?php

namespace Mondofute\Bundle\LogementBundle\Repository;

use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;

/**
 * LogementUnifieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogementUnifieRepository extends \Doctrine\ORM\EntityRepository
{
    public function rechercherParFournisseurHebergement(FournisseurHebergement $fournisseurHebergement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('lu')
            ->from('MondofuteLogementBundle:LogementUnifie', 'lu')
            ->join('lu.logements', 'l')
            ->join('l.fournisseurHebergement', 'fh')
            ->where("fh.id = :id")
            ->setParameter('id', $fournisseurHebergement->getId());
//        ->setParameter('code' , $locale)
//        $qb->orderBy('r.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

}