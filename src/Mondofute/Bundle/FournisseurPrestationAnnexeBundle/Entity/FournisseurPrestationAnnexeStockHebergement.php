<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;

/**
 * FournisseurPrestationAnnexeStockHebergement
 */
class FournisseurPrestationAnnexeStockHebergement extends FournisseurPrestationAnnexeStock
{

    /**
     * @var FournisseurPrestationAnnexeStock
     */
    private $fournisseurPrestationAnnexe;

    /**
     * @var FournisseurPrestationAnnexeStock
     */
    private $periode;

    /**
     * @var Hebergement
     */
    private $hebergement;

    /**
     * Get hebergement
     *
     * @return Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return FournisseurPrestationAnnexeStockHebergement
     */
    public function setHebergement(Hebergement $hebergement)
    {
        $this->hebergement = $hebergement;

        return $this;
    }
}
