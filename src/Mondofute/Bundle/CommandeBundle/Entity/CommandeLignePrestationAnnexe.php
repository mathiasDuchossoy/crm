<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use DateTime;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\StationBundle\Entity\Station;

/**
 * CommandeLignePrestationAnnexe
 */
class CommandeLignePrestationAnnexe extends CommandeLigne
{

    /**
     * @var CommandeLigneSejour
     */
    private $commandeLigneSejour;
    /**
     * @var FournisseurPrestationAnnexeParam
     */
    private $fournisseurPrestationAnnexeParam;
    /**
     * @var DateTime
     */
    private $dateDebut;
    /**
     * @var DateTime
     */
    private $dateFin;
    /**
     * @var Station
     */
    private $station;

    /**
     * Get commandeLigneSejour
     *
     * @return CommandeLigneSejour
     */
    public function getCommandeLigneSejour()
    {
        return $this->commandeLigneSejour;
    }

    /**
     * Set commandeLigneSejour
     *
     * @param CommandeLigneSejour $commandeLigneSejour
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setCommandeLigneSejour(CommandeLigneSejour $commandeLigneSejour = null)
    {
        $this->commandeLigneSejour = $commandeLigneSejour;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut
     *
     * @param DateTime $dateDebut
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param DateTime $dateFin
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getFournisseur()
    {
        return $this->getFournisseurPrestationAnnexeParam()->getFournisseurPrestationAnnexe()->getFournisseur();
    }

    /**
     * Get fournisseurPrestationAnnexeParam
     *
     * @return FournisseurPrestationAnnexeParam
     */
    public function getFournisseurPrestationAnnexeParam()
    {
        return $this->fournisseurPrestationAnnexeParam;
    }

    /**
     * Set fournisseurPrestationAnnexeParam
     *
     * @param FournisseurPrestationAnnexeParam $fournisseurPrestationAnnexeParam
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setFournisseurPrestationAnnexeParam(FournisseurPrestationAnnexeParam $fournisseurPrestationAnnexeParam = null)
    {
        $this->fournisseurPrestationAnnexeParam = $fournisseurPrestationAnnexeParam;

        return $this;
    }

    /**
     * Get station
     *
     * @return Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param Station $station
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setStation(Station $station = null)
    {
        $this->station = $station;

        return $this;
    }
}
