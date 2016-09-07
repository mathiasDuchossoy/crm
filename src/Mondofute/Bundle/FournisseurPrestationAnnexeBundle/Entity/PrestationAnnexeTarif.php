<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * PrestationAnnexeTarif
 */
class PrestationAnnexeTarif
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $prixPublic;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $periodeValidite;
    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe
     */
    private $prestationAnnexe;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periodeValidite = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Get prixPublic
     *
     * @return string
     */
    public function getPrixPublic()
    {
        return $this->prixPublic;
    }

    /**
     * Set prixPublic
     *
     * @param string $prixPublic
     *
     * @return PrestationAnnexeTarif
     */
    public function setPrixPublic($prixPublic)
    {
        $this->prixPublic = $prixPublic;

        return $this;
    }

    /**
     * Add periodeValidite
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite $periodeValidite
     *
     * @return PrestationAnnexeTarif
     */
    public function addPeriodeValidite(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite $periodeValidite)
    {
        $this->periodeValidite[] = $periodeValidite->setTarif($this);

        return $this;
    }

    /**
     * Remove periodeValidite
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite $periodeValidite
     */
    public function removePeriodeValidite(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite $periodeValidite)
    {
        $this->periodeValidite->removeElement($periodeValidite);
    }

    /**
     * Get periodeValidite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriodeValidite()
    {
        return $this->periodeValidite;
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
     * @return PrestationAnnexeTarif
     */
    public function setPrestationAnnexe(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe $prestationAnnexe = null)
    {
        $this->prestationAnnexe = $prestationAnnexe;

        return $this;
    }
}
