<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

use DateTime;

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
     * @var DateTime
     */
    private $dateDebut;

    /**
     * @var DateTime
     */
    private $dateFin;
    /**
     * @var PrestationAnnexeTarif
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get tarif
     *
     * @return PrestationAnnexeTarif
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set tarif
     *
     * @param PrestationAnnexeTarif $tarif
     *
     * @return PeriodeValidite
     */
    public function setTarif(PrestationAnnexeTarif $tarif = null)
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function __toString()
    {
        return $this->getDateDebut()->format('d/m/Y H:i') . PHP_EOL . '-' . PHP_EOL . $this->getDateFin()->format('d/m/Y H:i');

    }

    /**
     * Get dateDebut
     *
     * @return DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut
     *
     * @param DateTime $dateDebut
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
     * @return DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param DateTime $dateFin
     *
     * @return PeriodeValidite
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }


}
