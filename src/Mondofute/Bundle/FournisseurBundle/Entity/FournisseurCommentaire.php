<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

use Mondofute\Bundle\CommentaireBundle\Entity\Commentaire;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;

/**
 * FournisseurCommentaire
 */
class FournisseurCommentaire extends Commentaire
{
    /**
     * @var Fournisseur
     */
    private $fournisseur;


    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return FournisseurCommentaire
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }
}
