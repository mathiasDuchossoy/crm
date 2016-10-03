<?php

namespace Mondofute\Bundle\CodePromoApplicationBundle\Entity;

use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;

/**
 * CodePromoFournisseurPrestationAnnexe
 */
class CodePromoFournisseurPrestationAnnexe
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var FournisseurPrestationAnnexe
     */
    private $fournisseurPrestationAnnexe;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var CodePromo
     */
    private $codePromo;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get fournisseurPrestationAnnexe
     *
     * @return FournisseurPrestationAnnexe
     */
    public function getFournisseurPrestationAnnexe()
    {
        return $this->fournisseurPrestationAnnexe;
    }

    /**
     * Set fournisseurPrestationAnnexe
     *
     * @param FournisseurPrestationAnnexe $fournisseurPrestationAnnexe
     *
     * @return CodePromoFournisseurPrestationAnnexe
     */
    public function setFournisseurPrestationAnnexe(FournisseurPrestationAnnexe $fournisseurPrestationAnnexe = null)
    {
        $this->fournisseurPrestationAnnexe = $fournisseurPrestationAnnexe;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return CodePromoFournisseurPrestationAnnexe
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get codePromo
     *
     * @return CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param CodePromo $codePromo
     *
     * @return CodePromoFournisseurPrestationAnnexe
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
