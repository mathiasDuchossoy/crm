<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ZoneTouristiqueImage
 */
class ZoneTouristiqueImage
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $actif = false;
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
    private $image;

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
     * @return ZoneTouristiqueImage
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param ZoneTouristiqueImageTraduction $traduction
     *
     * @return ZoneTouristiqueImage
     */
    public function addTraduction(ZoneTouristiqueImageTraduction $traduction)
    {
        $this->traductions[] = $traduction->setImage($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param ZoneTouristiqueImageTraduction $traduction
     */
    public function removeTraduction(ZoneTouristiqueImageTraduction $traduction)
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
     * @return ZoneTouristiqueImage
     */
    public function setZoneTouristique(ZoneTouristique $zoneTouristique = null)
    {
        $this->zoneTouristique = $zoneTouristique;

        return $this;
    }

    /**
     * Get image
     *
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param Media $image
     *
     * @return ZoneTouristiqueImage
     */
    public function setImage(Media $image = null)
    {
        $this->image = $image;

        return $this;
    }
}
