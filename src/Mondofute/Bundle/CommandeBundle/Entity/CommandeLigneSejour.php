<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\StationBundle\Entity\StationTraduction;

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
        parent::__construct();
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

    public function getFournisseur()
    {
        return $this->logement->getFournisseur();
    }

    public function getStation()
    {
        $site = $this->logement->getSite();
        return $this->logement->getFournisseurHebergement()->getHebergement()->getHebergements()->filter(function (Hebergement $element) use ($site) {
            return $element->getSite() == $site;
        })->first()->getStation()->getTraductions()->filter(function (StationTraduction $element) {
            return $element->getLangue()->getCode() == 'fr_FR';
        })->first()->getLibelle();
    }

    public function getHebergement()
    {
        $site = $this->logement->getSite();
        return $this->logement->getFournisseurHebergement()->getHebergement()->getHebergements()->filter(function (Hebergement $element) use ($site) {
            return $element->getSite() == $site;
        })->first()->getTraductions()->filter(function (HebergementTraduction $element) {
            return $element->getLangue()->getCode() == 'fr_FR';
        })->first()->getNom();
    }

    public function getLocationMaterielExists()
    {
        $remonteeMecaniques = $this->getCommandeLignePrestationAnnexes()->filter(function (CommandeLignePrestationAnnexe $element) {
            return $element->getFournisseurPrestationAnnexeParam()->getFournisseurPrestationAnnexe()->getPrestationAnnexe()->getFamillePrestationAnnexe()->getId() == 2;
        });
        if ($remonteeMecaniques->isEmpty()) {
            return false;
        }
        return true;
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

    public function getRemonteeMecaniqueExists()
    {
        $remonteeMecaniques = $this->getCommandeLignePrestationAnnexes()->filter(function (CommandeLignePrestationAnnexe $element) {
            return $element->getFournisseurPrestationAnnexeParam()->getFournisseurPrestationAnnexe()->getPrestationAnnexe()->getFamillePrestationAnnexe()->getId() == 1;
        });
        if ($remonteeMecaniques->isEmpty()) {
            return false;
        }
        return true;
    }

}
