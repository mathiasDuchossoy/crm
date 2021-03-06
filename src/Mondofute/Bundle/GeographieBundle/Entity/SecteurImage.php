<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * SecteurImage
 */
class SecteurImage
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Secteur
     */
    private $secteur;
    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $image;
    /**
     * @var boolean
     */
    private $actif = false;

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
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\SecteurImageTraduction $traduction
     *
     * @return SecteurImage
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\SecteurImageTraduction $traduction)
    {
        $this->traductions[] = $traduction->setImage($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\SecteurImageTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\SecteurImageTraduction $traduction)
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
     * @return SecteurImage
     */
    public function setSecteur(\Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur = null)
    {
        $this->secteur = $secteur;

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
     * @return SecteurImage
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
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
     * @return SecteurImage
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }
}
