<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * FournisseurPrestationAnnexeStockFournisseur
 */
class FournisseurPrestationAnnexeStockFournisseur extends FournisseurPrestationAnnexeStock
{

    /**
     * @var FournisseurPrestationAnnexe
     */
    private $fournisseurPrestationAnnexe;

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
     * @return FournisseurPrestationAnnexeStockFournisseur
     */
    public function setFournisseurPrestationAnnexe(FournisseurPrestationAnnexe $fournisseurPrestationAnnexe)
    {
        $this->fournisseurPrestationAnnexe = $fournisseurPrestationAnnexe;

        return $this;
    }
}
