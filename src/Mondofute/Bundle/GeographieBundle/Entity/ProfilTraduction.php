<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * ProfilTraduction
 */
class ProfilTraduction
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
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $accueil;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Profil
     */
    private $profil;
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
     * @return ProfilTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
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
     * @return ProfilTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get accueil
     *
     * @return string
     */
    public function getAccueil()
    {
        return $this->accueil;
    }

    /**
     * Set accueil
     *
     * @param string $accueil
     *
     * @return ProfilTraduction
     */
    public function setAccueil($accueil)
    {
        $this->accueil = $accueil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Profil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set profil
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Profil $profil
     *
     * @return ProfilTraduction
     */
    public function setProfil(\Mondofute\Bundle\GeographieBundle\Entity\Profil $profil = null)
    {
        $this->profil = $profil;

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
     * @return ProfilTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
