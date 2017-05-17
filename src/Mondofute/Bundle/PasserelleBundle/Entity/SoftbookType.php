<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SoftbookType
 */
class SoftbookType
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;


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
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return SoftbookType
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }
}
