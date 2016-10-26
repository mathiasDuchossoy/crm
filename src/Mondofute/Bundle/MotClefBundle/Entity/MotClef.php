<?php

namespace Mondofute\Bundle\MotClefBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;

/**
 * MotClef
 */
class MotClef
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
     * @var Collection
     */
    private $traductions;
    /**
     * @var Collection
     */
    private $hebergements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->hebergements = new ArrayCollection();
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
     * @return MotClef
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param MotClefTraduction $traduction
     *
     * @return MotClef
     */
    public function addTraduction(MotClefTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param MotClefTraduction $traduction
     */
    public function removeTraduction(MotClefTraduction $traduction)
    {
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
     * Add hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return MotClef
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
}
