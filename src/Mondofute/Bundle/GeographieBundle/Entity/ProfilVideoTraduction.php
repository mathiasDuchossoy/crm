<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * ProfilVideoTraduction
 */
class ProfilVideoTraduction
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
     * @var ProfilVideo
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
     * @return ProfilVideoTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get video
     *
     * @return ProfilVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param ProfilVideo $video
     *
     * @return ProfilVideoTraduction
     */
    public function setVideo(ProfilVideo $video = null)
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
     * @return ProfilVideoTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
