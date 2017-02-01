<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * EtatDossier
 */
class EtatDossier
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $commandeEtatDossiers;
    /**
     * @var string
     */
    private $codeCouleur;
    /**
     * @var Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandeEtatDossiers = new ArrayCollection();
        $this->traductions = new ArrayCollection();
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
     * Add commandeEtatDossier
     *
     * @param CommandeEtatDossier $commandeEtatDossier
     *
     * @return EtatDossier
     */
    public function addCommandeEtatDossier(CommandeEtatDossier $commandeEtatDossier)
    {
        $this->commandeEtatDossiers[] = $commandeEtatDossier;

        return $this;
    }

    /**
     * Remove commandeEtatDossier
     *
     * @param CommandeEtatDossier $commandeEtatDossier
     */
    public function removeCommandeEtatDossier(CommandeEtatDossier $commandeEtatDossier)
    {
        $this->commandeEtatDossiers->removeElement($commandeEtatDossier);
    }

    /**
     * Get commandeEtatDossiers
     *
     * @return Collection
     */
    public function getCommandeEtatDossiers()
    {
        return $this->commandeEtatDossiers;
    }

    /**
     * Get codeCouleur
     *
     * @return string
     */
    public function getCodeCouleur()
    {
        return $this->codeCouleur;
    }

    /**
     * Set codeCouleur
     *
     * @param string $codeCouleur
     *
     * @return EtatDossier
     */
    public function setCodeCouleur($codeCouleur)
    {
        $this->codeCouleur = $codeCouleur;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param EtatDossierTraduction $traduction
     *
     * @return EtatDossier
     */
    public function addTraduction(EtatDossierTraduction $traduction)
    {
        $this->traductions[] = $traduction->setEtatDossier($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param EtatDossierTraduction $traduction
     */
    public function removeTraduction(EtatDossierTraduction $traduction)
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
}
