<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * TypeFournisseur
 */
class TypeFournisseur
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurs;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return TypeFournisseur
     */
    public function addFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur)
    {
        $this->fournisseurs[] = $fournisseur;

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
     * Add traduction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseurTraduction $traduction
     *
     * @return TypeFournisseur
     */
    public function addTraduction(\Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseurTraduction $traduction)
    {
        $this->traductions[] = $traduction->setTypeFournisseur($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseurTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseurTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }
}
