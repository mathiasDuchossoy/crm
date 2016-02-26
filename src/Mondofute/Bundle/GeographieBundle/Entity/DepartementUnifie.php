<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * DepartementUnifie
 */
class DepartementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $departements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->departements = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     */
    public function removeDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement)
    {
        $this->departements->removeElement($departement);
    }

    /**
     * Get departements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartements()
    {
        return $this->departements;
    }

    /**
     * @param ArrayCollection $departements
     * @return DepartementUnifie $this
     */
    public function setDepartements(ArrayCollection $departements)
    {
        $this->getDepartements()->clear();

        foreach ($departements as $departement) {
            $this->addDepartement($departement);
        }
        return $this;
    }

    /**
     * Add departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     *
     * @return DepartementUnifie
     */
    public function addDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement)
    {
        $this->departements[] = $departement->setDepartementUnifie($this);

        return $this;
    }
}
