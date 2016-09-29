<?php

namespace Mondofute\Bundle\CodePromoApplicationBundle\Entity;

use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * CodePromoApplication
 */
abstract class CodePromoApplication
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var CodePromo
     */
    private $codePromo;

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
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return CodePromoApplication
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

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
     * @return CodePromoApplication
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get codePromo
     *
     * @return CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param CodePromo $codePromo
     *
     * @return CodePromoApplication
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
