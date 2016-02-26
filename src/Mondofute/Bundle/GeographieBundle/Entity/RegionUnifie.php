<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * RegionUnifie
 */
class RegionUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $regions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->regions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove region
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Region $region
     */
    public function removeRegion(\Mondofute\Bundle\GeographieBundle\Entity\Region $region)
    {
        $this->regions->removeElement($region);
    }

    /**
     * Get regions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param ArrayCollection $regions
     * @return ArrayCollection
     */
    public function setRegions(ArrayCollection $regions)
    {
//        $this->regions = $regions;
//        return $this;
        $this->getRegions()->clear();

        foreach ($regions as $region) {
            $this->addRegion($region);
        }
        return $this;
    }

    /**
     * Add region
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Region $region
     *
     * @return RegionUnifie
     */
    public function addRegion(\Mondofute\Bundle\GeographieBundle\Entity\Region $region)
    {
        $this->regions[] = $region->setRegionUnifie($this);

        return $this;
    }
}
