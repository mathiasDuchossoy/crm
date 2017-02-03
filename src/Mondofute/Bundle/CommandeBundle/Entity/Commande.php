<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/02/2017
 * Time: 11:10
 */

namespace Mondofute\Bundle\CommandeBundle\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use ReflectionClass;

class Commande
{

    /**
     * @var integer
     */
    private $id;
    /**
     * @var DateTime
     */
    private $dateCommande;
    /**
     * @var integer
     */
    private $numCommande;
    /**
     * @var Collection
     */
    private $clients;
    /**
     * @var Collection
     */
    private $commandeLignes;
    /**
     * @var Collection
     */
    private $commandeEtatDossiers;
    /**
     * @var Collection
     */
    private $commandeStatutDossiers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->commandeLignes = new ArrayCollection();
        $this->commandeEtatDossiers = new ArrayCollection();
        $this->commandeStatutDossiers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get dateCommande
     *
     * @return DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
    }

    /**
     * Set dateCommande
     *
     * @param DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get numCommande
     *
     * @return integer
     */
    public function getNumCommande()
    {
        return $this->numCommande;
    }

    /**
     * Set numCommande
     *
     * @param integer $numCommande
     *
     * @return Commande
     */
    public function setNumCommande($numCommande)
    {
        $this->numCommande = $numCommande;

        return $this;
    }

    /**
     * Add client
     *
     * @param Client $client
     *
     * @return Commande
     */
    public function addClient(Client $client)
    {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client
     *
     * @param Client $client
     */
    public function removeClient(Client $client)
    {
        $this->clients->removeElement($client);
    }

    /**
     * Get clients
     *
     * @return Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Add commandeLigne
     *
     * @param CommandeLigne $commandeLigne
     *
     * @return Commande
     */
    public function addCommandeLigne(CommandeLigne $commandeLigne)
    {
        $this->commandeLignes[] = $commandeLigne->setCommande($this);

        return $this;
    }

    /**
     * Remove commandeLigne
     *
     * @param CommandeLigne $commandeLigne
     */
    public function removeCommandeLigne(CommandeLigne $commandeLigne)
    {
        $this->commandeLignes->removeElement($commandeLigne);
    }

    /**
     * Get commandeLignes
     *
     * @return Collection
     */
    public function getCommandeLignes()
    {
        $newCommandeLignes = new ArrayCollection();
        foreach ($this->commandeLignes as $commandeLigne) {
            $oReflectionClass = new ReflectionClass($commandeLigne);
            if ($oReflectionClass->getShortName() == 'CommandeLignePrestationAnnexe') {
                /** @var CommandeLignePrestationAnnexe $commandeLigne */
                if (empty($commandeLigne->getCommandeLigneSejour())) {
                    $newCommandeLignes->add($commandeLigne);
                }
            } else {
                $newCommandeLignes->add($commandeLigne);
            }
        }
        $this->commandeLignes->clear();
        foreach ($newCommandeLignes as $newCommandeLigne) {
            $this->commandeLignes->add($newCommandeLigne);
        }
        return $this->commandeLignes;
    }

    /**
     * Add commandeEtatDossier
     *
     * @param CommandeEtatDossier $commandeEtatDossier
     *
     * @return Commande
     */
    public function addCommandeEtatDossier(CommandeEtatDossier $commandeEtatDossier)
    {
        $this->commandeEtatDossiers[] = $commandeEtatDossier->setCommande($this);

        return $this;
    }

    /**
     * Remove commandeEtatDossier
     *
     * @param CommandeEtatDossier $commandeEtatDossier
     */
    public function removeCommandeEtatDossier(CommandeEtatDossier $commandeEtatDossier)
    {
        $this->commandeEtatDossiers->removeElement($commandeEtatDossier);
    }

    /**
     * Get commandeEtatDossiers
     *
     * @return Collection
     */
    public function getCommandeEtatDossiers()
    {
        return $this->commandeEtatDossiers;
    }

    /**
     * Add commandeStatutDossier
     *
     * @param CommandeStatutDossier $commandeStatutDossier
     *
     * @return Commande
     */
    public function addCommandeStatutDossier(CommandeStatutDossier $commandeStatutDossier)
    {
        $this->commandeStatutDossiers[] = $commandeStatutDossier->setCommande($this);

        return $this;
    }

    /**
     * Remove commandeStatutDossier
     *
     * @param CommandeStatutDossier $commandeStatutDossier
     */
    public function removeCommandeStatutDossier(CommandeStatutDossier $commandeStatutDossier)
    {
        $this->commandeStatutDossiers->removeElement($commandeStatutDossier);
    }

    /**
     * Get commandeStatutDossiers
     *
     * @return Collection
     */
    public function getCommandeStatutDossiers()
    {
        return $this->commandeStatutDossiers;
    }
}
