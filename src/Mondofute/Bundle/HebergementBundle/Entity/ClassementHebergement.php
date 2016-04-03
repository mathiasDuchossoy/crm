<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

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
     * @var \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement
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
     * @return \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set unite
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement $unite
     *
     * @return ClassementHebergement
     */
    public function setUnite(\Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement $unite = null)
    {
        $this->unite = $unite;

        return $this;
    }
}
