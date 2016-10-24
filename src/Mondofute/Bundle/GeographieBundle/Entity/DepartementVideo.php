<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * DepartementVideo
 */
class DepartementVideo
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
     * @var Departement
     */
    private $departement;
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
     * @return DepartementVideo
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param DepartementVideoTraduction $traduction
     *
     * @return DepartementVideo
     */
    public function addTraduction(DepartementVideoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setVideo($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param DepartementVideoTraduction $traduction
     */
    public function removeTraduction(DepartementVideoTraduction $traduction)
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
     * Get departement
     *
     * @return Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departement
     *
     * @param Departement $departement
     *
     * @return DepartementVideo
     */
    public function setDepartement(Departement $departement = null)
    {
        $this->departement = $departement;

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
     * @return DepartementVideo
     */
    public function setVideo(Media $video = null)
    {
        $this->video = $video;

        return $this;
    }
}
