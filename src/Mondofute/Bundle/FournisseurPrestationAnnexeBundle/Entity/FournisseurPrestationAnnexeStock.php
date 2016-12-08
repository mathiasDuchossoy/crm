<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;

/**
 * FournisseurPrestationAnnexeStock
 */
class FournisseurPrestationAnnexeStock
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $stock;
    /**
     * @var FournisseurPrestationAnnexe
     */
    private $fournisseurPrestationAnnexe;
    /**
     * @var Periode
     */
    private $periode;
    /**
     * @var Logement
     */
    private $logement;

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
     * @return FournisseurPrestationAnnexeStock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get fournisseurPrestationAnnexe
     *
     * @return FournisseurPrestationAnnexe
     */
    public function getFournisseurPrestationAnnexe()
    {
        return $this->fournisseurPrestationAnnexe;
    }

    /**
     * Set fournisseurPrestationAnnexe
     *
     * @param FournisseurPrestationAnnexe $fournisseurPrestationAnnexe
     *
     * @return FournisseurPrestationAnnexeStock
     */
    public function setFournisseurPrestationAnnexe(FournisseurPrestationAnnexe $fournisseurPrestationAnnexe = null)
    {
        $this->fournisseurPrestationAnnexe = $fournisseurPrestationAnnexe;

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
     * @return FournisseurPrestationAnnexeStock
     */
    public function setPeriode(Periode $periode = null)
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
     * @return FournisseurPrestationAnnexeStock
     */
    public function setLogement(Logement $logement = null)
    {
        $this->logement = $logement;

        return $this;
    }
}
