<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * TypeHebergementUnifie
 */
class TypeHebergementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $typeHebergements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->typeHebergements = new ArrayCollection();
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
     * Add typeHebergement
     *
     * @param TypeHebergement $typeHebergement
     *
     * @return TypeHebergementUnifie
     */
    public function addTypeHebergement(TypeHebergement $typeHebergement)
    {
        $this->typeHebergements[] = $typeHebergement->setTypeHebergementUnifie($this);

        return $this;
    }

    /**
     * Remove typeHebergement
     *
     * @param TypeHebergement $typeHebergement
     */
    public function removeTypeHebergement(TypeHebergement $typeHebergement)
    {
        $this->typeHebergements->removeElement($typeHebergement);
    }

    /**
     * Get typeHebergements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypeHebergements()
    {
        return $this->typeHebergements;
    }
}
