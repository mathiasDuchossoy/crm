<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * DomaineVideoTraduction
 */
class DomaineVideoTraduction
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
     * @var DomaineVideo
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
     * @return DomaineVideoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get video
     *
     * @return DomaineVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param DomaineVideo $video
     *
     * @return DomaineVideoTraduction
     */
    public function setVideo(DomaineVideo $video = null)
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
     * @return DomaineVideoTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
