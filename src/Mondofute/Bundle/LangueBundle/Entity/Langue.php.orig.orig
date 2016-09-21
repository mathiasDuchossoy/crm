<?php

namespace Mondofute\Bundle\LangueBundle\Entity;

/**
 * Langue
 */
class Langue
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $code;
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Langue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\LangueTraduction $traduction
     *
     * @return Langue
     */
    public function addTraduction(\Mondofute\Bundle\LangueBundle\Entity\LangueTraduction $traduction)
    {
        $this->traductions[] = $traduction->setLangueTraduction($traduction);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\LangueTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\LangueBundle\Entity\LangueTraduction $traduction)
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
