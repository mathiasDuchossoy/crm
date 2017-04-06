<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\TelFixe;
use Nucleus\MoyenComBundle\Entity\TelMobile;


/**
 * FournisseurHebergement
 */
class FournisseurHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var HebergementUnifie
     */
    private $hebergement;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var TelFixe
     */
    private $telFixe;
    /**
     * @var TelMobile
     */
    private $telMobile;
    /**
     * @var Adresse
     */
    private $adresse;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var RemiseClef
     */
    private $remiseClef;
    /**
     * @var Collection
     */
    private $receptions;
    /**
     * @var Collection
     */
    private $logements;
    /**
     * @var Collection
     */
    private $saisonCodePasserelles;

    /**
     * FournisseurHebergement constructor.
     */
    public function __construct()
    {
        $this->adresse = new Adresse();
        $this->adresse->setCoordonneeGps(new CoordonneesGPS());
        $this->adresse->setDateCreation();
        $this->telFixe = new TelFixe();
        $this->telFixe->setDateCreation();
        $this->telMobile = new TelMobile();
        $this->telMobile->setDateCreation();
        $this->receptions = new ArrayCollection();
        $this->logements = new ArrayCollection();
        $this->saisonCodePasserelles = new ArrayCollection();
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
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get hebergement
     *
     * @return HebergementUnifie
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param HebergementUnifie $hebergement
     *
     * @return FournisseurHebergement
     */
    public function setHebergement(HebergementUnifie $hebergement = null)
    {
        $this->hebergement = $hebergement;
//        $this->fournisseur->addHebergement($this);

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
     * @return FournisseurHebergement
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get telFixe
     *
     * @return TelFixe
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * @param TelFixe|null $telFixe
     * @return $this
     */
    public function setTelFixe(TelFixe $telFixe = null)
    {
        $this->telFixe = $telFixe;

        return $this;
    }

    /**
     * Get telMobile
     *
     * @return TelMobile
     */
    public function getTelMobile()
    {
        return $this->telMobile;
    }

    /**
     * @param TelMobile|null $telMobile
     * @return $this
     */
    public function setTelMobile(TelMobile $telMobile = null)
    {
        $this->telMobile = $telMobile;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresse
     *
     * @param Adresse $adresse
     *
     * @return FournisseurHebergement
     */
    public function setAdresse(Adresse $adresse = null)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param FournisseurHebergementTraduction $traduction
     *
     * @return FournisseurHebergement
     */
    public function addTraduction(
        FournisseurHebergementTraduction $traduction
    )
    {
        $this->traductions[] = $traduction->setFournisseurHebergement($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param FournisseurHebergementTraduction $traduction
     */
    public function removeTraduction(
        FournisseurHebergementTraduction $traduction
    )
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
     * Get remiseClef
     *
     * @return RemiseClef
     */
    public function getRemiseClef()
    {
        return $this->remiseClef;
    }

    /**
     * Set remiseClef
     *
     * @param RemiseClef $remiseClef
     *
     * @return FournisseurHebergement
     */
    public function setRemiseClef(RemiseClef $remiseClef = null)
    {
        $this->remiseClef = $remiseClef;

        return $this;
    }

    /**
     * Add reception
     *
     * @param Reception $reception
     *
     * @return FournisseurHebergement
     */
    public function addReception(Reception $reception)
    {
        $this->receptions[] = $reception;

        return $this;
    }

    /**
     * Remove reception
     *
     * @param Reception $reception
     */
    public function removeReception(Reception $reception)
    {
        $this->receptions->removeElement($reception);
    }

    /**
     * Get receptions
     *
     * @return Collection
     */
    public function getReceptions()
    {
        return $this->receptions;
    }

    /**
     * Add logement
     *
     * @param Logement $logement
     *
     * @return FournisseurHebergement
     */
    public function addLogement(Logement $logement)
    {
        $this->logements[] = $logement;

        return $this;
    }

    /**
     * Remove logement
     *
     * @param Logement $logement
     */
    public function removeLogement(Logement $logement)
    {
        $this->logements->removeElement($logement);
    }

    /**
     * Get logements
     *
     * @return Collection
     */
    public function getLogements()
    {
        return $this->logements;
    }

    /**
     * Add saisonCodePasserelle
     *
     * @param SaisonCodePasserelle $saisonCodePasserelle
     *
     * @return FournisseurHebergement
     */
    public function addSaisonCodePasserelle(SaisonCodePasserelle $saisonCodePasserelle)
    {
        $this->saisonCodePasserelles[] = $saisonCodePasserelle;

        return $this;
    }

    /**
     * Remove saisonCodePasserelle
     *
     * @param SaisonCodePasserelle $saisonCodePasserelle
     */
    public function removeSaisonCodePasserelle(SaisonCodePasserelle $saisonCodePasserelle)
    {
        $this->saisonCodePasserelles->removeElement($saisonCodePasserelle);
    }

    /**
     * Get saisonCodePasserelles
     *
     * @return Collection
     */
    public function getSaisonCodePasserelles()
    {
        return $this->saisonCodePasserelles;
    }
}
