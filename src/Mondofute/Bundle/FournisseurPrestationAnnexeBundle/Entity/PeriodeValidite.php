<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * PeriodeValidite
 */
class PeriodeValidite
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateDebut;

    /**
     * @var \DateTime
     */
    private $dateFin;
    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif
     */
    private $tarif;

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
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return PeriodeValidite
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return PeriodeValidite
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set tarif
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif
     *
     * @return PeriodeValidite
     */
    public function setTarif(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif = null)
    {
        $this->tarif = $tarif;

        return $this;
    }
}
