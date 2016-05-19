<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * EmplacementCentreDeStation
 */
class EmplacementCentreDeStation extends Emplacement
{
    /**
     * @var int
     */
    private $id;


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
