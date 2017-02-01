<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * GroupeStatutDossierTraduction
 */
class GroupeStatutDossierTraduction
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
     * @var Langue
     */
    private $langue;
    /**
     * @var GroupeStatutDossier
     */
    private $groupeStatutDossier;

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
     * @return GroupeStatutDossierTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return GroupeStatutDossierTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get groupeStatutDossier
     *
     * @return GroupeStatutDossier
     */
    public function getGroupeStatutDossier()
    {
        return $this->groupeStatutDossier;
    }

    /**
     * Set groupeStatutDossier
     *
     * @param GroupeStatutDossier $groupeStatutDossier
     *
     * @return GroupeStatutDossierTraduction
     */
    public function setGroupeStatutDossier(GroupeStatutDossier $groupeStatutDossier = null)
    {
        $this->groupeStatutDossier = $groupeStatutDossier;

        return $this;
    }
}
