<?php

namespace Mondofute\Bundle\MotClefBundle\Entity;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * MotClefTraduction
 */
class MotClefTraduction
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
     * @var MotClef
     */
    private $motClef;
    /**
     * @var Langue
     */
    private $langue;

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
     * @return MotClefTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get motClef
     *
     * @return MotClef
     */
    public function getMotClef()
    {
        return $this->motClef;
    }

    /**
     * Set motClef
     *
     * @param MotClef $motClef
     *
     * @return MotClefTraduction
     */
    public function setMotClef(MotClef $motClef = null)
    {
        $this->motClef = $motClef;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return MotClefTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
