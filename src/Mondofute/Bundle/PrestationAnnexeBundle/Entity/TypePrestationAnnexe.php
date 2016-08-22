<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * TypePrestationAnnexe
 */
class TypePrestationAnnexe
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sousTypePrestationAnnexes;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sousTypePrestationAnnexes = new ArrayCollection();
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
     * Add sousTypePrestationAnnex
     *
     * @param SousTypePrestationAnnexe $sousTypePrestationAnnex
     *
     * @return TypePrestationAnnexe
     */
    public function addSousTypePrestationAnnex(SousTypePrestationAnnexe $sousTypePrestationAnnex)
    {
        $this->sousTypePrestationAnnexes[] = $sousTypePrestationAnnex->setTypePrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove sousTypePrestationAnnex
     *
     * @param SousTypePrestationAnnexe $sousTypePrestationAnnex
     */
    public function removeSousTypePrestationAnnex(SousTypePrestationAnnexe $sousTypePrestationAnnex)
    {
        $this->sousTypePrestationAnnexes->removeElement($sousTypePrestationAnnex);
    }

    /**
     * Get sousTypePrestationAnnexes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSousTypePrestationAnnexes()
    {
        return $this->sousTypePrestationAnnexes;
    }

    /**
     * Add traduction
     *
     * @param TypePrestationAnnexeTraduction $traduction
     *
     * @return TypePrestationAnnexe
     */
    public function addTraduction(TypePrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setTypePrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param TypePrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(TypePrestationAnnexeTraduction $traduction)
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
}
