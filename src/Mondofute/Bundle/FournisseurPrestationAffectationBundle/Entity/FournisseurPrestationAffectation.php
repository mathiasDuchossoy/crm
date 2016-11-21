<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * FournisseurPrestationAffectation
 */
class FournisseurPrestationAffectation
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
     * @return FournisseurPrestationAffectation
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
     * @return FournisseurPrestationAffectation
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }
}
