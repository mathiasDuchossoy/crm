<?php

namespace HiDev\Bundle\CodePromoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CodePromo
 */
abstract class CodePromo
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
     * @var string
     */
    private $code;

    /**
     * @var int
     */
    private $clientAffectation;

    /**
     * @var int
     */
    private $typeRemise;

    /**
     * @var string
     */
    private $valeurRemise;

    /**
     * @var string
     */
    private $prixMini;
    /**
     * @var integer
     */
    private $usage;
    /**
     * @var boolean
     */
    private $actif;
    /**
     * @var Collection
     */
    private $codePromoPeriodeValidites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->codePromoPeriodeValidites = new ArrayCollection();
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
     * @return CodePromo
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return CodePromo
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get clientAffectation
     *
     * @return int
     */
    public function getClientAffectation()
    {
        return $this->clientAffectation;
    }

    /**
     * Set clientAffectation
     *
     * @param integer $clientAffectation
     *
     * @return CodePromo
     */
    public function setClientAffectation($clientAffectation)
    {
        $this->clientAffectation = $clientAffectation;

        return $this;
    }

    /**
     * Get typeRemise
     *
     * @return int
     */
    public function getTypeRemise()
    {
        return $this->typeRemise;
    }

    /**
     * Set typeRemise
     *
     * @param integer $typeRemise
     *
     * @return CodePromo
     */
    public function setTypeRemise($typeRemise)
    {
        $this->typeRemise = $typeRemise;

        return $this;
    }

    /**
     * Get valeurRemise
     *
     * @return string
     */
    public function getValeurRemise()
    {
        return $this->valeurRemise;
    }

    /**
     * Set valeurRemise
     *
     * @param string $valeurRemise
     *
     * @return CodePromo
     */
    public function setValeurRemise($valeurRemise)
    {
        $this->valeurRemise = $valeurRemise;

        return $this;
    }

    /**
     * Get prixMini
     *
     * @return string
     */
    public function getPrixMini()
    {
        return $this->prixMini;
    }

    /**
     * Set prixMini
     *
     * @param string $prixMini
     *
     * @return CodePromo
     */
    public function setPrixMini($prixMini)
    {
        $this->prixMini = $prixMini;

        return $this;
    }

    /**
     * Get usage
     *
     * @return integer
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * Set usage
     *
     * @param integer $usage
     *
     * @return CodePromo
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;

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
     * @return CodePromo
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add codePromoPeriodeValidite
     *
     * @param CodePromoPeriodeValidite $codePromoPeriodeValidite
     *
     * @return CodePromo
     */
    public function addCodePromoPeriodeValidite(CodePromoPeriodeValidite $codePromoPeriodeValidite)
    {
        $this->codePromoPeriodeValidites[] = $codePromoPeriodeValidite;

        return $this;
    }

    /**
     * Remove codePromoPeriodeValidite
     *
     * @param CodePromoPeriodeValidite $codePromoPeriodeValidite
     */
    public function removeCodePromoPeriodeValidite(CodePromoPeriodeValidite $codePromoPeriodeValidite)
    {
        $this->codePromoPeriodeValidites->removeElement($codePromoPeriodeValidite);
    }

    /**
     * Get codePromoPeriodeValidites
     *
     * @return Collection
     */
    public function getCodePromoPeriodeValidites()
    {
        return $this->codePromoPeriodeValidites;
    }
}
