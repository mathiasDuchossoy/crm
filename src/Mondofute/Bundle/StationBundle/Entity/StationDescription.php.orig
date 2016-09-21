<?php

namespace Mondofute\Bundle\StationBundle\Entity;

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
     * @var \Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie
     */
    private $stationDescriptionUnifie;

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
     * @return StationDescription
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
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction $traduction
     *
     * @return StationDescription
     */
    public function addTraduction(\Mondofute\Bundle\StationBundle\Entity\StationDescriptionTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStationDescription($this);

        return $this;
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
     * @return StationDescription
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationDescriptionUnifie
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie
     */
    public function getStationDescriptionUnifie()
    {
        return $this->stationDescriptionUnifie;
    }

    /**
     * Set stationDescriptionUnifie
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie $stationDescriptionUnifie
     *
     * @return StationDescription
     */
    public function setStationDescriptionUnifie(\Mondofute\Bundle\StationBundle\Entity\StationDescriptionUnifie $stationDescriptionUnifie = null)
    {
        $this->stationDescriptionUnifie = $stationDescriptionUnifie;

        return $this;
    }
}
