<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * PrestationAnnexeTarif
 */
class PrestationAnnexeTarif
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $prixPublic;
    /**
     * @var Collection
     */
    private $periodeValidites;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $param;
    /**
     * @var string
     */
    private $prixCatalogue;
    /**
     * @var string
     */
    private $comMondofute;
    /**
     * @var string
     */
    private $prixAchat;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periodeValidites = new ArrayCollection();
    }

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get prixPublic
     *
     * @return string
     */
    public function getPrixPublic()
    {
        return $this->prixPublic;
    }

    /**
     * Set prixPublic
     *
     * @param string $prixPublic
     *
     * @return PrestationAnnexeTarif
     */
    public function setPrixPublic($prixPublic)
    {
        $this->prixPublic = $prixPublic;

        return $this;
    }

    /**
     * Add periodeValidite
     *
     * @param PeriodeValidite $periodeValidite
     *
     * @return PrestationAnnexeTarif
     */
    public function addPeriodeValidite(PeriodeValidite $periodeValidite)
    {
        $this->periodeValidites[] = $periodeValidite->setTarif($this);

        return $this;
    }

    /**
     * Remove periodeValidite
     *
     * @param PeriodeValidite $periodeValidite
     */
    public function removePeriodeValidite(PeriodeValidite $periodeValidite)
    {
        $this->periodeValidites->removeElement($periodeValidite);
    }

    /**
     * Get periodeValidites
     *
     * @return Collection
     */
    public function getPeriodeValidites()
    {
        return $this->periodeValidites;
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
     * @return PrestationAnnexeTarif
     */
    public function setParam(FournisseurPrestationAnnexeParam $param = null)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get prixCatalogue
     *
     * @return string
     */
    public function getPrixCatalogue()
    {
        return $this->prixCatalogue;
    }

    /**
     * Set prixCatalogue
     *
     * @param string $prixCatalogue
     *
     * @return PrestationAnnexeTarif
     */
    public function setPrixCatalogue($prixCatalogue)
    {
        $this->prixCatalogue = $prixCatalogue;

        return $this;
    }

    /**
     * Get comMondofute
     *
     * @return string
     */
    public function getComMondofute()
    {
        return $this->comMondofute;
    }

    /**
     * Set comMondofute
     *
     * @param string $comMondofute
     *
     * @return PrestationAnnexeTarif
     */
    public function setComMondofute($comMondofute)
    {
        $this->comMondofute = $comMondofute;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return string
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }

    /**
     * Set prixAchat
     *
     * @param string $prixAchat
     *
     * @return PrestationAnnexeTarif
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }
}
