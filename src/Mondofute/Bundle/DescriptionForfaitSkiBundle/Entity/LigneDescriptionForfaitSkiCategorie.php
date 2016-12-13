<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

/**
 * LigneDescriptionForfaitSkiCategorie
 */
class LigneDescriptionForfaitSkiCategorie
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $classement;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get classement
     *
     * @return int
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set classement
     *
     * @param integer $classement
     *
     * @return LigneDescriptionForfaitSkiCategorie
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get traductions
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * Set traductions
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $traductions
     *
     * @return LigneDescriptionForfaitSkiCategorie
     */
    public function setTraductions(\Mondofute\Bundle\LangueBundle\Entity\Langue $traductions = null)
    {
        $this->traductions = $traductions;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorieTraduction $traduction
     *
     * @return LigneDescriptionForfaitSkiCategorie
     */
    public function addTraduction(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorieTraduction $traduction
    )
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorieTraduction $traduction
     */
    public function removeTraduction(
        \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\LigneDescriptionForfaitSkiCategorieTraduction $traduction
    )
    {
        $this->traductions->removeElement($traduction);
    }
}
