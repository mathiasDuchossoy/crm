<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $domaineCarteIdentites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->domaineCarteIdentites = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     */
    public function removeDomaineCarteIdentite(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite)
    {
        $this->domaineCarteIdentites->removeElement($domaineCarteIdentite);
    }

    /**
     * Get domaineCarteIdentites
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return DomaineCarteIdentiteUnifie
     */
    public function addDomaineCarteIdentite(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite)
    {
        $this->domaineCarteIdentites[] = $domaineCarteIdentite->setDomaineCarteIdentiteUnifie($this);

        return $this;
    }
}
