<?php

namespace Mondofute\Bundle\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mondofute\Bundle\CoreBundle\Entity\User as User;

/**
 * UtilisateurUser
 */
class UtilisateurUser extends User
{
//    /**
//     * @var int
//     */
//    protected $id;
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var \Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur
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
     * @return \Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set utilisateur
     *
     * @param \Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur $utilisateur
     *
     * @return UtilisateurUser
     */
    public function setUtilisateur(\Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
