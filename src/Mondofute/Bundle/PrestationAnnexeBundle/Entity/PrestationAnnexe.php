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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
