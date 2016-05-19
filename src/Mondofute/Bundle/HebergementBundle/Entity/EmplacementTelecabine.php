<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * EmplacementTelecabine
 */
class EmplacementTelecabine extends Emplacement
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
