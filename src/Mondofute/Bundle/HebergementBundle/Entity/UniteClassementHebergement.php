<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * UniteClassementHebergement
 */
class UniteClassementHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add traduction
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergementTraduction $traduction
     *
     * @return UniteClassementHebergement
     */
    public function addTraduction(
        \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergementTraduction $traduction
    ) {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergementTraduction $traduction
     */
    public function removeTraduction(
        \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergementTraduction $traduction
    ) {
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
