<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * DomainePhotoTraduction
 */
class DomainePhotoTraduction
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
     * @var \Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto
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
     * @return DomainePhotoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto $photo
     *
     * @return DomainePhotoTraduction
     */
    public function setPhoto(\Mondofute\Bundle\DomaineBundle\Entity\DomainePhoto $photo = null)
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
     * @return DomainePhotoTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
