<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * DomaineVideo
 */
class DomaineVideo
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
     * @var Domaine
     */
    private $domaine;
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
     * @return DomaineVideo
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param DomaineVideoTraduction $traduction
     *
     * @return DomaineVideo
     */
    public function addTraduction(DomaineVideoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setVideo($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param DomaineVideoTraduction $traduction
     */
    public function removeTraduction(DomaineVideoTraduction $traduction)
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
     * Get domaine
     *
     * @return Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set domaine
     *
     * @param Domaine $domaine
     *
     * @return DomaineVideo
     */
    public function setDomaine(Domaine $domaine = null)
    {
        $this->domaine = $domaine;

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
     * @return DomaineVideo
     */
    public function setVideo(Media $video = null)
    {
        $this->video = $video;

        return $this;
    }
}
