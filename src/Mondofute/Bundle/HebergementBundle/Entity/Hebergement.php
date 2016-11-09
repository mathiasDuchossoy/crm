<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
=======
||||||| parent of a87b4fd... mise en place bdd, enitities et majlislancer
=======
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> a87b4fd... mise en place bdd, enitities et majlislancer
<<<<<<< af54ec0831e12c96df4c94d5b4706d67c77c326f
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
=======
||||||| parent of a87b4fd... mise en place bdd, enitities et majlislancer
=======
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> a87b4fd... mise en place bdd, enitities et majlislancer
<<<<<<< af54ec0831e12c96df4c94d5b4706d67c77c326f
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
||||||| parent of a0ed718... mise en place bdd, enitities et majlislancer
=======
||||||| parent of 6a618f5... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> 6a618f5... mise en place bdd, enitities et majlislancer
||||||| parent of ab1b732... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> ab1b732... mise en place bdd, enitities et majlislancer
||||||| parent of ecfbf7d... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> ecfbf7d... mise en place bdd, enitities et majlislancer
||||||| parent of b7592d0... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> b7592d0... mise en place bdd, enitities et majlislancer
||||||| parent of b8d8301... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> b8d8301... mise en place bdd, enitities et majlislancer
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
<<<<<<< HEAD
>>>>>>> a0ed718... mise en place bdd, enitities et majlislancer
||||||| parent of 6a618f5... mise en place bdd, enitities et majlislancer
=======
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
=======
||||||| parent of a87b4fd... mise en place bdd, enitities et majlislancer
=======
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> a87b4fd... mise en place bdd, enitities et majlislancer
<<<<<<< af54ec0831e12c96df4c94d5b4706d67c77c326f
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> 6a618f5... mise en place bdd, enitities et majlislancer
>>>>>>> ba9625ab52772394c59e14bf831e9f5aa1a2ab57
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
<<<<<<< HEAD
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
||||||| merged common ancestors
=======
<<<<<<< HEAD
use Doctrine\Common\Collections\Collection;
<<<<<<< HEAD
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
||||||| parent of 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
=======
use Doctrine\Common\Collections\Collection;
>>>>>>> 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
>>>>>>> ba9625ab52772394c59e14bf831e9f5aa1a2ab57
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
<<<<<<< HEAD
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< b62042932404edbd0486b874ceacc33a2e43ade9:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php
||||||| merged common ancestors
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< HEAD

||||||| parent of 5739219... création bundle, entités mis en place + sql et deploybundle,
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php.orig
>>>>>>> 5739219... création bundle, entités mis en place + sql et deploybundle,
}
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @param HebergementTraduction $traduction
     *
     * @return Hebergement
     */
    public function addTraduction(HebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHebergement($this);

        return $this;
    }
<<<<<<< HEAD

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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
}
=======
>>>>>>> mise en place bdd, enitities et majlislancer
}
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
>>>>>>> mise en place bdd, enitities et majlislancer
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
||||||| merged common ancestors
=======
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< b2f51bef12678cd5338e60b5d4abd76a031998e7
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
<<<<<<< HEAD
||||||| parent of 6384184... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> 6384184... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| parent of 04442cd... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> 04442cd... mise en place bdd, enitities et majlislancer
>>>>>>> 932a17f66f539f15cf65502141dfd39dba014c90
    /**
     * @var Collection
     */
    private $motClefs;
<<<<<<< HEAD
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< b62042932404edbd0486b874ceacc33a2e43ade9:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php
||||||| merged common ancestors
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> création bundle, entités mis en place + sql et deploybundle,:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php.orig
}
<<<<<<< HEAD
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @param HebergementTraduction $traduction
     *
     * @return Hebergement
     */
    public function addTraduction(HebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHebergement($this);

        return $this;
    }
