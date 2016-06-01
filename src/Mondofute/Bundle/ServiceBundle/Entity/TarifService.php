<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * TarifService
 */
class TarifService
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
     * @var \Mondofute\Bundle\ServiceBundle\Entity\Service
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
     * @return TarifService
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
     * @return TarifService
     */
    public function setUnitePeriode(\Mondofute\Bundle\UniteBundle\Entity\UnitePeriode $unitePeriode = null)
    {
        $this->unitePeriode = $unitePeriode;

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
     * @return TarifService
     */
    public function setService(\Mondofute\Bundle\ServiceBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }
}
