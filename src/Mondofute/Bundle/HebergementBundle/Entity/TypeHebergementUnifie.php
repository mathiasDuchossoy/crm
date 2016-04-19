<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

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
        $this->typeHebergements = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement $typeHebergement
     *
     * @return TypeHebergementUnifie
     */
    public function addTypeHebergement(\Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement $typeHebergement)
    {
        $this->typeHebergements[] = $typeHebergement->setTypeHebergementUnifie($this);

        return $this;
    }

    /**
     * Remove typeHebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement $typeHebergement
     */
    public function removeTypeHebergement(\Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement $typeHebergement)
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
