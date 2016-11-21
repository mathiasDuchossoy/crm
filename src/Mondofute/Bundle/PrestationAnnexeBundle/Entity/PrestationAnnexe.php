<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;

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
     * @var FamillePrestationAnnexe
     */
    private $famillePrestationAnnexe;

    /**
     * @var SousFamillePrestationAnnexe
     */
    private $sousFamillePrestationAnnexe;
    /**
     * @var Collection
     */
    private $fournisseurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->fournisseurs = new ArrayCollection();
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
     * @return PrestationAnnexe
     */
    public function setFamillePrestationAnnexe(FamillePrestationAnnexe $famillePrestationAnnexe = null)
    {
        $this->famillePrestationAnnexe = $famillePrestationAnnexe;

        return $this;
    }

    /**
     * Get sousFamillePrestationAnnexe
     *
     * @return SousFamillePrestationAnnexe
     */
    public function getSousFamillePrestationAnnexe()
    {
        return $this->sousFamillePrestationAnnexe;
    }

    /**
     * Set sousFamillePrestationAnnexe
     *
     * @param SousFamillePrestationAnnexe $sousFamillePrestationAnnexe
     *
     * @return PrestationAnnexe
     */
    public function setSousFamillePrestationAnnexe(SousFamillePrestationAnnexe $sousFamillePrestationAnnexe = null)
    {
        $this->sousFamillePrestationAnnexe = $sousFamillePrestationAnnexe;

        return $this;
    }

    /**
     * Add fournisseur
     *
     * @param FournisseurPrestationAnnexe $fournisseur
     *
     * @return PrestationAnnexe
     */
    public function addFournisseur(FournisseurPrestationAnnexe $fournisseur)
    {
        $this->fournisseurs[] = $fournisseur;

        return $this;
    }

    /**
     * Remove fournisseur
     *
     * @param FournisseurPrestationAnnexe $fournisseur
     */
    public function removeFournisseur(FournisseurPrestationAnnexe $fournisseur)
    {
        $this->fournisseurs->removeElement($fournisseur);
    }

    /**
     * Get fournisseurs
     *
     * @return Collection
     */
    public function getFournisseurs()
    {
        return $this->fournisseurs;
    }
}
