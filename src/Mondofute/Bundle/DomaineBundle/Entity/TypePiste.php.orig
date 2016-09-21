<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * TypePiste
 */
class TypePiste
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get classement
     *
     * @return integer
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
     * @return TypePiste
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\TypePisteTraduction $traduction
     *
     * @return TypePiste
     */
    public function addTraduction(\Mondofute\Bundle\DomaineBundle\Entity\TypePisteTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\TypePisteTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\DomaineBundle\Entity\TypePisteTraduction $traduction)
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
