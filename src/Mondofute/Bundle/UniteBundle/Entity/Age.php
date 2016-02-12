<?php

namespace Mondofute\Bundle\UniteBundle\Entity;

/**
 * Age
 */
class Age
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
     * @var \Mondofute\Bundle\UniteBundle\Entity\UniteAge
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
     * @return Age
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get unite
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\UniteAge
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set unite
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\UniteAge $unite
     *
     * @return Age
     */
    public function setUnite(\Mondofute\Bundle\UniteBundle\Entity\UniteAge $unite = null)
    {
        $this->unite = $unite;

        return $this;
    }
}
