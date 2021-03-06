<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * PromotionPeriodeValiditeDate
 */
class PromotionPeriodeValiditeDate
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \DateTime
     */
    private $dateDebut;
    /**
     * @var \DateTime
     */
    private $dateFin;

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
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return PromotionPeriodeValiditeDate
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return PromotionPeriodeValiditeDate
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }
}