<<<<<<< HEAD

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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
}
=======
>>>>>>> mise en place bdd, enitities et majlislancer
}
||||||| merged common ancestors
=======
<<<<<<< HEAD
||||||| merged common ancestors
=======
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
>>>>>>> mise en place bdd, enitities et majlislancer
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
||||||| parent of f0a203a... mise en place bdd, enitities et majlislancer
=======
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
<<<<<<< HEAD
>>>>>>> f0a203a... mise en place bdd, enitities et majlislancer
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
=======
<<<<<<< af54ec0831e12c96df4c94d5b4706d67c77c326f
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
||||||| parent of 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
=======
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
>>>>>>> 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
<<<<<<< HEAD
||||||| parent of df53520... mise en place bdd, enitities et majlislancer
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
=======
<<<<<<< b2f51bef12678cd5338e60b5d4abd76a031998e7
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
>>>>>>> df53520... mise en place bdd, enitities et majlislancer
    /**
<<<<<<< HEAD
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
<<<<<<< HEAD
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
||||||| merged common ancestors
<<<<<<<<< Temporary merge branch 1
=======
<<<<<<< HEAD
>>>>>>> 1b7d0b7201106540028541c0ee9088274cf1089c
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<<<<<<< HEAD
||||||| parent of 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
=======
    /**
||||||| parent of 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
=======
     * @var Collection
     */
    private $motClefs;
    /**
>>>>>>> 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
>>>>>>> 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
||||||| merged common ancestors
=======
>>>>>>> 868e3f99adcebb631dd595e858a1f4f6b235950b
||||||| merged common ancestors
=======
<<<<<<< HEAD
||||||| parent of a32e7ed... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> a32e7ed... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
>>>>>>> 08c1e788e9cd892c6c04f6069a0d5c6f7efcdbc3
||||||| merged common ancestors
||||||||| merged common ancestors
=========
=======
||||||| merged common ancestors
=======
>>>>>>> 1b7d0b7201106540028541c0ee9088274cf1089c
||||||| parent of 58cece8... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 58cece8... mise en place bdd, enitities et majlislancer
>>>>>>> 6be0d00aeb82bc3feca29e3b8c54902790c61b0e
<<<<<<< HEAD
||||||| parent of 6384184... mise en place bdd, enitities et majlislancer
=======
||||||| parent of f6df5f9... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> f6df5f9... mise en place bdd, enitities et majlislancer
>>>>>>> 6384184... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| parent of 04442cd... mise en place bdd, enitities et majlislancer
=======
||||||| parent of 5262f9d... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 5262f9d... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
>>>>>>> 04442cd... mise en place bdd, enitities et majlislancer
>>>>>>> 932a17f66f539f15cf65502141dfd39dba014c90
||||||| parent of 26ff9b2... création bundle, entités mis en place + sql et deploybundle,
>>>>>>> 2562faca76d25dccba2639625e824c3a56b749f4
=======
>>>>>>> 2562faca76d25dccba2639625e824c3a56b749f4
||||||| parent of b5a1adb... création bundle, entités mis en place + sql et deploybundle,
=======
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
>>>>>>> b5a1adb... création bundle, entités mis en place + sql et deploybundle,
>>>>>>> 26ff9b2... création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
<<<<<<< HEAD
<<<<<<< HEAD
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
||||||| parent of 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
=======
||||||| parent of 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
=======
        $this->moyenComs = new ArrayCollection();
>>>>>>> 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
        $this->visuels = new ArrayCollection();
<<<<<<< HEAD
>>>>>>> 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
||||||| parent of 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
=======
        $this->motClefs = new ArrayCollection();
>>>>>>> 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< b2f51bef12678cd5338e60b5d4abd76a031998e7
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
<<<<<<< HEAD
||||||| parent of 6384184... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> 6384184... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| parent of 04442cd... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> 04442cd... mise en place bdd, enitities et majlislancer
>>>>>>> 932a17f66f539f15cf65502141dfd39dba014c90

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
<<<<<<< HEAD
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
||||||| merged common ancestors
<<<<<<<<< Temporary merge branch 1
=======
<<<<<<< HEAD
>>>>>>> 1b7d0b7201106540028541c0ee9088274cf1089c
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======

    /**
||||||| parent of da4240e... création bundle, entités mis en place + sql et deploybundle,
=======
<<<<<<< HEAD
>>>>>>> da4240e... création bundle, entités mis en place + sql et deploybundle,
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< b62042932404edbd0486b874ceacc33a2e43ade9:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php
||||||| merged common ancestors
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< HEAD
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<<<<<<< HEAD
||||||| parent of 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
=======

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< b62042932404edbd0486b874ceacc33a2e43ade9:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php
||||||| merged common ancestors
||||||| merged common ancestors
||||||| merged common ancestors
||||||||| merged common ancestors
=========
||||||| parent of 58cece8... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
=======
||||||| parent of 58cece8... mise en place bdd, enitities et majlislancer
>>>>>>> 1b7d0b7201106540028541c0ee9088274cf1089c
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< HEAD
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }
<<<<<<< HEAD
>>>>>>> 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
||||||| parent of 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
=======

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> 7e6c28e... création bundle, entités mis en place + sql et deploybundle,
||||||| merged common ancestors
=======
>>>>>>> 868e3f99adcebb631dd595e858a1f4f6b235950b
||||||| merged common ancestors
=======
<<<<<<< HEAD
||||||| parent of a32e7ed... mise en place bdd, enitities et majlislancer
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> a32e7ed... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
>>>>>>> 08c1e788e9cd892c6c04f6069a0d5c6f7efcdbc3
||||||| parent of 58cece8... mise en place bdd, enitities et majlislancer
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< HEAD
>>>>>>> 58cece8... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> 6be0d00aeb82bc3feca29e3b8c54902790c61b0e
||||||| merged common ancestors
=======
>>>>>>> 6be0d00aeb82bc3feca29e3b8c54902790c61b0e
||||||| parent of 5739219... création bundle, entités mis en place + sql et deploybundle,
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php.orig
>>>>>>> 5739219... création bundle, entités mis en place + sql et deploybundle,
}
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
||||||| parent of a0ed718... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @param HebergementTraduction $traduction
     *
     * @return Hebergement
     */
    public function addTraduction(HebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHebergement($this);

        return $this;
    }
