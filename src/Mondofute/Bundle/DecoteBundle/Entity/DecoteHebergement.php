<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;

/**
 * DecoteHebergement
 */
class DecoteHebergement
{
    /**
     * @var Hebergement
     */
    private $hebergement;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var Decote
     */
    private $decote;

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
     * @return DecoteHebergement
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return DecoteHebergement
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get decote
     *
     * @return Decote
     */
    public function getDecote()
    {
        return $this->decote;
    }

    /**
     * Set decote
     *
     * @param Decote $decote
     *
     * @return DecoteHebergement
     */
    public function setDecote(Decote $decote = null)
    {
        $this->decote = $decote;

        return $this;
    }
}
