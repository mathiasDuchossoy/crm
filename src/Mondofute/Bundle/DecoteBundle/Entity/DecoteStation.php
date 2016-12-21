<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\StationBundle\Entity\Station;

/**
 * DecoteStation
 */
class DecoteStation
{
    /**
     * @var Station
     */
    private $station;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var Decote
     */
    private $decote;

    /**
     * Get station
     *
     * @return Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param Station $station
     *
     * @return DecoteStation
     */
    public function setStation(Station $station = null)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return DecoteStation
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get decote
     *
     * @return Decote
     */
    public function getDecote()
    {
        return $this->decote;
    }

    /**
     * Set decote
     *
     * @param Decote $decote
     *
     * @return DecoteStation
     */
    public function setDecote(Decote $decote = null)
    {
        $this->decote = $decote;

        return $this;
    }
}
