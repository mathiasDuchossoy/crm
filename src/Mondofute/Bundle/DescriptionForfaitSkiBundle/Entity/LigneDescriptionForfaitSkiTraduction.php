<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

/**
 * LigneDescriptionForfaitSkiTraduction
 */
class LigneDescriptionForfaitSkiTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $texteDur;

    /**
     * @var string
     */
    private $description;
    /**
     * @var \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki
     */
    private $ligneDescriptionForfaitSki;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;
    /**
     * @var string
     */
    private $libelle;

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
     * Get texteDur
     *
     * @return string
     */
    public function getTexteDur()
    {
        return $this->texteDur;
    }

    /**
     * Set texteDur
     *
     * @param string $texteDur
     *
     * @return LigneDescriptionForfaitSkiTraduction
     */
    public function setTexteDur($texteDur)
    {
        $this->texteDur = $texteDur;

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
     * @return LigneDescriptionForfaitSkiTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get ligneDescriptionForfaitSki
     *
     * @return \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki
     */
    public function getLigneDescriptionForfaitSki()
    {
        return $this->ligneDescriptionForfaitSki;
    }

    /**
     * Set ligneDescriptionForfaitSki
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki $ligneDescriptionForfaitSki
     *
     * @return LigneDescriptionForfaitSkiTraduction
     */
    public function setLigneDescriptionForfaitSki(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSki $ligneDescriptionForfaitSki = null
    )
    {
        $this->ligneDescriptionForfaitSki = $ligneDescriptionForfaitSki;

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
     * @return LigneDescriptionForfaitSkiTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
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
     * @return LigneDescriptionForfaitSkiTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }
}
