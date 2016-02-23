<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * Fournisseur
 */
class Fournisseur
{
    /**
     * @var integer
     */
    private $id;

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
     * Constructor
     */
    public function __construct()
    {
        $this->interlocuteurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enseigne
     *
     * @param string $enseigne
     *
     * @return Fournisseur
     */
    public function setEnseigne($enseigne)
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    /**
     * Get enseigne
     *
     * @return string
     */
    public function getEnseigne()
    {
        return $this->enseigne;
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
     * Get interlocuteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterlocuteurs()
    {
        return $this->interlocuteurs;
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

    /**
     * Get passerelle
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle
     */
    public function getPasserelle()
    {
        return $this->passerelle;
    }
}
