<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie
     */
    private $categorie;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Tarif
     */
    private $prix;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Age
     */
    private $ageMin;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Age
     */
    private $ageMax;
    /**
     * @var \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC
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
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction $traduction
     *
     * @return LigneDescriptionForfaitSki
     */
    public function addTraduction(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction $traduction
    ) {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction $traduction
     */
    public function removeTraduction(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiTraduction $traduction
    ) {
        $this->traductions->removeElement($traduction);
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
     * Get categorie
     *
     * @return \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set categorie
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $categorie
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setCategorie(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $categorie = null
    ) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get prix
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Tarif
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set prix
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Tarif $prix
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setPrix(\Mondofute\Bundle\UniteBundle\Entity\Tarif $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get ageMin
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Age
     */
    public function getAgeMin()
    {
        return $this->ageMin;
    }

    /**
     * Set ageMin
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Age $ageMin
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setAgeMin(\Mondofute\Bundle\UniteBundle\Entity\Age $ageMin = null)
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    /**
     * Get ageMax
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Age
     */
    public function getAgeMax()
    {
        return $this->ageMax;
    }

    /**
     * Set ageMax
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Age $ageMax
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setAgeMax(\Mondofute\Bundle\UniteBundle\Entity\Age $ageMax = null)
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    /**
     * Get present
     *
     * @return \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * Set present
     *
     * @param \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC $present
     *
     * @return LigneDescriptionForfaitSki
     */
    public function setPresent(\Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC $present = null)
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
