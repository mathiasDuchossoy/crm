<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


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
     * @var Collection
     */
    private $zoneTouristiques;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->zoneTouristiques = new ArrayCollection();
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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove zoneTouristique
     *
     * @param ZoneTouristique $zoneTouristique
     */
    public function removeZoneTouristique(ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques->removeElement($zoneTouristique);
    }

    /**
     * Get zoneTouristiques
     *
     * @return Collection
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
     * @param ZoneTouristique $zoneTouristique
     *
     * @return ZoneTouristiqueUnifie
     */
    public function addZoneTouristique(ZoneTouristique $zoneTouristique)
    {
        $this->zoneTouristiques[] = $zoneTouristique->setZoneTouristiqueUnifie($this);

        return $this;
    }

}
