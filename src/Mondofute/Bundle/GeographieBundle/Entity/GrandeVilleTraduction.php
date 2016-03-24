<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * GrandeVilleTraduction
 */
class GrandeVilleTraduction
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
     * @var \Mondofute\Bundle\GeographieBundle\Entity\grandeVille
     */
    private $grandeVille;
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
     * @return GrandeVilleTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get grandeVille
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\grandeVille
     */
    public function getGrandeVille()
    {
        return $this->grandeVille;
    }

    /**
     * Set grandeVille
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\grandeVille $grandeVille
     *
     * @return GrandeVilleTraduction
     */
    public function setGrandeVille(\Mondofute\Bundle\GeographieBundle\Entity\grandeVille $grandeVille = null)
    {
        $this->grandeVille = $grandeVille;

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
     * @return GrandeVilleTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
