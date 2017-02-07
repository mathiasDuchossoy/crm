<?php

namespace Mondofute\Bundle\PeriodeBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;

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
     * @var DateTime
     */
    private $debut;
    /**
     * @var DateTime
     */
    private $fin;
    /**
     * @var int
     */
    private $nbJour;
    /**
     * @var TypePeriode
     */
    private $type;
    /**
     * @var Collection
     */
    private $logementPeriodeLocatifs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logementPeriodeLocatifs = new ArrayCollection();
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
     * Get debut
     *
     * @return DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set debut
     *
     * @param DateTime $debut
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
     * @return DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set fin
     *
     * @param DateTime $fin
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
     * @return TypePeriode
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param TypePeriode $type
     *
     * @return Periode
     */
    public function setType(TypePeriode $type = null)
    {
        $this->type = $type;

        return $this;
    }

    public function __toString()
    {
        return 'Du ' . date_format($this->debut, "d/m/Y") . ' au ' . date_format($this->fin, "d/m/Y");
    }

    /**
     * Add logementPeriodeLocatif
     *
     * @param LogementPeriodeLocatif $logementPeriodeLocatif
     *
     * @return Periode
     */
    public function addLogementPeriodeLocatif(LogementPeriodeLocatif $logementPeriodeLocatif)
    {
        $this->logementPeriodeLocatifs[] = $logementPeriodeLocatif;

        return $this;
    }

    /**
     * Remove logementPeriodeLocatif
     *
     * @param LogementPeriodeLocatif $logementPeriodeLocatif
     */
    public function removeLogementPeriodeLocatif(LogementPeriodeLocatif $logementPeriodeLocatif)
    {
        $this->logementPeriodeLocatifs->removeElement($logementPeriodeLocatif);
    }

    /**
     * Get logementPeriodeLocatifs
     *
     * @return Collection
     */
    public function getLogementPeriodeLocatifs()
    {
        return $this->logementPeriodeLocatifs;
    }
}
