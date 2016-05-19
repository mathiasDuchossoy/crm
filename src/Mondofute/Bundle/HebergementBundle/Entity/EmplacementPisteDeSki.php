<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * EmplacementPisteDeSki
 */
class EmplacementPisteDeSki extends Emplacement
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
