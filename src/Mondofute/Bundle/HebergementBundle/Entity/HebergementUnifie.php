<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $hebergements;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hebergements = new ArrayCollection();
        $this->fournisseurs = new ArrayCollection();

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
     * @return \Doctrine\Common\Collections\Collection
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFournisseurs()
    {
        return $this->fournisseurs;
    }
}
