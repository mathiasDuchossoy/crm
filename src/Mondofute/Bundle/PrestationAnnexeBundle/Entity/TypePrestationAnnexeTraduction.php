<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * TypePrestationAnnexeTraduction
 */
class TypePrestationAnnexeTraduction
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
     * @var TypePrestationAnnexe
     */
    private $typePrestationAnnexe;
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
     * @return TypePrestationAnnexeTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get typePrestationAnnexe
     *
     * @return TypePrestationAnnexe
     */
    public function getTypePrestationAnnexe()
    {
        return $this->typePrestationAnnexe;
    }

    /**
     * Set typePrestationAnnexe
     *
     * @param TypePrestationAnnexe $typePrestationAnnexe
     *
     * @return TypePrestationAnnexeTraduction
     */
    public function setTypePrestationAnnexe(TypePrestationAnnexe $typePrestationAnnexe = null)
    {
        $this->typePrestationAnnexe = $typePrestationAnnexe;

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
     * @return TypePrestationAnnexeTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
