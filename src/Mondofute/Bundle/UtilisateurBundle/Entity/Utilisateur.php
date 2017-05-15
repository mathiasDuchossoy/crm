<?php

namespace Mondofute\Bundle\UtilisateurBundle\Entity;

use HiDev\Bundle\AuteurBundle\Entity\UtilisateurAuteur;
use Nucleus\ContactBundle\Entity\Physique;

/**
 * Utilisateur
 */
class Utilisateur extends Physique
{
    private $id;

    /**
     * @var UtilisateurAuteur auteur
     */
    private $auteur;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return UtilisateurAuteur auteur
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * @param UtilisateurAuteur $auteur
     *
     * @return $this
     */
    public function setAuteur(UtilisateurAuteur $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function __toString()
    {
        return $this->getPrenom() . ' ' . $this->getNom();
    }
}
