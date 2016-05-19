<?php

namespace Mondofute\Bundle\TrancheHoraireBundle\Entity;

/**
 * TrancheHoraire
 */
class TrancheHoraire
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $debut;

    /**
     * @var \DateTime
     */
    private $fin;


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
     * Get debut
     *
     * @return \DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     *
     * @return TrancheHoraire
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     *
     * @return TrancheHoraire
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }
}
