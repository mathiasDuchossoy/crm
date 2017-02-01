<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * EtatDossierTraduction
 */
class EtatDossierTraduction
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
     * @var EtatDossier
     */
    private $etatDossier;

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
     * @return EtatDossierTraduction
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
     * @return EtatDossierTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get etatDossier
     *
     * @return EtatDossier
     */
    public function getEtatDossier()
    {
        return $this->etatDossier;
    }

    /**
     * Set etatDossier
     *
     * @param EtatDossier $etatDossier
     *
     * @return EtatDossierTraduction
     */
    public function setEtatDossier(EtatDossier $etatDossier = null)
    {
        $this->etatDossier = $etatDossier;

        return $this;
    }
}
