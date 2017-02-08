<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use DateTime;

/**
 * CommandeLigne
 */
abstract class CommandeLigne
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
     * @var integer
     */
    private $prixCatalogue = 0;
    /**
     * @var integer
     */
    private $prixPublic = 0;
    /**
     * @var integer
     */
    private $prixAchat = 0;
    /**
     * @var integer
     */
    private $quantite = 1;
    /**
     * @var DateTime
     */
    private $dateAchat;

    public function __construct()
    {
        $this->dateAchat = new DateTime();
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
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return CommandeLigne
     */
    public function setCommande(Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get prixCatalogue
     *
     * @return integer
     */
    public function getPrixCatalogue()
    {
        return $this->prixCatalogue;
    }

    /**
     * Set prixCatalogue
     *
     * @param integer $prixCatalogue
     *
     * @return CommandeLigne
     */
    public function setPrixCatalogue($prixCatalogue)
    {
        $this->prixCatalogue = $prixCatalogue;

        return $this;
    }

    /**
     * Get prixPublic
     *
     * @return integer
     */
    public function getPrixPublic()
    {
        return $this->prixPublic;
    }

    /**
     * Set prixPublic
     *
     * @param integer $prixPublic
     *
     * @return CommandeLigne
     */
    public function setPrixPublic($prixPublic)
    {
        $this->prixPublic = $prixPublic;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return integer
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }

    /**
     * Set prixAchat
     *
     * @param integer $prixAchat
     *
     * @return CommandeLigne
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return CommandeLigne
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get dateAchat
     *
     * @return DateTime
     */
    public function getDateAchat()
    {
        return $this->dateAchat;
    }

    /**
     * Set dateAchat
     *
     * @param DateTime $dateAchat
     *
     * @return CommandeLigne
     */
    public function setDateAchat($dateAchat)
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }
}
