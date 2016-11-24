<?php

namespace Mondofute\Bundle\LogementBundle\Entity;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * NombreDeChambreTraduction
 */
class NombreDeChambreTraduction
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
     * @var NombreDeChambre
     */
    private $nombreDeChambre;
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
     * @return NombreDeChambreTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get nombreDeChambre
     *
     * @return NombreDeChambre
     */
    public function getNombreDeChambre()
    {
        return $this->nombreDeChambre;
    }

    /**
     * Set nombreDeChambre
     *
     * @param NombreDeChambre $nombreDeChambre
     *
     * @return NombreDeChambreTraduction
     */
    public function setNombreDeChambre(NombreDeChambre $nombreDeChambre = null)
    {
        $this->nombreDeChambre = $nombreDeChambre;

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
     * @return NombreDeChambreTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
