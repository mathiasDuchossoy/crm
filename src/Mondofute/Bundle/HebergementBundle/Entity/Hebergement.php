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
<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
<<<<<<< HEAD
use Doctrine\Common\Collections\Collection;
<<<<<<< HEAD
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
||||||| parent of 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
=======
use Doctrine\Common\Collections\Collection;
>>>>>>> 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
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
<<<<<<< b2f51bef12678cd5338e60b5d4abd76a031998e7
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb
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
<<<<<<< b2f51bef12678cd5338e60b5d4abd76a031998e7
<<<<<<< 33d972edc0ce676ad13fe75c5879134f38593fbb

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
||||||| parent of 8ec36cd... création bundle, entités mis en place + sql et deploybundle,
=======

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
        $this->coupDeCoeur = $coupDeCoeur->setHebergement($this);

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
}
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> mise en place bdd, enitities et majlislancer
>>>>>>> b2c71ca... mise en place bdd, enitities et majlislancer
