<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * TypeService
 */
class TypeService
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
     * @param \Mondofute\Bundle\ServiceBundle\Entity\TypeServiceTraduction $traduction
     *
     * @return TypeService
     */
    public function addTraduction(\Mondofute\Bundle\ServiceBundle\Entity\TypeServiceTraduction $traduction)
    {
        $this->traductions[] = $traduction->setTypeService($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\TypeServiceTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\ServiceBundle\Entity\TypeServiceTraduction $traduction)
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
