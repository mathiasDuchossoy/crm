<?php

namespace Mondofute\Bundle\LangueBundle\Entity;

/**
 * LangueTraduction
 */
class LangueTraduction
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return LangueTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
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
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;


    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return LangueTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\LangueTraduction
     */
    private $langueTraduction;


    /**
     * Set langueTraduction
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\LangueTraduction $langueTraduction
     *
     * @return LangueTraduction
     */
    public function setLangueTraduction(\Mondofute\Bundle\LangueBundle\Entity\LangueTraduction $langueTraduction = null)
    {
        $this->langueTraduction = $langueTraduction;

        return $this;
    }

    /**
     * Get langueTraduction
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\LangueTraduction
     */
    public function getLangueTraduction()
    {
        return $this->langueTraduction;
    }
}
