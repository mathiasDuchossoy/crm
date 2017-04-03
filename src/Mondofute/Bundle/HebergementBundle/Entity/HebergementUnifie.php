<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement;

/**
 * HebergementUnifie
 */
class HebergementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $hebergements;
    /**
     * @var Collection
     */
    private $fournisseurs;
    /**
     * @var ListeService
     */
    private $listeService;
    /**
     * @var Collection
     */
    private $services;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hebergements = new ArrayCollection();
        $this->fournisseurs = new ArrayCollection();
        $this->services = new ArrayCollection();
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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove hebergement
     *
     * @param Hebergement $hebergement
     */
    public function removeHebergement(Hebergement $hebergement)
    {
        $this->hebergements->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return Collection
     */
    public function getHebergements()
    {
        return $this->hebergements;
    }

    /**
     * @param $hebergements
     * @return $this
     */
    public function setHebergements($hebergements)
    {
        $this->getHebergements()->clear();

        foreach ($hebergements as $hebergement) {
            $this->addHebergement($hebergement);
        }
        return $this;
    }

    /**
     * Add hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return HebergementUnifie
     */
    public function addHebergement(Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement->setHebergementUnifie($this);

        return $this;
    }

    /**
     * Add fournisseur
     *
     * @param FournisseurHebergement $fournisseur
     *
     * @return HebergementUnifie
     */
    public function addFournisseur(FournisseurHebergement $fournisseur)
    {
        $this->fournisseurs[] = $fournisseur->setHebergement($this);

        return $this;
    }

    /**
     * Remove fournisseur
     *
     * @param FournisseurHebergement $fournisseur
     */
    public function removeFournisseur(FournisseurHebergement $fournisseur)
    {
        $this->fournisseurs->removeElement($fournisseur);
    }

    /**
     * Get fournisseurs
     *
     * @return Collection
     */
    public function getFournisseurs()
    {
        return $this->fournisseurs;
    }

    public function setFournisseurs($newFournisseurs)
    {
        $this->fournisseurs->clear();
        foreach ($newFournisseurs as $key => $newFournisseur) {
            $this->fournisseurs->set($key, $newFournisseur);
        }
    }

    /**
     * Get listeService
     *
     * @return ListeService
     */
    public function getListeService()
    {
        return $this->listeService;
    }

    /**
     * Set listeService
     *
     * @param ListeService $listeService
     *
     * @return HebergementUnifie
     */
    public function setListeService(ListeService $listeService = null)
    {
        $this->listeService = $listeService;

        return $this;
    }

    /**
     * Add service
     *
     * @param ServiceHebergement $service
     *
     * @return HebergementUnifie
     */
    public function addService(ServiceHebergement $service)
    {
        $this->services[] = $service;

        return $this;
    }

    /**
     * Remove service
     *
     * @param ServiceHebergement $service
     */
    public function removeService(ServiceHebergement $service)
    {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return Collection
     */
    public function getServices()
    {
        return $this->services;
    }
}
