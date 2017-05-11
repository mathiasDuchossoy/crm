<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Doctrine\Common\Collections\Collection
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
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
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
    ) {
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
    ) {
        $this->commandeLitigeDossier->removeElement($commandeLitigeDossier);
    }

    /**
     * Get commandeLitigeDossier
     *
     * @return \Doctrine\Common\Collections\Collection
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
}
