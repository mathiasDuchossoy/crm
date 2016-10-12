<?php

namespace Mondofute\Bundle\CodePromoApplicationBundle\Entity;

use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;

/**
 * CodePromoFamillePrestationAnnexe
 */
class CodePromoFamillePrestationAnnexe
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var FamillePrestationAnnexe
     */
    private $famillePrestationAnnexe;
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
     * @return CodePromoFamillePrestationAnnexe
     */
    public function setFamillePrestationAnnexe(FamillePrestationAnnexe $famillePrestationAnnexe = null)
    {
        $this->famillePrestationAnnexe = $famillePrestationAnnexe;

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
     * @return CodePromoFamillePrestationAnnexe
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
     * @return CodePromoFamillePrestationAnnexe
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
