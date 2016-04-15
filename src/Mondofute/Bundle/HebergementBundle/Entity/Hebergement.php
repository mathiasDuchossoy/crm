<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;

/**
 * Hebergement
 */
class Hebergement
{
    /**
     * @var \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    private $hebergementUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\Station
     */
    private $station;

    /**
     * @var ClassementHebergement
     */
    private $classement;
    /**
     * @var integer
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $moyenComs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get hebergementUnifie
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie
     */
    public function getHebergementUnifie()
    {
        return $this->hebergementUnifie;
    }

    /**
     * Set hebergementUnifie
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergementUnifie
     *
     * @return Hebergement
     */
    public function setHebergementUnifie(
        \Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie $hebergementUnifie = null
    ) {
        $this->hebergementUnifie = $hebergementUnifie;

        return $this;
    }

    public function __clone()
    {
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setHebergement($this);
            }
        }
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction $traduction
     *
     * @return Hebergement
     */
    public function addTraduction(\Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHebergement($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get site
     *
     * @return \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param \Mondofute\Bundle\SiteBundle\Entity\Site $site
     *
     * @return Hebergement
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get station
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return Hebergement
     */
    public function setStation(\Mondofute\Bundle\StationBundle\Entity\Station $station = null)
    {
        $this->station = $station;

        return $this;
    }


    /**
     * Get classement
     *
     * @return ClassementHebergement
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set classement
     *
     * @param ClassementHebergement $classement
     *
     * @return Hebergement
     */
    public function setClassement(ClassementHebergement $classement = null)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add moyenCom
     *
     * @param \Nucleus\MoyenComBundle\Entity\MoyenCommunication $moyenCom
     *
     * @return Hebergement
     */
    public function addMoyenCom(\Nucleus\MoyenComBundle\Entity\MoyenCommunication $moyenCom)
    {
        $this->moyenComs[] = $moyenCom;

        return $this;
    }

    /**
     * Remove moyenCom
     *
     * @param \Nucleus\MoyenComBundle\Entity\MoyenCommunication $moyenCom
     */
    public function removeMoyenCom(\Nucleus\MoyenComBundle\Entity\MoyenCommunication $moyenCom)
    {
        $this->moyenComs->removeElement($moyenCom);
    }

    /**
     * Get moyenComs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMoyenComs()
    {
        return $this->moyenComs;
    }
}
