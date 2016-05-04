<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * EmplacementRemonteesMecaniques
 */
class EmplacementRemonteesMecaniques extends Emplacement
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
