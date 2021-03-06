<?php

namespace Mondofute\Bundle\ClientBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\CommandeBundle\Entity\Commande;
use Nucleus\ContactBundle\Entity\Physique;

/**
 * Client
 */
class Client extends Physique
{
    /**
     * @var boolean
     */
    private $vip;
    /**
     * @var \DateTime
     */
    private $dateNaissance;
    /**
     * @var string
     */
    private $prenom;

    /**
     * @var string
     */
    private $nom;
    /**
     * @var Collection
     */
    private $commandes;
    /**
     * @var \Mondofute\Bundle\ClientBundle\Entity\ClientUser
     */
    private $clientUser;

    public function getLabel()
    {
        return $this->getNom() . ', ' . $this->getPrenom();
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Physique
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Physique
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function __toString()
    {
        return $this->getNom() . ', ' . $this->getPrenom();
    }

    /**
     * Add commande
     *
     * @param Commande $commande
     *
     * @return Client
     */
    public function addCommande(Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande
     *
     * @param Commande $commande
     */
    public function removeCommande(Commande $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Get clientUser
     *
     * @return \Mondofute\Bundle\ClientBundle\Entity\ClientUser
     */
    public function getClientUser()
    {
        return $this->clientUser;
    }

    /**
     * Set clientUser
     *
     * @param \Mondofute\Bundle\ClientBundle\Entity\ClientUser $clientUser
     *
     * @return Client
     */
    public function setClientUser(\Mondofute\Bundle\ClientBundle\Entity\ClientUser $clientUser = null)
    {
        $this->clientUser = $clientUser->setClient($this);

        return $this;
    }

    public function duplique(Client &$client)
    {
        $client->setDateNaissance($this->getDateNaissance())
            ->setNom($this->getNom())
            ->setPrenom($this->getPrenom());
        $client->setVip($this->getVip());

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Client
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get vip
     *
     * @return boolean
     */
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * Set vip
     *
     * @param boolean $vip
     *
     * @return Client
     */
    public function setVip($vip)
    {
        $this->vip = $vip;

        return $this;
    }
}
