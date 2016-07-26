<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * ProfilPhotoTraduction
 */
class ProfilPhotoTraduction
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto
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
     * @return ProfilPhotoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto $photo
     *
     * @return ProfilPhotoTraduction
     */
    public function setPhoto(\Mondofute\Bundle\GeographieBundle\Entity\ProfilPhoto $photo = null)
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
     * @return ProfilPhotoTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
