<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle;

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
     * @var boolean
     */
    private $freeSale = false;
    /**
     * @var Collection
     */
    private $periodeIndisponibles;
    /**
     * @var Collection
     */
    private $fournisseurPrestationAnnexeStockHebergements;
    /**
     * @var Collection
     */
    private $fournisseurPrestationAnnexeStockFournisseurs;
    /**
     * @var Collection
     */
    private $saisonCodePasserelles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->fournisseurPrestationAnnexeStocks = new ArrayCollection();
        $this->params = new ArrayCollection();
        $this->promotionFournisseurPrestationAnnexes = new ArrayCollection();
        $this->periodeIndisponibles = new ArrayCollection();
        $this->fournisseurPrestationAnnexeStockHebergements = new ArrayCollection();
        $this->fournisseurPrestationAnnexeStockFournisseurs = new ArrayCollection();
        $this->saisonCodePasserelles = new ArrayCollection();
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
    )
    {
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
    )
    {
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

    /**
     * Get freeSale
     *
     * @return boolean
     */
    public function getFreeSale()
    {
        return $this->freeSale;
    }

    /**
     * Set freeSale
     *
     * @param boolean $freeSale
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setFreeSale($freeSale)
    {
        $this->freeSale = $freeSale;

        return $this;
    }

    /**
     * Add periodeIndisponible
     *
     * @param FournisseurPrestationAnnexePeriodeIndisponible $periodeIndisponible
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addPeriodeIndisponible(FournisseurPrestationAnnexePeriodeIndisponible $periodeIndisponible)
    {
        $this->periodeIndisponibles[] = $periodeIndisponible->setFournisseurPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove periodeIndisponible
     *
     * @param FournisseurPrestationAnnexePeriodeIndisponible $periodeIndisponible
     */
    public function removePeriodeIndisponible(FournisseurPrestationAnnexePeriodeIndisponible $periodeIndisponible)
    {
        $this->periodeIndisponibles->removeElement($periodeIndisponible);
    }

    /**
     * Get periodeIndisponibles
     *
     * @return Collection
     */
    public function getPeriodeIndisponibles()
    {
        return $this->periodeIndisponibles;
    }

    /**
     * Add fournisseurPrestationAnnexeStockHebergement
     *
     * @param FournisseurPrestationAnnexeStockHebergement $fournisseurPrestationAnnexeStockHebergement
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addFournisseurPrestationAnnexeStockHebergement(FournisseurPrestationAnnexeStockHebergement $fournisseurPrestationAnnexeStockHebergement)
    {
        $this->fournisseurPrestationAnnexeStockHebergements[] = $fournisseurPrestationAnnexeStockHebergement;

        return $this;
    }

    /**
     * Remove fournisseurPrestationAnnexeStockHebergement
     *
     * @param FournisseurPrestationAnnexeStockHebergement $fournisseurPrestationAnnexeStockHebergement
     */
    public function removeFournisseurPrestationAnnexeStockHebergement(FournisseurPrestationAnnexeStockHebergement $fournisseurPrestationAnnexeStockHebergement)
    {
        $this->fournisseurPrestationAnnexeStockHebergements->removeElement($fournisseurPrestationAnnexeStockHebergement);
    }

    /**
     * Get fournisseurPrestationAnnexeStockHebergements
     *
     * @return Collection
     */
    public function getFournisseurPrestationAnnexeStockHebergements()
    {
        return $this->fournisseurPrestationAnnexeStockHebergements;
    }

    /**
     * Add fournisseurPrestationAnnexeStockFournisseur
     *
     * @param FournisseurPrestationAnnexeStockFournisseur $fournisseurPrestationAnnexeStockFournisseur
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addFournisseurPrestationAnnexeStockFournisseur(FournisseurPrestationAnnexeStockFournisseur $fournisseurPrestationAnnexeStockFournisseur)
    {
        $this->fournisseurPrestationAnnexeStockFournisseurs[] = $fournisseurPrestationAnnexeStockFournisseur;

        return $this;
    }

    /**
     * Remove fournisseurPrestationAnnexeStockFournisseur
     *
     * @param FournisseurPrestationAnnexeStockFournisseur $fournisseurPrestationAnnexeStockFournisseur
     */
    public function removeFournisseurPrestationAnnexeStockFournisseur(FournisseurPrestationAnnexeStockFournisseur $fournisseurPrestationAnnexeStockFournisseur)
    {
        $this->fournisseurPrestationAnnexeStockFournisseurs->removeElement($fournisseurPrestationAnnexeStockFournisseur);
    }

    /**
     * Get fournisseurPrestationAnnexeStockFournisseurs
     *
     * @return Collection
     */
    public function getFournisseurPrestationAnnexeStockFournisseurs()
    {
        return $this->fournisseurPrestationAnnexeStockFournisseurs;
    }

    /**
     * Remove saisonCodePasserelle
     *
     * @param SaisonCodePasserelle $saisonCodePasserelle
     */
    public function removeSaisonCodePasserelle(SaisonCodePasserelle $saisonCodePasserelle)
    {
        $this->saisonCodePasserelles->removeElement($saisonCodePasserelle);
    }

    /**
     * Get saisonCodePasserelles
     *
     * @return Collection
     */
    public function getSaisonCodePasserelles()
    {
        $iterator = $this->saisonCodePasserelles->getIterator();
        $iterator->uasort(function (SaisonCodePasserelle $a, SaisonCodePasserelle $b) {
            return ($a->getSaison()->getDateDebut() > $b->getSaison()->getDateDebut()) ? -1 : 1;
        });
        $this->saisonCodePasserelles->clear();
        $newCodePasserelles = new ArrayCollection(iterator_to_array($iterator));
        foreach ($newCodePasserelles as $item) {
            $this->addSaisonCodePasserelle($item);
        }
        return $this->saisonCodePasserelles;
    }

    /**
     * Add saisonCodePasserelle
     *
     * @param SaisonCodePasserelle $saisonCodePasserelle
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addSaisonCodePasserelle(SaisonCodePasserelle $saisonCodePasserelle)
    {
        $this->saisonCodePasserelles[] = $saisonCodePasserelle;

        return $this;
    }
}
