<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * SecteurTraduction
 */
class SecteurTraduction
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
     * @var string
     */
    private $description;
    /**
     * @var Secteur
     */
    private $secteur;
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
     * @return SecteurTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SecteurTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get secteur
     *
     * @return Secteur
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * Set secteur
     *
     * @param Secteur $secteur
     *
     * @return SecteurTraduction
     */
    public function setSecteur(Secteur $secteur = null)
    {
        $this->secteur = $secteur;

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
     * @return SecteurTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
