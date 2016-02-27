<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Secteur
 */
class Secteur
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var SecteurUnifie
     */
    private $secteurUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
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
     * Add traduction
     *
     * @param SecteurTraduction $traduction
     *
     * @return Secteur
     */
    public function addTraduction(SecteurTraduction $traduction)
    {
        $this->traductions[] = $traduction->setSecteur($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param SecteurTraduction $traduction
     */
    public function removeTraduction(SecteurTraduction $traduction)
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
     * @return Secteur
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get secteurUnifie
     *
     * @return SecteurUnifie
     */
    public function getSecteurUnifie()
    {
        return $this->secteurUnifie;
    }

    /**
     * Set secteurUnifie
     *
     * @param SecteurUnifie $secteurUnifie
     *
     * @return Secteur
     */
    public function setSecteurUnifie(SecteurUnifie $secteurUnifie = null)
    {
        $this->secteurUnifie = $secteurUnifie;

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
                $cloneTraduction->setSecteur($this);
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

    public function setTraductions($traductions)
    {
        $this->traductions = $traductions;
        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return Secteur
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
