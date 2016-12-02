<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Promotion
 */
class Promotion
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var string
     */
    private $libelle;
    /**
     * @var integer
     */
    private $typeRemise;
    /**
     * @var string
     */
    private $valeurRemise;
    /**
     * @var PromotionUnifie
     */
    private $promotionUnifie;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var integer
     */
    private $typePeriodeSejour;
    /**
     * @var Collection
     */
    private $promotionTypeAffectations;
    /**
     * @var integer
     */
    private $typeApplication;
    /**
     * @var Collection
     */
    private $typeFournisseurs;
    /**
     * @var Collection
     */
    private $promotionFournisseurPrestationAnnexes;
    /**
     * @var Collection
     */
    private $promotionFamillePrestationAnnexes;
    /**
     * @var Collection
     */
    private $promotionFournisseurs;
    /**
     * @var Collection
     */
    private $promotionHebergements;
    /**
     * @var Collection
     */
    private $promotionLogements;
    /**
     * @var Collection
     */
    private $periodeValidites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->promotionTypeAffectations = new ArrayCollection();
        $this->typeFournisseurs = new ArrayCollection();
        $this->promotionFournisseurPrestationAnnexes = new ArrayCollection();
        $this->promotionFamillePrestationAnnexes = new ArrayCollection();
        $this->promotionFournisseurs = new ArrayCollection();
        $this->promotionHebergements = new ArrayCollection();
        $this->promotionLogements = new ArrayCollection();
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
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Promotion
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Promotion
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get typeRemise
     *
     * @return integer
     */
    public function getTypeRemise()
    {
        return $this->typeRemise;
    }

    /**
     * Set typeRemise
     *
     * @param integer $typeRemise
     *
     * @return Promotion
     */
    public function setTypeRemise($typeRemise)
    {
        $this->typeRemise = $typeRemise;

        return $this;
    }

    /**
     * Get valeurRemise
     *
     * @return string
     */
    public function getValeurRemise()
    {
        return $this->valeurRemise;
    }

    /**
     * Set valeurRemise
     *
     * @param string $valeurRemise
     *
     * @return Promotion
     */
    public function setValeurRemise($valeurRemise)
    {
        $this->valeurRemise = $valeurRemise;

        return $this;
    }

    /**
     * Get promotionUnifie
     *
     * @return PromotionUnifie
     */
    public function getPromotionUnifie()
    {
        return $this->promotionUnifie;
    }

    /**
     * Set promotionUnifie
     *
     * @param PromotionUnifie $promotionUnifie
     *
     * @return Promotion
     */
    public function setPromotionUnifie(PromotionUnifie $promotionUnifie = null)
    {
        $this->promotionUnifie = $promotionUnifie;

        return $this;
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Promotion
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get typePeriodeSejour
     *
     * @return integer
     */
    public function getTypePeriodeSejour()
    {
        return $this->typePeriodeSejour;
    }

    /**
     * Set typePeriodeSejour
     *
     * @param integer $typePeriodeSejour
     *
     * @return Promotion
     */
    public function setTypePeriodeSejour($typePeriodeSejour)
    {
        $this->typePeriodeSejour = $typePeriodeSejour;

        return $this;
    }

    /**
     * Add promotionTypeAffectation
     *
     * @param PromotionTypeAffectation $promotionTypeAffectation
     *
     * @return Promotion
     */
    public function addPromotionTypeAffectation(PromotionTypeAffectation $promotionTypeAffectation)
    {
        $this->promotionTypeAffectations[] = $promotionTypeAffectation->setPromotion($this);

        return $this;
    }

    /**
     * Remove promotionTypeAffectation
     *
     * @param PromotionTypeAffectation $promotionTypeAffectation
     */
    public function removePromotionTypeAffectation(PromotionTypeAffectation $promotionTypeAffectation)
    {
        $this->promotionTypeAffectations->removeElement($promotionTypeAffectation);
    }

    /**
     * Get promotionTypeAffectations
     *
     * @return Collection
     */
    public function getPromotionTypeAffectations()
    {
        return $this->promotionTypeAffectations;
    }

    /**
     * Get typeApplication
     *
     * @return integer
     */
    public function getTypeApplication()
    {
        return $this->typeApplication;
    }

    /**
     * Set typeApplication
     *
     * @param integer $typeApplication
     *
     * @return Promotion
     */
    public function setTypeApplication($typeApplication)
    {
        $this->typeApplication = $typeApplication;

        return $this;
    }

    /**
     * Add typeFournisseur
     *
     * @param FamillePrestationAnnexe $typeFournisseur
     *
     * @return Promotion
     */
    public function addTypeFournisseur(FamillePrestationAnnexe $typeFournisseur)
    {
        $this->typeFournisseurs[] = $typeFournisseur;

        return $this;
    }

    /**
     * Remove typeFournisseur
     *
     * @param FamillePrestationAnnexe $typeFournisseur
     */
    public function removeTypeFournisseur(FamillePrestationAnnexe $typeFournisseur)
    {
        $this->typeFournisseurs->removeElement($typeFournisseur);
    }

    /**
     * Get typeFournisseurs
     *
     * @return Collection
     */
    public function getTypeFournisseurs()
    {
        return $this->typeFournisseurs;
    }

    /**
     * Add promotionFournisseurPrestationAnnex
     *
     * @param PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex
     *
     * @return Promotion
     */
    public function addPromotionFournisseurPrestationAnnex(PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex)
    {
        $this->promotionFournisseurPrestationAnnexes[] = $promotionFournisseurPrestationAnnex->setPromotion($this);

        return $this;
    }

    /**
     * Remove promotionFournisseurPrestationAnnex
     *
     * @param PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex
     */
    public function removePromotionFournisseurPrestationAnnex(PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex)
    {
        $this->promotionFournisseurPrestationAnnexes->removeElement($promotionFournisseurPrestationAnnex);
    }

    /**
     * Get promotionFournisseurPrestationAnnexes
     *
     * @return Collection
     */
    public function getPromotionFournisseurPrestationAnnexes()
    {
        return $this->promotionFournisseurPrestationAnnexes;
    }

    /**
     * Add promotionFamillePrestationAnnex
     *
     * @param PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex
     *
     * @return Promotion
     */
    public function addPromotionFamillePrestationAnnex(PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex)
    {
        $this->promotionFamillePrestationAnnexes[] = $promotionFamillePrestationAnnex->setPromotion($this);

        return $this;
    }

    /**
     * Remove promotionFamillePrestationAnnex
     *
     * @param PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex
     */
    public function removePromotionFamillePrestationAnnex(PromotionFamillePrestationAnnexe $promotionFamillePrestationAnnex)
    {
        $this->promotionFamillePrestationAnnexes->removeElement($promotionFamillePrestationAnnex);
    }

    /**
     * Get promotionFamillePrestationAnnexes
     *
     * @return Collection
     */
    public function getPromotionFamillePrestationAnnexes()
    {
        return $this->promotionFamillePrestationAnnexes;
    }

    /**
     * Add promotionFournisseur
     *
     * @param PromotionFournisseur $promotionFournisseur
     *
     * @return Promotion
     */
    public function addPromotionFournisseur(PromotionFournisseur $promotionFournisseur)
    {
        $this->promotionFournisseurs[] = $promotionFournisseur->setPromotion($this);

        return $this;
    }

    /**
     * Remove promotionFournisseur
     *
     * @param PromotionFournisseur $promotionFournisseur
     */
    public function removePromotionFournisseur(PromotionFournisseur $promotionFournisseur)
    {
        $this->promotionFournisseurs->removeElement($promotionFournisseur);
    }

    /**
     * Get promotionFournisseurs
     *
     * @return Collection
     */
    public function getPromotionFournisseurs()
    {
        return $this->promotionFournisseurs;
    }

    /**
     * Add promotionHebergement
     *
     * @param PromotionHebergement $promotionHebergement
     *
     * @return Promotion
     */
    public function addPromotionHebergement(PromotionHebergement $promotionHebergement)
    {
        $this->promotionHebergements[] = $promotionHebergement->setPromotion($this);

        return $this;
    }

    /**
     * Remove promotionHebergement
     *
     * @param PromotionHebergement $promotionHebergement
     */
    public function removePromotionHebergement(PromotionHebergement $promotionHebergement)
    {
        $this->promotionHebergements->removeElement($promotionHebergement);
    }

    /**
     * Get promotionHebergements
     *
     * @return Collection
     */
    public function getPromotionHebergements()
    {
        return $this->promotionHebergements;
    }

    /**
     * Add promotionLogement
     *
     * @param PromotionLogement $promotionLogement
     *
     * @return Promotion
     */
    public function addPromotionLogement(PromotionLogement $promotionLogement)
    {
        $this->promotionLogements[] = $promotionLogement->setPromotion($this);

        return $this;
    }

    /**
     * Remove promotionLogement
     *
     * @param PromotionLogement $promotionLogement
     */
    public function removePromotionLogement(PromotionLogement $promotionLogement)
    {
        $this->promotionLogements->removeElement($promotionLogement);
    }

    /**
     * Get promotionLogements
     *
     * @return Collection
     */
    public function getPromotionLogements()
    {
        return $this->promotionLogements;
    }

    /**
     * Add periodeValidite
     *
     * @param PeriodeValidite $periodeValidite
     *
     * @return Promotion
     */
    public function addPeriodeValidite(PeriodeValidite $periodeValidite)
    {
        $this->periodeValidites[] = $periodeValidite;

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
}
