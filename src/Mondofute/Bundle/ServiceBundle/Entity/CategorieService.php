<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * CategorieService
 */
class CategorieService
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sousCategories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sousCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add sousCategory
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService $sousCategory
     *
     * @return CategorieService
     */
    public function addSousCategory(\Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService $sousCategory)
    {
        $this->sousCategories[] = $sousCategory;

        return $this;
    }

    /**
     * Remove sousCategory
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService $sousCategory
     */
    public function removeSousCategory(\Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService $sousCategory)
    {
        $this->sousCategories->removeElement($sousCategory);
    }

    /**
     * Get sousCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSousCategories()
    {
        return $this->sousCategories;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return 'categorieservice' . $this->getId();
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
}
