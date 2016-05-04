<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * HebergementTraduction
 */
class HebergementTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nom = '';

    /**
     * @var string
     */
    private $avisMondofute = '';

    /**
     * @var string
     */
    private $restauration = '';

    /**
     * @var string
     */
    private $bienEtre = '';

    /**
     * @var string
     */
    private $pourLesEnfants = '';

    /**
     * @var string
     */
    private $activites = '';
    /**
     * @var Hebergement
     */
    private $hebergement;
    /**
     * @var Langue
     */
    private $langue;

    /**
     * HebergementTraduction constructor.
     */
    public function __construct()
    {
        $this->nom = '';
        $this->activites = '';
        $this->avisMondofute = '';
        $this->bienEtre = '';
        $this->restauration = '';
        $this->pourLesEnfants = '';
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
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return HebergementTraduction
     */
    public function setNom($nom)
    {
        $this->nom = !empty($nom) ? $nom : '';

        return $this;
    }

    /**
     * Get avisMondofute
     *
     * @return string
     */
    public function getAvisMondofute()
    {
        return $this->avisMondofute;
    }

    /**
     * Set avisMondofute
     *
     * @param string $avisMondofute
     *
     * @return HebergementTraduction
     */
    public function setAvisMondofute($avisMondofute)
    {
        $this->avisMondofute = !empty($avisMondofute) ? $avisMondofute : '';

        return $this;
    }

    /**
     * Get restauration
     *
     * @return string
     */
    public function getRestauration()
    {
        return $this->restauration;
    }

    /**
     * Set restauration
     *
     * @param string $restauration
     *
     * @return HebergementTraduction
     */
    public function setRestauration($restauration)
    {
        $this->restauration = !empty($restauration) ? $restauration : '';

        return $this;
    }

    /**
     * Get bienEtre
     *
     * @return string
     */
    public function getBienEtre()
    {
        return $this->bienEtre;
    }

    /**
     * Set bienEtre
     *
     * @param string $bienEtre
     *
     * @return HebergementTraduction
     */
    public function setBienEtre($bienEtre)
    {
        $this->bienEtre = !empty($bienEtre) ? $bienEtre : '';

        return $this;
    }

    /**
     * Get pourLesEnfants
     *
     * @return string
     */
    public function getPourLesEnfants()
    {
        return $this->pourLesEnfants;
    }

    /**
     * Set pourLesEnfants
     *
     * @param string $pourLesEnfants
     *
     * @return HebergementTraduction
     */
    public function setPourLesEnfants($pourLesEnfants)
    {
        $this->pourLesEnfants = !empty($pourLesEnfants) ? $pourLesEnfants : '';

        return $this;
    }

    /**
     * Get activites
     *
     * @return string
     */
    public function getActivites()
    {
        return $this->activites;
    }

    /**
     * Set activites
     *
     * @param string $activites
     *
     * @return HebergementTraduction
     */
    public function setActivites($activites)
    {
        $this->activites = !empty($activites) ? $activites : '';

        return $this;
    }

    /**
     * Get hebergement
     *
     * @return Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return HebergementTraduction
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

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
     * @return HebergementTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
