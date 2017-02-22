<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\PromotionBundle\Entity\Promotion;

/**
 * RemisePromotion
 */
class RemisePromotion extends CommandeLigneRemise
{
    /**
     * @var Promotion
     */
    private $promotion;

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
     * @return RemisePromotion
     */
    public function setPromotion(Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
