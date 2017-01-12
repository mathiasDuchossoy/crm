<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

use Mondofute\Bundle\LogementBundle\Entity\Logement;

/**
 * PromotionLogement
 */
class PromotionLogement
{
    /**
     * @var Promotion
     */
    private $promotion;
    /**
     * @var Logement
     */
    private $logement;

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
     * @return PromotionLogement
     */
    public function setPromotion(Promotion $promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get logement
     *
     * @return Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param Logement $logement
     *
     * @return PromotionLogement
     */
    public function setLogement(Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
}
