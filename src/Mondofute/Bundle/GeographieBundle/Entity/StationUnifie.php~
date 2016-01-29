<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add station
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Station $station
     *
     * @return StationUnifie
     */
    public function addStation(\Mondofute\Bundle\GeographieBundle\Entity\Station $station)
    {
        $this->stations[] = $station->setStationUnifie($this);

        return $this;
    }

    /**
     * Remove station
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Station $station
     */
    public function removeStation(\Mondofute\Bundle\GeographieBundle\Entity\Station $station)
    {
        $this->stations->removeElement($station);
    }

    /**
     * Get stations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStations()
    {
        return $this->stations;
    }

    public function setStations($stations)
    {
        $this->stations = $stations;
        return $this;
    }
}
