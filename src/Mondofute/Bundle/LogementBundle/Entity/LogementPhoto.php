<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

/**
 * LogementPhoto
 */
class LogementPhoto
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
     * @var \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    private $logement;
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
     * @return LogementPhoto
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\LogementPhotoTraduction $traduction
     *
     * @return LogementPhoto
     */
    public function addTraduction(\Mondofute\Bundle\LogementBundle\Entity\LogementPhotoTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPhoto($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\LogementPhotoTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\LogementBundle\Entity\LogementPhotoTraduction $traduction)
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
     * Get logement
     *
     * @return \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\Logement $logement
     *
     * @return LogementPhoto
     */
    public function setLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement = null)
    {
        $this->logement = $logement;

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
     * @return LogementPhoto
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }
}
