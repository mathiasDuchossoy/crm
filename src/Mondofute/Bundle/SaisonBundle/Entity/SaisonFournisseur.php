<?php

namespace Mondofute\Bundle\SaisonBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * SaisonFournisseur
 */
class SaisonFournisseur
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $contrat;

    /**
     * @var integer
     */
    private $stock;

    /**
     * @var integer
     */
    private $flux;

    /**
     * @var integer
     */
    private $valideOptions;

    /**
     * @var integer
     */
    private $earlybooking;

    /**
     * @var string
     */
    private $conditionEarlybooking;

    /**
     * @var integer
     */
    private $ficheTechniques = 0;

    /**
     * @var integer
     */
    private $tarifTechniques = 0;

    /**
     * @var integer
     */
    private $photosTechniques = 0;

    /**
     * @var Utilisateur
     */
    private $agentSaisie;

    /**
     * @var Utilisateur
     */
    private $agentProd;

    /**
     * @var Utilisateur
     */
    private $agentMaJProd;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var Saison
     */
    private $saison;

    /**
     * Get id
     *
     * @return integer
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
     * Get contrat
     *
     * @return integer
     */
    public function getContrat()
    {
        return $this->contrat;
    }

    /**
     * Set contrat
     *
     * @param integer $contrat
     *
     * @return SaisonFournisseur
     */
    public function setContrat($contrat)
    {
        $this->contrat = $contrat;

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
     * @return SaisonFournisseur
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get flux
     *
     * @return integer
     */
    public function getFlux()
    {
        return $this->flux;
    }

    /**
     * Set flux
     *
     * @param integer $flux
     *
     * @return SaisonFournisseur
     */
    public function setFlux($flux)
    {
        $this->flux = $flux;

        return $this;
    }

    /**
     * Get valideOptions
     *
     * @return integer
     */
    public function getValideOptions()
    {
        return $this->valideOptions;
    }

    /**
     * Set valideOptions
     *
     * @param integer $valideOptions
     *
     * @return SaisonFournisseur
     */
    public function setValideOptions($valideOptions)
    {
        $this->valideOptions = $valideOptions;

        return $this;
    }

    /**
     * Get earlybooking
     *
     * @return integer
     */
    public function getEarlybooking()
    {
        return $this->earlybooking;
    }

    /**
     * Set earlybooking
     *
     * @param integer $earlybooking
     *
     * @return SaisonFournisseur
     */
    public function setEarlybooking($earlybooking)
    {
        $this->earlybooking = $earlybooking;

        return $this;
    }

    /**
     * Get conditionEarlybooking
     *
     * @return string
     */
    public function getConditionEarlybooking()
    {
        return $this->conditionEarlybooking;
    }

    /**
     * Set conditionEarlybooking
     *
     * @param string $conditionEarlybooking
     *
     * @return SaisonFournisseur
     */
    public function setConditionEarlybooking($conditionEarlybooking)
    {
        $this->conditionEarlybooking = $conditionEarlybooking;

        return $this;
    }

    /**
     * Get ficheTechniques
     *
     * @return integer
     */
    public function getFicheTechniques()
    {
        return $this->ficheTechniques;
    }

    /**
     * Set ficheTechniques
     *
     * @param integer $ficheTechniques
     *
     * @return SaisonFournisseur
     */
    public function setFicheTechniques($ficheTechniques)
    {
        $this->ficheTechniques = $ficheTechniques;

        return $this;
    }

    /**
     * Get tarifTechniques
     *
     * @return integer
     */
    public function getTarifTechniques()
    {
        return $this->tarifTechniques;
    }

    /**
     * Set tarifTechniques
     *
     * @param integer $tarifTechniques
     *
     * @return SaisonFournisseur
     */
    public function setTarifTechniques($tarifTechniques)
    {
        $this->tarifTechniques = $tarifTechniques;

        return $this;
    }

    /**
     * Get photosTechniques
     *
     * @return integer
     */
    public function getPhotosTechniques()
    {
        return $this->photosTechniques;
    }

    /**
     * Set photosTechniques
     *
     * @param integer $photosTechniques
     *
     * @return SaisonFournisseur
     */
    public function setPhotosTechniques($photosTechniques)
    {
        $this->photosTechniques = $photosTechniques;

        return $this;
    }

    /**
     * Get agentSaisie
     *
     * @return Utilisateur
     */
    public function getAgentSaisie()
    {
        return $this->agentSaisie;
    }

    /**
     * Set agentSaisie
     *
     * @param Utilisateur $agentSaisie
     *
     * @return SaisonFournisseur
     */
    public function setAgentSaisie(Utilisateur $agentSaisie = null)
    {
        $this->agentSaisie = $agentSaisie;

        return $this;
    }

    /**
     * Get agentProd
     *
     * @return Utilisateur
     */
    public function getAgentProd()
    {
        return $this->agentProd;
    }

    /**
     * Set agentProd
     *
     * @param Utilisateur $agentProd
     *
     * @return SaisonFournisseur
     */
    public function setAgentProd(Utilisateur $agentProd = null)
    {
        $this->agentProd = $agentProd;

        return $this;
    }

    /**
     * Get agentMaJProd
     *
     * @return Utilisateur
     */
    public function getAgentMaJProd()
    {
        return $this->agentMaJProd;
    }

    /**
     * Set agentMaJProd
     *
     * @param Utilisateur $agentMaJProd
     *
     * @return SaisonFournisseur
     */
    public function setAgentMaJProd(Utilisateur $agentMaJProd = null)
    {
        $this->agentMaJProd = $agentMaJProd;

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
     * @return SaisonFournisseur
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get saison
     *
     * @return Saison
     */
    public function getSaison()
    {
        return $this->saison;
    }

    /**
     * Set saison
     *
     * @param Saison $saison
     *
     * @return SaisonFournisseur
     */
    public function setSaison(Saison $saison = null)
    {
        $this->saison = $saison;

        return $this;
    }
}
