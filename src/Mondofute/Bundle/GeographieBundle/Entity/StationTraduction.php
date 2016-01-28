<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * StationTraduction
 */
class StationTraduction
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
     * @var string
     */
    private $enVoiture;

    /**
     * @var string
     */
    private $enTrain;

    /**
     * @var string
     */
    private $enAvion;

    /**
     * @var string
     */
    private $distancesGrandesVilles;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Station
     */
    private $station;
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
     * @return StationTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get enVoiture
     *
     * @return string
     */
    public function getEnVoiture()
    {
        return $this->enVoiture;
    }

    /**
     * Set enVoiture
     *
     * @param string $enVoiture
     *
     * @return StationTraduction
     */
    public function setEnVoiture($enVoiture)
    {
        $this->enVoiture = $enVoiture;

        return $this;
    }

    /**
     * Get enTrain
     *
     * @return string
     */
    public function getEnTrain()
    {
        return $this->enTrain;
    }

    /**
     * Set enTrain
     *
     * @param string $enTrain
     *
     * @return StationTraduction
     */
    public function setEnTrain($enTrain)
    {
        $this->enTrain = $enTrain;

        return $this;
    }

    /**
     * Get enAvion
     *
     * @return string
     */
    public function getEnAvion()
    {
        return $this->enAvion;
    }

    /**
     * Set enAvion
     *
     * @param string $enAvion
     *
     * @return StationTraduction
     */
    public function setEnAvion($enAvion)
    {
        $this->enAvion = $enAvion;

        return $this;
    }

    /**
     * Get distancesGrandesVilles
     *
     * @return string
     */
    public function getDistancesGrandesVilles()
    {
        return $this->distancesGrandesVilles;
    }

    /**
     * Set distancesGrandesVilles
     *
     * @param string $distancesGrandesVilles
     *
     * @return StationTraduction
     */
    public function setDistancesGrandesVilles($distancesGrandesVilles)
    {
        $this->distancesGrandesVilles = $distancesGrandesVilles;

        return $this;
    }

    /**
     * Get station
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Station $station
     *
     * @return StationTraduction
     */
    public function setStation(\Mondofute\Bundle\GeographieBundle\Entity\Station $station = null)
    {
        $this->station = $station;

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
     * @return StationTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
