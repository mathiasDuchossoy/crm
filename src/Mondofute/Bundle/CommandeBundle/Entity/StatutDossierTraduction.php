<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

/**
 * StatutDossierTraduction
 */
class StatutDossierTraduction
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
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;
    /**
     * @var \Mondofute\Bundle\CommandeBundle\Entity\StatutDossier
     */
    private $statutDossier;

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
     * @return StatutDossierTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return StatutDossierTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get statutDossier
     *
     * @return \Mondofute\Bundle\CommandeBundle\Entity\StatutDossier
     */
    public function getStatutDossier()
    {
        return $this->statutDossier;
    }

    /**
     * Set statutDossier
     *
     * @param \Mondofute\Bundle\CommandeBundle\Entity\StatutDossier $statutDossier
     *
     * @return StatutDossierTraduction
     */
    public function setStatutDossier(\Mondofute\Bundle\CommandeBundle\Entity\StatutDossier $statutDossier = null)
    {
        $this->statutDossier = $statutDossier;

        return $this;
    }
}
