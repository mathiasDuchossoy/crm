<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * KmPistes
 */
abstract class KmPistes
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Distance
     */
    private $longueur;

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
     * Get longueur
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Distance
     */
    public function getLongueur()
    {
        return $this->longueur;
    }

    /**
     * Set longueur
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Distance $longueur
     *
     * @return KmPistes
     */
    public function setLongueur(\Mondofute\Bundle\UniteBundle\Entity\Distance $longueur = null)
    {
        $this->longueur = $longueur;

        return $this;
    }
}
