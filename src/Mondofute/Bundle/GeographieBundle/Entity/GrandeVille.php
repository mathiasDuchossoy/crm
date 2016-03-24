<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\CoordonneesGps
     */
    private $coordonneesGps;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get coordonneesGps
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\CoordonneesGps
     */
    public function getCoordonneesGps()
    {
        return $this->coordonneesGps;
    }

    /**
     * Set coordonneesGps
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\CoordonneesGps $coordonneesGps
     *
     * @return GrandeVille
     */
    public function setCoordonneesGps(\Mondofute\Bundle\GeographieBundle\Entity\CoordonneesGps $coordonneesGps = null)
    {
        $this->coordonneesGps = $coordonneesGps;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\GrandeVilleTraduction $traduction
     *
     * @return GrandeVille
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\GrandeVilleTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\GrandeVilleTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\GrandeVilleTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }
}
