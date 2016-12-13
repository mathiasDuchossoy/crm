<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationDescriptionTraduction
 */
class StationDescriptionTraduction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $accroche;

    /**
     * @var string
     */
    private $generalite;

    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationDescription
     */
    private $stationDescription;

    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get accroche
     *
     * @return string
     */
    public function getAccroche()
    {
        return $this->accroche;
    }

    /**
     * Set accroche
     *
     * @param string $accroche
     *
     * @return StationDescriptionTraduction
     */
    public function setAccroche($accroche)
    {
        $this->accroche = $accroche;

        return $this;
    }

    /**
     * Get generalite
     *
     * @return string
     */
    public function getGeneralite()
    {
        return $this->generalite;
    }

    /**
     * Set generalite
     *
     * @param string $generalite
     *
     * @return StationDescriptionTraduction
     */
    public function setGeneralite($generalite)
    {
        $this->generalite = $generalite;

        return $this;
    }

    /**
     * Get stationDescription
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationDescription
     */
    public function getStationDescription()
    {
        return $this->stationDescription;
    }

    /**
     * Set stationDescription
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription
     *
     * @return StationDescriptionTraduction
     */
    public function setStationDescription(
        \Mondofute\Bundle\StationBundle\Entity\StationDescription $stationDescription = null
    ) {
        $this->stationDescription = $stationDescription;

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
     * @return StationDescriptionTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
