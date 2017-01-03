<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * PromotionPeriodeValiditeJour
 */
class PromotionPeriodeValiditeJour
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $jourDebut;
    /**
     * @var integer
     */
    private $jourFin;

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
     * Get jourDebut
     *
     * @return integer
     */
    public function getJourDebut()
    {
        return $this->jourDebut;
    }

    /**
     * Set jourDebut
     *
     * @param integer $jourDebut
     *
     * @return PromotionPeriodeValiditeJour
     */
    public function setJourDebut($jourDebut)
    {
        $this->jourDebut = $jourDebut;

        return $this;
    }

    /**
     * Get jourFin
     *
     * @return integer
     */
    public function getJourFin()
    {
        return $this->jourFin;
    }

    /**
     * Set jourFin
     *
     * @param integer $jourFin
     *
     * @return PromotionPeriodeValiditeJour
     */
    public function setJourFin($jourFin)
    {
        $this->jourFin = $jourFin;

        return $this;
    }
}
