<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Handiski
 */
class Handiski
{
    /**
     * @var integer
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add traduction
     *
     * @param HandiskiTraduction $traduction
     *
     * @return Handiski
     */
    public function addTraduction(HandiskiTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHandiski($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param HandiskiTraduction $traduction
     */
    public function removeTraduction(HandiskiTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    public function __clone()
    {
        /** @var HandiskiTraduction $cloneTraduction */
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setHandiski($this);
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
