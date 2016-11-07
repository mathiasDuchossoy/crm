<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Distance;


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
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var DomaineCarteIdentiteUnifie
     */
    private $domaineCarteIdentiteUnifie;
    /**
     * @var Collection
     */
    private $domaines;
    /**
     * @var Handiski
     */
    private $handiski;
    /**
     * @var Snowpark
     */
    private $snowpark;
    /**
     * @var RemonteeMecanique
     */
    private $remonteeMecanique;
    /**
     * @var NiveauSkieur
     */
    private $niveauSkieur;
    /**
     * @var Collection
     */
    private $pistes;
    /**
     * @var Distance
     */
    private $altitudeMini;
    /**
     * @var Distance
     */
    private $altitudeMaxi;
    /**
     * @var KmPistesAlpin
     */
    private $kmPistesSkiAlpin;
    /**
     * @var KmPistesNordique
     */
    private $kmPistesSkiNordique;
    /**
     * @var Collection
     */
    private $images;
    /**
     * @var Collection
     */
    private $photos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->domaines = new ArrayCollection();
        $this->pistes = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->photos = new ArrayCollection();
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
     * Remove traduction
     *
     * @param DomaineCarteIdentiteTraduction $traduction
     */
    public function removeTraduction(DomaineCarteIdentiteTraduction $traduction)
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
     * @return DomaineCarteIdentite
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get domaineCarteIdentiteUnifie
     *
     * @return DomaineCarteIdentiteUnifie
     */
    public function getDomaineCarteIdentiteUnifie()
    {
        return $this->domaineCarteIdentiteUnifie;
    }

    /**
     * Set domaineCarteIdentiteUnifie
     *
     * @param DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie
     *
     * @return DomaineCarteIdentite
     */
    public function setDomaineCarteIdentiteUnifie(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie = null)
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
        $this->snowpark = clone $this->getSnowpark();
        $this->handiski = clone $this->getHandiski();
        $this->altitudeMini = clone $this->getAltitudeMini();
        $this->altitudeMaxi = clone $this->getAltitudeMaxi();
        $this->remonteeMecanique = clone $this->getRemonteeMecanique();
        $pistes = $this->getPistes();
        $this->pistes = new ArrayCollection();
        if (count($pistes) > 0) {
            foreach ($pistes as $piste) {
                $clonePiste = clone $piste;
                $this->pistes->add($clonePiste);
                $clonePiste->setDomaineCarteIdentite($this);
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
     * @return DomaineCarteIdentite $this
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
     * Get snowpark
     *
     * @return Snowpark
     */
    public function getSnowpark()
    {
        return $this->snowpark;
    }

    /**
     * Set snowpark
     *
     * @param Snowpark $snowpark
     *
     * @return DomaineCarteIdentite
     */
    public function setSnowpark(Snowpark $snowpark = null)
    {
        $this->snowpark = $snowpark;

        return $this;
    }

    /**
     * Get handiski
     *
     * @return Handiski
     */
    public function getHandiski()
    {
        return $this->handiski;
    }

    /**
     * Set handiski
     *
     * @param Handiski $handiski
     *
     * @return DomaineCarteIdentite
     */
    public function setHandiski(Handiski $handiski = null)
    {
        $this->handiski = $handiski;

        return $this;
    }

    /**
     * Get altitudeMini
     *
     * @return Distance
     */
    public function getAltitudeMini()
    {
        return $this->altitudeMini;
    }

    /**
     * Set altitudeMini
     *
     * @param Distance $altitudeMini
     *
     * @return DomaineCarteIdentite
     */
    public function setAltitudeMini(Distance $altitudeMini = null)
    {
        $this->altitudeMini = $altitudeMini;

        return $this;
    }

    /**
     * Get altitudeMaxi
     *
     * @return Distance
     */
    public function getAltitudeMaxi()
    {
        return $this->altitudeMaxi;
    }

    /**
     * Set altitudeMaxi
     *
     * @param Distance $altitudeMaxi
     *
     * @return DomaineCarteIdentite
     */
    public function setAltitudeMaxi(Distance $altitudeMaxi = null)
    {
        $this->altitudeMaxi = $altitudeMaxi;

        return $this;
    }

    /**
     * Get remonteeMecanique
     *
     * @return RemonteeMecanique
     */
    public function getRemonteeMecanique()
    {
        return $this->remonteeMecanique;
    }

    /**
     * Set remonteeMecanique
     *
     * @param RemonteeMecanique $remonteeMecanique
     *
     * @return DomaineCarteIdentite
     */
    public function setRemonteeMecanique(RemonteeMecanique $remonteeMecanique = null)
    {
        $this->remonteeMecanique = $remonteeMecanique;

        return $this;
    }

    /**
     * Get pistes
     *
     * @return Collection
     */
    public function getPistes()
    {
        return $this->pistes;
    }

    /**
     * Add traduction
     *
     * @param DomaineCarteIdentiteTraduction $traduction
     *
     * @return DomaineCarteIdentite
     */
    public function addTraduction(DomaineCarteIdentiteTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Add domaine
     *
     * @param Domaine $domaine
     *
     * @return DomaineCarteIdentite
     */
    public function addDomaine(Domaine $domaine)
    {
        $this->domaines[] = $domaine->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove domaine
     *
     * @param Domaine $domaine
     */
    public function removeDomaine(Domaine $domaine)
    {
        $this->domaines->removeElement($domaine);
    }

    /**
     * Get domaines
     *
     * @return Collection
     */
    public function getDomaines()
    {
        return $this->domaines;
    }

    /**
     * Get niveauSkieur
     *
     * @return NiveauSkieur
     */
    public function getNiveauSkieur()
    {
        return $this->niveauSkieur;
    }

    /**
     * Set niveauSkieur
     *
     * @param NiveauSkieur $niveauSkieur
     *
     * @return DomaineCarteIdentite
     */
    public function setNiveauSkieur(NiveauSkieur $niveauSkieur = null)
    {
        $this->niveauSkieur = $niveauSkieur;

        return $this;
    }

    /**
     * Add piste
     *
     * @param Piste $piste
     *
     * @return DomaineCarteIdentite
     */
    public function addPiste(Piste $piste)
    {
        $this->pistes[] = $piste->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove piste
     *
     * @param Piste $piste
     */
    public function removePiste(Piste $piste)
    {
        $this->pistes->removeElement($piste);
    }

    /**
     * Get kmPistesSkiAlpin
     *
     * @return KmPistesAlpin
     */
    public function getKmPistesSkiAlpin()
    {
        return $this->kmPistesSkiAlpin;
    }

    /**
     * Set kmPistesSkiAlpin
     *
     * @param KmPistesAlpin $kmPistesSkiAlpin
     *
     * @return DomaineCarteIdentite
     */
    public function setKmPistesSkiAlpin(KmPistesAlpin $kmPistesSkiAlpin = null)
    {
        $this->kmPistesSkiAlpin = $kmPistesSkiAlpin;

        return $this;
    }

    /**
     * Get kmPistesSkiNordique
     *
     * @return KmPistesNordique
     */
    public function getKmPistesSkiNordique()
    {
        return $this->kmPistesSkiNordique;
    }

    /**
     * Set kmPistesSkiNordique
     *
     * @param KmPistesNordique $kmPistesSkiNordique
     *
     * @return DomaineCarteIdentite
     */
    public function setKmPistesSkiNordique(KmPistesNordique $kmPistesSkiNordique = null)
    {
        $this->kmPistesSkiNordique = $kmPistesSkiNordique;

        return $this;
    }

    /**
     * Add image
     *
     * @param DomaineCarteIdentiteImage $image
     *
     * @return DomaineCarteIdentite
     */
    public function addImage(DomaineCarteIdentiteImage $image)
    {
        $this->images[] = $image->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param DomaineCarteIdentiteImage $image
     */
    public function removeImage(DomaineCarteIdentiteImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add photo
     *
     * @param DomaineCarteIdentitePhoto $photo
     *
     * @return DomaineCarteIdentite
     */
    public function addPhoto(DomaineCarteIdentitePhoto $photo)
    {
        $this->photos[] = $photo->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param DomaineCarteIdentitePhoto $photo
     */
    public function removePhoto(DomaineCarteIdentitePhoto $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
