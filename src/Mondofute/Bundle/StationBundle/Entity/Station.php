<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\SiteBundle\Entity\Site;

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
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var StationUnifie
     */
    private $stationUnifie;
    /**
     * @var Departement
     */
    private $departement;
    /**
     * @var Domaine
     */
    private $domaine;
    /**
     * @var StationCarteIdentite
     */
    private $stationCarteIdentite;
    /**
     * @var StationCommentVenir
     */
    private $stationCommentVenir;
    /**
     * @var StationDescription
     */
    private $stationDescription;
    /**
     * @var Collection
     */
    private $secteurs;
    /**
     * @var Collection
     */
    private $zoneTouristiques;
    /**
     * @var Collection
     */
    private $profils;
    /**
     * @var Collection
     */
    private $stations;
    /**
     * @var Station
     */
    private $stationMere;
    /**
     * @var Collection
     */
    private $hebergements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $videosParent = false;
    /**
     * @var boolean
     */
    private $photosParent = false;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $stationLabels;
    /**
     * @var OuiNonNC
     */
    private $stationDeSki;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->secteurs = new ArrayCollection();
        $this->zoneTouristiques = new ArrayCollection();
        $this->profils = new ArrayCollection();
        $this->stations = new ArrayCollection();
        $this->hebergements = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->stationLabels = new ArrayCollection();
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
     * @param StationTraduction $traduction
     */
    public function removeTraduction(StationTraduction $traduction)
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
     * @return Station
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationUnifie
     *
     * @return StationUnifie
     */
    public function getStationUnifie()
    {
        return $this->stationUnifie;
    }

    /**
     * Set stationUnifie
     *
     * @param StationUnifie $stationUnifie
     *
     * @return Station
     */
    public function setStationUnifie(StationUnifie $stationUnifie = null)
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
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * @param $traductions
     * @return $this
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
     * @param StationTraduction $traduction
     *
     * @return Station
     */
    public function addTraduction(StationTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStation($this);

        return $this;
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
     * @return Station
     */
    public function setDepartement(Departement $departement = null)
    {
        $this->departement = $departement;

        return $this;
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
     * @return Station
     */
    public function setDomaine(Domaine $domaine = null)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get stationCarteIdentite
     *
     * @return StationCarteIdentite
     */
    public function getStationCarteIdentite()
    {
        return $this->stationCarteIdentite;
    }

    /**
     * Set stationCarteIdentite
     *
     * @param StationCarteIdentite $stationCarteIdentite
     *
     * @return Station
     */
    public function setStationCarteIdentite(StationCarteIdentite $stationCarteIdentite = null)
    {
//        if($stationCarteIdentite == null){
//            $this->getStationCarteIdentite()->removeStation($this);
//        }
        $this->stationCarteIdentite = $stationCarteIdentite;

        return $this;
    }

    /**
     * Get stationCommentVenir
     *
     * @return StationCommentVenir
     */
    public function getStationCommentVenir()
    {
        return $this->stationCommentVenir;
    }

    /**
     * Set stationCommentVenir
     *
     * @param StationCommentVenir $stationCommentVenir
     *
     * @return Station
     */
    public function setStationCommentVenir(StationCommentVenir $stationCommentVenir = null)
    {
        $this->stationCommentVenir = $stationCommentVenir;

        return $this;
    }

    /**
     * Get stationDescription
     *
     * @return StationDescription
     */
    public function getStationDescription()
    {
        return $this->stationDescription;
    }

    /**
     * Set stationDescription
     *
     * @param StationDescription $stationDescription
     *
     * @return Station
     */
    public function setStationDescription(StationDescription $stationDescription = null)
    {
        $this->stationDescription = $stationDescription;

        return $this;
    }

    /**
     * Remove secteur
     *
     * @param Secteur $secteur
     */
    public function removeSecteur(Secteur $secteur)
    {
        $this->secteurs->removeElement($secteur);
    }

    /**
     * Get secteurs
     *
     * @return Collection
     */
    public function getSecteurs()
    {
        return $this->secteurs;
    }

    /**
     * @param $secteurs
     * @return $this
     */
    public function setSecteurs($secteurs)
    {
        if (!empty($this->getSecteurs())) {
            $this->getSecteurs()->clear();
        }
        if (!empty($secteurs)) {
            foreach ($secteurs as $secteur) {
                $this->addSecteur($secteur);
            }
        }
        return $this;
    }

    /**
     * Add secteur
     *
     * @param Secteur $secteur
     *
     * @return Station
     */
    public function addSecteur(Secteur $secteur)
    {
        $this->secteurs[] = $secteur->addStation($this);

        return $this;
    }

    /**
     * Remove zoneTouristique
     *
     * @param ZoneTouristique $zoneTouristique
     */
    public function removeZoneTouristique(ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques->removeElement($zoneTouristique);
    }

    /**
     * Get zoneTouristiques
     *
     * @return Collection
     */
    public function getZoneTouristiques()
    {
        return $this->zoneTouristiques;
    }

    /**
     * @param $zoneTouristiques
     * @return $this
     */
    public function setZoneTouristiques($zoneTouristiques)
    {
        if (!empty($this->getZoneTouristiques())) {
            $this->getZoneTouristiques()->clear();
        }
        if (!empty($zoneTouristiques)) {
            foreach ($zoneTouristiques as $zoneTouristique) {
                $this->addZoneTouristique($zoneTouristique);
            }
        }
        return $this;
    }

    /**
     * Add zoneTouristique
     *
     * @param ZoneTouristique $zoneTouristique
     *
     * @return Station
     */
    public function addZoneTouristique(ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques[] = $zoneTouristique->addStation($this);

        return $this;
    }

    /**
     * Remove profil
     *
     * @param Profil $profil
     */
    public function removeProfil(Profil $profil)
    {
        $this->profils->removeElement($profil);
    }

    /**
     * Get profils
     *
     * @return Collection
     */
    public function getProfils()
    {
        return $this->profils;
    }

    /**
     * @param $profils
     * @return $this
     */
    public function setProfils($profils)
    {
        if (!empty($this->getProfils())) {
            $this->getProfils()->clear();
        }
        if (!empty($profils)) {
            foreach ($profils as $profil) {
                $this->addProfil($profil);
            }
        }
        return $this;
    }

    /**
     * Add profil
     *
     * @param Profil $profil
     *
     * @return Station
     */
    public function addProfil(Profil $profil)
    {
        $this->profils[] = $profil->addStation($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param Station $station
     *
     * @return Station
     */
    public function addStation(Station $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Remove station
     *
     * @param Station $station
     */
    public function removeStation(Station $station)
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
     * Get stationMere
     *
     * @return Station
     */
    public function getStationMere()
    {
        return $this->stationMere;
    }

    /**
     * Set stationMere
     *
     * @param Station $stationMere
     *
     * @return Station
     */
    public function setStationMere(Station $stationMere = null)
    {
        $this->stationMere = $stationMere;

        return $this;
    }

    /**
     * Add hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return Station
     */
    public function addHebergement(Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement->setStation($this);

        return $this;
    }

    /**
     * Remove hebergement
     *
     * @param Hebergement $hebergement
     */
    public function removeHebergement(Hebergement $hebergement)
    {
        $this->hebergements->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return Collection
     */
    public function getHebergements()
    {
        return $this->hebergements;
    }

    /**
     * Add visuel
     *
     * @param StationVisuel $visuel
     *
     * @return Station
     */
    public function addVisuel(StationVisuel $visuel)
    {
        $this->visuels[] = $visuel->setStation($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param StationVisuel $visuel
     */
    public function removeVisuel(StationVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get videosParent
     *
     * @return boolean
     */
    public function getVideosParent()
    {
        return $this->videosParent;
    }

    /**
     * Set videosParent
     *
     * @param boolean $videosParent
     *
     * @return Station
     */
    public function setVideosParent($videosParent)
    {
        $this->videosParent = $videosParent;

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
     * @return Station
     */
    public function setPhotosParent($photosParent)
    {
        $this->photosParent = $photosParent;

        return $this;
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
     * @return Station
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add stationLabel
     *
     * @param StationLabel $stationLabel
     *
     * @return Station
     */
    public function addStationLabel(StationLabel $stationLabel)
    {
        $this->stationLabels[] = $stationLabel;

        return $this;
    }

    /**
     * Remove stationLabel
     *
     * @param StationLabel $stationLabel
     */
    public function removeStationLabel(StationLabel $stationLabel)
    {
        $this->stationLabels->removeElement($stationLabel);
    }

    /**
     * Get stationLabels
     *
     * @return Collection
     */
    public function getStationLabels()
    {
        return $this->stationLabels;
    }

    /**
     * Get stationDeSki
     *
     * @return OuiNonNC
     */
    public function getStationDeSki()
    {
        return $this->stationDeSki;
    }

    /**
     * Set stationDeSki
     *
     * @param OuiNonNC $stationDeSki
     *
     * @return Station
     */
    public function setStationDeSki(OuiNonNC $stationDeSki = null)
    {
        $this->stationDeSki = $stationDeSki;

        return $this;
    }
}
