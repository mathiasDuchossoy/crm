<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Nucleus\ContactBundle\Entity\Physique;

/**
 * Participant
 */
class Participant extends Physique
{

    /**
     * @var CommandeLigne
     */
    private $commandeLigne;
    /**
     * @var integer
     */
    private $id;
    /**
     * @var \DateTime
     */
    private $dateNaissance;

    /**
     * Get commandeLigne
     *
     * @return CommandeLigne
     */
    public function getCommandeLigne()
    {
        return $this->commandeLigne;
    }

    /**
     * Set commandeLigne
     *
     * @param CommandeLigne $commandeLigne
     *
     * @return Participant
     */
    public function setCommandeLigne(CommandeLigne $commandeLigne = null)
    {
        $this->commandeLigne = $commandeLigne;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Participant
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
