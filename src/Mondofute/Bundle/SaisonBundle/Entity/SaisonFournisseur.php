<?php

namespace Mondofute\Bundle\SaisonBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
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
     * @var Utilisateur
     */
    private $agentMaJSaisie;
    /**
     * @var integer
     */
    private $nbHebergementsActive = 0;

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

    /**
     * Get agentMaJSaisie
     *
     * @return Utilisateur
     */
    public function getAgentMaJSaisie()
    {
        return $this->agentMaJSaisie;
    }

    /**
     * Set agentMaJSaisie
     *
     * @param Utilisateur $agentMaJSaisie
     *
     * @return SaisonFournisseur
     */
    public function setAgentMaJSaisie(Utilisateur $agentMaJSaisie = null)
    {
        $this->agentMaJSaisie = $agentMaJSaisie;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbHebergementsActive($site = 'crm')
    {
        // compter le nombre d'hébergement actif pour cette saison pour ce fournisseur
        // parcourir tout les hebergement de ce fournisseur
        // récupérer le saisonHebergement concerné et vérifier si il est actif pour alimenter la variable de comptage
        /** @var FournisseurHebergement $fournisseurHebergement */
        /** @var Hebergement $hebergement */
        foreach ($this->fournisseur->getHebergements() as $fournisseurHebergement) {
            foreach ($fournisseurHebergement->getHebergement()->getHebergements() as $hebergement) {
                if ($hebergement->getSite()->getLibelle() == $site) {
                    /** @var SaisonHebergement $saisonHebergement */
                    $saisonHebergement = $hebergement->getSaisonHebergements()->filter(function (SaisonHebergement $element) {
                        return $element->getSaison() == $this->saison;
                    })->first();
                    if ($saisonHebergement->getActif()) {
                        $this->nbHebergementsActive++;
                    }
                }
            }
        }

        return $this->nbHebergementsActive;
    }

    /**
     * @param int $nbHebergementsActive
     *
     * @return SaisonFournisseur
     */
    public function setNbHebergementsActive($nbHebergementsActive = 0)
    {
        $this->nbHebergementsActive = $nbHebergementsActive;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbHebergements()
    {
        return count($this->fournisseur->getHebergements());
    }

}
