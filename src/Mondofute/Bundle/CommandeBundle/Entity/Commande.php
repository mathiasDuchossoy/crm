<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

/**
 * Commande
 */
class Commande
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateCommande;

    /**
     * @var int
     */
    private $numCommande;


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
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
    }

    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get numCommande
     *
     * @return int
     */
    public function getNumCommande()
    {
        return $this->numCommande;
    }

    /**
     * Set numCommande
     *
     * @param integer $numCommande
     *
     * @return Commande
     */
    public function setNumCommande($numCommande)
    {
        $this->numCommande = $numCommande;

        return $this;
    }
}

