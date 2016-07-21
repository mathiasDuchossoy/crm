<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * SecteurPhoto
 */
class SecteurPhoto
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Secteur
     */
    private $secteur;
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
     * @return SecteurPhoto
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\SecteurPhotoTraduction $traduction
     *
     * @return SecteurPhoto
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\SecteurPhotoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPhoto($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\SecteurPhotoTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\SecteurPhotoTraduction $traduction)
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
     * Get secteur
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Secteur
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * Set secteur
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur
     *
     * @return SecteurPhoto
     */
    public function setSecteur(\Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur = null)
    {
        $this->secteur = $secteur;

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
     * @return SecteurPhoto
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }
}
