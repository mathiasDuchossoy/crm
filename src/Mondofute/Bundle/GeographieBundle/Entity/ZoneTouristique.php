<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ZoneTouristique
 */
class ZoneTouristique
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie
     */
    private $zoneTouristiqueUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction)
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
     * @return ZoneTouristique
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get zoneTouristiqueUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie
     */
    public function getZoneTouristiqueUnifie()
    {
        return $this->zoneTouristiqueUnifie;
    }

    /**
     * Set zoneTouristiqueUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie $zoneTouristiqueUnifie
     *
     * @return ZoneTouristique
     */
    public function setZoneTouristiqueUnifie(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie $zoneTouristiqueUnifie = null)
    {
        $this->zoneTouristiqueUnifie = $zoneTouristiqueUnifie;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setZoneTouristique($this);
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

    public function setTraductions($traductions)
    {
//        $this->traductions = $traductions;
//        return $this;
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction
     *
     * @return ZoneTouristique
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction)
    {
        $this->traductions[] = $traduction->setZoneTouristique($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return ZoneTouristique
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
}
