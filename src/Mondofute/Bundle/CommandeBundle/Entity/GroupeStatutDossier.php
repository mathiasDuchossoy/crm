<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * GroupeStatutDossier
 */
class GroupeStatutDossier
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $libelle;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Collection
     */
    private $statutDossiers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->statutDossiers = new ArrayCollection();
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
     * @return GroupeStatutDossier
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param GroupeStatutDossierTraduction $traduction
     *
     * @return GroupeStatutDossier
     */
    public function addTraduction(GroupeStatutDossierTraduction $traduction)
    {
        $this->traductions[] = $traduction->setGroupeStatutDossier($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param GroupeStatutDossierTraduction $traduction
     */
    public function removeTraduction(GroupeStatutDossierTraduction $traduction)
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
     * Add statutDossier
     *
     * @param StatutDossier $statutDossier
     *
     * @return GroupeStatutDossier
     */
    public function addStatutDossier(StatutDossier $statutDossier)
    {
        $this->statutDossiers[] = $statutDossier;

        return $this;
    }

    /**
     * Remove statutDossier
     *
     * @param StatutDossier $statutDossier
     */
    public function removeStatutDossier(StatutDossier $statutDossier)
    {
        $this->statutDossiers->removeElement($statutDossier);
    }

    /**
     * Get statutDossiers
     *
     * @return Collection
     */
    public function getStatutDossiers()
    {
        return $this->statutDossiers;
    }
}
