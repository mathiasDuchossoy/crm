<?php

namespace Mondofute\Bundle\CoupDeCoeurBundle\Entity;

use DateTime;

/**
 * CoupDeCoeur
 */
abstract class CoupDeCoeur
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $dateHeureDebut;

    /**
     * @var DateTime
     */
    private $dateHeureFin;


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
     * Get dateHeureDebut
     *
     * @return DateTime
     */
    public function getDateHeureDebut()
    {
        return $this->dateHeureDebut;
    }

    /**
     * Set dateHeureDebut
     *
     * @param DateTime $dateHeureDebut
     *
     * @return CoupDeCoeur
     */
    public function setDateHeureDebut($dateHeureDebut)
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    /**
     * Get dateHeureFin
     *
     * @return DateTime
     */
    public function getDateHeureFin()
    {
        return $this->dateHeureFin;
    }

    /**
     * Set dateHeureFin
     *
     * @param DateTime $dateHeureFin
     *
     * @return CoupDeCoeur
     */
    public function setDateHeureFin($dateHeureFin)
    {
        $this->dateHeureFin = $dateHeureFin;

        return $this;
    }
}

