<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;

/**
 * PrestationAnnexeFournisseurUnifie
 */
class PrestationAnnexeFournisseurUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $prestationAnnexeFournisseurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestationAnnexeFournisseurs = new ArrayCollection();
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
     * Add prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     *
     * @return PrestationAnnexeFournisseurUnifie
     */
    public function addPrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs[] = $prestationAnnexeFournisseur->setPrestationAnnexeFournisseurUnifie($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     */
    public function removePrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs->removeElement($prestationAnnexeFournisseur);
    }

    /**
     * Get prestationAnnexeFournisseurs
     *
     * @return Collection
     */
    public function getPrestationAnnexeFournisseurs()
    {
        return $this->prestationAnnexeFournisseurs;
    }
}
