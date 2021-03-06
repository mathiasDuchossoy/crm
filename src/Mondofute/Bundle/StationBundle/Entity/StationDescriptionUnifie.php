<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationDescriptionUnifie
 */
class StationDescriptionUnifie
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stationDescriptions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stationDescriptions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Remove stationDescription
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription
     */
    public function removeStationDescription(
        \Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription
    ) {
        $this->stationDescriptions->removeElement($stationDescription);
    }

    /**
     * Get stationDescriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStationDescriptions()
    {
        return $this->stationDescriptions;
    }

    /**
     * @param $stationDescriptions
     * @return $this
     */
    public function setStationDescriptions($stationDescriptions)
    {
        $this->getStationDescriptions()->clear();

        foreach ($stationDescriptions as $stationDescription) {
            $this->addStationDescription($stationDescription);
        }
        return $this;
    }

    /**
     * Add stationDescription
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription
     *
     * @return StationDescriptionUnifie
     */
    public function addStationDescription(\Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription)
    {
        $this->stationDescriptions[] = $stationDescription->setStationDescriptionUnifie($this);

        return $this;
    }
}
