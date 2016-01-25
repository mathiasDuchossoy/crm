<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Region
 */
class Region
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie
     */
    private $regionUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $departements;

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
     * @return Region
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get regionUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie
     */
    public function getRegionUnifie()
    {
        return $this->regionUnifie;
    }

    /**
     * Set regionUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie $regionUnifie
     *
     * @return Region
     */
    public function setRegionUnifie(\Mondofute\Bundle\GeographieBundle\Entity\RegionUnifie $regionUnifie = null)
    {
        $this->regionUnifie = $regionUnifie;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction $traduction
     *
     * @return Region
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction $traduction)
    {
        $this->traductions[] = $traduction->setRegion($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\RegionTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
                $cloneTraduction->setRegion($this);
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
        $this->traductions = $traductions;
        return $this;
    }

    /**
     * Add departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     *
     * @return Region
     */
    public function addDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement)
    {
        $this->departements[] = $departement;

        return $this;
    }

    /**
     * Remove departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     */
    public function removeDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement)
    {
        $this->departements->removeElement($departement);
    }

    /**
     * Get departements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartements()
    {
        return $this->departements;
    }




}
