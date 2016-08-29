<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * SousTypePrestationAnnexeTraduction
 */
class SousTypePrestationAnnexeTraduction
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
     * @var SousTypePrestationAnnexe
     */
    private $sousTypePrestationAnnexe;
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
     * @return SousTypePrestationAnnexeTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get sousTypePrestationAnnexe
     *
     * @return SousTypePrestationAnnexe
     */
    public function getSousTypePrestationAnnexe()
    {
        return $this->sousTypePrestationAnnexe;
    }

    /**
     * Set sousTypePrestationAnnexe
     *
     * @param SousTypePrestationAnnexe $sousTypePrestationAnnexe
     *
     * @return SousTypePrestationAnnexeTraduction
     */
    public function setSousTypePrestationAnnexe(SousTypePrestationAnnexe $sousTypePrestationAnnexe = null)
    {
        $this->sousTypePrestationAnnexe = $sousTypePrestationAnnexe;

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
     * @return SousTypePrestationAnnexeTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
