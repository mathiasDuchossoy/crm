<?php

namespace Mondofute\Bundle\UniteBundle\Entity;

/**
 * ClassementHebergement
 */
class ClassementHebergement
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $valeur;
    /**
     * @var UniteClassementHebergement
     */
    private $unite;

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
     * Get valeur
     *
     * @return int
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set valeur
     *
     * @param integer $valeur
     *
     * @return ClassementHebergement
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get unite
     *
     * @return UniteClassementHebergement
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set unite
     *
     * @param UniteClassementHebergement $unite
     *
     * @return ClassementHebergement
     */
    public function setUnite(UniteClassementHebergement $unite = null)
    {
        $this->unite = $unite;

        return $this;
    }
}
