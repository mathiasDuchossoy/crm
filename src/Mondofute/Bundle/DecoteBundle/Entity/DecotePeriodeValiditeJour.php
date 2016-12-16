<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

/**
 * DecotePeriodeValiditeJour
 */
class DecotePeriodeValiditeJour
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
     * @return DecotePeriodeValiditeJour
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
     * @return DecotePeriodeValiditeJour
     */
    public function setJourFin($jourFin)
    {
        $this->jourFin = $jourFin;

        return $this;
    }
}
