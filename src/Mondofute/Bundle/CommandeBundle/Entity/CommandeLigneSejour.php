<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\LogementBundle\Entity\Logement;

/**
 * CommandeLigneSejour
 */
class CommandeLigneSejour extends CommandeLigne
{

    /**
     * @var Logement
     */
    private $logement;
    /**
     * @var Collection
     */
    private $commandeLignePrestationAnnexes;
    /**
     * @var integer
     */
    private $nbParticipants;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandeLignePrestationAnnexes = new ArrayCollection();
    }

    /**
     * Get logement
     *
     * @return Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param Logement $logement
     *
     * @return CommandeLigneSejour
     */
    public function setLogement(Logement $logement = null)
    {
        $this->logement = $logement;

        return $this;
    }

    /**
     * Add commandeLignePrestationAnnex
     *
     * @param CommandeLignePrestationAnnexe $commandeLignePrestationAnnex
     *
     * @return CommandeLigneSejour
     */
    public function addCommandeLignePrestationAnnex(CommandeLignePrestationAnnexe $commandeLignePrestationAnnex)
    {
        $this->commandeLignePrestationAnnexes[] = $commandeLignePrestationAnnex->setCommandeLigneSejour($this);

        return $this;
    }

    /**
     * Remove commandeLignePrestationAnnex
     *
     * @param CommandeLignePrestationAnnexe $commandeLignePrestationAnnex
     */
    public function removeCommandeLignePrestationAnnex(CommandeLignePrestationAnnexe $commandeLignePrestationAnnex)
    {
        $this->commandeLignePrestationAnnexes->removeElement($commandeLignePrestationAnnex);
    }

    /**
     * Get commandeLignePrestationAnnexes
     *
     * @return Collection
     */
    public function getCommandeLignePrestationAnnexes()
    {
        return $this->commandeLignePrestationAnnexes;
    }

    /**
     * Get nbParticipants
     *
     * @return integer
     */
    public function getNbParticipants()
    {
        return $this->nbParticipants;
    }

    /**
     * Set nbParticipants
     *
     * @param integer $nbParticipants
     *
     * @return CommandeLigneSejour
     */
    public function setNbParticipants($nbParticipants)
    {
        $this->nbParticipants = $nbParticipants;

        return $this;
    }
}
