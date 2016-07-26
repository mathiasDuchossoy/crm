<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * ZoneTouristiqueImageTraduction
 */
class ZoneTouristiqueImageTraduction
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage
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
     * @return ZoneTouristiqueImageTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage $image
     *
     * @return ZoneTouristiqueImageTraduction
     */
    public function setImage(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage $image = null)
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
     * @return ZoneTouristiqueImageTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
