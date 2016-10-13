<?php

namespace Mondofute\Bundle\PeriodeBundle\Entity;

/**
 * Periode
 */
class Periode
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
     * @var int
     */
    private $nbJour;
    /**
     * @var \Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode
     */
    private $type;

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
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Periode
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
     * @return Periode
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get nbJour
     *
     * @return int
     */
    public function getNbJour()
    {
        return $this->nbJour;
    }

    /**
     * Set nbJour
     *
     * @param integer $nbJour
     *
     * @return Periode
     */
    public function setNbJour($nbJour)
    {
        $this->nbJour = $nbJour;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode $type
     *
     * @return Periode
     */
    public function setType(\Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode $type = null)
    {
        $this->type = $type;

        return $this;
    }
}
