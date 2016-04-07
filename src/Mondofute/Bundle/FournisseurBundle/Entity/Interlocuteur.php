<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

use Nucleus\ContactBundle\Entity\Physique;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Interlocuteur
 */
class Interlocuteur extends Physique
{
//    /**
//     * @var integer
//     */
//    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurs;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction
     */
    private $fonction;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur
     */
    private $service;
//    /**
//     * @var string
//     */
//    private $prenom;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDateCreation();
        $this->setDateModification(new \Datetime());
//        $this->setActif(true);
        $this->fournisseurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

//    /**
//     * Get id
//     *
//     * @return integer
//     */
//    public function getId()
//    {
//        return $this->id;
//    }

    /**
     * Add fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $fournisseur
     *
     * @return Interlocuteur
     */
    public function addFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $fournisseur)
    {
        $this->fournisseurs[] = $fournisseur->setInterlocuteur($this);

        return $this;
    }

    /**
     * Remove fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $fournisseur
     */
    public function removeFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $fournisseur)
    {
        $this->fournisseurs->removeElement($fournisseur);
    }

    /**
     * Get fournisseurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFournisseurs()
    {
        return $this->fournisseurs;
    }

    /**
     * Get fonction
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set fonction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction $fonction
     *
     * @return Interlocuteur
     */
    public function setFonction(\Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get service
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur $service
     *
     * @return Interlocuteur
     */
    public function setService(\Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur $service = null)
    {
        $this->service = $service;

        return $this;
    }

//    /**
//     * Get prenom
//     *
//     * @return string
//     */
//    public function getPrenom()
//    {
//        return $this->prenom;
//    }

//    /**
//     * Set prenom
//     *
//     * @param string $prenom
//     *
//     * @return Interlocuteur
//     */
//    public function setPrenom($prenom)
//    {
//        $this->prenom = $prenom;
//
//        return $this;
//    }


}
