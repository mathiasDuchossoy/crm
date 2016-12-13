<?php

namespace Mondofute\Bundle\ClientBundle\Entity;

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


}
