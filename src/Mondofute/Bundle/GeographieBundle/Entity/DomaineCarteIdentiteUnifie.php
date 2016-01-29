<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;
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
     * Add domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return DomaineCarteIdentiteUnifie
     */
    public function addDomaineCarteIdentite(\Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite)
    {
        $this->domaineCarteIdentites[] = $domaineCarteIdentite->setDomaineCarteIdentiteUnifie($this);

        return $this;
    }

    /**
     * Remove domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     */
    public function removeDomaineCarteIdentite(\Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite)
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
        $this->domaineCarteIdentites = $domaineCarteIdentites;
        return $this;
    }
}
