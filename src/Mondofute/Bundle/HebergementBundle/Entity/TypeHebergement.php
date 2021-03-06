<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * TypeHebergement
 */
class TypeHebergement
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $individuel;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $hebergements;
    /**
     * @var TypeHebergementUnifie
     */
    private $typeHebergementUnifie;
    /**
     * @var Site
     */
    private $site;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->hebergements = new ArrayCollection();
    }

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
     * Get individuel
     *
     * @return bool
     */
    public function getIndividuel()
    {
        return $this->individuel;
    }

    /**
     * Set individuel
     *
     * @param boolean $individuel
     *
     * @return TypeHebergement
     */
    public function setIndividuel($individuel)
    {
        $this->individuel = $individuel;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param TypeHebergementTraduction $traduction
     *
     * @return TypeHebergement
     */
    public function addTraduction(TypeHebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setTypeHebergement($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param TypeHebergementTraduction $traduction
     */
    public function removeTraduction(TypeHebergementTraduction $traduction)
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
     * Add hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return TypeHebergement
     */
    public function addHebergement(Hebergement $hebergement)
    {
        $this->hebergements[] = $hebergement;

        return $this;
    }

    /**
     * Remove hebergement
     *
     * @param Hebergement $hebergement
     */
    public function removeHebergement(Hebergement $hebergement)
    {
        $this->hebergements->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHebergements()
    {
        return $this->hebergements;
    }

    /**
     * Get typeHebergementUnifie
     *
     * @return TypeHebergementUnifie
     */
    public function getTypeHebergementUnifie()
    {
        return $this->typeHebergementUnifie;
    }

    /**
     * Set typeHebergementUnifie
     *
     * @param TypeHebergementUnifie $typeHebergementUnifie
     *
     * @return TypeHebergement
     */
    public function setTypeHebergementUnifie(
        TypeHebergementUnifie $typeHebergementUnifie = null
    )
    {
        $this->typeHebergementUnifie = $typeHebergementUnifie;

        return $this;
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return TypeHebergement
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }
}
