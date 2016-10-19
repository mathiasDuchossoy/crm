<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * RegionVideoTraduction
 */
class RegionVideoTraduction
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
     * @var RegionVideo
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
     * @return RegionVideoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get video
     *
     * @return RegionVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param RegionVideo $video
     *
     * @return RegionVideoTraduction
     */
    public function setVideo(RegionVideo $video = null)
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
     * @return RegionVideoTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
