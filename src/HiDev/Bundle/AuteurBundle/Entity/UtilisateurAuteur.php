<?php

namespace HiDev\Bundle\AuteurBundle\Entity;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * UtilisateurAuteur
 */
class UtilisateurAuteur extends Auteur
{

    /**
     * @var Utilisateur
     */
    private $utilisateur;

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
     * @return string
     */
    public function getNom()
    {
        return $this->utilisateur->getPrenom() . ' ' . $this->utilisateur->getNom();
    }
}
