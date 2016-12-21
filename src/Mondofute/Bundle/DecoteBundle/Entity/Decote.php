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
     * @var \Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeSejourDate
     */
    private $decotePeriodeSejourDate;

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
     * @return \Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeSejourDate
     */
    public function getDecotePeriodeSejourDate()
    {
        return $this->decotePeriodeSejourDate;
    }

    /**
     * Set decotePeriodeSejourDate
     *
     * @param \Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeSejourDate $decotePeriodeSejourDate
     *
     * @return Decote
     */
    public function setDecotePeriodeSejourDate(\Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeSejourDate $decotePeriodeSejourDate = null)
    {
        $this->decotePeriodeSejourDate = $decotePeriodeSejourDate;

        return $this;
    }
}
