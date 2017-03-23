<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\CommandeBundle\Entity\Commande;
use Mondofute\Bundle\CommandeBundle\Entity\LitigeDossier;

/**
 * CommandeLitigeDossier
 */
class CommandeLitigeDossier
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateHeure;
    /**
     * @var Commande
     */
    private $commande;
    /**
     * @var LitigeDossier
     */
    private $litigeDossier;

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
     * @param int $id
     * @return CommandeLitigeDossier
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Get dateHeure
     *
     * @return \DateTime
     */
    public function getDateHeure()
    {
        return $this->dateHeure;
    }

    /**
     * Set dateHeure
     *
     * @param \DateTime $dateHeure
     *
     * @return CommandeLitigeDossier
     */
    public function setDateHeure($dateHeure)
    {
        $this->dateHeure = $dateHeure;

        return $this;
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
     * @return CommandeLitigeDossier
     */
    public function setCommande(Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get litigeDossier
     *
     * @return LitigeDossier
     */
    public function getLitigeDossier()
    {
        return $this->litigeDossier;
    }

    /**
     * Set litigeDossier
     *
     * @param LitigeDossier $litigeDossier
     *
     * @return CommandeLitigeDossier
     */
    public function setLitigeDossier(LitigeDossier $litigeDossier = null)
    {
        $this->litigeDossier = $litigeDossier;

        return $this;
    }
}
