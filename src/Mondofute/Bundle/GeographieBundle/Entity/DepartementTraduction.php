<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * DepartementTraduction
 */
class DepartementTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $libelle = '';

    /**
     * @var string
     */
    private $description = '';
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    private $departement;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

    public function __construct()
    {
        $this->description = '';
        $this->libelle = '';
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
     * @return DepartementTraduction
     */
    public function setDescription($description)
    {
        $this->description = !empty($description) ? $description : '';

        return $this;
    }

    /**
     * Get departement
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     *
     * @return DepartementTraduction
     */
    public function setDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

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
     * @return DepartementTraduction
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
     * @return DepartementTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }
}
