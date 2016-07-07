<?php

namespace Mondofute\Bundle\PeriodeBundle\Entity;

/**
 * TypePeriode
 */
class TypePeriode
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $nbJourDefaut;
    /**
     * @var boolean
     */
    private $court;

    /**
     * Get nbJourDefaut
     *
     * @return integer
     */
    public function getNbJourDefaut()
    {
        return $this->nbJourDefaut;
    }

    /**
     * Set nbJourDefaut
     *
     * @param integer $nbJourDefaut
     *
     * @return TypePeriode
     */
    public function setNbJourDefaut($nbJourDefaut)
    {
        $this->nbJourDefaut = $nbJourDefaut;

        return $this;
    }

    /**
     * Get court
     *
     * @return boolean
     */
    public function getCourt()
    {
        return $this->court;
    }

    /**
     * Set court
     *
     * @param boolean $court
     *
     * @return TypePeriode
     */
    public function setCourt($court)
    {
        $this->court = $court;

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return 'type_periode.' . $this->getId();
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
}
