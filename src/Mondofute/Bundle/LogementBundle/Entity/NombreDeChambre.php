<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * NombreDeChambre
 */
class NombreDeChambre
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $logements;
    /**
     * @var Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logements = new ArrayCollection();
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
     * @param int $id
     *
     * @return NombreDeChambre
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add logement
     *
     * @param Logement $logement
     *
     * @return NombreDeChambre
     */
    public function addLogement(Logement $logement)
    {
        $this->logements[] = $logement;

        return $this;
    }

    /**
     * Remove logement
     *
     * @param Logement $logement
     */
    public function removeLogement(Logement $logement)
    {
        $this->logements->removeElement($logement);
    }

    /**
     * Get logements
     *
     * @return Collection
     */
    public function getLogements()
    {
        return $this->logements;
    }

    /**
     * Add traduction
     *
     * @param NombreDeChambreTraduction $traduction
     *
     * @return NombreDeChambre
     */
    public function addTraduction(NombreDeChambreTraduction $traduction)
    {
        $this->traductions[] = $traduction->setNombreDeChambre($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param NombreDeChambreTraduction $traduction
     */
    public function removeTraduction(NombreDeChambreTraduction $traduction)
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
}
