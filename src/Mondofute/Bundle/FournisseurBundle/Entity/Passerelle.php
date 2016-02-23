<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * Passerelle
 */
class Passerelle
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;

    /**
     * @var string
     */
    private $data;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fournisseurs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Passerelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Passerelle
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle $fournisseur
     *
     * @return Passerelle
     */
    public function addFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle $fournisseur)
    {
        $this->fournisseurs[] = $fournisseur->setPasserelle($this);

        return $this;
    }

    /**
     * Remove fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle $fournisseur
     */
    public function removeFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\FournisseurPasserelle $fournisseur)
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
}
