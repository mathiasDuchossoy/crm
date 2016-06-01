<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * SousCategorieService
 */
class SousCategorieService
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\ServiceBundle\Entity\CategorieService
     */
    private $categorieParent;

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
     * Get categorieParent
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\CategorieService
     */
    public function getCategorieParent()
    {
        return $this->categorieParent;
    }

    /**
     * Set categorieParent
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\CategorieService $categorieParent
     *
     * @return SousCategorieService
     */
    public function setCategorieParent(\Mondofute\Bundle\ServiceBundle\Entity\CategorieService $categorieParent = null)
    {
        $this->categorieParent = $categorieParent;

        return $this;
    }
}
