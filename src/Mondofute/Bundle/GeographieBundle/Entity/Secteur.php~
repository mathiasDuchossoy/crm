<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Secteur
 */
class Secteur
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var SecteurUnifie
     */
    private $secteurUnifie;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
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
     * Add traduction
     *
     * @param SecteurTraduction $traduction
     *
     * @return Secteur
     */
    public function addTraduction(SecteurTraduction $traduction)
    {
        $this->traductions[] = $traduction->setSecteur($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param SecteurTraduction $traduction
     */
    public function removeTraduction(SecteurTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * @return Secteur
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get secteurUnifie
     *
     * @return SecteurUnifie
     */
    public function getSecteurUnifie()
    {
        return $this->secteurUnifie;
    }

    /**
     * Set secteurUnifie
     *
     * @param SecteurUnifie $secteurUnifie
     *
     * @return Secteur
     */
    public function setSecteurUnifie(SecteurUnifie $secteurUnifie = null)
    {
        $this->secteurUnifie = $secteurUnifie;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setSecteur($this);
            }
        }
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    public function setTraductions($traductions)
    {
        $this->traductions = $traductions;
        return $this;
    }


}
