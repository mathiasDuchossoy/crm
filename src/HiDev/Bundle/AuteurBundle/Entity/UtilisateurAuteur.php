<?php

namespace HiDev\Bundle\AuteurBundle\Entity;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * UtilisateurAuteur
 */
class UtilisateurAuteur extends Auteur
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Utilisateur
     */
    private $utilisateur;

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->utilisateur->getPrenom() . ' ' . $this->utilisateur->getNom();
    }

    public function __toString()
    {
        return $this->getUtilisateur()->__toString();
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     *
     * @return UtilisateurAuteur
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
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
}
