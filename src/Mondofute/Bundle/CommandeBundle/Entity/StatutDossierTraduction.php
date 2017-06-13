<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * StatutDossierTraduction
 */
class StatutDossierTraduction
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
     * @var StatutDossier
     */
    private $statutDossier;

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
     * @return StatutDossierTraduction
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
     * @return StatutDossierTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get statutDossier
     *
     * @return StatutDossier
     */
    public function getStatutDossier()
    {
        return $this->statutDossier;
    }

    /**
     * Set statutDossier
     *
     * @param StatutDossier $statutDossier
     *
     * @return StatutDossierTraduction
     */
    public function setStatutDossier(StatutDossier $statutDossier = null)
    {
        $this->statutDossier = $statutDossier;

        return $this;
    }
}
