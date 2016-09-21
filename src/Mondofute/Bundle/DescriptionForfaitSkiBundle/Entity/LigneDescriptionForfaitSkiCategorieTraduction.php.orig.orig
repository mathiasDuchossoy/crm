<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

/**
 * LigneDescriptionForfaitSkiCategorieTraduction
 */
class LigneDescriptionForfaitSkiCategorieTraduction
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ligneDescriptionForfaitSkiCategorie;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ligneDescriptionForfaitSkiCategorie = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return LigneDescriptionForfaitSkiCategorieTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Add ligneDescriptionForfaitSkiCategorie
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $ligneDescriptionForfaitSkiCategorie
     *
     * @return LigneDescriptionForfaitSkiCategorieTraduction
     */
    public function addLigneDescriptionForfaitSkiCategorie(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $ligneDescriptionForfaitSkiCategorie
    ) {
        $this->ligneDescriptionForfaitSkiCategorie[] = $ligneDescriptionForfaitSkiCategorie;

        return $this;
    }

    /**
     * Remove ligneDescriptionForfaitSkiCategorie
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $ligneDescriptionForfaitSkiCategorie
     */
    public function removeLigneDescriptionForfaitSkiCategorie(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $ligneDescriptionForfaitSkiCategorie
    ) {
        $this->ligneDescriptionForfaitSkiCategorie->removeElement($ligneDescriptionForfaitSkiCategorie);
    }

    /**
     * Get ligneDescriptionForfaitSkiCategorie
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLigneDescriptionForfaitSkiCategorie()
    {
        return $this->ligneDescriptionForfaitSkiCategorie;
    }

    /**
     * Set ligneDescriptionForfaitSkiCategorie
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $ligneDescriptionForfaitSkiCategorie
     *
     * @return LigneDescriptionForfaitSkiCategorieTraduction
     */
    public function setLigneDescriptionForfaitSkiCategorie(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorie $ligneDescriptionForfaitSkiCategorie = null
    ) {
        $this->ligneDescriptionForfaitSkiCategorie = $ligneDescriptionForfaitSkiCategorie;

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
     * @return LigneDescriptionForfaitSkiCategorieTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
