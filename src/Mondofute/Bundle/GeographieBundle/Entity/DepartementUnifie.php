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
     * @return ArrayCollection
     */
    public function setDepartements(ArrayCollection $departements)
    {
        $this->departements = $departements;
        return $this;
    }
}
