<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * DomaineUnifie
 */
class DomaineUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $domaines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->domaines = new ArrayCollection();
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
     * Remove domaine
     *
     * @param Domaine $domaine
     */
    public function removeDomaine(Domaine $domaine)
    {
        $this->domaines->removeElement($domaine);
    }

    /**
     * Get domaines
     *
     * @return Collection
     */
    public function getDomaines()
    {
        return $this->domaines;
    }

    /**
     * @param $domaines
     * @return DomaineUnifie
     */
    public function setDomaines($domaines)
    {
        $this->getDomaines()->clear();

        foreach ($domaines as $domaine) {
            $this->addDomaine($domaine);
        }
        return $this;
    }

    /**
     * Add domaine
     *
     * @param Domaine $domaine
     *
     * @return DomaineUnifie
     */
    public function addDomaine(Domaine $domaine)
    {
        $this->domaines[] = $domaine->setDomaineUnifie($this);

        return $this;
    }
}
