<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * LitigeDossier
 */
class LitigeDossier
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
     * @var Collection
     */
    private $commandeLitigeDossier;
    /**
     * @var integer
     */
    private $message;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->message = 1;
        $this->traductions = new ArrayCollection();
        $this->commandeLitigeDossier = new ArrayCollection();
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
     * @return LitigeDossier
     */
    public function setCodeCouleur($codeCouleur)
    {
        $this->codeCouleur = $codeCouleur;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param LitigeDossierTraduction $traduction
     *
     * @return LitigeDossier
     */
    public function addTraduction(LitigeDossierTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param LitigeDossierTraduction $traduction
     */
    public function removeTraduction(LitigeDossierTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Add commandeLitigeDossier
     *
     * @param CommandeLitigeDossier $commandeLitigeDossier
     *
     * @return LitigeDossier
     */
    public function addCommandeLitigeDossier(
        CommandeLitigeDossier $commandeLitigeDossier
    )
    {
        $this->commandeLitigeDossier[] = $commandeLitigeDossier;

        return $this;
    }

    /**
     * Remove commandeLitigeDossier
     *
     * @param CommandeLitigeDossier $commandeLitigeDossier
     */
    public function removeCommandeLitigeDossier(
        CommandeLitigeDossier $commandeLitigeDossier
    )
    {
        $this->commandeLitigeDossier->removeElement($commandeLitigeDossier);
    }

    /**
     * Get commandeLitigeDossier
     *
     * @return Collection
     */
    public function getCommandeLitigeDossier()
    {
        return $this->commandeLitigeDossier;
    }

    /**
     * Get message
     *
     * @return integer
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param integer $message
     *
     * @return LitigeDossier
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $locale = 'fr_FR';
        $return = $this->getTraductions()->filter(function (LitigeDossierTraduction $element) use ($locale) {
            return $element->getLangue()->getCode() == $locale;
        })->first()->getLibelle();
        return $return;
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
