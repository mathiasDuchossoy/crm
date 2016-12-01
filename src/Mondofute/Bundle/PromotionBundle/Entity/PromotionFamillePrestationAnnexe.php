<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

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
     * @var \Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe
     */
    private $famillePrestationAnnexe;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;
    /**
     * @var \Mondofute\Bundle\PromotionBundle\Entity\Promotion
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
     * Get famillePrestationAnnexe
     *
     * @return \Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe
     */
    public function getFamillePrestationAnnexe()
    {
        return $this->famillePrestationAnnexe;
    }

    /**
     * Set famillePrestationAnnexe
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe $famillePrestationAnnexe
     *
     * @return PromotionFamillePrestationAnnexe
     */
    public function setFamillePrestationAnnexe(\Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe $famillePrestationAnnexe = null)
    {
        $this->famillePrestationAnnexe = $famillePrestationAnnexe;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return PromotionFamillePrestationAnnexe
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return \Mondofute\Bundle\PromotionBundle\Entity\Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Set promotion
     *
     * @param \Mondofute\Bundle\PromotionBundle\Entity\Promotion $promotion
     *
     * @return PromotionFamillePrestationAnnexe
     */
    public function setPromotion(\Mondofute\Bundle\PromotionBundle\Entity\Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
