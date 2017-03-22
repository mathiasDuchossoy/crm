<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Passerelle
 */
abstract class Passerelle implements IPasserelle
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Pass
     */
    private $passerelle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fournisseurs = new ArrayCollection();
    }

    static public function liste()
    {
        $liste[] = 'Anite';
        $liste[] = 'Softbook';
        $liste[] = 'Arkiane';

        return $liste;
    }

    static public function getListe()
    {
        echo get_class();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCatalogue()
    {
        // TODO: Implement getCatalogue() method.
        throw new \Exception('pas de méthode' . __METHOD__);
    }

    public function getStocks()
    {
        // TODO: Implement getStocks() method.
        throw new \Exception('pas de méthode' . __METHOD__);
    }

    public function getTarifs()
    {
        // TODO: Implement getTarifs() method.
        throw new \Exception('pas de méthode' . __METHOD__);
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        $class = new \ReflectionClass($this);
        return $class->getShortName();
    }

    /**
     * Get passerelle
     *
     * @return Pass
     */
    public function getPasserelle()
    {
        return $this->passerelle;
    }

    /**
     * Set passerelle
     *
     * @param Pass $passerelle
     * @return Passerelle
     */
    public function setPasserelle(Pass $passerelle = null)
    {
        $this->passerelle = $passerelle;

        return $this;
    }
}
