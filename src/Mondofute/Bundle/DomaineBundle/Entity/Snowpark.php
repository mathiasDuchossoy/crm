<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC
     */
    private $present;

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
     * @param \Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction $traduction
     *
     * @return Snowpark
     */
    public function addTraduction(\Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction $traduction)
    {
        $this->traductions[] = $traduction->setSnowpark($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\DomaineBundle\Entity\SnowparkTraduction $traduction)
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * Get present
     *
     * @return \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * Set present
     *
     * @param \Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC $present
     *
     * @return Snowpark
     */
    public function setPresent(\Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC $present = null)
    {
        $this->present = $present;

        return $this;
    }
}
