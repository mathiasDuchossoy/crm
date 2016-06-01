<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

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
     * @var \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    private $hebergementUnifie;
    /**
     * @var \Mondofute\Bundle\ServiceBundle\Entity\Service
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
        $this->tarifs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    public function getHebergementUnifie()
    {
        return $this->hebergementUnifie;
    }

    /**
     * Set hebergementUnifie
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergementUnifie
     *
     * @return ServiceHebergement
     */
    public function setHebergementUnifie(
        \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergementUnifie = null
    ) {
        $this->hebergementUnifie = $hebergementUnifie;

        return $this;
    }

    /**
     * Get service
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\Service $service
     *
     * @return ServiceHebergement
     */
    public function setService(\Mondofute\Bundle\ServiceBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Add tarif
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif $tarif
     *
     * @return ServiceHebergement
     */
    public function addTarif(\Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif $tarif)
    {
        $this->tarifs[] = $tarif;

        return $this;
    }

    /**
     * Remove tarif
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif $tarif
     */
    public function removeTarif(\Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif $tarif)
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
