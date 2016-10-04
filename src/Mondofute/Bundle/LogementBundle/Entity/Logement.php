<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Logement
 */
class Logement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $accesPMR;
    /**
     * @var integer unsigned
     */
    private $capacite;
    /**
     * @var smallint unsigned
     */
    private $nbChambre;
    /**
     * @var smallint unsigned
     */
    private $superficieMin;
    /**
     * @var smallint unsigned
     */
    private $superficieMax;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var LogementUnifie
     */
    private $logementUnifie;
    /**
     * @var FournisseurHebergement
     */
    private $fournisseurHebergement;
    /**
     * @var Collection
     */
    private $photos;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $periodes;

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
        return $this;
    }

    /**
     * Get accesPMR
     *
     * @return boolean
     */
    public function getAccesPMR()
    {
        return $this->accesPMR;
    }

    /**
     * Set accesPMR
     *
     * @param boolean $accesPMR
     *
     * @return Logement
     */
    public function setAccesPMR($accesPMR)
    {
        $this->accesPMR = $accesPMR;

        return $this;
    }

    /**
     * Get capacite
     *
     * @return integer
     */
    public function getCapacite()
    {
        return $this->capacite;
    }

    /**
     * Set capacite
     *
     * @param integer $capacite
     *
     * @return Logement
     */
    public function setCapacite($capacite)
    {
        $this->capacite = $capacite;

        return $this;
    }

    /**
     * Get nbChambre
     *
     * @return integer
     */
    public function getNbChambre()
    {
        return $this->nbChambre;
    }

    /**
     * Set nbChambre
     *
     * @param integer $nbChambre
     *
     * @return Logement
     */
    public function setNbChambre($nbChambre)
    {
        $this->nbChambre = $nbChambre;

        return $this;
    }

    /**
     * Get superficieMin
     *
     * @return integer
     */
    public function getSuperficieMin()
    {
        return $this->superficieMin;
    }

    /**
     * Set superficieMin
     *
     * @param integer $superficieMin
     *
     * @return Logement
     */
    public function setSuperficieMin($superficieMin)
    {
        $this->superficieMin = $superficieMin;

        return $this;
    }

    /**
     * Get superficieMax
     *
     * @return integer
     */
    public function getSuperficieMax()
    {
        return $this->superficieMax;
    }

    /**
     * Set superficieMax
     *
     * @param integer $superficieMax
     *
     * @return Logement
     */
    public function setSuperficieMax($superficieMax)
    {
        $this->superficieMax = $superficieMax;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param LogementTraduction $traduction
     */
    public function removeTraduction(LogementTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
        $traduction->setLogement(null);
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
     * @param ArrayCollection $traductions
     * @return Logement $this
     */
    public function setTraductions(ArrayCollection $traductions)
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
     * @param LogementTraduction $traduction
     *
     * @return Logement
     */
    public function addTraduction(LogementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setLogement($this);

        return $this;
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
     * @return Logement
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get logementUnifie
     *
     * @return LogementUnifie
     */
    public function getLogementUnifie()
    {
        return $this->logementUnifie;
    }

    /**
     * Set logementUnifie
     *
     * @param LogementUnifie $logementUnifie
     *
     * @return Logement
     */
    public function setLogementUnifie(LogementUnifie $logementUnifie = null)
    {
        $this->logementUnifie = $logementUnifie;

        return $this;
    }

    /**
     * Get fournisseurHebergement
     *
     * @return FournisseurHebergement
     */
    public function getFournisseurHebergement()
    {
        return $this->fournisseurHebergement;
    }

    /**
     * Set fournisseurHebergement
     *
     * @param FournisseurHebergement $fournisseurHebergement
     *
     * @return Logement
     */
    public function setFournisseurHebergement(
        FournisseurHebergement $fournisseurHebergement = null
    ) {
        $this->fournisseurHebergement = $fournisseurHebergement;

        return $this;
    }

    /**
     * Add photo
     *
     * @param LogementPhoto $photo
     *
     * @return Logement
     */
    public function addPhoto(LogementPhoto $photo)
    {
        $this->photos[] = $photo->setLogement($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param LogementPhoto $photo
     */
    public function removePhoto(LogementPhoto $photo)
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
     * @return Logement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add periode
     *
     * @param \Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode $periode
     *
     * @return Logement
     */
    public function addPeriode(\Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode $periode)
    {
        $this->periodes[] = $periode;

        return $this;
    }

    /**
     * Remove periode
     *
     * @param \Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode $periode
     */
    public function removePeriode(\Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode $periode)
    {
        $this->periodes->removeElement($periode);
    }

    /**
     * Get periodes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriodes()
    {
        return $this->periodes;
    }
}
