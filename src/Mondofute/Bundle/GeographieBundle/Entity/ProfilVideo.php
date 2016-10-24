<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ProfilVideo
 */
class ProfilVideo
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
     * @var Profil
     */
    private $profil;
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
     * @return ProfilVideo
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param ProfilVideoTraduction $traduction
     *
     * @return ProfilVideo
     */
    public function addTraduction(ProfilVideoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setVideo($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param ProfilVideoTraduction $traduction
     */
    public function removeTraduction(ProfilVideoTraduction $traduction)
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
     * Get profil
     *
     * @return Profil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set profil
     *
     * @param Profil $profil
     *
     * @return ProfilVideo
     */
    public function setProfil(Profil $profil = null)
    {
        $this->profil = $profil;

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
     * @return ProfilVideo
     */
    public function setVideo(Media $video = null)
    {
        $this->video = $video;

        return $this;
    }
}
