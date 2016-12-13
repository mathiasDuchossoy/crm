<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * ServiceInterlocuteur
 */
class ServiceInterlocuteur
{
    /**
     * @var integer
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteurTraduction $traduction
     *
     * @return ServiceInterlocuteur
     */
    public function addTraduction(\Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteurTraduction $traduction)
    {
        $this->traductions[] = $traduction->setService($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteurTraduction $traduction
     */
    public function removeTraduction(
        \Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteurTraduction $traduction
    ) {
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
