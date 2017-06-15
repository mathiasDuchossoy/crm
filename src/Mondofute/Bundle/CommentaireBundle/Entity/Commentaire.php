<?php

namespace Mondofute\Bundle\CommentaireBundle\Entity;

use HiDev\Bundle\AuteurBundle\Entity\Auteur;
use HiDev\Bundle\CommentaireBundle\Entity\Commentaire as BaseCommentaire;

/**
 * Commentaire
 */
class Commentaire extends BaseCommentaire
{
    /**
     * @var Auteur
     */
    private $auteur;

    /**
     * @return Auteur
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * @param Auteur $auteur
     *
     * @return $this
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

}
