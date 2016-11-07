<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction;
use Mondofute\Bundle\UniteBundle\Entity\Age;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;

/**
 * DescriptionForfaitSki
 */
class DescriptionForfaitSki
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var float
     */
    private $quantite;

    /**
     * @var integer
     */
    private $classement;

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
     * @var Collection
     */
    private $traductions;

    /**
     * @var OuiNonNC
     */
    private $present;

    /**
     * @var LigneDescriptionForfaitSki
     */
    private $ligneDescriptionForfaitSki;

    /**
     * @var ModeleDescriptionForfaitSki
     */
    private $modele;

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
     * @return integer
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
     * @return DescriptionForfaitSki
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

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
     * @return DescriptionForfaitSki
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param DescriptionForfaitSkiTraduction $traduction
     */
    public function removeTraduction(DescriptionForfaitSkiTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * @return DescriptionForfaitSki
     */
    public function setPresent(OuiNonNC $present = null)
    {
        $this->present = $present;

        return $this;
    }

    /**
     * Get ligneDescriptionForfaitSki
     *
     * @return LigneDescriptionForfaitSki
     */
    public function getLigneDescriptionForfaitSki()
    {
        return $this->ligneDescriptionForfaitSki;
    }

    /**
     * Set ligneDescriptionForfaitSki
     *
     * @param LigneDescriptionForfaitSki $ligneDescriptionForfaitSki
     *
     * @return DescriptionForfaitSki
     */
    public function setLigneDescriptionForfaitSki(LigneDescriptionForfaitSki $ligneDescriptionForfaitSki = null)
    {
        $this->ligneDescriptionForfaitSki = $ligneDescriptionForfaitSki;

        return $this;
    }

    /**
     * Get modele
     *
     * @return ModeleDescriptionForfaitSki
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set modele
     *
     * @param ModeleDescriptionForfaitSki $modele
     *
     * @return DescriptionForfaitSki
     */
    public function setModele(ModeleDescriptionForfaitSki $modele = null)
    {
        $this->modele = $modele;

        return $this;
    }

    public function __clone()
    {
        /** @var DescriptionForfaitSkiTraduction $traduction */
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setDescriptionForfaitSki($this);
            }
        }
        $this->ageMin = clone $this->getAgeMin();
        $this->ageMax = clone $this->getAgeMax();
        $this->prix = clone $this->getPrix();
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

    public function setTraductions($traductions)
    {
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
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
     * @return DescriptionForfaitSki
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
     * @return DescriptionForfaitSki
     */
    public function setAgeMax(Age $ageMax = null)
    {
        $this->ageMax = $ageMax;

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
     * Add traduction
     *
     * @param DescriptionForfaitSkiTraduction $traduction
     *
     * @return DescriptionForfaitSki
     */
    public function addTraduction(DescriptionForfaitSkiTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDescriptionForfaitSki($this);

        return $this;
    }

    /**
     * Set prix
     *
     * @param Tarif $prix
     *
     * @return DescriptionForfaitSki
     */
    public function setPrix(Tarif $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }
}
