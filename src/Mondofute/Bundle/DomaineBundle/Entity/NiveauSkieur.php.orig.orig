<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * NiveauSkieur
 */
class NiveauSkieur
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $domaineCarteIDentite;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->domaineCarteIDentite = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Add traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieurTraduction $traduction
     *
     * @return NiveauSkieur
     */
    public function addTraduction(\Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieurTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieurTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieurTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * Add domaineCarteIDentite
     *
     * @param \Mondofute\Bundle\DomaineBUndle\Entity\DomaineCarteIdentite $domaineCarteIDentite
     *
     * @return NiveauSkieur
     */
    public function addDomaineCarteIDentite(\Mondofute\Bundle\DomaineBUndle\Entity\DomaineCarteIdentite $domaineCarteIDentite)
    {
        $this->domaineCarteIDentite[] = $domaineCarteIDentite;

        return $this;
    }

    /**
     * Remove domaineCarteIDentite
     *
     * @param \Mondofute\Bundle\DomaineBUndle\Entity\DomaineCarteIdentite $domaineCarteIDentite
     */
    public function removeDomaineCarteIDentite(\Mondofute\Bundle\DomaineBUndle\Entity\DomaineCarteIdentite $domaineCarteIDentite)
    {
        $this->domaineCarteIDentite->removeElement($domaineCarteIDentite);
    }

    /**
     * Get domaineCarteIDentite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDomaineCarteIDentite()
    {
        return $this->domaineCarteIDentite;
    }
}
