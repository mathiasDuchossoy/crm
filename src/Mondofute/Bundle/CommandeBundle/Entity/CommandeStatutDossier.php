<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use DateTime;

/**
 * CommandeStatutDossier
 */
class CommandeStatutDossier
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $dateHeure;
    /**
     * @var Commande
     */
    private $commande;
    /**
     * @var StatutDossier
     */
    private $statutDossier;

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
     * @param int $id
     * @return CommandeStatutDossier
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get dateHeure
     *
     * @return DateTime
     */
    public function getDateHeure()
    {
        return $this->dateHeure;
    }

    /**
     * Set dateHeure
     *
     * @param DateTime $dateHeure
     *
     * @return CommandeStatutDossier
     */
    public function setDateHeure($dateHeure)
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    /**
     * Get commande
     *
     * @return Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set commande
     *
     * @param Commande $commande
     *
     * @return CommandeStatutDossier
     */
    public function setCommande(Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get statutDossier
     *
     * @return StatutDossier
     */
    public function getStatutDossier()
    {
        return $this->statutDossier;
    }

    /**
     * Set statutDossier
     *
     * @param StatutDossier $statutDossier
     *
     * @return CommandeStatutDossier
     */
    public function setStatutDossier(StatutDossier $statutDossier = null)
    {
        $this->statutDossier = $statutDossier;

        return $this;
    }
}
