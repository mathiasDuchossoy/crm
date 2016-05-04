<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\TrancheHoraireBundle\Entity\TrancheHoraire;

/**
 * Reception
 */
class Reception
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $jour;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var TrancheHoraire
     */
    private $tranche1;
    /**
     * @var TrancheHoraire
     */
    private $tranche2;

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
     * Get jour
     *
     * @return int
     */
    public function getJour()
    {
        return $this->jour;
    }

    /**
     * Set jour
     *
     * @param integer $jour
     *
     * @return Reception
     */
    public function setJour($jour)
    {
        $this->jour = $jour;

        return $this;
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
     * @return Reception
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get tranche1
     *
     * @return TrancheHoraire
     */
    public function getTranche1()
    {
        return $this->tranche1;
    }

    /**
     * Set tranche1
     *
     * @param TrancheHoraire $tranche1
     *
     * @return Reception
     */
    public function setTranche1(TrancheHoraire $tranche1 = null)
    {
        $this->tranche1 = $tranche1;

        return $this;
    }

    /**
     * Get tranche2
     *
     * @return TrancheHoraire
     */
    public function getTranche2()
    {
        return $this->tranche2;
    }

    /**
     * Set tranche2
     *
     * @param TrancheHoraire $tranche2
     *
     * @return Reception
     */
    public function setTranche2(TrancheHoraire $tranche2 = null)
    {
        $this->tranche2 = $tranche2;

        return $this;
    }
}
