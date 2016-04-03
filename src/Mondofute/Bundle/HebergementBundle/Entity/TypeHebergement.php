<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * TypeHebergement
 */
class TypeHebergement
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
     * Add hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     *
     * @return TypeHebergement
     */
    public function addHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement;

        return $this;
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
}
