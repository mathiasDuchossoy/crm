<?php

namespace Mondofute\Bundle\UniteBundle\Entity;

/**
 * Unite
 */
class Unite
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $multiplicateurReference;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Unite
     */
    private $reference;

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
     * Get multiplicateurReference
     *
     * @return float
     */
    public function getMultiplicateurReference()
    {
        return $this->multiplicateurReference;
    }

    /**
     * Set multiplicateurReference
     *
     * @param float $multiplicateurReference
     *
     * @return Unite
     */
    public function setMultiplicateurReference($multiplicateurReference)
    {
        $this->multiplicateurReference = $multiplicateurReference;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\UniteTraduction $traduction
     *
     * @return Unite
     */
    public function addTraduction(\Mondofute\Bundle\UniteBundle\Entity\UniteTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\UniteTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\UniteBundle\Entity\UniteTraduction $traduction)
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

    /**
     * Get reference
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Unite
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set reference
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Unite $reference
     *
     * @return Unite
     */
    public function setReference(\Mondofute\Bundle\UniteBundle\Entity\Unite $reference = null)
    {
        $this->reference = $reference;

        return $this;
    }
}
