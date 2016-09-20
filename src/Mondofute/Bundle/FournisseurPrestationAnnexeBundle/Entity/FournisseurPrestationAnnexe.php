<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\ModeAffectation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeTraduction;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;

/**
 * FournisseurPrestationAnnexe
 */
class FournisseurPrestationAnnexe
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
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var PrestationAnnexe
     */
    private $prestationAnnexe;
    /**
     * @var integer
     */
    private $modeAffectation = ModeAffectation::Station;
    /**
     * @var Collection
     */
    private $prestationAnnexeFournisseurs;
    /**
     * @var Collection
     */
    private $prestationAnnexeHebergements;
    /**
     * @var Collection
     */
    private $prestationAnnexeLogements;
    /**
     * @var Collection
     */
    private $prestationAnnexeStations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
        $this->traductions = new ArrayCollection();
        $this->prestationAnnexeFournisseurs = new ArrayCollection();
        $this->prestationAnnexeHebergements = new ArrayCollection();
        $this->prestationAnnexeLogements = new ArrayCollection();
        $this->prestationAnnexeStations = new ArrayCollection();
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
     * @return FournisseurPrestationAnnexe
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return FournisseurPrestationAnnexe
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
     * @return FournisseurPrestationAnnexe
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
     * @return FournisseurPrestationAnnexe
     */
    public function addTarif(PrestationAnnexeTarif $tarif)
    {
        $this->tarifs[] = $tarif->setPrestationAnnexe($this);

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
     * @return FournisseurPrestationAnnexe
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param FournisseurPrestationAnnexeTraduction $traduction
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addTraduction(FournisseurPrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param FournisseurPrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(FournisseurPrestationAnnexeTraduction $traduction)
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
     * Get prestationAnnexe
     *
     * @return PrestationAnnexe
     */
    public function getPrestationAnnexe()
    {
        return $this->prestationAnnexe;
    }

    /**
     * Set prestationAnnexe
     *
     * @param PrestationAnnexe $prestationAnnexe
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setPrestationAnnexe(PrestationAnnexe $prestationAnnexe = null)
    {
        $this->prestationAnnexe = $prestationAnnexe;

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
     * @return FournisseurPrestationAnnexe
     */
    public function setModeAffectation($modeAffectation)
    {
        $this->modeAffectation = $modeAffectation;

        return $this;
    }

    /**
     * Add prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addPrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs[] = $prestationAnnexeFournisseur->setFournisseurPrestationAnnexe($this);

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
     * Add prestationAnnexeHebergement
     *
     * @param PrestationAnnexeHebergement $prestationAnnexeHebergement
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addPrestationAnnexeHebergement(PrestationAnnexeHebergement $prestationAnnexeHebergement)
    {
        $this->prestationAnnexeHebergements[] = $prestationAnnexeHebergement->setFournisseurPrestationAnnexe($this);

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
     * @return FournisseurPrestationAnnexe
     */
    public function addPrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements[] = $prestationAnnexeLogement->setFournisseurPrestationAnnexe($this);

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
     * Add prestationAnnexeStation
     *
     * @param PrestationAnnexeStation $prestationAnnexeStation
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addPrestationAnnexeStation(PrestationAnnexeStation $prestationAnnexeStation)
    {
        $this->prestationAnnexeStations[] = $prestationAnnexeStation->setFournisseurPrestationAnnexe($this);

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
}
