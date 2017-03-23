<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\CommandeBundle\Entity\LitigeDossier;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * LitigeDossierTraduction
 */
class LitigeDossierTraduction
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
     * @var LitigeDossier
     */
    private $litigeDossier;

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
     * @return LitigeDossierTraduction
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
     * @return LitigeDossierTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get litigeDossier
     *
     * @return LitigeDossier
     */
    public function getLitigeDossier()
    {
        return $this->litigeDossier;
    }

    /**
     * Set litigeDossier
     *
     * @param LitigeDossier $litigeDossier
     *
     * @return LitigeDossierTraduction
     */
    public function setLitigeDossier(LitigeDossier $litigeDossier = null)
    {
        $this->litigeDossier = $litigeDossier;

        return $this;
    }
}
