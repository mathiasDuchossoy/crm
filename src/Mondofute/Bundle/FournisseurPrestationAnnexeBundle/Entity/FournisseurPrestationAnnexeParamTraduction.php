<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var string
     */
    private $libelleParam;

    /**
     * @var string
     */
    private $libelleFournisseurPrestationAnnexeParam;

    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam
     */
    private $param;

    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;


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
     * Get libelleParam
     *
     * @return string
     */
    public function getLibelleParam()
    {
        return $this->libelleParam;
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
     * Get libelleFournisseurPrestationAnnexeParam
     *
     * @return string
     */
    public function getLibelleFournisseurPrestationAnnexeParam()
    {
        return $this->libelleFournisseurPrestationAnnexeParam;
    }

    /**
     * Set param
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam $param
     *
     * @return FournisseurPrestationAnnexeParamTraduction
     */
    public function setParam(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get param
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return FournisseurPrestationAnnexeParamTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }
}
