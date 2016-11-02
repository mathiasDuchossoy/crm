<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStationUnifie;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\StationBundle\Entity\Station;

/**
 * PrestationAnnexeStation
 */
class PrestationAnnexeStation extends FournisseurPrestationAffectation
{
    /**
     * @var PrestationAnnexeStationUnifie
     */
    private $prestationAnnexeStationUnifie;

    /**
     * @var Station
     */
    private $station;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $param;

    /**
     * Get prestationAnnexeStationUnifie
     *
     * @return PrestationAnnexeStationUnifie
     */
    public function getPrestationAnnexeStationUnifie()
    {
        return $this->prestationAnnexeStationUnifie;
    }

    /**
     * Set prestationAnnexeStationUnifie
     *
     * @param PrestationAnnexeStationUnifie $prestationAnnexeStationUnifie
     *
     * @return PrestationAnnexeStation
     */
    public function setPrestationAnnexeStationUnifie(PrestationAnnexeStationUnifie $prestationAnnexeStationUnifie = null)
    {
        $this->prestationAnnexeStationUnifie = $prestationAnnexeStationUnifie;

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
     * @return PrestationAnnexeStation
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
     * @return PrestationAnnexeStation
     */
    public function setParam(FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }
}
