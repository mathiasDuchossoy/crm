<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

/**
 * LogementPhotoTraduction
 */
class LogementPhotoTraduction
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $libelle;
    /**
     * @var \Mondofute\Bundle\LogementBundle\Entity\LogementPhoto
     */
    private $photo;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

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
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return LogementPhotoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Mondofute\Bundle\LogementBundle\Entity\LogementPhoto
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\LogementPhoto $photo
     *
     * @return LogementPhotoTraduction
     */
    public function setPhoto(\Mondofute\Bundle\LogementBundle\Entity\LogementPhoto $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return LogementPhotoTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
