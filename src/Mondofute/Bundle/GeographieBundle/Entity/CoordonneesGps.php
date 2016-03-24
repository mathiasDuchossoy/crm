<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * CoordonneesGps
 */
class CoordonneesGps
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;


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
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return CoordonneesGps
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return CoordonneesGps
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }
}
