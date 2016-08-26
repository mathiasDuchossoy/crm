<?php

namespace Mondofute\Bundle\StationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;

    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie
     */
    private $stationCommentVenirUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $grandeVilles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return StationCommentVenir
     */
    public function addStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Remove station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     */
    public function removeStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
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

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get site
     *
     * @return \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param \Mondofute\Bundle\SiteBundle\Entity\Site $site
     *
     * @return StationCommentVenir
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationCommentVenirUnifie
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie
     */
    public function getStationCommentVenirUnifie()
    {
        return $this->stationCommentVenirUnifie;
    }

    /**
     * Set stationCommentVenirUnifie
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie $stationCommentVenirUnifie
     *
     * @return StationCommentVenir
     */
    public function setStationCommentVenirUnifie(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie $stationCommentVenirUnifie = null)
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
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction $traduction
     *
     * @return StationCommentVenir
     */
    public function addTraduction(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenirTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStationCommentVenir($this);

        return $this;
    }

    /**
     * Add grandeVille
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille $grandeVille
     *
     * @return StationCommentVenir
     */
    public function addGrandeVille(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille $grandeVille)
    {
        $this->grandeVilles[] = $grandeVille->setStationCommentVenir($this);

        return $this;
    }

    /**
     * Remove grandeVille
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille $grandeVille
     */
    public function removeGrandeVille(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille $grandeVille)
    {
        $this->grandeVilles->removeElement($grandeVille);
    }

    /**
     * Get grandeVilles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrandeVilles()
    {
        return $this->grandeVilles;
    }
}
