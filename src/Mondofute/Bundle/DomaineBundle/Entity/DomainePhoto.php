<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * DomainePhoto
 */
class DomainePhoto
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
     * @var \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    private $domaine;
    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $photo;

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
     * @return DomainePhoto
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomainePhotoTraduction $traduction
     *
     * @return DomainePhoto
     */
    public function addTraduction(\Mondofute\Bundle\DomaineBundle\Entity\DomainePhotoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPhoto($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomainePhotoTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\DomaineBundle\Entity\DomainePhotoTraduction $traduction)
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
     * Get domaine
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set domaine
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaine
     *
     * @return DomainePhoto
     */
    public function setDomaine(\Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaine = null)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $photo
     *
     * @return DomainePhoto
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }
}
