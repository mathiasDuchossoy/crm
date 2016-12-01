<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * PromotionHebergement
 */
class PromotionHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\HebergementBundle\Entity\Hebergement
     */
    private $hebergement;
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
     * Get hebergement
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     *
     * @return PromotionHebergement
     */
    public function setHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

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
     * @return PromotionHebergement
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
     * @return PromotionHebergement
     */
    public function setPromotion(\Mondofute\Bundle\PromotionBundle\Entity\Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
