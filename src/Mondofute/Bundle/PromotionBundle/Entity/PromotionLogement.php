<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

use Mondofute\Bundle\LogementBundle\Entity\Logement;

/**
 * PromotionLogement
 */
class PromotionLogement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Logement
     */
    private $logement;
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
    public function setLogement(Logement $logement = null)
    {
        $this->logement = $logement;

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
     * @return PromotionLogement
     */
    public function setPromotion(Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
