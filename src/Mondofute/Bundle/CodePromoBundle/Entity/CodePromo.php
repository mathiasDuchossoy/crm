<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use HiDev\Bundle\CodePromoBundle\Entity\CodePromo as BaseCodePromo;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * CodePromo
 */
class CodePromo extends BaseCodePromo
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var Collection
     */
    private $codePromoPeriodeSejours;
    /**
     * @var CodePromoUnifie
     */
    private $codePromoUnifie;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var boolean
     */
    private $actifSite = true;

    public function __construct()
    {
        parent::__construct();
        $this->codePromoPeriodeSejours = new ArrayCollection();
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
     * Add codePromoPeriodeSejour
     *
     * @param CodePromoPeriodeSejour $codePromoPeriodeSejour
     *
     * @return CodePromo
     */
    public function addCodePromoPeriodeSejour(CodePromoPeriodeSejour $codePromoPeriodeSejour)
    {
        $this->codePromoPeriodeSejours[] = $codePromoPeriodeSejour;

        return $this;
    }

    /**
     * Remove codePromoPeriodeSejour
     *
     * @param CodePromoPeriodeSejour $codePromoPeriodeSejour
     */
    public function removeCodePromoPeriodeSejour(CodePromoPeriodeSejour $codePromoPeriodeSejour)
    {
        $this->codePromoPeriodeSejours->removeElement($codePromoPeriodeSejour);
    }

    /**
     * Get codePromoPeriodeSejours
     *
     * @return Collection
     */
    public function getCodePromoPeriodeSejours()
    {
        return $this->codePromoPeriodeSejours;
    }

    /**
     * Get codePromoUnifie
     *
     * @return CodePromoUnifie
     */
    public function getCodePromoUnifie()
    {
        return $this->codePromoUnifie;
    }

    /**
     * Set codePromoUnifie
     *
     * @param CodePromoUnifie $codePromoUnifie
     *
     * @return CodePromo
     */
    public function setCodePromoUnifie(CodePromoUnifie $codePromoUnifie = null)
    {
        $this->codePromoUnifie = $codePromoUnifie;

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
     * @return CodePromo
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get actifSite
     *
     * @return boolean
     */
    public function getActifSite()
    {
        return $this->actifSite;
    }

    /**
     * Set actifSite
     *
     * @param boolean $actifSite
     *
     * @return CodePromo
     */
    public function setActifSite($actifSite)
    {
        $this->actifSite = $actifSite;

        return $this;
    }
}
