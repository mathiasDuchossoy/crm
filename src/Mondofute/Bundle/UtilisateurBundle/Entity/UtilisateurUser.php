<?php

namespace Mondofute\Bundle\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mondofute\Bundle\CoreBundle\Entity\User as User;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * UtilisateurUser
 */
class UtilisateurUser extends User
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected  $id;
    /**
     * @var Utilisateur
     */
    private $utilisateur;

//    /**
//     * Get name
//     *
//     * @return string
//     */
//    public function getName()
//    {
//        return $this->name;
//    }
//
//    /**
//     * Set name
//     *
//     * @param string $name
//     *
//     * @return UtilisateurUser
//     */
//    public function setName($name)
//    {
//        $this->name = $name;
//
//        return $this;
//    }

//    /**
//     * Get id
//     *
//     * @return int
//     */
//    public function getId()
//    {
//        return $this->id;
//    }

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
     * @return UtilisateurUser
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
