<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ZoneTouristiqueUnifie
 */
class ZoneTouristiqueUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $zoneTouristiques;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->zoneTouristiques = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove zoneTouristique
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique
     */
    public function removeZoneTouristique(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques->removeElement($zoneTouristique);
    }

    /**
     * Get zoneTouristiques
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZoneTouristiques()
    {
        return $this->zoneTouristiques;
    }

    /**
     * @param ArrayCollection $zoneToutistiques
     * @return $this
     */
    public function setZoneTouristiques(ArrayCollection $zoneToutistiques)
    {
        $this->getZoneTouristiques()->clear();

        foreach ($zoneToutistiques as $zoneToutistique) {
            $this->addZoneTouristique($zoneToutistique);
        }
        return $this;
    }

    /**
     * Add zoneTouristique
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique
     *
     * @return ZoneTouristiqueUnifie
     */
    public function addZoneTouristique(\Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques[] = $zoneTouristique->setZoneTouristiqueUnifie($this);

        return $this;
    }

}
