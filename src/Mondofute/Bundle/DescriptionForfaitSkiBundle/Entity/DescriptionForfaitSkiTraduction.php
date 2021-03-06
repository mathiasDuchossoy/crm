<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

/**
 * DescriptionForfaitSkiTraduction
 */
class DescriptionForfaitSkiTraduction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki
     */
    private $descriptionForfaitSki;

    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;
    /**
     * @var string
     */
    private $libelle;
    /**
     * @var string
     */
    private $texteDur;

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
     * @return DescriptionForfaitSkiTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get descriptionForfaitSki
     *
     * @return \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki
     */
    public function getDescriptionForfaitSki()
    {
        return $this->descriptionForfaitSki;
    }

    /**
     * Set descriptionForfaitSki
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki $descriptionForfaitSki
     *
     * @return DescriptionForfaitSkiTraduction
     */
    public function setDescriptionForfaitSki(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki $descriptionForfaitSki = null)
    {
        $this->descriptionForfaitSki = $descriptionForfaitSki;

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
     * @return DescriptionForfaitSkiTraduction
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
     * @return DescriptionForfaitSkiTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
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
     * @return DescriptionForfaitSkiTraduction
     */
    public function setTexteDur($texteDur)
    {
        $this->texteDur = $texteDur;

        return $this;
    }
}
