<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;

/**
 * PrestationAnnexeLogementUnifie
 */
class PrestationAnnexeLogementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $prestationAnnexeLogements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestationAnnexeLogements = new ArrayCollection();
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
     * Add prestationAnnexeLogement
     *
     * @param PrestationAnnexeLogement $prestationAnnexeLogement
     *
     * @return PrestationAnnexeLogementUnifie
     */
    public function addPrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements[] = $prestationAnnexeLogement->setPrestationAnnexeLogementUnifie($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeLogement
     *
     * @param PrestationAnnexeLogement $prestationAnnexeLogement
     */
    public function removePrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements->removeElement($prestationAnnexeLogement);
    }

    /**
     * Get prestationAnnexeLogements
     *
     * @return Collection
     */
    public function getPrestationAnnexeLogements()
    {
        return $this->prestationAnnexeLogements;
    }
}
