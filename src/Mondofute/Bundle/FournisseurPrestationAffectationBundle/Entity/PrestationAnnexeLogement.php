<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\LogementBundle\Entity\Logement;

/**
 * PrestationAnnexeLogement
 */
class PrestationAnnexeLogement extends FournisseurPrestationAffectation
{
    /**
     * @var PrestationAnnexeLogementUnifie
     */
    private $prestationAnnexeLogementUnifie;

    /**
     * @var Logement
     */
    private $logement;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $param;

    /**
     * Get prestationAnnexeLogementUnifie
     *
     * @return PrestationAnnexeLogementUnifie
     */
    public function getPrestationAnnexeLogementUnifie()
    {
        return $this->prestationAnnexeLogementUnifie;
    }

    /**
     * Set prestationAnnexeLogementUnifie
     *
     * @param PrestationAnnexeLogementUnifie $prestationAnnexeLogementUnifie
     *
     * @return PrestationAnnexeLogement
     */
    public function setPrestationAnnexeLogementUnifie(PrestationAnnexeLogementUnifie $prestationAnnexeLogementUnifie = null)
    {
        $this->prestationAnnexeLogementUnifie = $prestationAnnexeLogementUnifie;

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
     * @return PrestationAnnexeLogement
     */
    public function setLogement(Logement $logement = null)
    {
        $this->logement = $logement;

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
     * @return PrestationAnnexeLogement
     */
    public function setParam(FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }
}