<<<<<<< HEAD

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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
>>>>>>> mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
>>>>>>> a0ed718... mise en place bdd, enitities et majlislancer
||||||| parent of 6a618f5... mise en place bdd, enitities et majlislancer
=======
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
}
=======
>>>>>>> mise en place bdd, enitities et majlislancer
}
||||||| merged common ancestors
=======
||||||| parent of ab1b732... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
=======
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> ab1b732... mise en place bdd, enitities et majlislancer
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< fc1e91c783a524b52b05631d824a586e4b76f17d
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<<<<<<< HEAD
||||||| parent of b8d8301... mise en place bdd, enitities et majlislancer
||||||| parent of f0a203a... mise en place bdd, enitities et majlislancer
=======
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
<<<<<<< HEAD
>>>>>>> f0a203a... mise en place bdd, enitities et majlislancer
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
=======
<<<<<<< af54ec0831e12c96df4c94d5b4706d67c77c326f
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
=======
||||||| parent of f0a203a... mise en place bdd, enitities et majlislancer
=======
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
||||||| parent of 35ab73b... mise en place bdd, enitities et majlislancer
=======
<<<<<<< HEAD
>>>>>>> 35ab73b... mise en place bdd, enitities et majlislancer
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
<<<<<<< HEAD
>>>>>>> f0a203a... mise en place bdd, enitities et majlislancer
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
=======
||||||| parent of a87b4fd... mise en place bdd, enitities et majlislancer
=======
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> a87b4fd... mise en place bdd, enitities et majlislancer
<<<<<<< af54ec0831e12c96df4c94d5b4706d67c77c326f
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
>>>>>>> b8d8301... mise en place bdd, enitities et majlislancer
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
<<<<<<< HEAD
||||||| parent of df53520... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> df53520... mise en place bdd, enitities et majlislancer

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }
<<<<<<< HEAD
||||||| parent of df53520... mise en place bdd, enitities et majlislancer
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
=======
<<<<<<< b2f51bef12678cd5338e60b5d4abd76a031998e7
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
>>>>>>> df53520... mise en place bdd, enitities et majlislancer

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
>>>>>>> 868e3f99adcebb631dd595e858a1f4f6b235950b
||||||| merged common ancestors
=======
>>>>>>> 6be0d00aeb82bc3feca29e3b8c54902790c61b0e
>>>>>>> 08c1e788e9cd892c6c04f6069a0d5c6f7efcdbc3
||||||| merged common ancestors
>>>>>>> 58cece8... mise en place bdd, enitities et majlislancer
>>>>>>>>> Temporary merge branch 2
=======
>>>>>>> 58cece8... mise en place bdd, enitities et majlislancer
>>>>>>> 6be0d00aeb82bc3feca29e3b8c54902790c61b0e
>>>>>>> 1b7d0b7201106540028541c0ee9088274cf1089c
<<<<<<< HEAD
||||||| parent of 6384184... mise en place bdd, enitities et majlislancer
=======
||||||| parent of f6df5f9... mise en place bdd, enitities et majlislancer
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> f6df5f9... mise en place bdd, enitities et majlislancer
>>>>>>> 6384184... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| parent of 04442cd... mise en place bdd, enitities et majlislancer
=======
||||||| parent of 5262f9d... mise en place bdd, enitities et majlislancer
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> 5262f9d... mise en place bdd, enitities et majlislancer
>>>>>>> 04442cd... mise en place bdd, enitities et majlislancer
>>>>>>> 932a17f66f539f15cf65502141dfd39dba014c90
||||||| parent of 2a97d3f... création bundle, entités mis en place + sql et deploybundle,
>>>>>>> 5262f9d... mise en place bdd, enitities et majlislancer
>>>>>>> 2562faca76d25dccba2639625e824c3a56b749f4
=======
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< HEAD
>>>>>>> 5262f9d... mise en place bdd, enitities et majlislancer
>>>>>>> 2562faca76d25dccba2639625e824c3a56b749f4
||||||| parent of da4240e... création bundle, entités mis en place + sql et deploybundle,
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,:src/Mondofute/Bundle/HebergementBundle/Entity/Hebergement.php.orig
>>>>>>> da4240e... création bundle, entités mis en place + sql et deploybundle,
>>>>>>> 2a97d3f... création bundle, entités mis en place + sql et deploybundle,
}
<<<<<<< HEAD
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
>>>>>>> 6a618f5... mise en place bdd, enitities et majlislancer
||||||| parent of ecfbf7d... mise en place bdd, enitities et majlislancer
=======
||||||| parent of f0a203a... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @param HebergementTraduction $traduction
     *
     * @return Hebergement
     */
    public function addTraduction(HebergementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setHebergement($this);

        return $this;
    }
