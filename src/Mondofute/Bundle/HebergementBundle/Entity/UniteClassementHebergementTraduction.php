<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * UniteClassementHebergementTraduction
 */
class UniteClassementHebergementTraduction
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
     * @var \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement
     */
    private $uniteClassementHebergement;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
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
     * @return UniteClassementHebergementTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get uniteClassementHebergement
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement
     */
    public function getUniteClassementHebergement()
    {
        return $this->uniteClassementHebergement;
    }

    /**
     * Set uniteClassementHebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement $uniteClassementHebergement
     *
     * @return UniteClassementHebergementTraduction
     */
    public function setUniteClassementHebergement(
        \Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement $uniteClassementHebergement = null
    ) {
        $this->uniteClassementHebergement = $uniteClassementHebergement;

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
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return UniteClassementHebergementTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
