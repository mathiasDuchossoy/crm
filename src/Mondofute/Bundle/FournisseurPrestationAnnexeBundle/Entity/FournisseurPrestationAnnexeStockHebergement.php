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
}
