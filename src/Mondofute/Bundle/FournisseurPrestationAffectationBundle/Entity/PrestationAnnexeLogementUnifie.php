<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
