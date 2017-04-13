<?php

namespace Mondofute\Bundle\CatalogueBundle\Entity;

use Mondofute\Bundle\LogementBundle\Entity\Logement;
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
     * @var Periode
     */
    private $periode;

    /**
     * @var Logement
     */
    private $logement;
    /**
     * @var string
     */
    private $prixAchat;
    /**
     * @var string
     */
    private $prixCatalogue = 0;
    /**
     * @var string
     */
    private $comMondofute = 0;

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
     * @return LogementPeriodeLocatif
     */
    public function setPeriode(Periode $periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return string
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }

    /**
     * Set prixAchat
     *
     * @param string $prixAchat
     *
     * @return LogementPeriodeLocatif
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

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
     * @return LogementPeriodeLocatif
     */
    public function setLogement(Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }

    /**
     * Get prixCatalogue
     *
     * @return string
     */
    public function getPrixCatalogue()
    {
        return $this->prixCatalogue;
    }

    /**
     * Set prixCatalogue
     *
     * @param string $prixCatalogue
     *
     * @return LogementPeriodeLocatif
     */
    public function setPrixCatalogue($prixCatalogue)
    {
        $this->prixCatalogue = $prixCatalogue;

        return $this;
    }

    /**
     * Get comMondofute
     *
     * @return string
     */
    public function getComMondofute()
    {
        return $this->comMondofute;
    }

    /**
     * Set comMondofute
     *
     * @param string $comMondofute
     *
     * @return LogementPeriodeLocatif
     */
    public function setComMondofute($comMondofute)
    {
        $this->comMondofute = $comMondofute;

        return $this;
    }
}
