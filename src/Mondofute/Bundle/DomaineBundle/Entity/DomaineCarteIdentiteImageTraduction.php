<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * DomaineCarteIdentiteImageTraduction
 */
class DomaineCarteIdentiteImageTraduction
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
     * @var \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImage
     */
    private $image;
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
     * @return DomaineCarteIdentiteImageTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImage $image
     *
     * @return DomaineCarteIdentiteImageTraduction
     */
    public function setImage(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteImage $image = null)
    {
        $this->image = $image;

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
     * @return DomaineCarteIdentiteImageTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
