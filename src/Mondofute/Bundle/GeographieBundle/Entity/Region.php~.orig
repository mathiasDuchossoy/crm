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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $photos;

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

    /**
     * Add image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionImage $image
     *
     * @return Region
     */
    public function addImage(\Mondofute\Bundle\GeographieBundle\Entity\RegionImage $image)
    {
        $this->images[] = $image->setRegion($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionImage $image
     */
    public function removeImage(\Mondofute\Bundle\GeographieBundle\Entity\RegionImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionPhoto $photo
     *
     * @return Region
     */
    public function addPhoto(\Mondofute\Bundle\GeographieBundle\Entity\RegionPhoto $photo)
    {
        $this->photos[] = $photo->setRegion($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\RegionPhoto $photo
     */
    public function removePhoto(\Mondofute\Bundle\GeographieBundle\Entity\RegionPhoto $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
