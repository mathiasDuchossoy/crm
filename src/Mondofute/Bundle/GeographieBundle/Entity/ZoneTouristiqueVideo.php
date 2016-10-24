<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ZoneTouristiqueVideo
 */
class ZoneTouristiqueVideo
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var ZoneTouristique
     */
    private $zoneTouristique;
    /**
     * @var Media
     */
    private $video;

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
     * @return ZoneTouristiqueVideo
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param ZoneTouristiqueVideoTraduction $traduction
     *
     * @return ZoneTouristiqueVideo
     */
    public function addTraduction(ZoneTouristiqueVideoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setVideo($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param ZoneTouristiqueVideoTraduction $traduction
     */
    public function removeTraduction(ZoneTouristiqueVideoTraduction $traduction)
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
     * Get zoneTouristique
     *
     * @return ZoneTouristique
     */
    public function getZoneTouristique()
    {
        return $this->zoneTouristique;
    }

    /**
     * Set zoneTouristique
     *
     * @param ZoneTouristique $zoneTouristique
     *
     * @return ZoneTouristiqueVideo
     */
    public function setZoneTouristique(ZoneTouristique $zoneTouristique = null)
    {
        $this->zoneTouristique = $zoneTouristique;

        return $this;
    }

    /**
     * Get video
     *
     * @return Media
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param Media $video
     *
     * @return ZoneTouristiqueVideo
     */
    public function setVideo(Media $video = null)
    {
        $this->video = $video;

        return $this;
    }
}
