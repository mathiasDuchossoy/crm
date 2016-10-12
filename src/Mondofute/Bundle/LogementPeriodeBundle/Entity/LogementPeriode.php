<?php

namespace Mondofute\Bundle\LogementPeriodeBundle\Entity;

use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;

/**
 * LogementPeriode
 */
class LogementPeriode
{

    /**
     * @var bool
     */
    private $actif;
    /**
     * @var Periode
     */
    private $periode;
    /**
     * @var Logement
     */
    private $logement;

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return LogementPeriode
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get periode
     *
     * @return Periode
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set periode
     *
     * @param Periode $periode
     *
     * @return LogementPeriode
     */
    public function setPeriode(Periode $periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get logement
     *
     * @return Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param Logement $logement
     *
     * @return LogementPeriode
     */
    public function setLogement(Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
    /**
     * @var LogementPeriodeLocatif
     */
    private $locatif;


    /**
     * Set locatif
     *
     * @param LogementPeriodeLocatif $locatif
     *
     * @return LogementPeriode
     */
    public function setLocatif(LogementPeriodeLocatif $locatif = null)
    {
        $this->locatif = $locatif;

        return $this;
    }

    /**
     * Get locatif
     *
     * @return LogementPeriodeLocatif
     */
    public function getLocatif()
    {
        return $this->locatif;
    }
}
