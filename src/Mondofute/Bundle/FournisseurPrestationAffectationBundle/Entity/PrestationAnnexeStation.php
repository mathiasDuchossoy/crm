<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStationUnifie;
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
}