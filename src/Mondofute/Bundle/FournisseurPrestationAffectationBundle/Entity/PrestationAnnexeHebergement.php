<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;

/**
 * PrestationAnnexeHebergement
 */
class PrestationAnnexeHebergement extends FournisseurPrestationAffectation
{
    /**
     * @var PrestationAnnexeHebergementUnifie
     */
    private $prestationAnnexeHebergementUnifie;

    /**
     * @var Hebergement
     */
    private $hebergement;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;

    /**
     * Get prestationAnnexeHebergementUnifie
     *
     * @return PrestationAnnexeHebergementUnifie
     */
    public function getPrestationAnnexeHebergementUnifie()
    {
        return $this->prestationAnnexeHebergementUnifie;
    }

    /**
     * Set prestationAnnexeHebergementUnifie
     *
     * @param PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie
     *
     * @return PrestationAnnexeHebergement
     */
    public function setPrestationAnnexeHebergementUnifie(PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie = null)
    {
        $this->prestationAnnexeHebergementUnifie = $prestationAnnexeHebergementUnifie;

        return $this;
    }

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
     * @return PrestationAnnexeHebergement
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return PrestationAnnexeHebergement
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
}
