<?php

namespace Mondofute\Bundle\CommentaireBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use HiDev\Bundle\CommentaireBundle\Entity\Commentaire as BaseCommentaire;
use Mondofute\Bundle\ClientBundle\Entity\Client;

/**
 * CommentaireClient
 */
class CommentaireClient extends BaseCommentaire
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
     * @var Collection
     */
    private $reponses;

    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $utilisateurModification;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->reponses = new ArrayCollection();
    }

    /**
     * Add reponse
     *
     * @param CommentaireUtilisateur $reponse
     *
     * @return CommentaireClient
     */
    public function addReponse(CommentaireUtilisateur $reponse)
    {
        $this->reponses[] = $reponse->setCommentaireParent($this);

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param CommentaireUtilisateur $reponse
     */
    public function removeReponse(CommentaireUtilisateur $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    /**
     * Get reponses
     *
     * @return Collection
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param Client $client
     *
     * @return CommentaireClient
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set dateHeureModification
     *
     * @return CommentaireClient
     */
    public function setDateHeureModification()
    {
        $this->dateHeureModification = new DateTime();

        return $this;
    }

    /**
     * Get utilisateurModification
     *
     * @return string
     */
    public function getUtilisateurModification()
    {
        return $this->utilisateurModification;
    }

    /**
     * Set utilisateurModification
     *
     * @param string $utilisateurModification
     *
     * @return CommentaireClient
     */
    public function setUtilisateurModification($utilisateurModification)
    {
        $this->utilisateurModification = $utilisateurModification;

        return $this;
    }
}
