<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

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
     * @var \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    private $hebergement;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;
    /**
     * @var \Nucleus\MoyenComBundle\Entity\Fixe
     */
    private $telFixe;
    /**
     * @var \Nucleus\MoyenComBundle\Entity\Mobile
     */
    private $telMobile;
    /**
     * @var \Nucleus\MoyenComBundle\Entity\Adresse
     */
    private $adresse;

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
     * @return \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergement
     *
     * @return FournisseurHebergement
     */
    public function setHebergement(\Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return FournisseurHebergement
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get telFixe
     *
     * @return \Nucleus\MoyenComBundle\Entity\Fixe
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * Set telFixe
     *
     * @param \Nucleus\MoyenComBundle\Entity\Fixe $telFixe
     *
     * @return FournisseurHebergement
     */
    public function setTelFixe(\Nucleus\MoyenComBundle\Entity\Fixe $telFixe = null)
    {
        $this->telFixe = $telFixe;

        return $this;
    }

    /**
     * Get telMobile
     *
     * @return \Nucleus\MoyenComBundle\Entity\Mobile
     */
    public function getTelMobile()
    {
        return $this->telMobile;
    }

    /**
     * Set telMobile
     *
     * @param \Nucleus\MoyenComBundle\Entity\Mobile $telMobile
     *
     * @return FournisseurHebergement
     */
    public function setTelMobile(\Nucleus\MoyenComBundle\Entity\Mobile $telMobile = null)
    {
        $this->telMobile = $telMobile;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return \Nucleus\MoyenComBundle\Entity\Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresse
     *
     * @param \Nucleus\MoyenComBundle\Entity\Adresse $adresse
     *
     * @return FournisseurHebergement
     */
    public function setAdresse(\Nucleus\MoyenComBundle\Entity\Adresse $adresse = null)
    {
        $this->adresse = $adresse;

        return $this;
    }

}
