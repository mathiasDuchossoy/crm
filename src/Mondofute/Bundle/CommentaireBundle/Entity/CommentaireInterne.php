<?php

namespace Mondofute\Bundle\CommentaireBundle\Entity;

use HiDev\Bundle\CommentaireBundle\Entity\Commentaire as BaseCommentaire;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * CommentaireInterne
 */
class CommentaireInterne extends BaseCommentaire
{
//    /**
//     * @var int
//     */
//    private $id;
//
//
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
     * @return CommentaireInterne
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
