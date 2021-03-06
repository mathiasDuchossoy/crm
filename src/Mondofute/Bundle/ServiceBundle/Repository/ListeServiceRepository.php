<?php

namespace Mondofute\Bundle\ServiceBundle\Repository;

/**
 * ListeServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ListeServiceRepository extends \Doctrine\ORM\EntityRepository
{
    public function chargerParFournisseur($idFournisseur = '')
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('listeService')
            ->from('MondofuteServiceBundle:ListeService', 'listeService')
            ->where('listeService.fournisseur = :idFournisseur')
            ->setParameter('idFournisseur', $idFournisseur);
//        return $qb;
//        $qb->select('')
//            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
//            ->where("fournisseur.contient = :contient")
//            ->setParameter('contient', FournisseurContient::PRODUIT);
//        if (!empty($enseigne)) {
//            $qb->andWhere("fournisseur.enseigne LIKE :enseigne")
////            ->setParameters(array('contient'=> FournisseurContient::FOURNISSEUR , 'id' => $fournisseurId))
//                ->setParameter('enseigne', '%' . $enseigne . '%');
//        }
//        $qb->orderBy('fournisseur.enseigne', 'ASC');
        return $qb;
    }
}
