<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sousDomaines;
    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    private $domaineParent;
    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    private $domaineCarteIdentite;
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
    private $imagesParent = false;
    /**
     * @var boolean
     */
    private $photosParent = false;

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
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Domaine $sousDomaine
     *
     * @return Domaine
     */
    public function addSousDomaine(\Mondofute\Bundle\DomaineBundle\Entity\Domaine $sousDomaine)
    {
        $this->sousDomaines[] = $sousDomaine->setDomaineParent($this);

        return $this;
    }

    /**
     * Remove sousDomaine
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Domaine $sousDomaine
     */
    public function removeSousDomaine(\Mondofute\Bundle\DomaineBundle\Entity\Domaine $sousDomaine)
    {
        $this->sousDomaines->removeElement($sousDomaine);
    }

    /**
     * Get sousDomaines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSousDomaines()
    {
        return $this->sousDomaines;
    }

    /**
     * Get domaineParent
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    public function getDomaineParent()
    {
        return $this->domaineParent;
    }

    /**
     * Set domaineParent
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaineParent
     *
     * @return Domaine
     */
    public function setDomaineParent(\Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaineParent = null)
    {
        $this->domaineParent = $domaineParent;

        return $this;
    }

    /**
     * Get domaineCarteIdentite
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    public function getDomaineCarteIdentite()
    {
        return $this->domaineCarteIdentite;
    }

    /**
     * Set domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return Domaine
     */
    public function setDomaineCarteIdentite(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite = null)
    {
        $this->domaineCarteIdentite = $domaineCarteIdentite;

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return Domaine
     */
    public function addStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations[] = $station;

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
     * Add image
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineImage $image
     *
     * @return Domaine
     */
    public function addImage(\Mondofute\Bundle\DomaineBundle\Entity\DomaineImage $image)
    {
        $this->images[] = $image->setDomaine($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineImage $image
     */
    public function removeImage(\Mondofute\Bundle\DomaineBundle\Entity\DomaineImage $image)
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
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto $photo
     *
     * @return Domaine
     */
    public function addPhoto(\Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto $photo)
    {
        $this->photos[] = $photo->setDomaine($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto $photo
     */
    public function removePhoto(\Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto $photo)
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
}
