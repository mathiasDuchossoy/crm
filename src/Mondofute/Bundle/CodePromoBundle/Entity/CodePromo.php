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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $codePromoPeriodeSejours;


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
}
