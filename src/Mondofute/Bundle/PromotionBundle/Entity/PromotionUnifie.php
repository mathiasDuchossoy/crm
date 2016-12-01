<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\PromotionBundle\Entity\Promotion;

/**
 * PromotionUnifie
 */
class PromotionUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $promotions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->promotions = new ArrayCollection();
    }

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
     * @return PromotionUnifie
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove promotion
     *
     * @param Promotion $promotion
     */
    public function removePromotion(Promotion $promotion)
    {
        $this->promotions->removeElement($promotion);
    }

    /**
     * Get promotions
     *
     * @return Collection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param $promotions
     * @return $this
     */
    public function setPromotions($promotions)
    {
        $this->getPromotions()->clear();

        foreach ($promotions as $promotion) {
            $this->addPromotion($promotion);
        }
        return $this;
    }

    /**
     * Add promotion
     *
     * @param Promotion $promotion
     *
     * @return PromotionUnifie
     */
    public function addPromotion(Promotion $promotion)
    {
        $this->promotions[] = $promotion->setPromotionUnifie($this);

        return $this;
    }
}