<<<<<<< HEAD

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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
}
>>>>>>> mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
>>>>>>> f0a203a... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
>>>>>>> ecfbf7d... mise en place bdd, enitities et majlislancer
||||||| parent of b7592d0... mise en place bdd, enitities et majlislancer
=======
||||||| parent of e0840c7... mise en place bdd, enitities et majlislancer
=======
||||||| parent of b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
}
=======
>>>>>>> mise en place bdd, enitities et majlislancer
}
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<<<<<<< d2b0b0d12cef2407e07ecbbf1e23324b58ffcb34
>>>>>>> mise en place bdd, enitities et majlislancer
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
<<<<<<< HEAD
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }
<<<<<<< HEAD

    /**
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
}
<<<<<<< 61d76ef7d38132aefa999d1c551635cc2427cee7
>>>>>>> mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\ClassementHebergement;
use Nucleus\MoyenComBundle\Entity\MoyenCommunication;
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @var Collection
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
     * @var Collection
     */
    private $moyenComs;
    /**
     * @var TypeHebergement
     */
    private $typeHebergement;
    /**
     * @var Collection
     */
    private $emplacements;
    /**
     * @var Collection
     */
    private $visuels;
    /**
     * @var boolean
     */
    private $actif = true;
<<<<<<< HEAD
    /**
     * @var Collection
     */
    private $motClefs;
    /**
     * @var HebergementCoupDeCoeur
     */
    private $coupDeCoeur;
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
||||||| parent of 997839e... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
||||||| merged common ancestors
=======
    /**
     * @var Collection
     */
    private $motClefs;
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> 997839e... mise en place bdd, enitities et majlislancer
>>>>>>> ba9625ab52772394c59e14bf831e9f5aa1a2ab57

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->emplacements = new ArrayCollection();
        $this->moyenComs = new ArrayCollection();
        $this->visuels = new ArrayCollection();
        $this->motClefs = new ArrayCollection();
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
    )
    {
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
     * @return Collection
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
    )
    {
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
    public function triEmplacements($translator)
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
     * @return Collection
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

    /**
     * Add visuel
     *
     * @param HebergementVisuel $visuel
     *
     * @return Hebergement
     */
    public function addVisuel(HebergementVisuel $visuel)
    {
        $this->visuels[] = $visuel->setHebergement($this);

        return $this;
    }

    /**
     * Remove visuel
     *
     * @param HebergementVisuel $visuel
     */
    public function removeVisuel(HebergementVisuel $visuel)
    {
        $this->visuels->removeElement($visuel);
    }

    /**
     * Get visuels
     *
     * @return Collection
     */
    public function getVisuels()
    {
        return $this->visuels;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Hebergement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }
<<<<<<< HEAD

    /**
<<<<<<< HEAD
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
||||||| parent of fd39b4e... création bundle, entités mis en place + sql et deploybundle,
=======
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

        return $this;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
||||||| merged common ancestors
=======
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,

    /**
>>>>>>> fd39b4e... création bundle, entités mis en place + sql et deploybundle,
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
<<<<<<< 0846c6fcf30967b0020f60262dfffff76f741546
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
||||||| merged common ancestors
=======
<<<<<<< HEAD
<<<<<<< HEAD
||||||| parent of 997839e... mise en place bdd, enitities et majlislancer
=======
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
>>>>>>> 997839e... mise en place bdd, enitities et majlislancer

    /**
     * Get coupDeCoeur
     *
     * @return HebergementCoupDeCoeur
     */
    public function getCoupDeCoeur()
    {
        return $this->coupDeCoeur;
    }

    /**
     * Set coupDeCoeur
     *
     * @param HebergementCoupDeCoeur $coupDeCoeur
     *
     * @return Hebergement
     */
    public function setCoupDeCoeur(HebergementCoupDeCoeur $coupDeCoeur = null)
    {
        if(!empty($coupDeCoeur))
        {
            $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);
        }
        else{
            $this->coupDeCoeur = null;
        }

        return $this;
    }

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
//        $this->motClefs[] = $motClef;
        $this->motClefs[] = $motClef->addHebergement($this);

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
        $motClef->removeHebergement($this);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
