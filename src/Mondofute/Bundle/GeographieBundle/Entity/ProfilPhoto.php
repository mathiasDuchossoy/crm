<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * ProfilPhoto
 */
class ProfilPhoto
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Profil
     */
    private $profil;
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
     * @return ProfilPhoto
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhotoTraduction $traduction
     *
     * @return ProfilPhoto
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ProfilPhotoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPhoto($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhotoTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ProfilPhotoTraduction $traduction)
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
     * Get profil
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Profil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set profil
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Profil $profil
     *
     * @return ProfilPhoto
     */
    public function setProfil(\Mondofute\Bundle\GeographieBundle\Entity\Profil $profil = null)
    {
        $this->profil = $profil;

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
     * @return ProfilPhoto
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }
}
