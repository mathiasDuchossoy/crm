<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * FournisseurInterlocuteur
 */
class FournisseurInterlocuteur
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur
     */
    private $interlocuteur;


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
     * @return FournisseurInterlocuteur
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

//    function __clone()
//    {
//        /** @var Interlocuteur $interlocuteur */
//        $this->id = null;
//        $this->interlocuteur = clone $this->getInterlocuteur();
//
//        return $this;
//    }

    /**
     * Get interlocuteur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur
     */
    public function getInterlocuteur()
    {
        return $this->interlocuteur;
    }

    /**
     * Set interlocuteur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur $interlocuteur
     *
     * @return FournisseurInterlocuteur
     */
    public function setInterlocuteur(\Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur $interlocuteur = null)
    {
        $this->interlocuteur = $interlocuteur;

        return $this;
    }

}
