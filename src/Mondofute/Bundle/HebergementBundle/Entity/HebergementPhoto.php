<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * HebergementPhoto
 */
class HebergementPhoto extends HebergementVisuel
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
}
