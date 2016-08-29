<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * SousTypePrestationAnnexe
 */
class SousTypePrestationAnnexe
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
     * @var TypePrestationAnnexe
     */
    private $typePrestationAnnexe;

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
     * @param SousTypePrestationAnnexeTraduction $traduction
     *
     * @return SousTypePrestationAnnexe
     */
    public function addTraduction(SousTypePrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setSousTypePrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param SousTypePrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(SousTypePrestationAnnexeTraduction $traduction)
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
     * Get typePrestationAnnexe
     *
     * @return TypePrestationAnnexe
     */
    public function getTypePrestationAnnexe()
    {
        return $this->typePrestationAnnexe;
    }

    /**
     * Set typePrestationAnnexe
     *
     * @param TypePrestationAnnexe $typePrestationAnnexe
     *
     * @return SousTypePrestationAnnexe
     */
    public function setTypePrestationAnnexe(TypePrestationAnnexe $typePrestationAnnexe = null)
    {
        $this->typePrestationAnnexe = $typePrestationAnnexe;

        return $this;
    }
}
