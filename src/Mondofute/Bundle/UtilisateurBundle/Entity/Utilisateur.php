<?php

namespace Mondofute\Bundle\UtilisateurBundle\Entity;

use Nucleus\ContactBundle\Entity\Physique;

/**
 * Utilisateur
 */
class Utilisateur extends Physique
{
//    /**
//     * @var int
//     */
//    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;


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
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return Utilisateur
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Utilisateur
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

}
