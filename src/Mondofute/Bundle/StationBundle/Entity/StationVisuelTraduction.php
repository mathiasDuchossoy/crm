<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationVisuelTraduction
 */
class StationVisuelTraduction
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
     * @var \Mondofute\Bundle\StationBundle\Entity\StationVisuel
     */
    private $stationVisuel;
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
     * @return StationVisuelTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get stationVisuel
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationVisuel
     */
    public function getStationVisuel()
    {
        return $this->stationVisuel;
    }

    /**
     * Set stationVisuel
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationVisuel $stationVisuel
     *
     * @return StationVisuelTraduction
     */
    public function setStationVisuel(\Mondofute\Bundle\StationBundle\Entity\StationVisuel $stationVisuel = null)
    {
        $this->stationVisuel = $stationVisuel;

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
     * @return StationVisuelTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
