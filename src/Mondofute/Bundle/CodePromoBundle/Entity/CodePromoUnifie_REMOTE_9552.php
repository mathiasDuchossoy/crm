<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;

/**
 * CodePromoUnifie
 */
class CodePromoUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $codePromos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->codePromos = new ArrayCollection();
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
     * Add codePromo
     *
     * @param CodePromo $codePromo
     *
     * @return CodePromoUnifie
     */
    public function addCodePromo(CodePromo $codePromo)
    {
        $this->codePromos[] = $codePromo->setCodePromoUnifie($this);

        return $this;
    }

    /**
     * Remove codePromo
     *
     * @param CodePromo $codePromo
     */
    public function removeCodePromo(CodePromo $codePromo)
    {
        $this->codePromos->removeElement($codePromo);
    }

    /**
     * Get codePromos
     *
     * @return Collection
     */
    public function getCodePromos()
    {
        return $this->codePromos;
    }
}
