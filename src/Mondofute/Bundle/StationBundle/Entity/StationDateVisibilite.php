<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationDateVisibilite
 */
class StationDateVisibilite
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \DateTime
     */
    private $dateFin;
    /**
     * @var \DateTime
     */
    private $dateDebut;

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
     * @return StationDateVisibilite
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
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
     * @return StationDateVisibilite
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }
}
