<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Decote
 */
class Decote
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
     * @var DecoteUnifie
     */
    private $decoteUnifie;
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
    private $decoteTypeAffectations;
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
    private $decoteFournisseurPrestationAnnexes;
    /**
     * @var Collection
     */
    private $decoteFamillePrestationAnnexes;
    /**
     * @var Collection
     */
    private $decoteFournisseurs;
    /**
     * @var Collection
     */
    private $decoteHebergements;
    /**
     * @var Collection
     */
    private $periodeValidites;
    /**
     * @var Collection
     */
    private $logementPeriodes;
    /**
     * @var Collection
     */
    private $decoteStations;
    /**
     * @var DecotePeriodeValiditeDate
     */
    private $decotePeriodeValiditeDate;
    /**
     * @var DecotePeriodeValiditeJour
     */
    private $decotePeriodeValiditeJour;
    /**
     * @var integer
     */
    private $typePeriodeValidite;
    /**
     * @var DecotePeriodeSejourDate
     */
    private $decotePeriodeSejourDate;
    /**
     * @var integer
     */
    private $type;
    /**
     * @var Collection
     */
    private $canalDecotes;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Collection
     */
    private $decoteLogements;
    /**
     * @var integer
     */
    private $variante;
    /**
     * @var integer
     */
    private $choixVariante1;
    /**
     * @var integer
     */
    private $applicationRemise;
    /**
     * @var boolean
     */
    private $compteARebours = false;
    /**
     * @var integer
     */
    private $stock;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->decoteTypeAffectations = new ArrayCollection();
        $this->typeFournisseurs = new ArrayCollection();
        $this->decoteFournisseurPrestationAnnexes = new ArrayCollection();
        $this->decoteFamillePrestationAnnexes = new ArrayCollection();
        $this->decoteFournisseurs = new ArrayCollection();
        $this->decoteHebergements = new ArrayCollection();
        $this->periodeValidites = new ArrayCollection();
        $this->logementPeriodes = new ArrayCollection();
        $this->decoteStations = new ArrayCollection();
        $this->canalDecotes = new ArrayCollection();
        $this->traductions = new ArrayCollection();
        $this->decoteLogements = new ArrayCollection();
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
     * @return Decote
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
     * @return Decote
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
     * @return Decote
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
     * @return Decote
     */
    public function setValeurRemise($valeurRemise)
    {
        $this->valeurRemise = $valeurRemise;

        return $this;
    }

    /**
     * Get decoteUnifie
     *
     * @return DecoteUnifie
     */
    public function getDecoteUnifie()
    {
        return $this->decoteUnifie;
    }

    /**
     * Set decoteUnifie
     *
     * @param DecoteUnifie $decoteUnifie
     *
     * @return Decote
     */
    public function setDecoteUnifie(DecoteUnifie $decoteUnifie = null)
    {
        $this->decoteUnifie = $decoteUnifie;

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
     * @return Decote
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
     * @return Decote
     */
    public function setTypePeriodeSejour($typePeriodeSejour)
    {
        $this->typePeriodeSejour = $typePeriodeSejour;

        return $this;
    }

    /**
     * Add decoteTypeAffectation
     *
     * @param DecoteTypeAffectation $decoteTypeAffectation
     *
     * @return Decote
     */
    public function addDecoteTypeAffectation(DecoteTypeAffectation $decoteTypeAffectation)
    {
        $this->decoteTypeAffectations[] = $decoteTypeAffectation->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteTypeAffectation
     *
     * @param DecoteTypeAffectation $decoteTypeAffectation
     */
    public function removeDecoteTypeAffectation(DecoteTypeAffectation $decoteTypeAffectation)
    {
        $this->decoteTypeAffectations->removeElement($decoteTypeAffectation);
    }

    /**
     * Get decoteTypeAffectations
     *
     * @return Collection
     */
    public function getDecoteTypeAffectations()
    {
        return $this->decoteTypeAffectations;
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
     * @return Decote
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
     * @return Decote
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
     * Add decoteFournisseurPrestationAnnex
     *
     * @param DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnex
     *
     * @return Decote
     */
    public function addDecoteFournisseurPrestationAnnex(DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnex)
    {
        $this->decoteFournisseurPrestationAnnexes[] = $decoteFournisseurPrestationAnnex->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteFournisseurPrestationAnnex
     *
     * @param DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnex
     */
    public function removeDecoteFournisseurPrestationAnnex(DecoteFournisseurPrestationAnnexe $decoteFournisseurPrestationAnnex)
    {
        $this->decoteFournisseurPrestationAnnexes->removeElement($decoteFournisseurPrestationAnnex);
    }

    /**
     * Get decoteFournisseurPrestationAnnexes
     *
     * @return Collection
     */
    public function getDecoteFournisseurPrestationAnnexes()
    {
        return $this->decoteFournisseurPrestationAnnexes;
    }

    /**
     * Add decoteFamillePrestationAnnex
     *
     * @param DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex
     *
     * @return Decote
     */
    public function addDecoteFamillePrestationAnnex(DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex)
    {
        $this->decoteFamillePrestationAnnexes[] = $decoteFamillePrestationAnnex->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteFamillePrestationAnnex
     *
     * @param DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex
     */
    public function removeDecoteFamillePrestationAnnex(DecoteFamillePrestationAnnexe $decoteFamillePrestationAnnex)
    {
        $this->decoteFamillePrestationAnnexes->removeElement($decoteFamillePrestationAnnex);
    }

    /**
     * Get decoteFamillePrestationAnnexes
     *
     * @return Collection
     */
    public function getDecoteFamillePrestationAnnexes()
    {
        return $this->decoteFamillePrestationAnnexes;
    }

    /**
     * Add decoteFournisseur
     *
     * @param DecoteFournisseur $decoteFournisseur
     *
     * @return Decote
     */
    public function addDecoteFournisseur(DecoteFournisseur $decoteFournisseur)
    {
        $this->decoteFournisseurs[] = $decoteFournisseur->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteFournisseur
     *
     * @param DecoteFournisseur $decoteFournisseur
     */
    public function removeDecoteFournisseur(DecoteFournisseur $decoteFournisseur)
    {
        $this->decoteFournisseurs->removeElement($decoteFournisseur);
    }

    /**
     * Get decoteFournisseurs
     *
     * @return Collection
     */
    public function getDecoteFournisseurs()
    {
        return $this->decoteFournisseurs;
    }

    /**
     * Add decoteHebergement
     *
     * @param DecoteHebergement $decoteHebergement
     *
     * @return Decote
     */
    public function addDecoteHebergement(DecoteHebergement $decoteHebergement)
    {
        $this->decoteHebergements[] = $decoteHebergement->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteHebergement
     *
     * @param DecoteHebergement $decoteHebergement
     */
    public function removeDecoteHebergement(DecoteHebergement $decoteHebergement)
    {
        $this->decoteHebergements->removeElement($decoteHebergement);
    }

    /**
     * Get decoteHebergements
     *
     * @return Collection
     */
    public function getDecoteHebergements()
    {
        return $this->decoteHebergements;
    }

    /**
     * Add periodeValidite
     *
     * @param PeriodeValidite $periodeValidite
     *
     * @return Decote
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

    /**
     * Add logementPeriode
     *
     * @param DecoteLogementPeriode $logementPeriode
     *
     * @return Decote
     */
    public function addLogementPeriode(DecoteLogementPeriode $logementPeriode)
    {
        $this->logementPeriodes[] = $logementPeriode->setDecote($this);

        return $this;
    }

    /**
     * Remove logementPeriode
     *
     * @param DecoteLogementPeriode $logementPeriode
     */
    public function removeLogementPeriode(DecoteLogementPeriode $logementPeriode)
    {
        $this->logementPeriodes->removeElement($logementPeriode);
    }

    /**
     * Get logementPeriodes
     *
     * @return Collection
     */
    public function getLogementPeriodes()
    {
        return $this->logementPeriodes;
    }

    /**
     * Add decoteStation
     *
     * @param DecoteStation $decoteStation
     *
     * @return Decote
     */
    public function addDecoteStation(DecoteStation $decoteStation)
    {
        $this->decoteStations[] = $decoteStation->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteStation
     *
     * @param DecoteStation $decoteStation
     */
    public function removeDecoteStation(DecoteStation $decoteStation)
    {
        $this->decoteStations->removeElement($decoteStation);
    }

    /**
     * Get decoteStations
     *
     * @return Collection
     */
    public function getDecoteStations()
    {
        return $this->decoteStations;
    }

    /**
     * Get decotePeriodeValiditeDate
     *
     * @return DecotePeriodeValiditeDate
     */
    public function getDecotePeriodeValiditeDate()
    {
        return $this->decotePeriodeValiditeDate;
    }

    /**
     * Set decotePeriodeValiditeDate
     *
     * @param DecotePeriodeValiditeDate $decotePeriodeValiditeDate
     *
     * @return Decote
     */
    public function setDecotePeriodeValiditeDate(DecotePeriodeValiditeDate $decotePeriodeValiditeDate = null)
    {
        $this->decotePeriodeValiditeDate = $decotePeriodeValiditeDate;

        return $this;
    }

    /**
     * Get decotePeriodeValiditeJour
     *
     * @return DecotePeriodeValiditeJour
     */
    public function getDecotePeriodeValiditeJour()
    {
        return $this->decotePeriodeValiditeJour;
    }

    /**
     * Set decotePeriodeValiditeJour
     *
     * @param DecotePeriodeValiditeJour $decotePeriodeValiditeJour
     *
     * @return Decote
     */
    public function setDecotePeriodeValiditeJour(DecotePeriodeValiditeJour $decotePeriodeValiditeJour = null)
    {
        $this->decotePeriodeValiditeJour = $decotePeriodeValiditeJour;

        return $this;
    }

    /**
     * Get typePeriodeValidite
     *
     * @return integer
     */
    public function getTypePeriodeValidite()
    {
        return $this->typePeriodeValidite;
    }

    /**
     * Set typePeriodeValidite
     *
     * @param integer $typePeriodeValidite
     *
     * @return Decote
     */
    public function setTypePeriodeValidite($typePeriodeValidite)
    {
        $this->typePeriodeValidite = $typePeriodeValidite;

        return $this;
    }

    /**
     * Get decotePeriodeSejourDate
     *
     * @return DecotePeriodeSejourDate
     */
    public function getDecotePeriodeSejourDate()
    {
        return $this->decotePeriodeSejourDate;
    }

    /**
     * Set decotePeriodeSejourDate
     *
     * @param DecotePeriodeSejourDate $decotePeriodeSejourDate
     *
     * @return Decote
     */
    public function setDecotePeriodeSejourDate(DecotePeriodeSejourDate $decotePeriodeSejourDate = null)
    {
        $this->decotePeriodeSejourDate = $decotePeriodeSejourDate;

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
     * @return Decote
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Add canalDecote
     *
     * @param CanalDecote $canalDecote
     *
     * @return Decote
     */
    public function addCanalDecote(CanalDecote $canalDecote)
    {
        $this->canalDecotes[] = $canalDecote;

        return $this;
    }

    /**
     * Remove canalDecote
     *
     * @param CanalDecote $canalDecote
     */
    public function removeCanalDecote(CanalDecote $canalDecote)
    {
        $this->canalDecotes->removeElement($canalDecote);
    }

    /**
     * Get canalDecotes
     *
     * @return Collection
     */
    public function getCanalDecotes()
    {
        return $this->canalDecotes;
    }

    /**
     * Add traduction
     *
     * @param DecoteTraduction $traduction
     *
     * @return Decote
     */
    public function addTraduction(DecoteTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDecote($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param DecoteTraduction $traduction
     */
    public function removeTraduction(DecoteTraduction $traduction)
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
     * Add decoteLogement
     *
     * @param DecoteLogement $decoteLogement
     *
     * @return Decote
     */
    public function addDecoteLogement(DecoteLogement $decoteLogement)
    {
        $this->decoteLogements[] = $decoteLogement->setDecote($this);

        return $this;
    }

    /**
     * Remove decoteLogement
     *
     * @param DecoteLogement $decoteLogement
     */
    public function removeDecoteLogement(DecoteLogement $decoteLogement)
    {
        $this->decoteLogements->removeElement($decoteLogement);
    }

    /**
     * Get decoteLogements
     *
     * @return Collection
     */
    public function getDecoteLogements()
    {
        return $this->decoteLogements;
    }

    /**
     * Get variante
     *
     * @return integer
     */
    public function getVariante()
    {
        return $this->variante;
    }

    /**
     * Set variante
     *
     * @param integer $variante
     *
     * @return Decote
     */
    public function setVariante($variante)
    {
        $this->variante = $variante;

        return $this;
    }

    /**
     * Get choixVariante1
     *
     * @return integer
     */
    public function getChoixVariante1()
    {
        return $this->choixVariante1;
    }

    /**
     * Set choixVariante1
     *
     * @param integer $choixVariante1
     *
     * @return Decote
     */
    public function setChoixVariante1($choixVariante1 = null)
    {
        $this->choixVariante1 = $choixVariante1;

        return $this;
    }

    /**
     * Get applicationRemise
     *
     * @return integer
     */
    public function getApplicationRemise()
    {
        return $this->applicationRemise;
    }

    /**
     * Set applicationRemise
     *
     * @param integer $applicationRemise
     *
     * @return Decote
     */
    public function setApplicationRemise($applicationRemise = null)
    {
        $this->applicationRemise = $applicationRemise;

        return $this;
    }

    /**
     * Get compteARebours
     *
     * @return boolean
     */
    public function getCompteARebours()
    {
        return $this->compteARebours;
    }

    /**
     * Set compteARebours
     *
     * @param boolean $compteARebours
     *
     * @return Decote
     */
    public function setCompteARebours($compteARebours = null)
    {
        $this->compteARebours = $compteARebours;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return Decote
     */
    public function setStock($stock = null)
    {
        $this->stock = $stock;

        return $this;
    }
}
