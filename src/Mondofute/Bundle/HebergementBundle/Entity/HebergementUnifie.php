<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * HebergementUnifie
 */
class HebergementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $hebergements;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hebergements = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     */
    public function removeHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement)
    {
        $this->hebergements->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHebergements()
    {
        return $this->hebergements;
    }

    /**
     * @param $hebergements
     * @return $this
     */
    public function setHebergements($hebergements)
    {
        $this->getHebergements()->clear();

        foreach ($hebergements as $hebergement) {
            $this->addHebergement($hebergement);
        }
        return $this;
    }

    /**
     * Add hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     *
     * @return HebergementUnifie
     */
    public function addHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement->setHebergementUnifie($this);

        return $this;
    }

    /**
     * Add fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return HebergementUnifie
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
}
