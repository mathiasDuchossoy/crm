<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\DecoteBundle\Entity\Decote;

/**
 * RemiseDecote
 */
class RemiseDecote extends CommandeLigneRemise
{
    /**
     * @var Decote
     */
    private $decote;

    /**
     * Get decote
     *
     * @return Decote
     */
    public function getDecote()
    {
        return $this->decote;
    }

    /**
     * Set decote
     *
     * @param Decote $decote
     *
     * @return RemiseDecote
     */
    public function setDecote(Decote $decote = null)
    {
        $this->decote = $decote;

        return $this;
    }
}
