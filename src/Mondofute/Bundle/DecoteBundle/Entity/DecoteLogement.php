<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

use Mondofute\Bundle\LogementBundle\Entity\Logement;

/**
 * DecoteLogement
 */
class DecoteLogement
{
    /**
     * @var Decote
     */
    private $decote;
    /**
     * @var Logement
     */
    private $logement;

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
     * @return DecoteLogement
     */
    public function setDecote(Decote $decote)
    {
        $this->decote = $decote;

        return $this;
    }

    /**
     * Get logement
     *
     * @return Logement
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set logement
     *
     * @param Logement $logement
     *
     * @return DecoteLogement
     */
    public function setLogement(Logement $logement)
    {
        $this->logement = $logement;

        return $this;
    }
}
