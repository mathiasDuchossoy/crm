<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * FournisseurPrestationAnnexeParamTraduction
 */
class FournisseurPrestationAnnexeParamTraduction
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $libelleParam;
    /**
     * @var string
     */
    private $libelleFournisseurPrestationAnnexeParam;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $param;
    /**
     * @var Langue
     */
    private $langue;

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
     * Get libelleParam
     *
     * @return string
     */
    public function getLibelleParam()
    {
        return $this->libelleParam;
    }

    /**
     * Set libelleParam
     *
     * @param string $libelleParam
     *
     * @return FournisseurPrestationAnnexeParamTraduction
     */
    public function setLibelleParam($libelleParam)
    {
        $this->libelleParam = $libelleParam;

        return $this;
    }

    /**
     * Get libelleFournisseurPrestationAnnexeParam
     *
     * @return string
     */
    public function getLibelleFournisseurPrestationAnnexeParam()
    {
        return $this->libelleFournisseurPrestationAnnexeParam;
    }

    /**
     * Set libelleFournisseurPrestationAnnexeParam
     *
     * @param string $libelleFournisseurPrestationAnnexeParam
     *
     * @return FournisseurPrestationAnnexeParamTraduction
     */
    public function setLibelleFournisseurPrestationAnnexeParam($libelleFournisseurPrestationAnnexeParam)
    {
        $this->libelleFournisseurPrestationAnnexeParam = $libelleFournisseurPrestationAnnexeParam;

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
     * @return FournisseurPrestationAnnexeParamTraduction
     */
    public function setParam(FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return FournisseurPrestationAnnexeParamTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
