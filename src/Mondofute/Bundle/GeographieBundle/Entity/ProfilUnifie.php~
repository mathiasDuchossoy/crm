<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ProfilUnifie
 */
class ProfilUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $profils;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->profils = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add profil
     *
     * @param Profil $profil
     *
     * @return ProfilUnifie
     */
    public function addProfil(Profil $profil)
    {
        $this->profils[] = $profil->setProfilUnifie($this);

        return $this;
    }

    /**
     * Remove profil
     *
     * @param Profil $profil
     */
    public function removeProfil(Profil $profil)
    {
        $this->profils->removeElement($profil);
    }

    /**
     * Get profils
     *
     * @return Collection
     */
    public function getProfils()
    {
        return $this->profils;
    }

    public function setProfils(ArrayCollection $profils)
    {
        $this->profils = $profils;
    }

}
