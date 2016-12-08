<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * PrestationAnnexeUnifie
 */
class PrestationAnnexeUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prestationAnnexes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestationAnnexes = new ArrayCollection();
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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove prestationAnnexe
     *
     * @param PrestationAnnexe $prestationAnnexe
     */
    public function removePrestationAnnexe(PrestationAnnexe $prestationAnnex)
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

    /**
     * @param $prestationAnnexes
     * @return $this
     */
    public function setPrestationAnnexes($prestationAnnexes)
    {
        $this->getPrestationAnnexes()->clear();

        foreach ($prestationAnnexes as $prestationAnnexe) {
            $this->addPrestationAnnexe($prestationAnnexe);
        }
        return $this;
    }

    /**
     * Add prestationAnnexe
     *
     * @param PrestationAnnexe $prestationAnnexe
     *
     * @return PrestationAnnexeUnifie
     */
    public function addPrestationAnnexe(PrestationAnnexe $prestationAnnexe)
    {
        $this->prestationAnnexes[] = $prestationAnnexe->setPrestationAnnexeUnifie($this);

        return $this;
    }
}
