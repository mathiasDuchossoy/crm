<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * DomaineTraduction
 */
class DomaineTraduction
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Domaine
     */
    private $domaine;
    /**
     * @var Langue
     */
    private $langue;
    /**
     * @var string
     */
    private $libelle;
    /**
     * @var string
     */
    private $affichageTexte = '';

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
     * Get domaine
     *
     * @return Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set domaine
     *
     * @param Domaine $domaine
     *
     * @return DomaineTraduction
     */
    public function setDomaine(Domaine $domaine = null)
    {
        $this->domaine = $domaine;

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
     * @return DomaineTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return DomaineTraduction
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
     * @return DomaineTraduction
     */
    public function setAffichageTexte($affichageTexte)
    {
        $this->affichageTexte = $affichageTexte;

        return $this;
    }
}
