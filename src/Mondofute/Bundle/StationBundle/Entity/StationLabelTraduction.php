<?php

namespace Mondofute\Bundle\StationBundle\Entity;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * StationLabelTraduction
 */
class StationLabelTraduction
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
     * @var StationLabel
     */
    private $stationLabel;
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
     * @return StationLabelTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get stationLabel
     *
     * @return StationLabel
     */
    public function getStationLabel()
    {
        return $this->stationLabel;
    }

    /**
     * Set stationLabel
     *
     * @param StationLabel $stationLabel
     *
     * @return StationLabelTraduction
     */
    public function setStationLabel(StationLabel $stationLabel = null)
    {
        $this->stationLabel = $stationLabel;

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
     * @return StationLabelTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
