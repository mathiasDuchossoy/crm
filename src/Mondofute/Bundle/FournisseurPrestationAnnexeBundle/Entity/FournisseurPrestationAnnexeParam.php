<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation;

/**
 * FournisseurPrestationAnnexeParam
 */
class FournisseurPrestationAnnexeParam
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $type;
    /**
     * @var integer
     */
    private $modeAffectation;
    /**
     * @var FournisseurPrestationAnnexeCapacite
     */
    private $capacite;
    /**
     * @var FournisseurPrestationAnnexeDureeSejour
     */
    private $dureeSejour;
    /**
     * @var Collection
     */
    private $tarifs;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Collection
     */
    private $prestationAnnexeFournisseurs;
    /**
     * @var Collection
     */
    private $prestationAnnexeStations;
    /**
     * @var Collection
     */
    private $prestationAnnexeHebergements;
    /**
     * @var Collection
     */
    private $prestationAnnexeLogements;
    /**
     * @var FournisseurPrestationAnnexe
     */
    private $fournisseurPrestationAnnexe;
    /**
     * @var integer
     */
    private $forfaitQuantiteType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
        $this->traductions = new ArrayCollection();
        $this->prestationAnnexeFournisseurs = new ArrayCollection();
        $this->prestationAnnexeStations = new ArrayCollection();
        $this->prestationAnnexeHebergements = new ArrayCollection();
        $this->prestationAnnexeLogements = new ArrayCollection();
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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get modeAffectation
     *
     * @return integer
     */
    public function getModeAffectation()
    {
        return $this->modeAffectation;
    }

    /**
     * Set modeAffectation
     *
     * @param integer $modeAffectation
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function setModeAffectation($modeAffectation)
    {
        $this->modeAffectation = $modeAffectation;

        return $this;
    }

    /**
     * Get capacite
     *
     * @return FournisseurPrestationAnnexeCapacite
     */
    public function getCapacite()
    {
        return $this->capacite;
    }

    /**
     * Set capacite
     *
     * @param FournisseurPrestationAnnexeCapacite $capacite
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function setCapacite(FournisseurPrestationAnnexeCapacite $capacite = null)
    {
        $this->capacite = $capacite;

        return $this;
    }

    /**
     * Get dureeSejour
     *
     * @return FournisseurPrestationAnnexeDureeSejour
     */
    public function getDureeSejour()
    {
        return $this->dureeSejour;
    }

    /**
     * Set dureeSejour
     *
     * @param FournisseurPrestationAnnexeDureeSejour $dureeSejour
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function setDureeSejour(FournisseurPrestationAnnexeDureeSejour $dureeSejour = null)
    {
        $this->dureeSejour = $dureeSejour;

        return $this;
    }

    /**
     * Add tarif
     *
     * @param PrestationAnnexeTarif $tarif
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function addTarif(PrestationAnnexeTarif $tarif)
    {
        $this->tarifs[] = $tarif->setParam($this);

        return $this;
    }

    /**
     * Remove tarif
     *
     * @param PrestationAnnexeTarif $tarif
     */
    public function removeTarif(PrestationAnnexeTarif $tarif)
    {
        $this->tarifs->removeElement($tarif);
    }

    /**
     * Get tarifs
     *
     * @return Collection
     */
    public function getTarifs()
    {
        return $this->tarifs;
    }

    /**
     * Add traduction
     *
     * @param FournisseurPrestationAnnexeParamTraduction $traduction
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function addTraduction(FournisseurPrestationAnnexeParamTraduction $traduction)
    {
        $this->traductions[] = $traduction->setParam($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param FournisseurPrestationAnnexeParamTraduction $traduction
     */
    public function removeTraduction(FournisseurPrestationAnnexeParamTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * Add prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function addPrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs[] = $prestationAnnexeFournisseur->setParam($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     */
    public function removePrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs->removeElement($prestationAnnexeFournisseur);
    }

    /**
     * Get prestationAnnexeFournisseurs
     *
     * @return Collection
     */
    public function getPrestationAnnexeFournisseurs()
    {
        return $this->prestationAnnexeFournisseurs;
    }

    /**
     * Add prestationAnnexeStation
     *
     * @param PrestationAnnexeStation $prestationAnnexeStation
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function addPrestationAnnexeStation(PrestationAnnexeStation $prestationAnnexeStation)
    {
        $this->prestationAnnexeStations[] = $prestationAnnexeStation->setParam($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeStation
     *
     * @param PrestationAnnexeStation $prestationAnnexeStation
     */
    public function removePrestationAnnexeStation(PrestationAnnexeStation $prestationAnnexeStation)
    {
        $this->prestationAnnexeStations->removeElement($prestationAnnexeStation);
    }

    /**
     * Get prestationAnnexeStations
     *
     * @return Collection
     */
    public function getPrestationAnnexeStations()
    {
        return $this->prestationAnnexeStations;
    }

    /**
     * Add prestationAnnexeHebergement
     *
     * @param PrestationAnnexeHebergement $prestationAnnexeHebergement
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function addPrestationAnnexeHebergement(PrestationAnnexeHebergement $prestationAnnexeHebergement)
    {
        $this->prestationAnnexeHebergements[] = $prestationAnnexeHebergement->setParam($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeHebergement
     *
     * @param PrestationAnnexeHebergement $prestationAnnexeHebergement
     */
    public function removePrestationAnnexeHebergement(PrestationAnnexeHebergement $prestationAnnexeHebergement)
    {
        $this->prestationAnnexeHebergements->removeElement($prestationAnnexeHebergement);
    }

    /**
     * Get prestationAnnexeHebergements
     *
     * @return Collection
     */
    public function getPrestationAnnexeHebergements()
    {
        return $this->prestationAnnexeHebergements;
    }

    /**
     * Add prestationAnnexeLogement
     *
     * @param PrestationAnnexeLogement $prestationAnnexeLogement
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function addPrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements[] = $prestationAnnexeLogement->setParam($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeLogement
     *
     * @param PrestationAnnexeLogement $prestationAnnexeLogement
     */
    public function removePrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements->removeElement($prestationAnnexeLogement);
    }

    /**
     * Get prestationAnnexeLogements
     *
     * @return Collection
     */
    public function getPrestationAnnexeLogements()
    {
        return $this->prestationAnnexeLogements;
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
     * @return FournisseurPrestationAnnexeParam
     */
    public function setFournisseurPrestationAnnexe(FournisseurPrestationAnnexe $fournisseurPrestationAnnexe = null)
    {
        $this->fournisseurPrestationAnnexe = $fournisseurPrestationAnnexe;

        return $this;
    }

    /**
     * Get forfaitQuantiteType
     *
     * @return integer
     */
    public function getForfaitQuantiteType()
    {
        return $this->forfaitQuantiteType;
    }

    /**
     * Set forfaitQuantiteType
     *
     * @param integer $forfaitQuantiteType
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function setForfaitQuantiteType($forfaitQuantiteType = null)
    {
        $this->forfaitQuantiteType = $forfaitQuantiteType;

        return $this;
    }
}
