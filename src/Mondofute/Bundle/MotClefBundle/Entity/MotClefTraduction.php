<?php

namespace Mondofute\Bundle\MotClefBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\MotClefBundle\Entity\MotClefTraductionHebergement;

/**
 * MotClefTraduction
 */
class MotClefTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;
    /**
     * @var Langue
     */
    private $langue;
    /**
     * @var MotClef
     */
    private $motClef;
    /**
     * @var Collection
     */
    private $hebergements;
    /**
     * @var Collection
     */
    private $motClefTraductionHebergements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hebergements = new ArrayCollection();
        $this->motClefTraductionHebergements = new ArrayCollection();
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
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return MotClefTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return MotClefTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get motClef
     *
     * @return MotClef
     */
    public function getMotClef()
    {
        return $this->motClef;
    }

    /**
     * Set motClef
     *
     * @param MotClef $motClef
     *
     * @return MotClefTraduction
     */
    public function setMotClef(MotClef $motClef = null)
    {
        $this->motClef = $motClef;

        return $this;
    }

    /**
     * Add hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return MotClefTraduction
     */
    public function addHebergement(Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement;

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
     * Add motClefTraductionHebergement
     *
     * @param MotClefTraductionHebergement $motClefTraductionHebergement
     *
     * @return MotClefTraduction
     */
    public function addMotClefTraductionHebergement(MotClefTraductionHebergement $motClefTraductionHebergement)
    {
        $this->motClefTraductionHebergements[] = $motClefTraductionHebergement;

        return $this;
    }

    /**
     * Remove motClefTraductionHebergement
     *
     * @param MotClefTraductionHebergement $motClefTraductionHebergement
     */
    public function removeMotClefTraductionHebergement(MotClefTraductionHebergement $motClefTraductionHebergement)
    {
        $this->motClefTraductionHebergements->removeElement($motClefTraductionHebergement);
    }

    /**
     * Get motClefTraductionHebergements
     *
     * @return Collection
     */
    public function getMotClefTraductionHebergements()
    {
        return $this->motClefTraductionHebergements;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->libelle;
    }
}
