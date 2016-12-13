<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\UniteBundle\Entity\Age;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;

/**
 * LigneDescriptionForfaitSki
 */
class LigneDescriptionForfaitSki
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $quantite;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var LigneDescriptionForfaitSkiCategorie
     */
    private $categorie;
    /**
     * @var Tarif
     */
    private $prix;
    /**
     * @var Age
     */
    private $ageMin;
    /**
     * @var Age
     */
    private $ageMax;
    /**
     * @var OuiNonNC
     */
    private $present;
    /**
     * @var integer
     */
    private $classement;

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
     * Get quantite
     *
     * @return float
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set quantite
     *
     * @param float $quantite
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param LigneDescriptionForfaitSkiTraduction $traduction
     *
     * @return LigneDescriptionForfaitSki
     */
    public function addTraduction(
        LigneDescriptionForfaitSkiTraduction $traduction
    ) {
        $this->traductions[] = $traduction->setLigneDescriptionForfaitSki($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param LigneDescriptionForfaitSkiTraduction $traduction
     */
    public function removeTraduction(
        LigneDescriptionForfaitSkiTraduction $traduction
    ) {
        $this->traductions->removeElement($traduction);
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
     * Get categorie
     *
     * @return LigneDescriptionForfaitSkiCategorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set categorie
     *
     * @param LigneDescriptionForfaitSkiCategorie $categorie
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setCategorie(
        LigneDescriptionForfaitSkiCategorie $categorie = null
    ) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Tarif
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set prix
     *
     * @param Tarif $prix
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setPrix(Tarif $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get ageMin
     *
     * @return Age
     */
    public function getAgeMin()
    {
        return $this->ageMin;
    }

    /**
     * Set ageMin
     *
     * @param Age $ageMin
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setAgeMin(Age $ageMin = null)
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    /**
     * Get ageMax
     *
     * @return Age
     */
    public function getAgeMax()
    {
        return $this->ageMax;
    }

    /**
     * Set ageMax
     *
     * @param Age $ageMax
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setAgeMax(Age $ageMax = null)
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    /**
     * Get present
     *
     * @return OuiNonNC
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * Set present
     *
     * @param OuiNonNC $present
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setPresent(OuiNonNC $present = null)
    {
        $this->present = $present;

        return $this;
    }

    /**
     * Get classement
     *
     * @return integer
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set classement
     *
     * @param integer $classement
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }
}
