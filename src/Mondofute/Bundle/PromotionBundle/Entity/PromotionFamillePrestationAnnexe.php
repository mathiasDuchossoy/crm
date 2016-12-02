<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;

/**
 * PromotionFamillePrestationAnnexe
 */
class PromotionFamillePrestationAnnexe
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
     * @var Promotion
     */
    private $promotion;

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
     *
     * @return PromotionFamillePrestationAnnexe
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return PromotionFamillePrestationAnnexe
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
     * @return PromotionFamillePrestationAnnexe
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Set promotion
     *
     * @param Promotion $promotion
     *
     * @return PromotionFamillePrestationAnnexe
     */
    public function setPromotion(Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
