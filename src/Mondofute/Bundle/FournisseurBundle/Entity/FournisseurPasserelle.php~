<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * FournisseurPasserelle
 */
class FournisseurPasserelle
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $data;


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
     * Set data
     *
     * @param string $data
     *
     * @return FournisseurPasserelle
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurs;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Passerelle
     */
    private $passerelle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fournisseurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return FournisseurPasserelle
     */
    public function addFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur)
    {
        $this->fournisseurs[] = $fournisseur->setPasserelle($this);

        return $this;
    }

    /**
     * Remove fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     */
    public function removeFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur)
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
     * Set passerelle
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Passerelle $passerelle
     *
     * @return FournisseurPasserelle
     */
    public function setPasserelle(\Mondofute\Bundle\FournisseurBundle\Entity\Passerelle $passerelle = null)
    {
        $this->passerelle = $passerelle;

        return $this;
    }

    /**
     * Get passerelle
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Passerelle
     */
    public function getPasserelle()
    {
        return $this->passerelle;
    }
}
