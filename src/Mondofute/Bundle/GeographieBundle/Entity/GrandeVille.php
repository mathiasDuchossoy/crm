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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Nucleus\MoyenComBundle\Entity\CoordonneesGPS
     */
    private $coordonneesGps;

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

    /**
     * Get coordonneesGps
     *
     * @return \Nucleus\MoyenComBundle\Entity\CoordonneesGPS
     */
    public function getCoordonneesGps()
    {
        return $this->coordonneesGps;
    }

    /**
     * Set coordonneesGps
     *
     * @param \Nucleus\MoyenComBundle\Entity\CoordonneesGPS $coordonneesGps
     *
     * @return GrandeVille
     */
    public function setCoordonneesGps(\Nucleus\MoyenComBundle\Entity\CoordonneesGPS $coordonneesGps = null)
    {
        $this->coordonneesGps = $coordonneesGps;

        return $this;
    }
}
