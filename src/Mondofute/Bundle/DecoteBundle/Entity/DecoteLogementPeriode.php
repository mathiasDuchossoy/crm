<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

/**
 * DecoteLogementPeriode
 */
class DecoteLogementPeriode
{
    /**
     * @var \Mondofute\Bundle\DecoteBundle\Entity\Decote
     */
    private $decote;

    /**
     * @var \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    private $periode;

    /**
     * @var \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    private $logement;

    /**
     * Get decote
     *
     * @return \Mondofute\Bundle\DecoteBundle\Entity\Decote
     */
    public function getDecote()
    {
        return $this->decote;
    }

    /**
     * Set decote
     *
     * @param \Mondofute\Bundle\DecoteBundle\Entity\Decote $decote
     *
     * @return DecoteLogementPeriode
     */
    public function setDecote(\Mondofute\Bundle\DecoteBundle\Entity\Decote $decote)
    {
        $this->decote = $decote;

        return $this;
    }

    /**
     * Get periode
     *
     * @return \Mondofute\Bundle\PeriodeBundle\Entity\Periode
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set periode
     *
     * @param \Mondofute\Bundle\PeriodeBundle\Entity\Periode $periode
     *
     * @return DecoteLogementPeriode
     */
    public function setPeriode(\Mondofute\Bundle\PeriodeBundle\Entity\Periode $periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get logement
     *
     * @return \Mondofute\Bundle\LogementBundle\Entity\Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param \Mondofute\Bundle\LogementBundle\Entity\Logement $logement
     *
     * @return DecoteLogementPeriode
     */
    public function setLogement(\Mondofute\Bundle\LogementBundle\Entity\Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
}
