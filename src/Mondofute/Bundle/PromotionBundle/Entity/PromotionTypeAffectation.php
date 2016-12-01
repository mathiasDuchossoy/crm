<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * PromotionTypeAffectation
 */
class PromotionTypeAffectation
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $typeAffectation;
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
     * Get typeAffectation
     *
     * @return integer
     */
    public function getTypeAffectation()
    {
        return $this->typeAffectation;
    }

    /**
     * Set typeAffectation
     *
     * @param integer $typeAffectation
     *
     * @return PromotionTypeAffectation
     */
    public function setTypeAffectation($typeAffectation)
    {
        $this->typeAffectation = $typeAffectation;

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
     * @return PromotionTypeAffectation
     */
    public function setPromotion(Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
