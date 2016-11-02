<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseurUnifie;

/**
 * PrestationAnnexeFournisseur
 */
class PrestationAnnexeFournisseur extends FournisseurPrestationAffectation
{
    /**
     * @var PrestationAnnexeFournisseurUnifie
     */
    private $prestationAnnexeFournisseurUnifie;

    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\Station
     */
    private $station;

    /**
     * Get prestationAnnexeFournisseurUnifie
     *
     * @return PrestationAnnexeFournisseurUnifie
     */
    public function getPrestationAnnexeFournisseurUnifie()
    {
        return $this->prestationAnnexeFournisseurUnifie;
    }

    /**
     * Set prestationAnnexeFournisseurUnifie
     *
     * @param PrestationAnnexeFournisseurUnifie $prestationAnnexeFournisseurUnifie
     *
     * @return PrestationAnnexeFournisseur
     */
    public function setPrestationAnnexeFournisseurUnifie(PrestationAnnexeFournisseurUnifie $prestationAnnexeFournisseurUnifie = null)
    {
        $this->prestationAnnexeFournisseurUnifie = $prestationAnnexeFournisseurUnifie;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return PrestationAnnexeFournisseur
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get station
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return PrestationAnnexeFournisseur
     */
    public function setStation(\Mondofute\Bundle\StationBundle\Entity\Station $station = null)
    {
        $this->station = $station;

        return $this;
    }
}
