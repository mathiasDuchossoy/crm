<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Nucleus\MoyenComBundle\Entity\Adresse;

/**
 * StationCarteIdentite
 */
class StationCarteIdentite
{
    /**
     * @var integer
     */
    private $id;

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
     * @var Distance
     */
    private $altitudeVillage;

    /**
     * @var Collection
     */
    private $stations;

    /**
     * @var Site
     */
    private $site;

    /**
     * @var StationCarteIdentiteUnifie
     */
    private $stationCarteIdentiteUnifie;
    /**
     * @var Adresse
     */
    private $adresse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return StationCarteIdentite
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
     * @return StationCarteIdentite
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
     * @return StationCarteIdentite
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
     * @return StationCarteIdentite
     */
    public function setJourFermeture($jourFermeture)
    {
        $this->jourFermeture = $jourFermeture;

        return $this;
    }

    /**
     * Get altitudeVillage
     *
     * @return Distance
     */
    public function getAltitudeVillage()
    {
        return $this->altitudeVillage;
    }

    /**
     * Set altitudeVillage
     *
     * @param Distance $altitudeVillage
     *
     * @return StationCarteIdentite
     */
    public function setAltitudeVillage(Distance $altitudeVillage = null)
    {
        $this->altitudeVillage = $altitudeVillage;

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return StationCarteIdentite
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
     * @return Collection
     */
    public function getStations()
    {
        return $this->stations;
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
     * @return StationCarteIdentite
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationCarteIdentiteUnifie
     *
     * @return StationCarteIdentiteUnifie
     */
    public function getStationCarteIdentiteUnifie()
    {
        return $this->stationCarteIdentiteUnifie;
    }

    /**
     * Set stationCarteIdentiteUnifie
     *
     * @param StationCarteIdentiteUnifie $stationCarteIdentiteUnifie
     *
     * @return StationCarteIdentite
     */
    public function setStationCarteIdentiteUnifie(StationCarteIdentiteUnifie $stationCarteIdentiteUnifie = null)
    {
        $this->stationCarteIdentiteUnifie = $stationCarteIdentiteUnifie;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresse
     *
     * @param Adresse $adresse
     *
     * @return StationCarteIdentite
     */
    public function setAdresse(Adresse $adresse = null)
    {
        $this->adresse = $adresse;

        return $this;
    }
}
