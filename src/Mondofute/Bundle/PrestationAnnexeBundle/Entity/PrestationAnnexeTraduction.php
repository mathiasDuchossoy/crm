<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * PrestationAnnexeTraduction
 */
class PrestationAnnexeTraduction
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
     * @var prestationAnnexe
     */
    private $prestationAnnexe;
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
     * @return PrestationAnnexeTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get prestationAnnexe
     *
     * @return prestationAnnexe
     */
    public function getPrestationAnnexe()
    {
        return $this->prestationAnnexe;
    }

    /**
     * Set prestationAnnexe
     *
     * @param PrestationAnnexe $prestationAnnexe
     *
     * @return PrestationAnnexeTraduction
     */
    public function setPrestationAnnexe(PrestationAnnexe $prestationAnnexe = null)
    {
        $this->prestationAnnexe = $prestationAnnexe;

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
     * @return PrestationAnnexeTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
