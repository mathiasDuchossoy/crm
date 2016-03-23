<?php

namespace Mondofute\Bundle\UniteBundle\Entity;

/**
 * Tarif
 */
class Tarif
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $valeur;

    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\UniteTarif
     */
    private $unite;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Tarif
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get unite
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\UniteTarif
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set unite
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\UniteTarif $unite
     *
     * @return Tarif
     */
    public function setUnite(\Mondofute\Bundle\UniteBundle\Entity\UniteTarif $unite = null)
    {
        $this->unite = $unite;

        return $this;
    }
}
