<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * DomaineCarteIdentiteImage
 */
class DomaineCarteIdentiteImage
{
    /**
     * @var int
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
     * @var \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    private $domaineCarteIdentite;
    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $image;

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
     * @return DomaineCarteIdentiteImage
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImageTraduction $traduction
     *
     * @return DomaineCarteIdentiteImage
     */
    public function addTraduction(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImageTraduction $traduction)
    {
        $this->traductions[] = $traduction->setImage($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImageTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImageTraduction $traduction)
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
     * Get domaineCarteIdentite
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    public function getDomaineCarteIdentite()
    {
        return $this->domaineCarteIdentite;
    }

    /**
     * Set domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return DomaineCarteIdentiteImage
     */
    public function setDomaineCarteIdentite(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite = null)
    {
        $this->domaineCarteIdentite = $domaineCarteIdentite;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     *
     * @return DomaineCarteIdentiteImage
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }
}
