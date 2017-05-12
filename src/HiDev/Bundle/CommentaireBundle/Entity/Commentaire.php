<?php

namespace HiDev\Bundle\CommentaireBundle\Entity;

/**
 * Commentaire
 */
abstract class Commentaire implements CommentaireInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $dateHeureCreation;

    /**
     * @var \DateTime
     */
    protected $dateHeureModification;

    /**
     * @var bool
     */
    protected $validationModerateur;

    /**
     * @var string
     */
    protected $contenu;

    /**
     * @var
     */
    protected $auteur;

    /**
     * Commentaire constructor.
     */
    public function __construct()
    {
        $this->setValidationModerateur(false);
    }

    /**
     * @return mixed
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * @param mixed $auteur
     *
     * @return Commentaire
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
        return $this;
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
     * Get dateHeureCreation
     *
     * @return \DateTime
     */
    public function getDateHeureCreation()
    {
        return $this->dateHeureCreation;
    }

    /**
     * Set dateHeureCreation
     *
     * @return Commentaire
     */
    public function setDateHeureCreation()
    {
        $this->dateHeureCreation = new \DateTime();

        return $this;
    }

    /**
     * Get dateHeureModification
     *
     * @return \DateTime
     */
    public function getDateHeureModification()
    {
        return $this->dateHeureModification;
    }

    /**
     * Set dateHeureModification
     *
     * @return Commentaire
     */
    public function setDateHeureModification()
    {
        $this->dateHeureModification = new \DateTime();

        return $this;
    }

    /**
     * Get validationModerateur
     *
     * @return bool
     */
    public function getValidationModerateur()
    {
        return $this->validationModerateur;
    }

    /**
     * Set validationModerateur
     *
     * @param boolean $validationModerateur
     *
     * @return Commentaire
     */
    public function setValidationModerateur($validationModerateur)
    {
        $this->validationModerateur = $validationModerateur;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Commentaire
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }
}
