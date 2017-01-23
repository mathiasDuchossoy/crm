<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;

/**
 * FournisseurPrestationAnnexeStockHebergement
 */
class FournisseurPrestationAnnexeStockHebergement extends FournisseurPrestationAnnexeStock
{

//    /**
//     * @var FournisseurPrestationAnnexeStock
//     */
//    private $fournisseurPrestationAnnexe;
//
//    /**
//     * @var FournisseurPrestationAnnexeStock
//     */
//    private $periode;

    /**
     * @var FournisseurHebergement
     */
    private $fournisseurHebergement;
    /**
     * @var FournisseurPrestationAnnexe
     */
    private $fournisseurPrestationAnnexe;

    /**
     * Get fournisseurHebergement
     *
     * @return FournisseurHebergement
     */
    public function getFournisseurHebergement()
    {
        return $this->fournisseurHebergement;
    }

    /**
     * Set fournisseurHebergement
     *
     * @param FournisseurHebergement $fournisseurHebergement
     *
     * @return FournisseurPrestationAnnexeStockHebergement
     */
    public function setFournisseurHebergement(FournisseurHebergement $fournisseurHebergement = null)
    {
        $this->fournisseurHebergement = $fournisseurHebergement;

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
     * @return FournisseurPrestationAnnexeStockHebergement
     */
    public function setFournisseurPrestationAnnexe(FournisseurPrestationAnnexe $fournisseurPrestationAnnexe)
    {
        $this->fournisseurPrestationAnnexe = $fournisseurPrestationAnnexe;

        return $this;
    }
}
