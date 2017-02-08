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
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam
     */
    private $fournisseurPrestationAnnexeParam;

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

    /**
     * Get fournisseurPrestationAnnexeParam
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam
     */
    public function getFournisseurPrestationAnnexeParam()
    {
        return $this->fournisseurPrestationAnnexeParam;
    }

    /**
     * Set fournisseurPrestationAnnexeParam
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam $fournisseurPrestationAnnexeParam
     *
     * @return CommandeLignePrestationAnnexe
     */
    public function setFournisseurPrestationAnnexeParam(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam $fournisseurPrestationAnnexeParam = null)
    {
        $this->fournisseurPrestationAnnexeParam = $fournisseurPrestationAnnexeParam;

        return $this;
    }
}
