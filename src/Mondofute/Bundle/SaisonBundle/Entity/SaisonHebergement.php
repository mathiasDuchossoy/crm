<?php

namespace Mondofute\Bundle\SaisonBundle\Entity;

use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;

/**
 * SaisonHebergement
 */
class SaisonHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $valideFiche;
    /**
     * @var boolean
     */
    private $valideTarif;
    /**
     * @var boolean
     */
    private $validePhoto;
    /**
     * @var boolean
     */
    private $actif;
    /**
     * @var \Mondofute\Bundle\SaisonBundle\Entity\Saison
     */
    private $saison;
    /**
     * @var Hebergement
     */
    private $hebergement;

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
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get valideFiche
     *
     * @return boolean
     */
    public function getValideFiche()
    {
        return $this->valideFiche;
    }

    /**
     * Set valideFiche
     *
     * @param boolean $valideFiche
     *
     * @return SaisonHebergement
     */
    public function setValideFiche($valideFiche)
    {
        $this->valideFiche = $valideFiche;

        return $this;
    }

    /**
     * Get valideTarif
     *
     * @return boolean
     */
    public function getValideTarif()
    {
        return $this->valideTarif;
    }

    /**
     * Set valideTarif
     *
     * @param boolean $valideTarif
     *
     * @return SaisonHebergement
     */
    public function setValideTarif($valideTarif)
    {
        $this->valideTarif = $valideTarif;

        return $this;
    }

    /**
     * Get validePhoto
     *
     * @return boolean
     */
    public function getValidePhoto()
    {
        return $this->validePhoto;
    }

    /**
     * Set validePhoto
     *
     * @param boolean $validePhoto
     *
     * @return SaisonHebergement
     */
    public function setValidePhoto($validePhoto)
    {
        $this->validePhoto = $validePhoto;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return SaisonHebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get saison
     *
     * @return \Mondofute\Bundle\SaisonBundle\Entity\Saison
     */
    public function getSaison()
    {
        return $this->saison;
    }

    /**
     * Set saison
     *
     * @param \Mondofute\Bundle\SaisonBundle\Entity\Saison $saison
     *
     * @return SaisonHebergement
     */
    public function setSaison(\Mondofute\Bundle\SaisonBundle\Entity\Saison $saison = null)
    {
        $this->saison = $saison;

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
     * @return SaisonHebergement
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }
}
