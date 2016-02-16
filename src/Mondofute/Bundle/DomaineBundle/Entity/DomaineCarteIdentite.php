<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;


/**
 * DomaineCarteIdentite
 */
class DomaineCarteIdentite
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $altitudeMini;

    /**
     * @var int
     */
    private $altitudeMaxi;

    /**
     * @var int
     */
    private $kmPistesSkiAlpin;

    /**
     * @var int
     */
    private $kmPistesSkiNordique;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var DomaineCarteIdentiteUnifie
     */
    private $domaineCarteIdentiteUnifie;
    /**
     * @var Collection
     */
    private $domaines;
    /**
     * @var Handiski
     */
    private $handiski;
    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\Snowpark
     */
    private $snowpark;
    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\RemonteeMecanique
     */
    private $remonteeMecanique;

    /**
     * Constructor
     */
    public function __construct()
    {
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

    /**
     * Get altitudeMini
     *
     * @return int
     */
    public function getAltitudeMini()
    {
        return $this->altitudeMini;
    }

    /**
     * Set altitudeMini
     *
     * @param integer $altitudeMini
     *
     * @return DomaineCarteIdentite
     */
    public function setAltitudeMini($altitudeMini)
    {
        $this->altitudeMini = $altitudeMini;

        return $this;
    }

    /**
     * Get altitudeMaxi
     *
     * @return int
     */
    public function getAltitudeMaxi()
    {
        return $this->altitudeMaxi;
    }

    /**
     * Set altitudeMaxi
     *
     * @param integer $altitudeMaxi
     *
     * @return DomaineCarteIdentite
     */
    public function setAltitudeMaxi($altitudeMaxi)
    {
        $this->altitudeMaxi = $altitudeMaxi;

        return $this;
    }

    /**
     * Get kmPistesSkiAlpin
     *
     * @return int
     */
    public function getKmPistesSkiAlpin()
    {
        return $this->kmPistesSkiAlpin;
    }

    /**
     * Set kmPistesSkiAlpin
     *
     * @param integer $kmPistesSkiAlpin
     *
     * @return DomaineCarteIdentite
     */
    public function setKmPistesSkiAlpin($kmPistesSkiAlpin)
    {
        $this->kmPistesSkiAlpin = $kmPistesSkiAlpin;

        return $this;
    }

    /**
     * Get kmPistesSkiNordique
     *
     * @return int
     */
    public function getKmPistesSkiNordique()
    {
        return $this->kmPistesSkiNordique;
    }

    /**
     * Set kmPistesSkiNordique
     *
     * @param integer $kmPistesSkiNordique
     *
     * @return DomaineCarteIdentite
     */
    public function setKmPistesSkiNordique($kmPistesSkiNordique)
    {
        $this->kmPistesSkiNordique = $kmPistesSkiNordique;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param DomaineCarteIdentiteTraduction $traduction
     *
     * @return DomaineCarteIdentite
     */
    public function addTraduction(DomaineCarteIdentiteTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param DomaineCarteIdentiteTraduction $traduction
     */
    public function removeTraduction(DomaineCarteIdentiteTraduction $traduction)
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
     * @return DomaineCarteIdentite
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get domaineCarteIdentiteUnifie
     *
     * @return DomaineCarteIdentiteUnifie
     */
    public function getDomaineCarteIdentiteUnifie()
    {
        return $this->domaineCarteIdentiteUnifie;
    }

    /**
     * Set domaineCarteIdentiteUnifie
     *
     * @param DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie
     *
     * @return DomaineCarteIdentite
     */
    public function setDomaineCarteIdentiteUnifie(DomaineCarteIdentiteUnifie $domaineCarteIdentiteUnifie = null)
    {
        $this->domaineCarteIdentiteUnifie = $domaineCarteIdentiteUnifie;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setDomaineCarteIdentite($this);
            }
        }
        $this->snowpark = clone $this->getSnowpark();
        $this->handiski = clone $this->getHandiski();
        $this->remonteeMecanique = clone $this->getRemonteeMecanique();
        $pistes = $this->getPistes();
        $this->pistes = new ArrayCollection();
        if (count($pistes) > 0) {
            foreach ($pistes as $piste) {
                $clonePiste = clone $piste;
                $this->pistes->add($clonePiste);
                $clonePiste->setDomaineCarteIdentite($this);
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
     * @return DomaineCarteIdentite $this
     */
    public function setTraductions($traductions)
    {
        $this->traductions = $traductions;
        return $this;
    }

    /**
     * Get snowpark
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\Snowpark
     */
    public function getSnowpark()
    {
        return $this->snowpark;
    }

    /**
     * Set snowpark
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Snowpark $snowpark
     *
     * @return DomaineCarteIdentite
     */
    public function setSnowpark(\Mondofute\Bundle\DomaineBundle\Entity\Snowpark $snowpark = null)
    {
        $this->snowpark = $snowpark;

        return $this;
    }

    /**
     * Get handiski
     *
     * @return Handiski
     */
    public function getHandiski()
    {
        return $this->handiski;
    }

    /**
     * Set handiski
     *
     * @param Handiski $handiski
     *
     * @return DomaineCarteIdentite
     */
    public function setHandiski(Handiski $handiski = null)
    {
        $this->handiski = $handiski;

        return $this;
    }

    /**
     * Get remonteeMecanique
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\RemonteeMecanique
     */
    public function getRemonteeMecanique()
    {
        return $this->remonteeMecanique;
    }

    /**
     * Set remonteeMecanique
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\RemonteeMecanique $remonteeMecanique
     *
     * @return DomaineCarteIdentite
     */
    public function setRemonteeMecanique(\Mondofute\Bundle\DomaineBundle\Entity\RemonteeMecanique $remonteeMecanique = null)
    {
        $this->remonteeMecanique = $remonteeMecanique;

        return $this;
    }

    /**
     * Add domaine
     *
     * @param Domaine $domaine
     *
     * @return DomaineCarteIdentite
     */
    public function addDomaine(Domaine $domaine)
    {
        $this->domaines[] = $domaine->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove domaine
     *
     * @param Domaine $domaine
     */
    public function removeDomaine(Domaine $domaine)
    {
        $this->domaines->removeElement($domaine);
    }

    /**
     * Get domaines
     *
     * @return Collection
     */
    public function getDomaines()
    {
        return $this->domaines;
    }

    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur
     */
    private $niveauSkieur;


    /**
     * Set niveauSkieur
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur $niveauSkieur
     *
     * @return DomaineCarteIdentite
     */
    public function setNiveauSkieur(\Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur $niveauSkieur = null)
    {
        $this->niveauSkieur = $niveauSkieur;

        return $this;
    }

    /**
     * Get niveauSkieur
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur
     */
    public function getNiveauSkieur()
    {
        return $this->niveauSkieur;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pistes;


    /**
     * Add piste
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Piste $piste
     *
     * @return DomaineCarteIdentite
     */
    public function addPiste(\Mondofute\Bundle\DomaineBundle\Entity\Piste $piste)
    {
        $this->pistes[] = $piste->setDomaineCarteIdentite($this);

        return $this;
    }

    /**
     * Remove piste
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Piste $piste
     */
    public function removePiste(\Mondofute\Bundle\DomaineBundle\Entity\Piste $piste)
    {
        $this->pistes->removeElement($piste);
    }

    /**
     * Get pistes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPistes()
    {
        return $this->pistes;
    }
}
