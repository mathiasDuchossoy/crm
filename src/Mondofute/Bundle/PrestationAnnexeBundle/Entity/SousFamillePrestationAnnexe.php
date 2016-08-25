<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * SousFamillePrestationAnnexe
 */
class SousFamillePrestationAnnexe
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
     * @var FamillePrestationAnnexe
     */
    private $famillePrestationAnnexe;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prestationAnnexes;

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
     * @param SousFamillePrestationAnnexeTraduction $traduction
     *
     * @return SousFamillePrestationAnnexe
     */
    public function addTraduction(SousFamillePrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setSousFamillePrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param SousFamillePrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(SousFamillePrestationAnnexeTraduction $traduction)
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

    /**
     * Get famillePrestationAnnexe
     *
     * @return FamillePrestationAnnexe
     */
    public function getFamillePrestationAnnexe()
    {
        return $this->famillePrestationAnnexe;
    }

    /**
     * Set famillePrestationAnnexe
     *
     * @param FamillePrestationAnnexe $famillePrestationAnnexe
     *
     * @return SousFamillePrestationAnnexe
     */
    public function setFamillePrestationAnnexe(FamillePrestationAnnexe $famillePrestationAnnexe = null)
    {
        $this->famillePrestationAnnexe = $famillePrestationAnnexe;

        return $this;
    }

    /**
     * Add prestationAnnex
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnex
     *
     * @return SousFamillePrestationAnnexe
     */
    public function addPrestationAnnex(\Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnex)
    {
        $this->prestationAnnexes[] = $prestationAnnex;

        return $this;
    }

    /**
     * Remove prestationAnnex
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnex
     */
    public function removePrestationAnnex(\Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnex)
    {
        $this->prestationAnnexes->removeElement($prestationAnnex);
    }

    /**
     * Get prestationAnnexes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrestationAnnexes()
    {
        return $this->prestationAnnexes;
    }
}
