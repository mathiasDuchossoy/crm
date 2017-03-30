<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

/**
 * CodePasserelle
 */
class CodePasserelle
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
     * @return int
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
     *
     * @return CodePasserelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }
}

