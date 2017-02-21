<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\CommentaireBundle\Entity\Commentaire;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reponses;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurCommentaire
     */
    private $commentaireParent;

    /**
     * FournisseurCommentaire constructor.
     */
    public function __construct()
    {
        $this->validationModerateur = false;
        $this->reponses = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return FournisseurCommentaire
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Add reponse
     *
     * @param FournisseurCommentaire $reponse
     *
     * @return FournisseurCommentaire
     */
    public function addReponse(FournisseurCommentaire $reponse)
    {
        $this->reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param FournisseurCommentaire $reponse
     */
    public function removeReponse(FournisseurCommentaire $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    /**
     * Get reponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReponses()
    {
        $reponses = new ArrayCollection();
        foreach ($this->reponses as $reponse) {
            $reponses->set($reponse->getId(), $reponse);
        }
        return $reponses;
//        return $this->reponses;
    }

    /**
     * Get commentaireParent
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurCommentaire
     */
    public function getCommentaireParent()
    {
        return $this->commentaireParent;
    }

    /**
     * Set commentaireParent
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurCommentaire $commentaireParent
     *
     * @return FournisseurCommentaire
     */
    public function setCommentaireParent(
        FournisseurCommentaire $commentaireParent = null
    ) {
        $this->commentaireParent = $commentaireParent;

        return $this;
    }
}
