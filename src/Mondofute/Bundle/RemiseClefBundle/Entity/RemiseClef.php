<?php

namespace Mondofute\Bundle\RemiseClefBundle\Entity;

/**
 * RemiseClef
 */
class RemiseClef
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
     * @var int
     */
    private $heureRemiseClefLongSejour;

    /**
     * @var int
     */
    private $heureRemiseClefCourtSejour;

    /**
     * @var int
     */
    private $heureDepartLongSejour;

    /**
     * @var int
     */
    private $heureDepartCourtSejour;

    /**
     * @var int
     */
    private $heureTardiveLongSejour;

    /**
     * @var int
     */
    private $heureTardiveCourtSejour;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurHebergements;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;
    /**
     * @var boolean
     */
    private $standard;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fournisseurHebergements = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return RemiseClef
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get heureRemiseClefLongSejour
     *
     * @return int
     */
    public function getHeureRemiseClefLongSejour()
    {
        return $this->heureRemiseClefLongSejour;
    }

    /**
     * Set heureRemiseClefLongSejour
     *
     * @param integer $heureRemiseClefLongSejour
     *
     * @return RemiseClef
     */
    public function setHeureRemiseClefLongSejour($heureRemiseClefLongSejour)
    {
        $this->heureRemiseClefLongSejour = $heureRemiseClefLongSejour;

        return $this;
    }

    /**
     * Get heureRemiseClefCourtSejour
     *
     * @return int
     */
    public function getHeureRemiseClefCourtSejour()
    {
        return $this->heureRemiseClefCourtSejour;
    }

    /**
     * Set heureRemiseClefCourtSejour
     *
     * @param integer $heureRemiseClefCourtSejour
     *
     * @return RemiseClef
     */
    public function setHeureRemiseClefCourtSejour($heureRemiseClefCourtSejour)
    {
        $this->heureRemiseClefCourtSejour = $heureRemiseClefCourtSejour;

        return $this;
    }

    /**
     * Get heureDepartLongSejour
     *
     * @return int
     */
    public function getHeureDepartLongSejour()
    {
        return $this->heureDepartLongSejour;
    }

    /**
     * Set heureDepartLongSejour
     *
     * @param integer $heureDepartLongSejour
     *
     * @return RemiseClef
     */
    public function setHeureDepartLongSejour($heureDepartLongSejour)
    {
        $this->heureDepartLongSejour = $heureDepartLongSejour;

        return $this;
    }

    /**
     * Get heureDepartCourtSejour
     *
     * @return int
     */
    public function getHeureDepartCourtSejour()
    {
        return $this->heureDepartCourtSejour;
    }

    /**
     * Set heureDepartCourtSejour
     *
     * @param integer $heureDepartCourtSejour
     *
     * @return RemiseClef
     */
    public function setHeureDepartCourtSejour($heureDepartCourtSejour)
    {
        $this->heureDepartCourtSejour = $heureDepartCourtSejour;

        return $this;
    }

    /**
     * Get heureTardiveLongSejour
     *
     * @return int
     */
    public function getHeureTardiveLongSejour()
    {
        return $this->heureTardiveLongSejour;
    }

    /**
     * Set heureTardiveLongSejour
     *
     * @param integer $heureTardiveLongSejour
     *
     * @return RemiseClef
     */
    public function setHeureTardiveLongSejour($heureTardiveLongSejour)
    {
        $this->heureTardiveLongSejour = $heureTardiveLongSejour;

        return $this;
    }

    /**
     * Get heureTardiveCourtSejour
     *
     * @return int
     */
    public function getHeureTardiveCourtSejour()
    {
        return $this->heureTardiveCourtSejour;
    }

    /**
     * Set heureTardiveCourtSejour
     *
     * @param integer $heureTardiveCourtSejour
     *
     * @return RemiseClef
     */
    public function setHeureTardiveCourtSejour($heureTardiveCourtSejour)
    {
        $this->heureTardiveCourtSejour = $heureTardiveCourtSejour;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction $traduction
     *
     * @return RemiseClef
     */
    public function addTraduction(\Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction $traduction)
    {
        $this->traductions[] = $traduction->setRemiseClef($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * Add fournisseurHebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement $fournisseurHebergement
     *
     * @return RemiseClef
     */
    public function addFournisseurHebergement(
        \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement $fournisseurHebergement
    ) {
        $this->fournisseurHebergements[] = $fournisseurHebergement;

        return $this;
    }

    /**
     * Remove fournisseurHebergement
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement $fournisseurHebergement
     */
    public function removeFournisseurHebergement(
        \Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement $fournisseurHebergement
    ) {
        $this->fournisseurHebergements->removeElement($fournisseurHebergement);
    }

    /**
     * Get fournisseurHebergements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFournisseurHebergements()
    {
        return $this->fournisseurHebergements;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return RemiseClef
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get standard
     *
     * @return boolean
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set standard
     *
     * @param boolean $standard
     *
     * @return RemiseClef
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }
}
