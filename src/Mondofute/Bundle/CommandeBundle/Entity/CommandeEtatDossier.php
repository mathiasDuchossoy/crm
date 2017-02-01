<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use DateTime;

/**
 * CommandeEtatDossier
 */
class CommandeEtatDossier
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Commande
     */
    private $commande;
    /**
     * @var EtatDossier
     */
    private $etatDossier;
    /**
     * @var DateTime
     */
    private $dateHeure;

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
     * @return CommandeEtatDossier
     */
    public function setCommande(Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get etatDossier
     *
     * @return EtatDossier
     */
    public function getEtatDossier()
    {
        return $this->etatDossier;
    }

    /**
     * Set etatDossier
     *
     * @param EtatDossier $etatDossier
     *
     * @return CommandeEtatDossier
     */
    public function setEtatDossier(EtatDossier $etatDossier = null)
    {
        $this->etatDossier = $etatDossier;

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
     * @return CommandeEtatDossier
     */
    public function setDateHeure($dateHeure)
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }
}
