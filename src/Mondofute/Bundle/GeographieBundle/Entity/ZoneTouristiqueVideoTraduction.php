<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * ZoneTouristiqueVideoTraduction
 */
class ZoneTouristiqueVideoTraduction
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
     * @var ZoneTouristiqueVideo
     */
    private $video;
    /**
     * @var Langue
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
     * @return ZoneTouristiqueVideoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get video
     *
     * @return ZoneTouristiqueVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param ZoneTouristiqueVideo $video
     *
     * @return ZoneTouristiqueVideoTraduction
     */
    public function setVideo(ZoneTouristiqueVideo $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return ZoneTouristiqueVideoTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
