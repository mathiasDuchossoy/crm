<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * DepartementPhoto
 */
class DepartementPhoto
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    private $departement;
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
     * @return DepartementPhoto
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhotoTraduction $traduction
     *
     * @return DepartementPhoto
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DepartementPhotoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPhoto($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhotoTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DepartementPhotoTraduction $traduction)
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
     * Get departement
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     *
     * @return DepartementPhoto
     */
    public function setDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

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
     * @return DepartementPhoto
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }
}
