<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * FamillePrestationAnnexe
 */
class FamillePrestationAnnexe
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sousFamillePrestationAnnexes;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prestationAnnexes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sousFamillePrestationAnnexes = new ArrayCollection();
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
     * Add sousFamillePrestationAnnexe
     *
     * @param SousFamillePrestationAnnexe $sousFamillePrestationAnnexe
     *
     * @return FamillePrestationAnnexe
     */
    public function addSousFamillePrestationAnnex(SousFamillePrestationAnnexe $sousFamillePrestationAnnexe)
    {
        $this->sousFamillePrestationAnnexes[] = $sousFamillePrestationAnnexe->setFamillePrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove sousFamillePrestationAnnexe
     *
     * @param SousFamillePrestationAnnexe $sousFamillePrestationAnnexe
     */
    public function removeSousFamillePrestationAnnex(SousFamillePrestationAnnexe $sousFamillePrestationAnnexe)
    {
        $this->sousFamillePrestationAnnexes->removeElement($sousFamillePrestationAnnexe);
    }

    /**
     * Get sousFamillePrestationAnnexes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSousFamillePrestationAnnexes()
    {
        return $this->sousFamillePrestationAnnexes;
    }

    /**
     * Add traduction
     *
     * @param FamillePrestationAnnexeTraduction $traduction
     *
     * @return FamillePrestationAnnexe
     */
    public function addTraduction(FamillePrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setFamillePrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param FamillePrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(FamillePrestationAnnexeTraduction $traduction)
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
     * Add prestationAnnex
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnex
     *
     * @return FamillePrestationAnnexe
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
