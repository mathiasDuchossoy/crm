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
     * @var string
     */
    protected $nom;

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
     *
     * @return Auteur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
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
