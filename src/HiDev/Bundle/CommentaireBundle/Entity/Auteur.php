<?php

namespace HiDev\Bundle\CommentaireBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Auteur
 */
abstract class Auteur implements AuteurInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ArrayCollection
     */
    protected $commentaires;

    /**
     * Auteur constructor.
     */
    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    public function getCommentaires()
    {
        return $this->commentaires;
    }

    public function addCommentaire($commentaire)
    {
        $this->commentaires->add($commentaire);
        return $this;
    }

    public function removeCommentaire($commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }
}
