<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\FournisseurBundle\Entity\Traits\FournisseurTrait;
use Nucleus\ContactBundle\Entity\Moral;

class FournisseurContient
{
    const PRODUIT = 1; // 1
    const FOURNISSEUR = 2; // 10

    public static $libelles = array(
        FournisseurContient::FOURNISSEUR => 'Fournisseurs',
        FournisseurContient::PRODUIT => 'Produits'
    );

    static public function getLibelle($permission)
    {
        return self::$libelles[$permission];
    }

}

/**
 * Fournisseur
 */
class Fournisseur extends Moral
{
    use FournisseurTrait;

//    const PRODUIT = 1; // 1
//    const FOURNISSEUR = 2; // 10
//    /**
//     * @var integer
//     */
//    private $id;

    /**
     * @var string
     */
    private $enseigne;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $interlocuteurs;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle
     */
    private $passerelle;
    /**
     * @var integer
     */
    private $contient;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurEnfants;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseurParent;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur
     */
    private $type;

//    /**
//     * Get id
//     *
//     * @return integer
//     */
//    public function getId()
//    {
//        return $this->id;
//    }

//    /**
//     * Get enseigne
//     *
//     * @return string
//     */
//    public function getEnseigne()
//    {
//        return $this->enseigne;
//    }
//
//    /**
//     * Set enseigne
//     *
//     * @param string $enseigne
//     *
//     * @return Fournisseur
//     */
//    public function setEnseigne($enseigne)
//    {
//        $this->enseigne = $enseigne;
//
//        return $this;
//    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDateCreation();
        $this->setDateModification(new \Datetime());
//        $this->setActif(true);
        $this->interlocuteurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add interlocuteur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $interlocuteur
     *
     * @return Fournisseur
     */
    public function addInterlocuteur(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $interlocuteur)
    {
        $this->interlocuteurs[] = $interlocuteur->setFournisseur($this);

        return $this;
    }

    /**
     * Remove interlocuteur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $interlocuteur
     */
    public function removeInterlocuteur(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur $interlocuteur)
    {
        $this->interlocuteurs->removeElement($interlocuteur);
    }

    /**
     * Get passerelle
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle
     */
    public function getPasserelle()
    {
        return $this->passerelle;
    }

    /**
     * Set passerelle
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle $passerelle
     *
     * @return Fournisseur
     */
    public function setPasserelle(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle $passerelle = null)
    {
        $this->passerelle = $passerelle;

        return $this;
    }

    function __clone()
    {
        /** @var FournisseurInterlocuteur $interlocuteur */
//        $this->id = null;
        $interlocuteurs = $this->getInterlocuteurs();
        $this->interlocuteurs = new ArrayCollection();
        if (count($interlocuteurs) > 0) {
            foreach ($interlocuteurs as $interlocuteur) {
                $cloneInterlocuteur = clone $interlocuteur;
                $this->interlocuteurs->add($cloneInterlocuteur);
                $cloneInterlocuteur->setFournisseur($this);
            }
        }

        return $this;
    }

    /**
     * Get interlocuteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterlocuteurs()
    {
        return $this->interlocuteurs;
    }

    /**
     * Get contient
     *
     * @return integer
     */
    public function getContient()
    {
        return $this->contient;
    }

    /**
     * Set contient
     *
     * @param integer $contient
     *
     * @return Fournisseur
     */
    public function setContient($contient)
    {
        $this->contient = $contient;

        return $this;
    }

    /**
     * Add fournisseurEnfant
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurEnfant
     *
     * @return Fournisseur
     */
    public function addFournisseurEnfant(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurEnfant)
    {
        $this->fournisseurEnfants[] = $fournisseurEnfant;

        return $this;
    }

    /**
     * Remove fournisseurEnfant
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurEnfant
     */
    public function removeFournisseurEnfant(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurEnfant)
    {
        $this->fournisseurEnfants->removeElement($fournisseurEnfant);
    }

    /**
     * Get fournisseurEnfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFournisseurEnfants()
    {
        return $this->fournisseurEnfants;
    }

    /**
     * Get fournisseurParent
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseurParent()
    {
        return $this->fournisseurParent;
    }

    /**
     * Set fournisseurParent
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurParent
     *
     * @return Fournisseur
     */
    public function setFournisseurParent(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurParent = null)
    {
        $this->fournisseurParent = $fournisseurParent;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $type
     *
     * @return Fournisseur
     */
    public function setType(\Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $type = null)
    {
        $this->type = $type;

        return $this;
    }
}
