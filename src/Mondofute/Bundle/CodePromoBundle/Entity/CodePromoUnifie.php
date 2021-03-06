<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var string
     */
    private $code;

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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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

    /**
     * @param $codePromos
     * @return $this
     */
    public function setCodePromos($codePromos)
    {
        $this->getCodePromos()->clear();

        foreach ($codePromos as $codePromo) {
            $this->addCodePromo($codePromo);
        }
        return $this;
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return CodePromoUnifie
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
