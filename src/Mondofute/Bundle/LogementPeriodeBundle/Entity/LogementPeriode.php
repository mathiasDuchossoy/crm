<?php

namespace Mondofute\Bundle\LogementPeriodeBundle\Entity;

use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;

/**
 * LogementPeriode
 */
class LogementPeriode
{


    /**
     * @var boolean
     */
    private $actif;

    /**
     * @var \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    private $periode;

    /**
     * @var \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    private $logement;

    /**
     * @var LogementPeriodeLocatif
     */
    private $locatif;

    /**
     * @return LogementPeriodeLocatif
     */
    public function getLocatif()
    {
        return $this->locatif;
    }

    /**
     * @param LogementPeriodeLocatif $locatif
     */
    public function setLocatif($locatif)
    {
        $this->locatif = $locatif;
    }

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
     * @return \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set periode
     *
     * @param \Mondofute\Bundle\PeriodeBundle\Entity\Periode $periode
     *
     * @return LogementPeriode
     */
    public function setPeriode(\Mondofute\Bundle\PeriodeBundle\Entity\Periode $periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get logement
     *
     * @return \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\Logement $logement
     *
     * @return LogementPeriode
     */
    public function setLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
}
