<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Station
 */
class Station
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $codePostal;

    /**
     * @var string
     */
    private $moisOuverture;

    /**
     * @var string
     */
    private $jourOuverture;

    /**
     * @var string
     */
    private $moisFermeture;

    /**
     * @var string
     */
    private $jourFermeture;

    /**
     * @var string
     */
    private $lienMeteo;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\StationUnifie
     */
    private $stationUnifie;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique
     */
    private $zoneTouristique;

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
     * Get codePostal
     *
     * @return int
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set codePostal
     *
     * @param integer $codePostal
     *
     * @return Station
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get moisOuverture
     *
     * @return string
     */
    public function getMoisOuverture()
    {
        return $this->moisOuverture;
    }

    /**
     * Set moisOuverture
     *
     * @param string $moisOuverture
     *
     * @return Station
     */
    public function setMoisOuverture($moisOuverture)
    {
        $this->moisOuverture = $moisOuverture;

        return $this;
    }

    /**
     * Get jourOuverture
     *
     * @return string
     */
    public function getJourOuverture()
    {
        return $this->jourOuverture;
    }

    /**
     * Set jourOuverture
     *
     * @param string $jourOuverture
     *
     * @return Station
     */
    public function setJourOuverture($jourOuverture)
    {
        $this->jourOuverture = $jourOuverture;

        return $this;
    }

    /**
     * Get moisFermeture
     *
     * @return string
     */
    public function getMoisFermeture()
    {
        return $this->moisFermeture;
    }

    /**
     * Set moisFermeture
     *
     * @param string $moisFermeture
     *
     * @return Station
     */
    public function setMoisFermeture($moisFermeture)
    {
        $this->moisFermeture = $moisFermeture;

        return $this;
    }

    /**
     * Get jourFermeture
     *
     * @return string
     */
    public function getJourFermeture()
    {
        return $this->jourFermeture;
    }

    /**
     * Set jourFermeture
     *
     * @param string $jourFermeture
     *
     * @return Station
     */
    public function setJourFermeture($jourFermeture)
    {
        $this->jourFermeture = $jourFermeture;

        return $this;
    }

    /**
     * Get lienMeteo
     *
     * @return string
     */
    public function getLienMeteo()
    {
        return $this->lienMeteo;
    }

    /**
     * Set lienMeteo
     *
     * @param string $lienMeteo
     *
     * @return Station
     */
    public function setLienMeteo($lienMeteo)
    {
        $this->lienMeteo = $lienMeteo;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\StationTraduction $traduction
     *
     * @return Station
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\StationTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStation($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\StationTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\StationTraduction $traduction)
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
     * @return Station
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\StationUnifie
     */
    public function getStationUnifie()
    {
        return $this->stationUnifie;
    }

    /**
     * Set stationUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\StationUnifie $stationUnifie
     *
     * @return Station
     */
    public function setStationUnifie(\Mondofute\Bundle\GeographieBundle\Entity\StationUnifie $stationUnifie = null)
    {
        $this->stationUnifie = $stationUnifie;

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
                $cloneTraduction->setStation($this);
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
        $this->traductions = $traductions;
        return $this;
    }

    /**
     * Get zoneTouristique
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique
     */
    public function getZoneTouristique()
    {
        return $this->zoneTouristique;
    }

    /**
     * Set zoneTouristique
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique
     *
     * @return Station
     */
    public function setZoneTouristique(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique = null)
    {
        $this->zoneTouristique = $zoneTouristique;

        return $this;
    }
}
