<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * FournisseurHebergement
 */
class FournisseurHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    private $hebergement;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;

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
     * Get hebergement
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergement
     *
     * @return FournisseurHebergement
     */
    public function setHebergement(\Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return FournisseurHebergement
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
}
