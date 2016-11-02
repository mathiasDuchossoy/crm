<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
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
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $param;

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
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return PrestationAnnexeHebergement
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get param
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set param
     *
     * @param FournisseurPrestationAnnexeParam $param
     *
     * @return PrestationAnnexeHebergement
     */
    public function setParam(FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }
}
