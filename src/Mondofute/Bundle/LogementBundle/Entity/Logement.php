<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
    private $actif;
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\LogementBundle\Entity\LogementUnifie
     */
    private $logementUnifie;
    /**
     * @var \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement
     */
    private $fournisseurHebergement;

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
     * @param \Mondofute\Bundle\LogementBundle\Entity\LogementTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\LogementBundle\Entity\LogementTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
        $traduction->setLogement(null);
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
     * @param \Mondofute\Bundle\LogementBundle\Entity\LogementTraduction $traduction
     *
     * @return Logement
     */
    public function addTraduction(\Mondofute\Bundle\LogementBundle\Entity\LogementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setLogement($this);

        return $this;
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
     * @return Logement
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get logementUnifie
     *
     * @return \Mondofute\Bundle\LogementBundle\Entity\LogementUnifie
     */
    public function getLogementUnifie()
    {
        return $this->logementUnifie;
    }

    /**
     * Set logementUnifie
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\LogementUnifie $logementUnifie
     *
     * @return Logement
     */
    public function setLogementUnifie(\Mondofute\Bundle\LogementBundle\Entity\LogementUnifie $logementUnifie = null)
    {
        $this->logementUnifie = $logementUnifie;

        return $this;
    }


    /**
     * Get fournisseurHebergement
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement
     */
    public function getFournisseurHebergement()
    {
        return $this->fournisseurHebergement;
    }

    /**
     * Set fournisseurHebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement $fournisseurHebergement
     *
     * @return Logement
     */
    public function setFournisseurHebergement(
        \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement $fournisseurHebergement = null
    ) {
        $this->fournisseurHebergement = $fournisseurHebergement;

        return $this;
    }
}
