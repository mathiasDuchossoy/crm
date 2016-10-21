<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * StationLabel
 */
class StationLabel
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Remove traduction
     *
     * @param StationLabelTraduction $traduction
     */
    public function removeTraduction(StationLabelTraduction $traduction)
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
     * @param StationLabelTraduction $traduction
     *
     * @return StationLabel
     */
    public function addTraduction(StationLabelTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStationLabel($this);

        return $this;
    }

}
