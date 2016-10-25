<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * DomaineCarteIdentiteUnifie
 */
class DomaineCarteIdentiteUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $domaineCarteIdentites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->domaineCarteIdentites = new ArrayCollection();
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
     * Remove domaineCarteIdentite
     *
     * @param DomaineCarteIdentite $domaineCarteIdentite
     */
    public function removeDomaineCarteIdentite(DomaineCarteIdentite $domaineCarteIdentite)
    {
        $this->domaineCarteIdentites->removeElement($domaineCarteIdentite);
    }

    /**
     * Get domaineCarteIdentites
     *
     * @return Collection
     */
    public function getDomaineCarteIdentites()
    {
        return $this->domaineCarteIdentites;
    }

    public function setDomaineCarteIdentites(ArrayCollection $domaineCarteIdentites)
    {
        $this->getDomaineCarteIdentites()->clear();

        foreach ($domaineCarteIdentites as $domaineCarteIdentite) {
            $this->addDomaineCarteIdentite($domaineCarteIdentite);
        }
        return $this;
    }

    /**
     * Add domaineCarteIdentite
     *
     * @param DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return DomaineCarteIdentiteUnifie
     */
    public function addDomaineCarteIdentite(DomaineCarteIdentite $domaineCarteIdentite)
    {
        $this->domaineCarteIdentites[] = $domaineCarteIdentite->setDomaineCarteIdentiteUnifie($this);

        return $this;
    }
}
