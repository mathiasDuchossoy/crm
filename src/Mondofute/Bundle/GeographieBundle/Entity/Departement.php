<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;


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
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var DepartementUnifie
     */
    private $departementUnifie;
    /**
     * @var Region
     */
    private $region;
    /**
     * @var Collection
     */
    private $stations;
    /**
     * @var Collection
     */
    private $images;
    /**
     * @var Collection
     */
    private $photos;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $videos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->region = new ArrayCollection();
        $this->stations = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->videos = new ArrayCollection();
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
     * @param DepartementTraduction $traduction
     */
    public function removeTraduction(DepartementTraduction $traduction)
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
     * @return Departement
     */
    public function setSite(Site $site = null)
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
     * @return Collection
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
     * @param DepartementTraduction $traduction
     *
     * @return Departement
     */
    public function addTraduction(DepartementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDepartement($this);

        return $this;
    }

    /**
     * Get departementUnifie
     *
     * @return DepartementUnifie
     */
    public function getDepartementUnifie()
    {
        return $this->departementUnifie;
    }

    /**
     * Set departementUnifie
     *
     * @param DepartementUnifie $departementUnifie
     *
     * @return Departement
     */
    public function setDepartementUnifie(
        DepartementUnifie $departementUnifie = null
    )
    {
        $this->departementUnifie = $departementUnifie;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set region
     *
     * @param Region $region
     *
     * @return Departement
     */
    public function setRegion(Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Add station
     *
     * @param Station $station
     *
     * @return Departement
     */
    public function addStation(Station $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Remove station
     *
     * @param Station $station
     */
    public function removeStation(Station $station)
    {
        $this->stations->removeElement($station);
    }

    /**
     * Get stations
     *
     * @return Collection
     */
    public function getStations()
    {
        return $this->stations;
    }

    /**
     * Add image
     *
     * @param DepartementImage $image
     *
     * @return Departement
     */
    public function addImage(DepartementImage $image)
    {
        $this->images[] = $image->setDepartement($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param DepartementImage $image
     */
    public function removeImage(DepartementImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add photo
     *
     * @param DepartementPhoto $photo
     *
     * @return Departement
     */
    public function addPhoto(DepartementPhoto $photo)
    {
        $this->photos[] = $photo->setDepartement($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param DepartementPhoto $photo
     */
    public function removePhoto(DepartementPhoto $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return Collection
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

    /**
     * Add video
     *
     * @param DepartementVideo $video
     *
     * @return Departement
     */
    public function addVideo(DepartementVideo $video)
    {
        $this->videos[] = $video->setDepartement($this);

        return $this;
    }

    /**
     * Remove video
     *
     * @param DepartementVideo $video
     */
    public function removeVideo(DepartementVideo $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *
     * @return Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }
}
