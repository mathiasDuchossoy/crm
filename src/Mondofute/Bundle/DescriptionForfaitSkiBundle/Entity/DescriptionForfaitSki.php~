<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * @var \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC
     */
    private $present;

    /**
     * @var \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki
     */
    private $ligneDescriptionForfaitSki;

    /**
     * @var \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki
     */
    private $modele;

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
     * @return integer
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
     * Add traduction
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction $traduction
     *
     * @return DescriptionForfaitSki
     */
    public function addTraduction(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDescriptionForfaitSki($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * @return DescriptionForfaitSki
     */
    public function setPresent(\Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC $present = null)
    {
        $this->present = $present;

        return $this;
    }

    /**
     * Get ligneDescriptionForfaitSki
     *
     * @return \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki
     */
    public function getLigneDescriptionForfaitSki()
    {
        return $this->ligneDescriptionForfaitSki;
    }

    /**
     * Set ligneDescriptionForfaitSki
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki $ligneDescriptionForfaitSki
     *
     * @return DescriptionForfaitSki
     */
    public function setLigneDescriptionForfaitSki(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki $ligneDescriptionForfaitSki = null)
    {
        $this->ligneDescriptionForfaitSki = $ligneDescriptionForfaitSki;

        return $this;
    }

    /**
     * Get modele
     *
     * @return \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set modele
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki $modele
     *
     * @return DescriptionForfaitSki
     */
    public function setModele(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki $modele = null)
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
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
     * @return DescriptionForfaitSki
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
     * @return DescriptionForfaitSki
     */
    public function setAgeMax(\Mondofute\Bundle\UniteBundle\Entity\Age $ageMax = null)
    {
        $this->ageMax = $ageMax;

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
     * @return DescriptionForfaitSki
     */
    public function setPrix(\Mondofute\Bundle\UniteBundle\Entity\Tarif $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }
}
