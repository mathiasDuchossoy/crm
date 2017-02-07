<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\PeriodeBundle\Entity\Periode;

/**
 * SejourPeriode
 */
class SejourPeriode extends CommandeLigneSejour
{
    /**
     * @var Periode
     */
    private $periode;

    /**
     * Get periode
     *
     * @return Periode
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set periode
     *
     * @param Periode $periode
     *
     * @return SejourPeriode
     */
    public function setPeriode(Periode $periode = null)
    {
        $this->periode = $periode;

        return $this;
    }
}