||||||| parent of 6810fd4... mise en place bdd, enitities et majlislancer
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> 6810fd4... mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
||||||| parent of 997839e... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
=======
||||||| merged common ancestors
>>>>>>> 6052063bef0bd7f02dbfbb13aca410cbd374ba31
=======
>>>>>>> création bundle, entités mis en place + sql et deploybundle,
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> mise en place bdd, enitities et majlislancer
<<<<<<< HEAD
>>>>>>> 997839e... mise en place bdd, enitities et majlislancer
||||||| parent of df53520... mise en place bdd, enitities et majlislancer
=======
||||||| merged common ancestors
=======

    /**
     * Add motClef
     *
     * @param MotClef $motClef
     *
     * @return Hebergement
     */
    public function addMotClef(MotClef $motClef)
    {
        $this->motClefs[] = $motClef;

        return $this;
    }

    /**
     * Remove motClef
     *
     * @param MotClef $motClef
     */
    public function removeMotClef(MotClef $motClef)
    {
        $this->motClefs->removeElement($motClef);
    }

    /**
     * Get motClefs
     *
     * @return Collection
     */
    public function getMotClefs()
    {
        return $this->motClefs;
    }
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> df53520... mise en place bdd, enitities et majlislancer
>>>>>>> ba9625ab52772394c59e14bf831e9f5aa1a2ab57
}
<<<<<<< HEAD
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
||||||| merged common ancestors
=======
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
>>>>>>> e0840c7... mise en place bdd, enitities et majlislancer
>>>>>>> b7592d0... mise en place bdd, enitities et majlislancer
>>>>>>> ba9625ab52772394c59e14bf831e9f5aa1a2ab57
