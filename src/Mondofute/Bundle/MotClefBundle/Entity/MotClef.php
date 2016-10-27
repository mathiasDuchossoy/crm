<?php

namespace Mondofute\Bundle\MotClefBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

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
    private $hebergements;
    /**
     * @var Langue
     */
    private $langue;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set id
     *
     * @param int $id
     *
     * @return MotClef
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return MotClef
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
