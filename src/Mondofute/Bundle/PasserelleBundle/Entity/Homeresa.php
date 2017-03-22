<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Homeresa
 */
class Homeresa extends Passerelle
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $paramD;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get paramD
     *
     * @return string
     */
    public function getParamD()
    {
        return $this->paramD;
    }

    /**
     * Set paramD
     *
     * @param string $paramD
     * @return Homeresa
     */
    public function setParamD($paramD)
    {
        $this->paramD = $paramD;

        return $this;
    }
}
