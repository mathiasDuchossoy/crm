<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;

/**
 * RemiseCodePromo
 */
class RemiseCodePromo extends CommandeLigneRemise
{
    /**
     * @var CodePromo
     */
    private $codePromo;

    /**
     * Get codePromo
     *
     * @return CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param CodePromo $codePromo
     *
     * @return RemiseCodePromo
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
