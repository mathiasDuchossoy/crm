<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

/**
 * CommandeLignePrestationAnnexe
 */
class CommandeLignePrestationAnnexe extends CommandeLigne
{

    /**
     * @var CommandeLigneSejour
     */
    private $commandeLigneSejour;

    /**
     * Get commandeLigneSejour
     *
     * @return CommandeLigneSejour
     */
    public function getCommandeLigneSejour()
    {
        return $this->commandeLigneSejour;
    }

    /**
     * Set commandeLigneSejour
     *
     * @param CommandeLigneSejour $commandeLigneSejour
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setCommandeLigneSejour(CommandeLigneSejour $commandeLigneSejour = null)
    {
        $this->commandeLigneSejour = $commandeLigneSejour;

        return $this;
    }
}
