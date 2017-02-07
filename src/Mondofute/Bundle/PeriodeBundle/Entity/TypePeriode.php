<?php

namespace Mondofute\Bundle\PeriodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var Collection
     */
    private $periodes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periodes = new ArrayCollection();
    }

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

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add periode
     *
     * @param Periode $periode
     *
     * @return TypePeriode
     */
    public function addPeriode(Periode $periode)
    {
        $this->periodes[] = $periode;

        return $this;
    }

    /**
     * Remove periode
     *
     * @param Periode $periode
     */
    public function removePeriode(Periode $periode)
    {
        $this->periodes->removeElement($periode);
    }

    /**
     * Get periodes
     *
     * @return Collection
     */
    public function getPeriodes()
    {
        return $this->periodes;
    }
}
