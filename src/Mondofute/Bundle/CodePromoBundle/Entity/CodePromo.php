<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

use HiDev\Bundle\CodePromoBundle\Entity\CodePromo as BaseCodePromo;

/**
 * CodePromo
 */
class CodePromo extends BaseCodePromo
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $codePromoPeriodeSejours;
    /**
     * @var \Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie
     */
    private $codePromoUnifie;

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
     * Add codePromoPeriodeSejour
     *
     * @param \Mondofute\Bundle\CodePromoBundle\Entity\CodePromoPeriodeSejour $codePromoPeriodeSejour
     *
     * @return CodePromo
     */
    public function addCodePromoPeriodeSejour(\Mondofute\Bundle\CodePromoBundle\Entity\CodePromoPeriodeSejour $codePromoPeriodeSejour)
    {
        $this->codePromoPeriodeSejours[] = $codePromoPeriodeSejour;

        return $this;
    }

    /**
     * Remove codePromoPeriodeSejour
     *
     * @param \Mondofute\Bundle\CodePromoBundle\Entity\CodePromoPeriodeSejour $codePromoPeriodeSejour
     */
    public function removeCodePromoPeriodeSejour(\Mondofute\Bundle\CodePromoBundle\Entity\CodePromoPeriodeSejour $codePromoPeriodeSejour)
    {
        $this->codePromoPeriodeSejours->removeElement($codePromoPeriodeSejour);
    }

    /**
     * Get codePromoPeriodeSejours
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCodePromoPeriodeSejours()
    {
        return $this->codePromoPeriodeSejours;
    }

    /**
     * Get codePromoUnifie
     *
     * @return \Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie
     */
    public function getCodePromoUnifie()
    {
        return $this->codePromoUnifie;
    }

    /**
     * Set codePromoUnifie
     *
     * @param \Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie $codePromoUnifie
     *
     * @return CodePromo
     */
    public function setCodePromoUnifie(\Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie $codePromoUnifie = null)
    {
        $this->codePromoUnifie = $codePromoUnifie;

        return $this;
    }
}
