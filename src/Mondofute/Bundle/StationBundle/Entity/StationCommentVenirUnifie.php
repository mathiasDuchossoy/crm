<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationCommentVenirUnifie
 */
class StationCommentVenirUnifie
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stationCommentVenirs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stationCommentVenirs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove stationCommentVenir
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir
     */
    public function removeStationCommentVenir(
        \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir
    ) {
        $this->stationCommentVenirs->removeElement($stationCommentVenir);
    }

    /**
     * Get stationCommentVenirs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStationCommentVenirs()
    {
        return $this->stationCommentVenirs;
    }

    /**
     * @param $stationCommentVenirs
     * @return $this
     */
    public function setStationCommentVenirs($stationCommentVenirs)
    {
        $this->getStationCommentVenirs()->clear();

        foreach ($stationCommentVenirs as $stationCommentVenir) {
            $this->addStationCommentVenir($stationCommentVenir);
        }
        return $this;
    }

    /**
     * Add stationCommentVenir
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir
     *
     * @return StationCommentVenirUnifie
     */
    public function addStationCommentVenir(
        \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir
    ) {
        $this->stationCommentVenirs[] = $stationCommentVenir->setStationCommentVenirUnifie($this);

        return $this;
    }
}
