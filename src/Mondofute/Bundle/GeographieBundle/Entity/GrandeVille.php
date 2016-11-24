<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;

/**
 * GrandeVille
 */
class GrandeVille
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var CoordonneesGPS
     */
    private $coordonneesGps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
    }

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
     * Set id
     *
     * @param int $id
     *
     * @return GrandeVille
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param GrandeVilleTraduction $traduction
     */
    public function removeTraduction(GrandeVilleTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * @param $traductions
     * @return $this
     */
    public function setTraductions($traductions)
    {
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param GrandeVilleTraduction $traduction
     *
     * @return GrandeVille
     */
    public function addTraduction(GrandeVilleTraduction $traduction)
    {
        $this->traductions[] = $traduction->setGrandeVille($this);

        return $this;
    }

    /**
     * Get coordonneesGps
     *
     * @return CoordonneesGPS
     */
    public function getCoordonneesGps()
    {
        return $this->coordonneesGps;
    }

    /**
     * Set coordonneesGps
     *
     * @param CoordonneesGPS $coordonneesGps
     *
     * @return GrandeVille
     */
    public function setCoordonneesGps(CoordonneesGPS $coordonneesGps = null)
    {
        $this->coordonneesGps = $coordonneesGps;

        return $this;
    }
}
