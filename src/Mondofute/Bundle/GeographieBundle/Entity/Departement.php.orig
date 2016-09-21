<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Departement
 */
class Departement
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie
     */
    private $departementUnifie;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Region
     */
    private $region;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $photos;
    /**
     * @var boolean
     */
    private $actif = true;

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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction)
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
     * @return Departement
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

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
                $cloneTraduction->setDepartement($this);
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
     * @return Departement $this
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction
     *
     * @return Departement
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDepartement($this);

        return $this;
    }

    /**
     * Get departementUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie
     */
    public function getDepartementUnifie()
    {
        return $this->departementUnifie;
    }

    /**
     * Set departementUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie $departementUnifie
     *
     * @return Departement
     */
    public function setDepartementUnifie(
        \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie $departementUnifie = null
    )
    {
        $this->departementUnifie = $departementUnifie;

        return $this;
    }

    /**
     * Get region
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set region
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Region $region
     *
     * @return Departement
     */
    public function setRegion(\Mondofute\Bundle\GeographieBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return Departement
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
     * Add image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementImage $image
     *
     * @return Departement
     */
    public function addImage(\Mondofute\Bundle\GeographieBundle\Entity\DepartementImage $image)
    {
        $this->images[] = $image->setDepartement($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementImage $image
     */
    public function removeImage(\Mondofute\Bundle\GeographieBundle\Entity\DepartementImage $image)
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto $photo
     *
     * @return Departement
     */
    public function addPhoto(\Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto $photo)
    {
        $this->photos[] = $photo->setDepartement($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto $photo
     */
    public function removePhoto(\Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto $photo)
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
     * @return Departement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }
}
