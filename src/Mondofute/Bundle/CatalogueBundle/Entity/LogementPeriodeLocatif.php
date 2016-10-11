<?php

namespace Mondofute\Bundle\CatalogueBundle\Entity;

use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;

/**
 * LogementPeriodeLocatif
 */
class LogementPeriodeLocatif
{
    /**
     * @var string
     */
    private $prixPublic;

    /**
     * @var integer
     */
    private $stock;

    /**
     * @var \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    private $periode;

    /**
     * @var \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    private $logement;

    /**
     * Get prixPublic
     *
     * @return string
     */
    public function getPrixPublic()
    {
        return $this->prixPublic;
    }

    /**
     * Set prixPublic
     *
     * @param string $prixPublic
     *
     * @return LogementPeriodeLocatif
     */
    public function setPrixPublic($prixPublic)
    {
        $this->prixPublic = $prixPublic;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return LogementPeriodeLocatif
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

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
     * @return LogementPeriodeLocatif
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
     * @return LogementPeriodeLocatif
     */
    public function setLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
}
