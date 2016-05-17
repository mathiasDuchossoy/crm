<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * LogementUnifie
 */
class LogementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $logements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logements = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove logement
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\Logement $logement
     */
    public function removeLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement)
    {
        $this->logements->removeElement($logement);
    }

    /**
     * Get logements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogements()
    {
        return $this->logements;
    }

    /**
     * @param ArrayCollection $departements
     * @return LogementUnifie $this
     */
    public function setLogements(ArrayCollection $logements)
    {
        $this->getLogements()->clear();

        foreach ($logements as $logement) {
            $this->addLogement($logement);
        }
        return $this;
    }

    /**
     * Add logement
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\Logement $logement
     *
     * @return LogementUnifie
     */
    public function addLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement)
    {
        $this->logements[] = $logement;

        return $this;
    }
}
