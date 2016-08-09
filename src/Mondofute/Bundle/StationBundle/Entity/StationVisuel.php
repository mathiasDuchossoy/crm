<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationVisuel
 */
abstract class StationVisuel
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $actif = false;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\Station
     */
    private $station;
    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $visuel;

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
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return StationVisuel
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationVisuelTraduction $traduction
     *
     * @return StationVisuel
     */
    public function addTraduction(\Mondofute\Bundle\StationBundle\Entity\StationVisuelTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStationVisuel($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationVisuelTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\StationBundle\Entity\StationVisuelTraduction $traduction)
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
     * Get station
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return StationVisuel
     */
    public function setStation(\Mondofute\Bundle\StationBundle\Entity\Station $station = null)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get visuel
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getVisuel()
    {
        return $this->visuel;
    }

    /**
     * Set visuel
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $visuel
     *
     * @return StationVisuel
     */
    public function setVisuel(\Application\Sonata\MediaBundle\Entity\Media $visuel = null)
    {
        $this->visuel = $visuel;

        return $this;
    }
}
