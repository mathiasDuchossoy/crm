<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle;

/**
 * LogementUnifie
 */
class LogementUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $logements;
    /**
     * @var boolean
     */
    private $archive = false;
    /**
     * @var boolean
     */
    private $desactive = false;
    /**
     * @var Collection
     */
    private $saisonCodePasserelles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logements = new ArrayCollection();
        $this->saisonCodePasserelles = new ArrayCollection();
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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove logement
     *
     * @param Logement $logement
     */
    public function removeLogement(Logement $logement)
    {
        $this->logements->removeElement($logement);
        $logement->setLogementUnifie(null);
    }

    /**
     * Get logements
     *
     * @return Collection
     */
    public function getLogements()
    {
        return $this->logements;
    }

    /**
     * @param ArrayCollection $logements
     * @return LogementUnifie $this
     */
    public function setLogements(ArrayCollection $logements)
    {
        $this->getLogements()->clear();

        foreach ($logements as $logement) {
            $this->addLogement($logement);
        }
        return $this;
    }

    /**
     * Add logement
     *
     * @param Logement $logement
     *
     * @return LogementUnifie
     */
    public function addLogement(Logement $logement)
    {
        $this->logements[] = $logement->setLogementUnifie($this);

        return $this;
    }

    /**
     * Get archive
     *
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set archive
     *
     * @param boolean $archive
     *
     * @return LogementUnifie
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get desactive
     *
     * @return boolean
     */
    public function getDesactive()
    {
        return $this->desactive;
    }

    /**
     * Set desactive
     *
     * @param boolean $desactive
     *
     * @return LogementUnifie
     */
    public function setDesactive($desactive)
    {
        $this->desactive = $desactive;

        return $this;
    }

    /**
     * Remove saisonCodePasserelle
     *
     * @param SaisonCodePasserelle $saisonCodePasserelle
     */
    public function removeSaisonCodePasserelle(SaisonCodePasserelle $saisonCodePasserelle)
    {
        $this->saisonCodePasserelles->removeElement($saisonCodePasserelle);
    }

    /**
     * Get saisonCodePasserelles
     *
     * @return Collection
     */
    public function getSaisonCodePasserelles()
    {
        $iterator = $this->saisonCodePasserelles->getIterator();
        $iterator->uasort(function (SaisonCodePasserelle $a, SaisonCodePasserelle $b) {
            return ($a->getSaison()->getDateDebut() > $b->getSaison()->getDateDebut()) ? -1 : 1;
        });
        $this->saisonCodePasserelles->clear();
        $newCodePasserelles = new ArrayCollection(iterator_to_array($iterator));
        foreach ($newCodePasserelles as $item) {
            $this->addSaisonCodePasserelle($item);
        }
        return $this->saisonCodePasserelles;
    }

    /**
     * Add saisonCodePasserelle
     *
     * @param SaisonCodePasserelle $saisonCodePasserelle
     *
     * @return LogementUnifie
     */
    public function addSaisonCodePasserelle(SaisonCodePasserelle $saisonCodePasserelle)
    {
        $this->saisonCodePasserelles[] = $saisonCodePasserelle;

        return $this;
    }
}
