<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * RegionTraduction
 */
class RegionTraduction
{
    /**
     * @var int
     */
    private $id;


    /**
     * @var string
     */
    private $description = '';
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Region
     */
    private $region;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;
    /**
     * @var string
     */
    private $libelle = '';
    /**
     * @var string
     */
    private $affichageTexte = '';

    public function __construct()
    {
        $this->libelle = '';
        $this->description = '';
    }

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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return RegionTraduction
     */
    public function setDescription($description)
    {
        $this->description = !empty($description) ? $description : '';

        return $this;
    }

    /**
     * Get region
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set region
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Region $region
     *
     * @return RegionTraduction
     */
    public function setRegion(\Mondofute\Bundle\GeographieBundle\Entity\Region $region = null)
    {
        $this->region = $region;

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
     * @return RegionTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    public function __toString()
    {
        return $this->getLibelle();
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
     * @return RegionTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get affichageTexte
     *
     * @return string
     */
    public function getAffichageTexte()
    {
        return $this->affichageTexte;
    }

    /**
     * Set affichageTexte
     *
     * @param string $affichageTexte
     *
     * @return RegionTraduction
     */
    public function setAffichageTexte($affichageTexte)
    {
        $this->affichageTexte = $affichageTexte;

        return $this;
    }
}
