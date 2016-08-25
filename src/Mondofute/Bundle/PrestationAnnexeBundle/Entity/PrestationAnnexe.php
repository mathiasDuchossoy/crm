<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * PrestationAnnexeTraduction
 */
class PrestationAnnexe
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var Collection
     */
    private $traductions;

    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var PrestationAnnexeUnifie
     */
    private $prestationAnnexeUnifie;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe
     */
    private $famillePrestationAnnexe;
    /**
     * @var integer
     */
    private $type;
    /**
     * @var \Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe
     */
    private $sousFamillePrestationAnnexe;

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Remove traduction
     *
     * @param PrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(PrestationAnnexeTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param PrestationAnnexeTraduction $traduction
     *
     * @return PrestationAnnexe
     */
    public function addTraduction(PrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPrestationAnnexe($this);

        return $this;
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
     * @return PrestationAnnexe
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get prestationAnnexeUnifie
     *
     * @return PrestationAnnexeUnifie
     */
    public function getPrestationAnnexeUnifie()
    {
        return $this->prestationAnnexeUnifie;
    }

    /**
     * Set prestationAnnexeUnifie
     *
     * @param PrestationAnnexeUnifie $prestationAnnexeUnifie
     *
     * @return PrestationAnnexe
     */
    public function setPrestationAnnexeUnifie(PrestationAnnexeUnifie $prestationAnnexeUnifie = null)
    {
        $this->prestationAnnexeUnifie = $prestationAnnexeUnifie;

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
     * @return PrestationAnnexe
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get famillePrestationAnnexe
     *
     * @return \Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe
     */
    public function getFamillePrestationAnnexe()
    {
        return $this->famillePrestationAnnexe;
    }

    /**
     * Set famillePrestationAnnexe
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe $famillePrestationAnnexe
     *
     * @return PrestationAnnexe
     */
    public function setFamillePrestationAnnexe(\Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe $famillePrestationAnnexe = null)
    {
        $this->famillePrestationAnnexe = $famillePrestationAnnexe;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return PrestationAnnexe
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get sousFamillePrestationAnnexe
     *
     * @return \Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe
     */
    public function getSousFamillePrestationAnnexe()
    {
        return $this->sousFamillePrestationAnnexe;
    }

    /**
     * Set sousFamillePrestationAnnexe
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe $sousFamillePrestationAnnexe
     *
     * @return PrestationAnnexe
     */
    public function setSousFamillePrestationAnnexe(\Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe $sousFamillePrestationAnnexe = null)
    {
        $this->sousFamillePrestationAnnexe = $sousFamillePrestationAnnexe;

        return $this;
    }
}
