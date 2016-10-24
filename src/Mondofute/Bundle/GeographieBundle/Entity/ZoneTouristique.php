<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;


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
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var ZoneTouristiqueUnifie
     */
    private $zoneTouristiqueUnifie;
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
     * @param ZoneTouristiqueTraduction $traduction
     */
    public function removeTraduction(ZoneTouristiqueTraduction $traduction)
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
     * @return ZoneTouristique
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get zoneTouristiqueUnifie
     *
     * @return ZoneTouristiqueUnifie
     */
    public function getZoneTouristiqueUnifie()
    {
        return $this->zoneTouristiqueUnifie;
    }

    /**
     * Set zoneTouristiqueUnifie
     *
     * @param ZoneTouristiqueUnifie $zoneTouristiqueUnifie
     *
     * @return ZoneTouristique
     */
    public function setZoneTouristiqueUnifie(ZoneTouristiqueUnifie $zoneTouristiqueUnifie = null)
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
     * @return Collection
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
     * @param ZoneTouristiqueTraduction $traduction
     *
     * @return ZoneTouristique
     */
    public function addTraduction(ZoneTouristiqueTraduction $traduction)
    {
        $this->traductions[] = $traduction->setZoneTouristique($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param Station $station
     *
     * @return ZoneTouristique
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
     * @param ZoneTouristiqueImage $image
     *
     * @return ZoneTouristique
     */
    public function addImage(ZoneTouristiqueImage $image)
    {
        $this->images[] = $image->setZoneTouristique($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param ZoneTouristiqueImage $image
     */
    public function removeImage(ZoneTouristiqueImage $image)
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
     * @param ZoneTouristiquePhoto $photo
     *
     * @return ZoneTouristique
     */
    public function addPhoto(ZoneTouristiquePhoto $photo)
    {
        $this->photos[] = $photo->setZoneTouristique($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param ZoneTouristiquePhoto $photo
     */
    public function removePhoto(ZoneTouristiquePhoto $photo)
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
     * @return ZoneTouristique
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add video
     *
     * @param ZoneTouristiqueVideo $video
     *
     * @return ZoneTouristique
     */
    public function addVideo(ZoneTouristiqueVideo $video)
    {
        $this->videos[] = $video->setZoneTouristique($this);

        return $this;
    }

    /**
     * Remove video
     *
     * @param ZoneTouristiqueVideo $video
     */
    public function removeVideo(ZoneTouristiqueVideo $video)
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
