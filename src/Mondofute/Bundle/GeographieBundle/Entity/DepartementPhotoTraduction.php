<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * DepartementPhotoTraduction
 */
class DepartementPhotoTraduction
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto
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
     * @return DepartementPhotoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto $photo
     *
     * @return DepartementPhotoTraduction
     */
    public function setPhoto(\Mondofute\Bundle\GeographieBundle\Entity\DepartementPhoto $photo = null)
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
     * @return DepartementPhotoTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
