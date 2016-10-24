<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;

/**
 * Domaine
 */
class Domaine
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
     * @var DomaineUnifie
     */
    private $domaineUnifie;
    /**
     * @var Collection
     */
    private $sousDomaines;
    /**
     * @var Domaine
     */
    private $domaineParent;
    /**
     * @var DomaineCarteIdentite
     */
    private $domaineCarteIdentite;
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
    private $imagesParent = false;
    /**
     * @var boolean
     */
    private $photosParent = false;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $videos;
    /**
     * @var boolean
     */
    private $videosParent = false;
    /**
     * @var ModeleDescriptionForfaitSki
     */
    private $modeleDescriptionForfaitSki;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->sousDomaines = new ArrayCollection();
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
     * @param DomaineTraduction $traduction
     */
    public function removeTraduction(DomaineTraduction $traduction)
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
     * @return Domaine
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get domaineUnifie
     *
     * @return DomaineUnifie
     */
    public function getDomaineUnifie()
    {
        return $this->domaineUnifie;
    }

    /**
     * Set domaineUnifie
     *
     * @param DomaineUnifie $domaineUnifie
     *
     * @return Domaine
     */
    public function setDomaineUnifie(DomaineUnifie $domaineUnifie = null)
    {
        $this->domaineUnifie = $domaineUnifie;

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
                $cloneTraduction->setDomaine($this);
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
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param DomaineTraduction $traduction
     *
     * @return Domaine
     */
    public function addTraduction(DomaineTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDomaine($this);

        return $this;
    }

    /**
     * Add sousDomaine
     *
     * @param Domaine $sousDomaine
     *
     * @return Domaine
     */
    public function addSousDomaine(Domaine $sousDomaine)
    {
        $this->sousDomaines[] = $sousDomaine->setDomaineParent($this);

        return $this;
    }

    /**
     * Remove sousDomaine
     *
     * @param Domaine $sousDomaine
     */
    public function removeSousDomaine(Domaine $sousDomaine)
    {
        $this->sousDomaines->removeElement($sousDomaine);
    }

    /**
     * Get sousDomaines
     *
     * @return Collection
     */
    public function getSousDomaines()
    {
        return $this->sousDomaines;
    }

    /**
     * Get domaineParent
     *
     * @return Domaine
     */
    public function getDomaineParent()
    {
        return $this->domaineParent;
    }

    /**
     * Set domaineParent
     *
     * @param Domaine $domaineParent
     *
     * @return Domaine
     */
    public function setDomaineParent(Domaine $domaineParent = null)
    {
        $this->domaineParent = $domaineParent;

        return $this;
    }

    /**
     * Get domaineCarteIdentite
     *
     * @return DomaineCarteIdentite
     */
    public function getDomaineCarteIdentite()
    {
        return $this->domaineCarteIdentite;
    }

    /**
     * Set domaineCarteIdentite
     *
     * @param DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return Domaine
     */
    public function setDomaineCarteIdentite(DomaineCarteIdentite $domaineCarteIdentite = null)
    {
        $this->domaineCarteIdentite = $domaineCarteIdentite;

        return $this;
    }

    /**
     * Add station
     *
     * @param Station $station
     *
     * @return Domaine
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
     * @param DomaineImage $image
     *
     * @return Domaine
     */
    public function addImage(DomaineImage $image)
    {
        $this->images[] = $image->setDomaine($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param DomaineImage $image
     */
    public function removeImage(DomaineImage $image)
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
     * @param DomainePhoto $photo
     *
     * @return Domaine
     */
    public function addPhoto(DomainePhoto $photo)
    {
        $this->photos[] = $photo->setDomaine($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param DomainePhoto $photo
     */
    public function removePhoto(DomainePhoto $photo)
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
     * Get imagesParent
     *
     * @return boolean
     */
    public function getImagesParent()
    {
        return $this->imagesParent;
    }

    /**
     * Set imagesParent
     *
     * @param boolean $imagesParent
     *
     * @return Domaine
     */
    public function setImagesParent($imagesParent)
    {
        $this->imagesParent = $imagesParent;

        return $this;
    }

    /**
     * Get photosParent
     *
     * @return boolean
     */
    public function getPhotosParent()
    {
        return $this->photosParent;
    }

    /**
     * Set photosParent
     *
     * @param boolean $photosParent
     *
     * @return Domaine
     */
    public function setPhotosParent($photosParent)
    {
        $this->photosParent = $photosParent;

        return $this;
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
     * @return Domaine
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add video
     *
     * @param DomaineVideo $video
     *
     * @return Domaine
     */
    public function addVideo(DomaineVideo $video)
    {
        $this->videos[] = $video->setDomaine($this);

        return $this;
    }

    /**
     * Remove video
     *
     * @param DomaineVideo $video
     */
    public function removeVideo(DomaineVideo $video)
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

    /**
     * Get videosParent
     *
     * @return boolean
     */
    public function getVideosParent()
    {
        return $this->videosParent;
    }

    /**
     * Set videosParent
     *
     * @param boolean $videosParent
     *
     * @return Domaine
     */
    public function setVideosParent($videosParent)
    {
        $this->videosParent = $videosParent;

        return $this;
    }

    /**
     * Get modeleDescriptionForfaitSki
     *
     * @return ModeleDescriptionForfaitSki
     */
    public function getModeleDescriptionForfaitSki()
    {
        return $this->modeleDescriptionForfaitSki;
    }

    /**
     * Set modeleDescriptionForfaitSki
     *
     * @param ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki
     *
     * @return Domaine
     */
    public function setModeleDescriptionForfaitSki(ModeleDescriptionForfaitSki $modeleDescriptionForfaitSki = null)
    {
        $this->modeleDescriptionForfaitSki = $modeleDescriptionForfaitSki;

        return $this;
    }
}
