<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * StationUnifie
 */
class StationUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $stations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stations = new ArrayCollection();
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
     * Remove station
     *
     * @param Station $station
     */
    public function removeStation(Station $station)
    {
        $this->stations->removeElement($station);
    }

    /**
     * Get stations
     *
     * @return Collection
     */
    public function getStations()
    {
        return $this->stations;
    }

    /**
     * @param $stations
     * @return $this
     */
    public function setStations($stations)
    {
        $this->getStations()->clear();

        foreach ($stations as $station) {
            $this->addStation($station);
        }
        return $this;
    }

    /**
     * Add station
     *
     * @param Station $station
     *
     * @return StationUnifie
     */
    public function addStation(Station $station)
    {
        $this->stations[] = $station->setStationUnifie($this);

        return $this;
    }
}
