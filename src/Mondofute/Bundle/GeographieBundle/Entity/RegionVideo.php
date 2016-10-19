<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * RegionVideo
 */
class RegionVideo
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
     * @var Region
     */
    private $region;
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
     * @return RegionVideo
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param RegionVideoTraduction $traduction
     *
     * @return RegionVideo
     */
    public function addTraduction(RegionVideoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setVideo($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param RegionVideoTraduction $traduction
     */
    public function removeTraduction(RegionVideoTraduction $traduction)
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
     * @return RegionVideo
     */
    public function setRegion(Region $region = null)
    {
        $this->region = $region;

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
     * @return RegionVideo
     */
    public function setVideo(Media $video = null)
    {
        $this->video = $video;

        return $this;
    }
}
