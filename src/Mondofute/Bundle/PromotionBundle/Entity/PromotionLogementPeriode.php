<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * PromotionLogementPeriode
 */
class PromotionLogementPeriode
{
    /**
     * @var \Mondofute\Bundle\PromotionBundle\Entity\Promotion
     */
    private $promotion;

    /**
     * @var \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    private $periode;

    /**
     * @var \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    private $logement;

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
     * @return PromotionLogementPeriode
     */
    public function setPromotion(\Mondofute\Bundle\PromotionBundle\Entity\Promotion $promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get periode
     *
     * @return \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set periode
     *
     * @param \Mondofute\Bundle\PeriodeBundle\Entity\Periode $periode
     *
     * @return PromotionLogementPeriode
     */
    public function setPeriode(\Mondofute\Bundle\PeriodeBundle\Entity\Periode $periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get logement
     *
     * @return \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\Logement $logement
     *
     * @return PromotionLogementPeriode
     */
    public function setLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
}
