<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie
     */
    private $profilUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $photos;
    /**
     * @var boolean
     */
    private $actif = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get site
     *
     * @return \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param \Mondofute\Bundle\SiteBundle\Entity\Site $site
     *
     * @return Profil
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get profilUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie
     */
    public function getProfilUnifie()
    {
        return $this->profilUnifie;
    }

    /**
     * Set profilUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie $profilUnifie
     *
     * @return Profil
     */
    public function setProfilUnifie(\Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie $profilUnifie = null)
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
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction
     *
     * @return Profil
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction)
    {
        $this->traductions[] = $traduction->setProfil($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return Profil
     */
    public function addStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Add image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilImage $image
     *
     * @return Profil
     */
    public function addImage(\Mondofute\Bundle\GeographieBundle\Entity\ProfilImage $image)
    {
        $this->images[] = $image->setProfil($this);

        return $this;
    }

    /**
     * Remove station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     */
    public function removeStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations->removeElement($station);
    }

    /**
     * Get stations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStations()
    {
        return $this->stations;
    }

    /**
     * Remove image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilImage $image
     */
    public function removeImage(\Mondofute\Bundle\GeographieBundle\Entity\ProfilImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto $photo
     *
     * @return Profil
     */
    public function addPhoto(\Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto $photo)
    {
        $this->photos[] = $photo->setProfil($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto $photo
     */
    public function removePhoto(\Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
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
}
