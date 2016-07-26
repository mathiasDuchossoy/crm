<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ZoneTouristique
 */
class ZoneTouristique
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie
     */
    private $zoneTouristiqueUnifie;
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction)
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
     * @return ZoneTouristique
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get zoneTouristiqueUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie
     */
    public function getZoneTouristiqueUnifie()
    {
        return $this->zoneTouristiqueUnifie;
    }

    /**
     * Set zoneTouristiqueUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie $zoneTouristiqueUnifie
     *
     * @return ZoneTouristique
     */
    public function setZoneTouristiqueUnifie(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueUnifie $zoneTouristiqueUnifie = null)
    {
        $this->zoneTouristiqueUnifie = $zoneTouristiqueUnifie;

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
                $cloneTraduction->setZoneTouristique($this);
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction
     *
     * @return ZoneTouristique
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueTraduction $traduction)
    {
        $this->traductions[] = $traduction->setZoneTouristique($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return ZoneTouristique
     */
    public function addStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations[] = $station->setZoneTouristique($this);

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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage $image
     *
     * @return ZoneTouristique
     */
    public function addImage(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage $image)
    {
        $this->images[] = $image->setZoneTouristique($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage $image
     */
    public function removeImage(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage $image)
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiquePhoto $photo
     *
     * @return ZoneTouristique
     */
    public function addPhoto(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiquePhoto $photo)
    {
        $this->photos[] = $photo->setZoneTouristique($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiquePhoto $photo
     */
    public function removePhoto(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiquePhoto $photo)
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
}
