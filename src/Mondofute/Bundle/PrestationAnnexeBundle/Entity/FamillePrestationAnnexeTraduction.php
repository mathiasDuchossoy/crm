<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * FamillePrestationAnnexeTraduction
 */
class FamillePrestationAnnexeTraduction
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
     * @var FamillePrestationAnnexe
     */
    private $famillePrestationAnnexe;
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
     * @return FamillePrestationAnnexeTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get famillePrestationAnnexe
     *
     * @return FamillePrestationAnnexe
     */
    public function getFamillePrestationAnnexe()
    {
        return $this->famillePrestationAnnexe;
    }

    /**
     * Set famillePrestationAnnexe
     *
     * @param FamillePrestationAnnexe $famillePrestationAnnexe
     *
     * @return FamillePrestationAnnexeTraduction
     */
    public function setFamillePrestationAnnexe(FamillePrestationAnnexe $famillePrestationAnnexe = null)
    {
        $this->famillePrestationAnnexe = $famillePrestationAnnexe;

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
     * @return FamillePrestationAnnexeTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
