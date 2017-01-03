<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;

/**
 * FournisseurPrestationAnnexe
 */
class FournisseurPrestationAnnexe
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var PrestationAnnexe
     */
    private $prestationAnnexe;
    /**
     * @var Collection
     */
    private $fournisseurPrestationAnnexeStocks;
    /**
     * @var Collection
     */
    private $params;
    /**
     * @var Collection
     */
    private $promotionFournisseurPrestationAnnexes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->fournisseurPrestationAnnexeStocks = new ArrayCollection();
        $this->params = new ArrayCollection();
        $this->promotionFournisseurPrestationAnnexes = new ArrayCollection();
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get fournisseur
     *
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param FournisseurPrestationAnnexeTraduction $traduction
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addTraduction(FournisseurPrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param FournisseurPrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(FournisseurPrestationAnnexeTraduction $traduction)
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
     * Get prestationAnnexe
     *
     * @return PrestationAnnexe
     */
    public function getPrestationAnnexe()
    {
        return $this->prestationAnnexe;
    }

    /**
     * Set prestationAnnexe
     *
     * @param PrestationAnnexe $prestationAnnexe
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setPrestationAnnexe(PrestationAnnexe $prestationAnnexe = null)
    {
        $this->prestationAnnexe = $prestationAnnexe;

        return $this;
    }

    /**
     * Add fournisseurPrestationAnnexeStock
     *
     * @param FournisseurPrestationAnnexeStock $fournisseurPrestationAnnexeStock
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addFournisseurPrestationAnnexeStock(
        FournisseurPrestationAnnexeStock $fournisseurPrestationAnnexeStock
    ) {
        $this->fournisseurPrestationAnnexeStocks[] = $fournisseurPrestationAnnexeStock->setFournisseurPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove fournisseurPrestationAnnexeStock
     *
     * @param FournisseurPrestationAnnexeStock $fournisseurPrestationAnnexeStock
     */
    public function removeFournisseurPrestationAnnexeStock(
        FournisseurPrestationAnnexeStock $fournisseurPrestationAnnexeStock
    ) {
        $this->fournisseurPrestationAnnexeStocks->removeElement($fournisseurPrestationAnnexeStock);
    }

    /**
     * Get fournisseurPrestationAnnexeStocks
     *
     * @return Collection
     */
    public function getFournisseurPrestationAnnexeStocks()
    {
        return $this->fournisseurPrestationAnnexeStocks;
    }

    /**
     * Add param
     *
     * @param FournisseurPrestationAnnexeParam $param
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addParam(FournisseurPrestationAnnexeParam $param)
    {
        $this->params[] = $param->setFournisseurPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove param
     *
     * @param FournisseurPrestationAnnexeParam $param
     */
    public function removeParam(FournisseurPrestationAnnexeParam $param)
    {
        $this->params->removeElement($param);
    }

    /**
     * Get params
     *
     * @return Collection
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Add promotionFournisseurPrestationAnnex
     *
     * @param PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addPromotionFournisseurPrestationAnnex(PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex)
    {
        $this->promotionFournisseurPrestationAnnexes[] = $promotionFournisseurPrestationAnnex->setFournisseurPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove promotionFournisseurPrestationAnnex
     *
     * @param PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex
     */
    public function removePromotionFournisseurPrestationAnnex(PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex)
    {
        $this->promotionFournisseurPrestationAnnexes->removeElement($promotionFournisseurPrestationAnnex);
    }

    /**
     * Get promotionFournisseurPrestationAnnexes
     *
     * @return Collection
     */
    public function getPromotionFournisseurPrestationAnnexes()
    {
        return $this->promotionFournisseurPrestationAnnexes;
    }
}
