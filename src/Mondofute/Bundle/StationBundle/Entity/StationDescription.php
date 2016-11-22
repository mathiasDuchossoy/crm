<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * StationDescription
 */
class StationDescription
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var Collection
     */
    private $stations;

    /**
     * @var Collection
     */
    private $traductions;

    /**
     * @var Site
     */
    private $site;

    /**
     * @var StationDescriptionUnifie
     */
    private $stationDescriptionUnifie;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stations = new ArrayCollection();
        $this->traductions = new ArrayCollection();
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
     * Add station
     *
     * @param Station $station
     *
     * @return StationDescription
     */
    public function addStation(Station $station)
    {
        $this->stations[] = $station;

        return $this;
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
     * Remove traduction
     *
     * @param StationDescriptionTraduction $traduction
     */
    public function removeTraduction(StationDescriptionTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * @param $traductions
     * @return $this
     */
    public function setTraductions($traductions)
    {
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param StationDescriptionTraduction $traduction
     *
     * @return StationDescription
     */
    public function addTraduction(StationDescriptionTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStationDescription($this);

        return $this;
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return StationDescription
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationDescriptionUnifie
     *
     * @return StationDescriptionUnifie
     */
    public function getStationDescriptionUnifie()
    {
        return $this->stationDescriptionUnifie;
    }

    /**
     * Set stationDescriptionUnifie
     *
     * @param StationDescriptionUnifie $stationDescriptionUnifie
     *
     * @return StationDescription
     */
    public function setStationDescriptionUnifie(StationDescriptionUnifie $stationDescriptionUnifie = null)
    {
        $this->stationDescriptionUnifie = $stationDescriptionUnifie;

        return $this;
    }
}
