<?php

namespace Mondofute\Bundle\CommentaireBundle\Entity;

use HiDev\Bundle\CommentaireBundle\Entity\Commentaire as BaseCommentaire;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * CommentaireUtilisateur
 */
class CommentaireUtilisateur extends BaseCommentaire
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var Utilisateur
     */
    private $utilisateur;
    /**
     * @var CommentaireClient
     */
    private $commentaireParent;

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
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return CommentaireUtilisateur
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get commentaireParent
     *
     * @return CommentaireClient
     */
    public function getCommentaireParent()
    {
        return $this->commentaireParent;
    }

    /**
     * Set commentaireParent
     *
     * @param CommentaireClient $commentaireParent
     *
     * @return CommentaireUtilisateur
     */
    public function setCommentaireParent(CommentaireClient $commentaireParent = null)
    {
        $this->commentaireParent = $commentaireParent;

        return $this;
    }
}
