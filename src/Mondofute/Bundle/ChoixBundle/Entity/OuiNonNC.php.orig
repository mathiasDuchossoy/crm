<?php

namespace Mondofute\Bundle\ChoixBundle\Entity;

/**
 * OuiNonNC
 */
class OuiNonNC
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $classement;
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
     * Get classement
     *
     * @return int
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set classement
     *
     * @param integer $classement
     *
     * @return OuiNonNC
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNCTraduction $traduction
     *
     * @return OuiNonNC
     */
    public function addTraduction(\Mondofute\Bundle\ChoixBundle\Entity\OuiNonNCTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNCTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\ChoixBundle\Entity\OuiNonNCTraduction $traduction)
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
