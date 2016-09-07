<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * FournisseurPrestationAnnexeTraduction
 */
class FournisseurPrestationAnnexeTraduction
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
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe
     */
    private $prestationAnnexe;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

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
     * @return FournisseurPrestationAnnexeTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get prestationAnnexe
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe
     */
    public function getPrestationAnnexe()
    {
        return $this->prestationAnnexe;
    }

    /**
     * Set prestationAnnexe
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe $prestationAnnexe
     *
     * @return FournisseurPrestationAnnexeTraduction
     */
    public function setPrestationAnnexe(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe $prestationAnnexe = null)
    {
        $this->prestationAnnexe = $prestationAnnexe;

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
     * @return FournisseurPrestationAnnexeTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
