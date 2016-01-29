<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * DomaineCarteIdentite
 */
class DomaineCarteIdentite
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $altitudeMini;

    /**
     * @var int
     */
    private $altitudeMaxi;

    /**
     * @var int
     */
    private $kmPistesSkiAlpin;

    /**
     * @var int
     */
    private $kmPistesSkiNordique;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteUnifie
     */
    private $domaineCarteIdentiteUnifie;

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
     * Get altitudeMini
     *
     * @return int
     */
    public function getAltitudeMini()
    {
        return $this->altitudeMini;
    }

    /**
     * Set altitudeMini
     *
     * @param integer $altitudeMini
     *
     * @return DomaineCarteIdentite
     */
    public function setAltitudeMini($altitudeMini)
    {
        $this->altitudeMini = $altitudeMini;

        return $this;
    }

    /**
     * Get altitudeMaxi
     *
     * @return int
     */
    public function getAltitudeMaxi()
    {
        return $this->altitudeMaxi;
    }

    /**
     * Set altitudeMaxi
     *
     * @param integer $altitudeMaxi
     *
     * @return DomaineCarteIdentite
     */
    public function setAltitudeMaxi($altitudeMaxi)
    {
        $this->altitudeMaxi = $altitudeMaxi;

        return $this;
    }

    /**
     * Get kmPistesSkiAlpin
     *
     * @return int
     */
    public function getKmPistesSkiAlpin()
    {
        return $this->kmPistesSkiAlpin;
    }

    /**
     * Set kmPistesSkiAlpin
     *
     * @param integer $kmPistesSkiAlpin
     *
     * @return DomaineCarteIdentite
     */
    public function setKmPistesSkiAlpin($kmPistesSkiAlpin)
    {
        $this->kmPistesSkiAlpin = $kmPistesSkiAlpin;

        return $this;
    }

    /**
     * Get kmPistesSkiNordique
     *
     * @return int
     */
    public function getKmPistesSkiNordique()
    {
        return $this->kmPistesSkiNordique;
    }

    /**
     * Set kmPistesSkiNordique
     *
     * @param integer $kmPistesSkiNordique
     *
     * @return DomaineCarteIdentite
     */
    public function setKmPistesSkiNordique($kmPistesSkiNordique)
    {
        $this->kmPistesSkiNordique = $kmPistesSkiNordique;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteTraduction $traduction
     *
     * @return DomaineCarteIdentite
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteTraduction $traduction)
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
     * @return DomaineCarteIdentite
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get domaineCarteIdentiteUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteUnifie
     */
    public function getDomaineCarteIdentiteUnifie()
    {
        return $this->domaineCarteIdentiteUnifie;
    }

    /**
     * Set domaineCarteIdentiteUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie
     *
     * @return DomaineCarteIdentite
     */
    public function setDomaineCarteIdentiteUnifie(\Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie = null)
    {
        $this->domaineCarteIdentiteUnifie = $domaineCarteIdentiteUnifie;

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
                $cloneTraduction->setDomaineCarteIdentite($this);
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
     * @return DomaineCarteIdentite $this
     */
    public function setTraductions($traductions)
    {
        $this->traductions = $traductions;
        return $this;
    }
}