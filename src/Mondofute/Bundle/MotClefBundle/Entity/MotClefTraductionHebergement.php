<?php

namespace Mondofute\Bundle\MotClefBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * MotClefTraductionHebergement
 */
class MotClefTraductionHebergement
{
    /**
     * @var int
     */
    private $classement;
    /**
     * @var MotClefTraduction
     */
    private $motClefTraduction;
    /**
     * @var Hebergement
     */
    private $hebergement;

    /**
     * Get classement
     *
     * @return int
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
     * @return MotClefTraductionHebergement
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get hebergement
     *
     * @return Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return MotClefTraductionHebergement
     */
    public function setHebergement(Hebergement $hebergement)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

//    /**
//     * @return string
//     */
//    public function __toString()
//    {
//        $this->getMotClefTraduction()->getLibelle();
//    }

    /**
     * Get motClefTraduction
     *
     * @return MotClefTraduction
     */
    public function getMotClefTraduction()
    {
        return $this->motClefTraduction;
    }

    /**
     * Set motClefTraduction
     *
     * @param MotClefTraduction $motClefTraduction
     *
     * @return MotClefTraductionHebergement
     */
    public function setMotClefTraduction(MotClefTraduction $motClefTraduction)
    {
        $this->motClefTraduction = $motClefTraduction;

        return $this;
    }

    /**
     * @return Langue
     */
    public function getLangue()
    {
        return $this->getMotClefTraduction()->getLangue();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->getMotClefTraduction()->getId();
    }

    /**
     * Add hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return MotClefTraductionHebergement
     */
    public function addHebergement(Hebergement $hebergement)
    {
        $this->getMotClefTraduction()->addHebergement($hebergement);

        return $this;
    }

    /**
     * Remove hebergement
     *
     * @param Hebergement $hebergement
     */
    public function removeHebergement(Hebergement $hebergement)
    {
        $this->getHebergements()->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return Collection
     */
    public function getHebergements()
    {
//        dump($this->getMotClefTraduction());die;
        return $this->getMotClefTraduction()->getHebergements();
    }

}
