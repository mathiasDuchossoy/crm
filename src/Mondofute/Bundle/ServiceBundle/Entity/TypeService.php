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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $services;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->services = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Add service
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\Service $service
     *
     * @return TypeService
     */
    public function addService(\Mondofute\Bundle\ServiceBundle\Entity\Service $service)
    {
        $this->services[] = $service->setType($this);

        return $this;
    }

    /**
     * Remove service
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\Service $service
     */
    public function removeService(\Mondofute\Bundle\ServiceBundle\Entity\Service $service)
    {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }
}
