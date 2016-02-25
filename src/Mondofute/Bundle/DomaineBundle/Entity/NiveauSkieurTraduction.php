<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * NiveauSkieurTraduction
 */
class NiveauSkieurTraduction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;

    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur
     */
    private $niveauSkieur;


    /**
     * Get id
     *
     * @return integer
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
     * @return NiveauSkieurTraduction
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
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return NiveauSkieurTraduction
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
     * Set niveauSkieur
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur $niveauSkieur
     *
     * @return NiveauSkieurTraduction
     */
    public function setNiveauSkieur(\Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur $niveauSkieur = null)
    {
        $this->niveauSkieur = $niveauSkieur;

        return $this;
    }

    /**
     * Get niveauSkieur
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur
     */
    public function getNiveauSkieur()
    {
        return $this->niveauSkieur;
    }
}
