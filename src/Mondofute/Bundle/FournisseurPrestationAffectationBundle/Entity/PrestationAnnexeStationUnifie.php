<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * PrestationAnnexeStationUnifie
 */
class PrestationAnnexeStationUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $prestationAnnexeStations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestationAnnexeStations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add prestationAnnexeStation
     *
     * @param PrestationAnnexeStation $prestationAnnexeStation
     *
     * @return PrestationAnnexeStationUnifie
     */
    public function addPrestationAnnexeStation(PrestationAnnexeStation $prestationAnnexeStation)
    {
        $this->prestationAnnexeStations[] = $prestationAnnexeStation->setPrestationAnnexeStationUnifie($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeStation
     *
     * @param PrestationAnnexeStation $prestationAnnexeStation
     */
    public function removePrestationAnnexeStation(PrestationAnnexeStation $prestationAnnexeStation)
    {
        $this->prestationAnnexeStations->removeElement($prestationAnnexeStation);
    }

    /**
     * Get prestationAnnexeStations
     *
     * @return Collection
     */
    public function getPrestationAnnexeStations()
    {
        return $this->prestationAnnexeStations;
    }
}
