<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * SecteurUnifie
 */
class SecteurUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $secteurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->secteurs = new ArrayCollection();
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
     * Remove secteur
     *
     * @param Secteur $secteur
     */
    public function removeSecteur(Secteur $secteur)
    {
        $this->secteurs->removeElement($secteur);
    }

    /**
     * Get secteurs
     *
     * @return Collection
     */
    public function getSecteurs()
    {
        return $this->secteurs;
    }

    /**
     * @param ArrayCollection $secteurs
     * @return $this
     */
    public function setSecteurs(ArrayCollection $secteurs)
    {
        $this->getSecteurs()->clear();

        foreach ($secteurs as $secteur) {
            $this->addSecteur($secteur);
        }
        return $this;
    }

    /**
     * Add secteur
     *
     * @param Secteur $secteur
     *
     * @return SecteurUnifie
     */
    public function addSecteur(Secteur $secteur)
    {
        $this->secteurs[] = $secteur->setSecteurUnifie($this);

        return $this;
    }
}
