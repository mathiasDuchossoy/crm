<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use ReflectionClass;

/**
 * Emplacement
 */
abstract class Emplacement
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

    public function __toString()
    {
        $reflect = new ReflectionClass($this);
        // TODO: Implement __toString() method.
        return $reflect->getShortName();
    }
}
