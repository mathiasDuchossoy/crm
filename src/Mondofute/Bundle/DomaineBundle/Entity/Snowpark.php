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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
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

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
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

}
