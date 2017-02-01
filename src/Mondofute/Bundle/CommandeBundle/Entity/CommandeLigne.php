<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

/**
 * CommandeLigne
 */
abstract class CommandeLigne
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Commande
     */
    private $commande;
    /**
     * @var integer
     */
    private $montant;

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
     * Get commande
     *
     * @return Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set commande
     *
     * @param Commande $commande
     *
     * @return CommandeLigne
     */
    public function setCommande(Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get montant
     *
     * @return integer
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set montant
     *
     * @param integer $montant
     *
     * @return CommandeLigne
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }
}
