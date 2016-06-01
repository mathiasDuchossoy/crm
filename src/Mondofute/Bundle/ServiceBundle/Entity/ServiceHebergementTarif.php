<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * ServiceHebergementTarif
 */
class ServiceHebergementTarif
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Tarif
     */
    private $tarif;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\UnitePeriode
     */
    private $unitePeriode;
    /**
     * @var \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement
     */
    private $service;

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
     * Get tarif
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Tarif
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set tarif
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Tarif $tarif
     *
     * @return ServiceHebergementTarif
     */
    public function setTarif(\Mondofute\Bundle\UniteBundle\Entity\Tarif $tarif = null)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get unitePeriode
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\UnitePeriode
     */
    public function getUnitePeriode()
    {
        return $this->unitePeriode;
    }

    /**
     * Set unitePeriode
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\UnitePeriode $unitePeriode
     *
     * @return ServiceHebergementTarif
     */
    public function setUnitePeriode(\Mondofute\Bundle\UniteBundle\Entity\UnitePeriode $unitePeriode = null)
    {
        $this->unitePeriode = $unitePeriode;

        return $this;
    }

    /**
     * Get service
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement $service
     *
     * @return ServiceHebergementTarif
     */
    public function setService(\Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement $service = null)
    {
        $this->service = $service;

        return $this;
    }
}
