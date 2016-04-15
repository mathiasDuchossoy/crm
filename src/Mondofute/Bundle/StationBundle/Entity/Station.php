<?php

namespace Mondofute\Bundle\StationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    private $departement;
    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    private $domaine;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite
     */
    private $stationCarteIdentite;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir
     */
    private $stationCommentVenir;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationDescription
     */
    private $stationDescription;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $secteurs;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $zoneTouristiques;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $profils;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\Station
     */
    private $stationMere;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $hebergements;

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
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     *
     * @return Station
     */
    public function setDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set domaine
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaine
     *
     * @return Station
     */
    public function setDomaine(\Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaine = null)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get stationCarteIdentite
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite
     */
    public function getStationCarteIdentite()
    {
        return $this->stationCarteIdentite;
    }

    /**
     * Set stationCarteIdentite
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite $stationCarteIdentite
     *
     * @return Station
     */
    public function setStationCarteIdentite(\Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite $stationCarteIdentite = null)
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
     * @return \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir
     */
    public function getStationCommentVenir()
    {
        return $this->stationCommentVenir;
    }

    /**
     * Set stationCommentVenir
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir
     *
     * @return Station
     */
    public function setStationCommentVenir(\Mondofute\Bundle\StationBundle\Entity\StationCommentVenir $stationCommentVenir = null)
    {
        $this->stationCommentVenir = $stationCommentVenir;

        return $this;
    }

    /**
     * Get stationDescription
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationDescription
     */
    public function getStationDescription()
    {
        return $this->stationDescription;
    }

    /**
     * Set stationDescription
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription
     *
     * @return Station
     */
    public function setStationDescription(\Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription = null)
    {
        $this->stationDescription = $stationDescription;

        return $this;
    }

    /**
     * Remove secteur
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur
     */
    public function removeSecteur(\Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur)
    {
        $this->secteurs->removeElement($secteur);
    }

    /**
     * Get secteurs
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur
     *
     * @return Station
     */
    public function addSecteur(\Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur)
    {
        $this->secteurs[] = $secteur->addStation($this);

        return $this;
    }

    /**
     * Remove zoneTouristique
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique
     */
    public function removeZoneTouristique(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques->removeElement($zoneTouristique);
    }

    /**
     * Get zoneTouristiques
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique
     *
     * @return Station
     */
    public function addZoneTouristique(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques[] = $zoneTouristique->addStation($this);

        return $this;
    }

    /**
     * Remove profil
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Profil $profil
     */
    public function removeProfil(\Mondofute\Bundle\GeographieBundle\Entity\Profil $profil)
    {
        $this->profils->removeElement($profil);
    }

    /**
     * Get profils
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Profil $profil
     *
     * @return Station
     */
    public function addProfil(\Mondofute\Bundle\GeographieBundle\Entity\Profil $profil)
    {
        $this->profils[] = $profil->addStation($this);

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return Station
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
     * Get stationMere
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\Station
     */
    public function getStationMere()
    {
        return $this->stationMere;
    }

    /**
     * Set stationMere
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $stationMere
     *
     * @return Station
     */
    public function setStationMere(\Mondofute\Bundle\StationBundle\Entity\Station $stationMere = null)
    {
        $this->stationMere = $stationMere;

        return $this;
    }

    /**
     * Add hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     *
     * @return Station
     */
    public function addHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement;

        return $this;
    }

    /**
     * Remove hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     */
    public function removeHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement)
    {
        $this->hebergements->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHebergements()
    {
        return $this->hebergements;
    }
}
