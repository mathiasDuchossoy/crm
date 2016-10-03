<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * FournisseurPrestationAnnexeDureeSejour
 */
class FournisseurPrestationAnnexeDureeSejour
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;


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
     * Get min
     *
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set min
     *
     * @param integer $min
     *
     * @return FournisseurPrestationAnnexeDureeSejour
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get max
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set max
     *
     * @param integer $max
     *
     * @return FournisseurPrestationAnnexeDureeSejour
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }
}
