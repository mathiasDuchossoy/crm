<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;

/**
 * PrestationAnnexeHebergementUnifie
 */
class PrestationAnnexeHebergementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $prestationAnnexeHebergements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestationAnnexeHebergements = new ArrayCollection();
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
     * Add prestationAnnexeHebergement
     *
     * @param PrestationAnnexeHebergement $prestationAnnexeHebergement
     *
     * @return PrestationAnnexeHebergementUnifie
     */
    public function addPrestationAnnexeHebergement(PrestationAnnexeHebergement $prestationAnnexeHebergement)
    {
        $this->prestationAnnexeHebergements[] = $prestationAnnexeHebergement->setPrestationAnnexeHebergementUnifie($this);

        return $this;
    }

    /**
     * Remove prestationAnnexeHebergement
     *
     * @param PrestationAnnexeHebergement $prestationAnnexeHebergement
     */
    public function removePrestationAnnexeHebergement(PrestationAnnexeHebergement $prestationAnnexeHebergement)
    {
        $this->prestationAnnexeHebergements->removeElement($prestationAnnexeHebergement);
    }

    /**
     * Get prestationAnnexeHebergements
     *
     * @return Collection
     */
    public function getPrestationAnnexeHebergements()
    {
        return $this->prestationAnnexeHebergements;
    }
}
