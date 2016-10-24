<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

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
     * @var Site
     */
    private $site;
    /**
     * @var RegionUnifie
     */
    private $regionUnifie;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Collection
     */
    private $departements;
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
        $this->departements = new ArrayCollection();
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
     * @return Region
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get regionUnifie
     *
     * @return RegionUnifie
     */
    public function getRegionUnifie()
    {
        return $this->regionUnifie;
    }

    /**
     * Set regionUnifie
     *
     * @param RegionUnifie $regionUnifie
     *
     * @return Region
     */
    public function setRegionUnifie(RegionUnifie $regionUnifie = null)
    {
        $this->regionUnifie = $regionUnifie;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param RegionTraduction $traduction
     */
    public function removeTraduction(RegionTraduction $traduction)
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
     * @param RegionTraduction $traduction
     *
     * @return Region
     */
    public function addTraduction(RegionTraduction $traduction)
    {
        $this->traductions[] = $traduction->setRegion($this);

        return $this;
    }

    /**
     * Add departement
     *
     * @param Departement $departement
     *
     * @return Region
     */
    public function addDepartement(Departement $departement)
    {
        $this->departements[] = $departement;

        return $this;
    }

    /**
     * Remove departement
     *
     * @param Departement $departement
     */
    public function removeDepartement(Departement $departement)
    {
        $this->departements->removeElement($departement);
    }

    /**
     * Get departements
     *
     * @return Collection
     */
    public function getDepartements()
    {
        return $this->departements;
    }

    /**
     * Add image
     *
     * @param RegionImage $image
     *
     * @return Region
     */
    public function addImage(RegionImage $image)
    {
        $this->images[] = $image->setRegion($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param RegionImage $image
     */
    public function removeImage(RegionImage $image)
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
     * @param RegionPhoto $photo
     *
     * @return Region
     */
    public function addPhoto(RegionPhoto $photo)
    {
        $this->photos[] = $photo->setRegion($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param RegionPhoto $photo
     */
    public function removePhoto(RegionPhoto $photo)
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
     * @return Region
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add video
     *
     * @param RegionVideo $video
     *
     * @return Region
     */
    public function addVideo(RegionVideo $video)
    {
        $this->videos[] = $video->setRegion($this);

        return $this;
    }

    /**
     * Remove video
     *
     * @param RegionVideo $video
     */
    public function removeVideo(RegionVideo $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *l
     * @return Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }
}
