<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * HebergementVisuel
 */
abstract class HebergementVisuel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $actif = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * @var \Mondofute\Bundle\HebergementBundle\Entity\Hebergement
     */
    private $hebergement;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $visuel;

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
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return HebergementVisuel
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction $traduction
     *
     * @return HebergementVisuel
     */
    public function addTraduction(\Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction $traduction)
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
     * Get hebergement
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement
     *
     * @return HebergementVisuel
     */
    public function setHebergement(\Mondofute\Bundle\HebergementBundle\Entity\Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    /**
     * Get visuel
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getVisuel()
    {
        return $this->visuel;
    }

    /**
     * Set visuel
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $visuel
     *
     * @return HebergementVisuel
     */
    public function setVisuel(\Application\Sonata\MediaBundle\Entity\Media $visuel = null)
    {
        $this->visuel = $visuel;

        return $this;
    }
}
