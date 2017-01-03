<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * FournisseurHebergementTraduction
 */
class FournisseurHebergementTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $acces;
    /**
     * @var FournisseurHebergement
     */
    private $fournisseurHebergement;
    /**
     * @var Langue
     */
    private $langue;

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
     * Get acces
     *
     * @return string
     */
    public function getAcces()
    {
        return $this->acces;
    }

    /**
     * Set acces
     *
     * @param string $acces
     *
     * @return FournisseurHebergementTraduction
     */
    public function setAcces($acces)
    {
        $this->acces = $acces;

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
     * @return FournisseurHebergementTraduction
     */
    public function setFournisseurHebergement(
        FournisseurHebergement $fournisseurHebergement = null
    )
    {
        $this->fournisseurHebergement = $fournisseurHebergement;

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
     * @return FournisseurHebergementTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
