<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * NombreDeChambre
 */
class NombreDeChambre
{
    /**
     * @var int
     */
    private $id;
<<<<<<< HEAD
    /**
     * @var Collection
     */
    private $logements;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var integer
     */
    private $classement;
||||||| parent of 5ebee9e... CRM-155 logement - nb chambres => mise en place crud et sql
=======
    /**
     * @var Collection
     */
    private $logements;
    /**
     * @var Collection
     */
    private $traductions;
>>>>>>> 5ebee9e... CRM-155 logement - nb chambres => mise en place crud et sql

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logements = new ArrayCollection();
        $this->traductions = new ArrayCollection();
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

<<<<<<< HEAD
    /**
     * @param int $id
     *
     * @return NombreDeChambre
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add logement
     *
     * @param Logement $logement
     *
     * @return NombreDeChambre
     */
    public function addLogement(Logement $logement)
    {
        $this->logements[] = $logement;

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
     * Remove traduction
     *
     * @param NombreDeChambreTraduction $traduction
     */
    public function removeTraduction(NombreDeChambreTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * @param $traductions
     * @return $this
     */
    public function setTraductions($traductions)
    {
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param NombreDeChambreTraduction $traduction
     *
     * @return NombreDeChambre
     */
    public function addTraduction(NombreDeChambreTraduction $traduction)
    {
        $this->traductions[] = $traduction->setNombreDeChambre($this);

        return $this;
    }

    /**
     * Get classement
     *
     * @return integer
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set classement
     *
     * @param integer $classement
     *
     * @return NombreDeChambre
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }
}
||||||| parent of 5ebee9e... CRM-155 logement - nb chambres => mise en place crud et sql
=======
    /**
     * @param int $id
     *
     * @return NombreDeChambre
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add logement
     *
     * @param Logement $logement
     *
     * @return NombreDeChambre
     */
    public function addLogement(Logement $logement)
    {
        $this->logements[] = $logement;

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
     * Remove traduction
     *
     * @param NombreDeChambreTraduction $traduction
     */
    public function removeTraduction(NombreDeChambreTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    /**
     * @param $traductions
     * @return $this
     */
    public function setTraductions($traductions)
    {
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param NombreDeChambreTraduction $traduction
     *
     * @return NombreDeChambre
     */
    public function addTraduction(NombreDeChambreTraduction $traduction)
    {
        $this->traductions[] = $traduction->setNombreDeChambre($this);

        return $this;
    }
}
>>>>>>> 5ebee9e... CRM-155 logement - nb chambres => mise en place crud et sql
