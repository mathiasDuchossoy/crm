<?php

namespace Mondofute\Bundle\LogementBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
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


    /**
     * @return mixed
     */
    public function countTotalToFournisseur($idFournisseurHebergement, $site = 1)
    {
        return $this->createQueryBuilder('entity')
            ->select('COUNT(entity)')
            ->join('entity.logements', 'logements')
            ->where('logements.site = :site')
            ->setParameter('site', $site)
            ->join('logements.fournisseurHebergement', 'fournisseurHebergement')
            ->andWhere('fournisseurHebergement.id = :idFournisseurHebergement')
            ->setParameter('idFournisseurHebergement', $idFournisseurHebergement)
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
    public function getListToFournisseur($page = 1, $maxperpage, $locale, $sortbyArray = array(), $fournisseurHebergement, $site = 1)
    {
        $q = $this->createQueryBuilder('unifie')
            ->select('unifie')
            ->join('unifie.logements', 'entities')
            ->join('entities.traductions', 'traductions')
            ->join('traductions.langue', 'langue')
            ->join('entities.fournisseurHebergement', 'fournisseurHebergement')
            ->where('entities.site = :site')
            ->setParameter('site', $site)
            ->andWhere('fournisseurHebergement.id = :fournisseurHebergement')
            ->setParameter('fournisseurHebergement', $fournisseurHebergement)
            ->andWhere('langue.code = :code')
            ->setParameter('code', $locale)
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        $q->setParameters(array(
            'site' => $site,
            'fournisseurHebergement' => $fournisseurHebergement,
            'code' => $locale,
        ));

        foreach ($sortbyArray as $key => $item) {
            $q
                ->orderBy($key, $item);
        }


        return new Paginator($q);
    }



    /**
     * @param $fournisseurId
     * @param $hebergementUnifieId
     * @return ArrayCollection
     */
    public function findByFournisseurHebergement($fournisseurId , $hebergementUnifieId){
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('logementUnifie , logements, fournisseurHebergement')
            ->from('MondofuteLogementBundle:LogementUnifie', 'logementUnifie')
            ->join('logementUnifie.logements', 'logements')
            ->join('logements.fournisseurHebergement', 'fournisseurHebergement')
            ->where('fournisseurHebergement.hebergement = :hebergementUnifieId')
            ->setParameter('hebergementUnifieId' ,$hebergementUnifieId )
            ->andWhere('fournisseurHebergement.fournisseur = :fournisseurId')
            ->setParameter('fournisseurId' , $fournisseurId)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getFournisseur($logementUnfieId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('fournisseur.id')
            ->from('MondofuteLogementBundle:LogementUnifie', 'logementUnifie')
            ->join('logementUnifie.logements', 'logements')
            ->join('logements.fournisseurHebergement' , 'fournisseurHebergement')
            ->join('fournisseurHebergement.fournisseur' , 'fournisseur')
            ->where('logementUnifie.id = :logementUnfieId')
            ->setParameter('logementUnfieId' , $logementUnfieId)
            ->groupBy('fournisseur')
        ;

        $result = $qb->getQuery()->getSingleScalarResult();

//        dump($result);die;

        return $result;
    }

}
