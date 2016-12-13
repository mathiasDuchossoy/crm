<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * SousFamillePrestationAnnexeTraduction
 */
class SousFamillePrestationAnnexeTraduction
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
     * @var SousFamillePrestationAnnexe
     */
    private $sousFamillePrestationAnnexe;
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
     * @return SousFamillePrestationAnnexeTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get sousFamillePrestationAnnexe
     *
     * @return SousFamillePrestationAnnexe
     */
    public function getSousFamillePrestationAnnexe()
    {
        return $this->sousFamillePrestationAnnexe;
    }

    /**
     * Set sousFamillePrestationAnnexe
     *
     * @param SousFamillePrestationAnnexe $sousFamillePrestationAnnexe
     *
     * @return SousFamillePrestationAnnexeTraduction
     */
    public function setSousFamillePrestationAnnexe(SousFamillePrestationAnnexe $sousFamillePrestationAnnexe = null)
    {
        $this->sousFamillePrestationAnnexe = $sousFamillePrestationAnnexe;

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
     * @return SousFamillePrestationAnnexeTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
