<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\StationBundle\Entity\Station;

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
     * @var Station
     */
    private $station;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $param;

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
    public function setPrestationAnnexeFournisseurUnifie(
        PrestationAnnexeFournisseurUnifie $prestationAnnexeFournisseurUnifie = null
    ) {
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
     * @return Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param Station $station
     *
     * @return PrestationAnnexeFournisseur
     */
    public function setStation(Station $station = null)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get param
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set param
     *
     * @param FournisseurPrestationAnnexeParam $param
     *
     * @return PrestationAnnexeFournisseur
     */
    public function setParam(FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }
}
