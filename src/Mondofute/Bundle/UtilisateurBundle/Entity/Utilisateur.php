<?php

namespace Mondofute\Bundle\UtilisateurBundle\Entity;

use Nucleus\ContactBundle\Entity\Physique;

/**
 * Utilisateur
 */
class Utilisateur extends Physique
{
    private $id;

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

}
