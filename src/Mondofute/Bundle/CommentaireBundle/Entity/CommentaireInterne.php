<?php

namespace Mondofute\Bundle\CommentaireBundle\Entity;

/**
 * CommentaireInterne
 */
class CommentaireInterne extends Commentaire
{
    /**
     * @var int
     */
    protected $id;

//    protected $auteur;

//    /**
//     * Get auteur
//     *
//     * @return UtilisateurAuteur
//     */
//    public function getAuteur()
//    {
//        return $this->auteur;
//    }
//
//    /**
//     * @param UtilisateurAuteur $auteur
//     *
//     * @return $this
//     */
//    public function setAuteur($auteur)
//    {
//        $this->auteur = $auteur;
//
//        return $this;
//    }
//

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
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
//    /**
//     * @var Utilisateur
//     */
//    private $utilisateur;
//
//    /**
//     * Get utilisateur
//     *
//     * @return Utilisateur
//     */
//    public function getUtilisateur()
//    {
//        return $this->utilisateur;
//    }
//
//    /**
//     * Set utilisateur
//     *
//     * @param Utilisateur $utilisateur
//     *
//     * @return CommentaireInterne
//     */
//    public function setUtilisateur(Utilisateur $utilisateur = null)
//    {
//        $this->utilisateur = $utilisateur;
//
//        return $this;
//    }

}
