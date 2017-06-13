<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * StatutDossier
 */
class StatutDossier
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $codeCouleur;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var GroupeStatutDossier
     */
    private $groupeStatutDossier;
    /**
     * @var Collection
     */
    private $commandeStatutDossier;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->commandeStatutDossier = new ArrayCollection();
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
     * @return StatutDossier
     */
    public function setCodeCouleur($codeCouleur)
    {
        $this->codeCouleur = $codeCouleur;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param StatutDossierTraduction $traduction
     *
     * @return StatutDossier
     */
    public function addTraduction(StatutDossierTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStatutDossier($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param StatutDossierTraduction $traduction
     */
    public function removeTraduction(StatutDossierTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Add commandeStatutDossier
     *
     * @param CommandeStatutDossier $commandeStatutDossier
     *
     * @return StatutDossier
     */
    public function addCommandeStatutDossier(CommandeStatutDossier $commandeStatutDossier)
    {
        $this->commandeStatutDossier[] = $commandeStatutDossier;

        return $this;
    }

    /**
     * Remove commandeStatutDossier
     *
     * @param CommandeStatutDossier $commandeStatutDossier
     */
    public function removeCommandeStatutDossier(CommandeStatutDossier $commandeStatutDossier)
    {
        $this->commandeStatutDossier->removeElement($commandeStatutDossier);
    }

    /**
     * Get commandeStatutDossier
     *
     * @return Collection
     */
    public function getCommandeStatutDossier()
    {
        return $this->commandeStatutDossier;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $locale = 'fr_FR';
        $return = '';
        if (!empty($this->getGroupeStatutDossier())) {
            $return .= $this->getGroupeStatutDossier()->getTraductions()->filter(function (GroupeStatutDossierTraduction $element) use ($locale) {
                    return $element->getLangue()->getCode() == $locale;
                })->first()->getLibelle() . ' - ';
        }
        $return .= $this->getTraductions()->filter(function (StatutDossierTraduction $element) use ($locale) {
            return $element->getLangue()->getCode() == $locale;
        })->first()->getLibelle();
        return $return;
    }

    /**
     * Get groupeStatutDossier
     *
     * @return GroupeStatutDossier
     */
    public function getGroupeStatutDossier()
    {
        return $this->groupeStatutDossier;
    }

    /**
     * Set groupeStatutDossier
     *
     * @param GroupeStatutDossier $groupeStatutDossier
     *
     * @return StatutDossier
     */
    public function setGroupeStatutDossier(GroupeStatutDossier $groupeStatutDossier = null)
    {
        $this->groupeStatutDossier = $groupeStatutDossier;

        return $this;
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
