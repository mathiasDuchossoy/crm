<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;

/**
 * ServiceHebergement
 */
class ServiceHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var HebergementUnifie
     */
    private $hebergementUnifie;
    /**
     * @var Service
     */
    private $service;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
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
     * Get hebergementUnifie
     *
     * @return HebergementUnifie
     */
    public function getHebergementUnifie()
    {
        return $this->hebergementUnifie;
    }

    /**
     * Set hebergementUnifie
     *
     * @param HebergementUnifie $hebergementUnifie
     *
     * @return ServiceHebergement
     */
    public function setHebergementUnifie(
        HebergementUnifie $hebergementUnifie = null
    ) {
        $this->hebergementUnifie = $hebergementUnifie;

        return $this;
    }

    /**
     * Get service
     *
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param Service $service
     *
     * @return ServiceHebergement
     */
    public function setService(Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Add tarif
     *
     * @param ServiceHebergementTarif $tarif
     *
     * @return ServiceHebergement
     */
    public function addTarif(ServiceHebergementTarif $tarif)
    {
        $this->tarifs[] = $tarif->setService($this);

        return $this;
    }

    /**
     * Remove tarif
     *
     * @param ServiceHebergementTarif $tarif
     */
    public function removeTarif(ServiceHebergementTarif $tarif)
    {
        $this->tarifs->removeElement($tarif);
    }

    /**
     * Get tarifs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarifs()
    {
        return $this->tarifs;
    }
}
