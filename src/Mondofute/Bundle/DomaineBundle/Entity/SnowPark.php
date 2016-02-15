<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Snowpark
 */
class Snowpark
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
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
     * @param SnowparkTraduction $traduction
     *
     * @return Snowpark
     */
    public function addTraduction(SnowparkTraduction $traduction)
    {
        $this->traductions[] = $traduction->setSnowpark($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param SnowparkTraduction $traduction
     */
    public function removeTraduction(SnowparkTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    public function __clone()
    {
        /** @var SnowparkTraduction $cloneTraduction */
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setSnowpark($this);
            }
        }
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

}
