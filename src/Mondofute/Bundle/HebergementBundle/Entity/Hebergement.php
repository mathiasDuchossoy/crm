<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Translator;

/**
 * Hebergement
 */
class Hebergement
{
    /**
     * @var HebergementUnifie
     */
    private $hebergementUnifie;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var Station
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
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $emplacements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
    }

    /**
     * Get hebergementUnifie
     *
     * @return HebergementUnifie
     */
    public function getHebergementUnifie()
    {
        return $this->hebergementUnifie;
    }

    /**
     * Set hebergementUnifie
     *
     * @param HebergementUnifie $hebergementUnifie
     *
     * @return Hebergement
     */
    public function setHebergementUnifie(
        HebergementUnifie $hebergementUnifie = null
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
     * @param HebergementTraduction $traduction
     *
     * @return Hebergement
     */
    public function addTraduction(HebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHebergement($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param HebergementTraduction $traduction
     */
    public function removeTraduction(HebergementTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Hebergement
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get station
     *
     * @return Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set station
     *
     * @param Station $station
     *
     * @return Hebergement
     */
    public function setStation(Station $station = null)
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
     * @param MoyenCommunication $moyenCom
     *
     * @return Hebergement
     */
    public function addMoyenCom(MoyenCommunication $moyenCom)
    {
        $this->moyenComs[] = $moyenCom;

        return $this;
    }

    /**
     * Remove moyenCom
     *
     * @param MoyenCommunication $moyenCom
     */
    public function removeMoyenCom(MoyenCommunication $moyenCom)
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

    /**
     * Get typeHebergement
     *
     * @return TypeHebergement
     */
    public function getTypeHebergement()
    {
        return $this->typeHebergement;
    }

    /**
     * Set typeHebergement
     *
     * @param TypeHebergement $typeHebergement
     *
     * @return Hebergement
     */
    public function setTypeHebergement(
        TypeHebergement $typeHebergement = null
    ) {
        $this->typeHebergement = $typeHebergement;

        return $this;
    }

    /**
     * Add emplacement
     *
     * @param EmplacementHebergement $emplacement
     *
     * @return Hebergement
     */
    public function addEmplacement(EmplacementHebergement $emplacement)
    {
        $this->emplacements[] = $emplacement->setHebergement($this);

        return $this;
    }

    /**
     * tri la collection d'emplacements en fonction de leur valeur traduite grace à l'objet $translator passé en parametre)
     * @param DataCollectorTranslator $translator
     * @return $this
     */
    public function triEmplacements(DataCollectorTranslator $translator)
    {
        // Trier les emplacements en fonction de leurs ordre d'affichage
        $emplacements = $this->getEmplacements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $emplacements->getIterator();
        unset($emplacements);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (EmplacementHebergement $a, EmplacementHebergement $b) use ($translator) {
            $libelle1 = $translator->trans(((string)$a->getTypeEmplacement()) . 'Libelle');
            $libelle2 = $translator->trans(((string)$b->getTypeEmplacement()) . 'Libelle');
            return strcmp($libelle1, $libelle2);
        });
        // passer le tableau trié dans une nouvelle collection
        $this->emplacements = new ArrayCollection(iterator_to_array($iterator));
        return $this;
    }

    /**
     * Get emplacements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmplacements()
    {
        return $this->emplacements;
    }

    /**
     * Remove emplacement
     *
     * @param EmplacementHebergement $emplacement
     */
    public function removeEmplacement(EmplacementHebergement $emplacement)
    {
        $this->emplacements->removeElement($emplacement);
    }
}
