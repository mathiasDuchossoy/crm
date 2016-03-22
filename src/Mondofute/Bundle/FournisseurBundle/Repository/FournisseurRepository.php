<?php

namespace Mondofute\Bundle\FournisseurBundle\Repository;

use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;

/**
 * FournisseurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurRepository extends \Doctrine\ORM\EntityRepository
{
    public function getFournisseurDeFournisseur($fournisseurId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fournisseur')
            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
            ->where("fournisseur.contient = :contient")
            ->setParameter('contient', FournisseurContient::FOURNISSEUR);
        if (!empty($fournisseurId)) {
            $qb->andWhere("fournisseur.id != :id")
//            ->setParameters(array('contient'=> FournisseurContient::FOURNISSEUR , 'id' => $fournisseurId))
                ->setParameter('id', $fournisseurId);
        }
        $qb->orderBy('fournisseur.id', 'ASC');
        return $qb;
    }

    public function rechercherTypeHebergement($enseigne = '')
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fournisseur')
            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
            ->where("fournisseur.contient = :contient")
            ->setParameter('contient', FournisseurContient::PRODUIT);
        if (!empty($enseigne)) {
            $qb->andWhere("fournisseur.enseigne LIKE :enseigne")
//            ->setParameters(array('contient'=> FournisseurContient::FOURNISSEUR , 'id' => $fournisseurId))
                ->setParameter('enseigne', '%' . $enseigne . '%');
        }
        $qb->orderBy('fournisseur.enseigne', 'ASC');
        return $qb;
    }
}
