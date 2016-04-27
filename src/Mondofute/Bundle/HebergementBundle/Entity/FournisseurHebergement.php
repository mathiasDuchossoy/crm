<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Fixe;
use Nucleus\MoyenComBundle\Entity\Mobile;


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
     * @var Fixe
     */
    private $telFixe;
    /**
     * @var Mobile
     */
    private $telMobile;
    /**
     * @var Adresse
     */
    private $adresse;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef
     */
    private $remiseClef;

    /**
     * FournisseurHebergement constructor.
     */
    public function __construct()
    {
        $this->adresse = new Adresse();
        $this->adresse->setCoordonneeGPS(new CoordonneesGPS());
        $this->adresse->setDateCreation();
        $this->telFixe = new Fixe();
        $this->telFixe->setDateCreation();
        $this->telMobile = new Mobile();
        $this->telMobile->setDateCreation();

//        $coordonneesGPSFournisseurSite = new CoordonneesGPS();
//        $adresseFournisseurSite->setCoordonneeGPS($coordonneesGPSFournisseurSite);
//        $adresseFournisseurSite->setDateCreation();
//        $telFixeFournisseurSite = new Fixe();
//        $telFixeFournisseurSite->setDateCreation();
//        $telMobileFournisseurSite = new Mobile();
//        $telMobileFournisseurSite->setDateCreation();
//        $fournisseurSite->setAdresse($adresseFournisseurSite);
//        $fournisseurSite->setTelFixe($telFixeFournisseurSite);
//        $fournisseurSite->setTelMobile($telMobileFournisseurSite);
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
     * @return Fixe
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * Set telFixe
     *
     * @param Fixe $telFixe
     *
     * @return FournisseurHebergement
     */
    public function setTelFixe(Fixe $telFixe = null)
    {
        $this->telFixe = $telFixe;

        return $this;
    }

    /**
     * Get telMobile
     *
     * @return Mobile
     */
    public function getTelMobile()
    {
        return $this->telMobile;
    }

    /**
     * Set telMobile
     *
     * @param Mobile $telMobile
     *
     * @return FournisseurHebergement
     */
    public function setTelMobile(Mobile $telMobile = null)
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
     * @param \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction $traduction
     *
     * @return FournisseurHebergement
     */
    public function addTraduction(
        \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction $traduction
    ) {
        $this->traductions[] = $traduction->setFournisseurHebergement($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction $traduction
     */
    public function removeTraduction(
        \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction $traduction
    ) {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * Get remiseClef
     *
     * @return \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef
     */
    public function getRemiseClef()
    {
        return $this->remiseClef;
    }

    /**
     * Set remiseClef
     *
     * @param \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef $remiseClef
     *
     * @return FournisseurHebergement
     */
    public function setRemiseClef(\Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef $remiseClef = null)
    {
        $this->remiseClef = $remiseClef;

        return $this;
    }
}
