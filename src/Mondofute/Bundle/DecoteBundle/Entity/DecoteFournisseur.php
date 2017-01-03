<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;

/**
 * DecoteFournisseur
 */
class DecoteFournisseur
{
    /**
     * @var integer
     */
    private $type;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var Decote
     */
    private $decote;

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return DecoteFournisseur
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return DecoteFournisseur
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
     * @return DecoteFournisseur
     */
    public function setDecote(Decote $decote = null)
    {
        $this->decote = $decote;

        return $this;
    }
}
