<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;

/**
 * Profil
 */
class Profil
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
     * @var ProfilUnifie
     */
    private $profilUnifie;
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
     * @param ProfilTraduction $traduction
     */
    public function removeTraduction(ProfilTraduction $traduction)
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
     * @return Profil
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get profilUnifie
     *
     * @return ProfilUnifie
     */
    public function getProfilUnifie()
    {
        return $this->profilUnifie;
    }

    /**
     * Set profilUnifie
     *
     * @param ProfilUnifie $profilUnifie
     *
     * @return Profil
     */
    public function setProfilUnifie(ProfilUnifie $profilUnifie = null)
    {
        $this->profilUnifie = $profilUnifie;

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
                $cloneTraduction->setProfil($this);
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
     * @return Profil $this
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
     * @param ProfilTraduction $traduction
     *
     * @return Profil
     */
    public function addTraduction(ProfilTraduction $traduction)
    {
        $this->traductions[] = $traduction->setProfil($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param Station $station
     *
     * @return Profil
     */
    public function addStation(Station $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Add image
     *
     * @param ProfilImage $image
     *
     * @return Profil
     */
    public function addImage(ProfilImage $image)
    {
        $this->images[] = $image->setProfil($this);

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
     * Remove image
     *
     * @param ProfilImage $image
     */
    public function removeImage(ProfilImage $image)
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
     * @param ProfilPhoto $photo
     *
     * @return Profil
     */
    public function addPhoto(ProfilPhoto $photo)
    {
        $this->photos[] = $photo->setProfil($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param ProfilPhoto $photo
     */
    public function removePhoto(ProfilPhoto $photo)
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
     * @return Profil
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add video
     *
     * @param ProfilVideo $video
     *
     * @return Profil
     */
    public function addVideo(ProfilVideo $video)
    {
        $this->videos[] = $video->setProfil($this);

        return $this;
    }

    /**
     * Remove video
     *
     * @param ProfilVideo $video
     */
    public function removeVideo(ProfilVideo $video)
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
