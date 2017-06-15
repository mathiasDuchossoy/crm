<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Repository;

use DateTime;

/**
 * FournisseurPrestationAnnexeParamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurPrestationAnnexeParamRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * retourner les prestationAnnexeAnnexe dont le founisseur n'est pas un hébergement
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByFournisseurNotHebergement()
    {
        $qb = $this->createQueryBuilder('entity');
        $qb
            ->select('entity')
            ->join('entity.fournisseurPrestationAnnexe', 'fournisseurPrestationAnnexe')
            ->join('fournisseurPrestationAnnexe.fournisseur', 'fournisseur')
            ->join('fournisseur.types', 'famillePrestationAnnexe')
            ->where('famillePrestationAnnexe.id != 9');

        return $qb;

    }

    public function findPrestationAnnexeExterne($dateDebut, $dateFin, $fournisseurId, $typeId, $stationId)
    {

        $qb = $this->createQueryBuilder('entity');
        $qb->select('entity')
            ->join('entity.tarifs', 'tarifs')
            ->leftJoin('tarifs.periodeValidites', 'periodeValidites')
            ->join('entity.fournisseurPrestationAnnexe', 'fournisseurPrestationAnnexe')
            ->join('fournisseurPrestationAnnexe.fournisseur', 'fournisseur')
            ->join('fournisseur.types', 'types')
            ->join('entity.prestationAnnexeStations', 'prestationAnnexeStations')
            ->join('prestationAnnexeStations.station', 'station')
            ->where('fournisseur.id = :fournisseurId AND periodeValidites.dateDebut <= :dateDebut AND periodeValidites.dateFin >= :dateFin')
//            ->orWhere('fournisseur.id = :fournisseurId AND tarifs.periodeValidites IS EMPTY')
            ->orWhere('fournisseur.id = :fournisseurId AND fournisseurPrestationAnnexe.freeSale = true')
            ->andWhere('types.id = :typeId')
            ->andWhere('station.id = :stationId')
            ->andWhere('entity.tarifs is not empty');

        $dateDebut = new DateTime($dateDebut);
        $dateFin = new DateTime($dateFin);
        $qb->setParameters([
            'fournisseurId' => $fournisseurId,
            'dateDebut' => $dateDebut->format('Y-m-d'),
            'dateFin' => $dateFin->format('Y-m-d'),
            'typeId' => $typeId,
            'stationId' => $stationId,
        ]);

        $result = $qb->getQuery()->getResult();
//        dump($result);
//        die;
        return $result;

    }
}
