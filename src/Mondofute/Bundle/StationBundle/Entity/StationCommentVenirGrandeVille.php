<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationCommentVenirGrandeVille
 */
class StationCommentVenirGrandeVille
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir
     */
    private $stationCommentVenir;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\GrandeVille
     */
    private $grandeVille;

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
     * Get stationCommentVenir
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir
     */
    public function getStationCommentVenir()
    {
        return $this->stationCommentVenir;
    }

    /**
     * Set stationCommentVenir
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir
     *
     * @return StationCommentVenirGrandeVille
     */
    public function setStationCommentVenir(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir = null)
    {
        $this->stationCommentVenir = $stationCommentVenir;

        return $this;
    }

    /**
     * Get grandeVille
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\GrandeVille
     */
    public function getGrandeVille()
    {
        return $this->grandeVille;
    }

    /**
     * Set grandeVille
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\GrandeVille $grandeVille
     *
     * @return StationCommentVenirGrandeVille
     */
    public function setGrandeVille(\Mondofute\Bundle\GeographieBundle\Entity\GrandeVille $grandeVille = null)
    {
        $this->grandeVille = $grandeVille;

        return $this;
    }
}
