<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

/**
 * HebergementVisuelTraduction
 */
class HebergementVisuelTraduction
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
     * @var \Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel
     */
    private $hebergementVisuel;
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
     * @return HebergementVisuelTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get hebergementVisuel
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel
     */
    public function getHebergementVisuel()
    {
        return $this->hebergementVisuel;
    }

    /**
     * Set hebergementVisuel
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel $hebergementVisuel
     *
     * @return HebergementVisuelTraduction
     */
    public function setHebergementVisuel(
        \Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel $hebergementVisuel = null
    ) {
        $this->hebergementVisuel = $hebergementVisuel;

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
     * @return HebergementVisuelTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
