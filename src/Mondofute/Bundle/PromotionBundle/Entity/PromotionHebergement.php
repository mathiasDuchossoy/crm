<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;

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
     * @var Hebergement
     */
    private $hebergement;
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
     * @return PromotionHebergement
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get hebergement
     *
     * @return Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return PromotionHebergement
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

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
     * @return PromotionHebergement
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
     * @return PromotionHebergement
     */
    public function setPromotion(Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
