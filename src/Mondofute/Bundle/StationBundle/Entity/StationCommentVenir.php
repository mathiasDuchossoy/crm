<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * StationCommentVenir
 */
class StationCommentVenir
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
     * @var StationCommentVenirUnifie
     */
    private $stationCommentVenirUnifie;
    /**
     * @var Collection
     */
    private $grandeVilles;

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
     * @return StationCommentVenir
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
     * @param StationCommentVenirTraduction $traduction
     */
    public function removeTraduction(StationCommentVenirTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * @return StationCommentVenir
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationCommentVenirUnifie
     *
     * @return StationCommentVenirUnifie
     */
    public function getStationCommentVenirUnifie()
    {
        return $this->stationCommentVenirUnifie;
    }

    /**
     * Set stationCommentVenirUnifie
     *
     * @param StationCommentVenirUnifie $stationCommentVenirUnifie
     *
     * @return StationCommentVenir
     */
    public function setStationCommentVenirUnifie(StationCommentVenirUnifie $stationCommentVenirUnifie = null)
    {
        $this->stationCommentVenirUnifie = $stationCommentVenirUnifie;

        return $this;
    }

    public function __clone()
    {
        /** @var StationCommentVenirTraduction $traduction */
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setStationCommentVenir($this);
            }
        }
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
     * @param StationCommentVenirTraduction $traduction
     *
     * @return StationCommentVenir
     */
    public function addTraduction(StationCommentVenirTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStationCommentVenir($this);

        return $this;
    }

    /**
     * Add grandeVille
     *
     * @param StationCommentVenirGrandeVille $grandeVille
     *
     * @return StationCommentVenir
     */
    public function addGrandeVille(StationCommentVenirGrandeVille $grandeVille)
    {
        $this->grandeVilles[] = $grandeVille->setStationCommentVenir($this);

        return $this;
    }

    /**
     * Remove grandeVille
     *
     * @param StationCommentVenirGrandeVille $grandeVille
     */
    public function removeGrandeVille(StationCommentVenirGrandeVille $grandeVille)
    {
        $this->grandeVilles->removeElement($grandeVille);
    }

    /**
     * Get grandeVilles
     *
     * @return Collection
     */
    public function getGrandeVilles()
    {
        return $this->grandeVilles;
    }
}
