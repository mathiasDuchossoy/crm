<?php

namespace Mondofute\Bundle\SiteBundle\Entity;

/**
 * Site
 */
class Site
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
     * @var bool
     */
    private $crm;
    /**
     * @var integer
     */
    private $classementAffichage;
    /**
     * @var integer
     */
    private $classementReferent;

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
     * Get crm
     *
     * @return bool
     */
    public function getCrm()
    {
        return $this->crm;
    }

    /**
     * Set crm
     *
     * @param boolean $crm
     *
     * @return Site
     */
    public function setCrm($crm)
    {
        $this->crm = $crm;

        return $this;
    }

    /**
     * Get classementAffichage
     *
     * @return integer
     */
    public function getClassementAffichage()
    {
        return $this->classementAffichage;
    }

    /**
     * Set classementAffichage
     *
     * @param integer $classementAffichage
     *
     * @return Site
     */
    public function setClassementAffichage($classementAffichage)
    {
        $this->classementAffichage = $classementAffichage;

        return $this;
    }

    /**
     * Get classementReferent
     *
     * @return integer
     */
    public function getClassementReferent()
    {
        return $this->classementReferent;
    }

    /**
     * Set classementReferent
     *
     * @param integer $classementReferent
     *
     * @return Site
     */
    public function setClassementReferent($classementReferent)
    {
        $this->classementReferent = $classementReferent;

        return $this;
    }

    public function __toString()
    {
        return $this->getLibelle();
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
     * @return Site
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }
}
