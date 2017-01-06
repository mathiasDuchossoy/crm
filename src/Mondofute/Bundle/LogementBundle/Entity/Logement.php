<?php

namespace Mondofute\Bundle\LogementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteLogement;
use Mondofute\Bundle\DecoteBundle\Entity\DecoteLogementPeriode;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogement;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionLogementPeriode;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Logement
 */
class Logement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $accesPMR;
    /**
     * @var integer unsigned
     */
    private $capacite;
    /**
     * @var smallint unsigned
     */
    private $superficieMin;
    /**
     * @var smallint unsigned
     */
    private $superficieMax;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var LogementUnifie
     */
    private $logementUnifie;
    /**
     * @var FournisseurHebergement
     */
    private $fournisseurHebergement;
    /**
     * @var Collection
     */
    private $photos;
    /**
     * @var boolean
     */
    private $actif = true;
    /**
     * @var Collection
     */
    private $periodes;
    /**
     * @var Collection
     */
    private $prestationAnnexeLogements;
    /**
     * @var NombreDeChambre
     */
    private $nombreDeChambre;
    /**
     * @var Collection
     */
    private $typePeriodes;
    /**
     * @var Collection
     */
    private $promotionLogements;
    /**
     * @var Collection
     */
    private $decoteLogements;
    /**
     * @var Collection
     */
    private $promotionLogementPeriodes;
    /**
     * @var Collection
     */
    private $decoteLogementPeriode;
    /**
     * @var Collection
     */
    private $logementPeriodeLocatifs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
        $this->periodes = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->prestationAnnexeLogements = new ArrayCollection();
        $this->typePeriodes = new ArrayCollection();
        $this->promotionLogements = new ArrayCollection();
        $this->decoteLogements = new ArrayCollection();
        $this->promotionLogementPeriodes = new ArrayCollection();
        $this->decoteLogementPeriode = new ArrayCollection();
        $this->logementPeriodeLocatifs = new ArrayCollection();
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
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get accesPMR
     *
     * @return boolean
     */
    public function getAccesPMR()
    {
        return $this->accesPMR;
    }

    /**
     * Set accesPMR
     *
     * @param boolean $accesPMR
     *
     * @return Logement
     */
    public function setAccesPMR($accesPMR)
    {
        $this->accesPMR = $accesPMR;

        return $this;
    }

    /**
     * Get capacite
     *
     * @return integer
     */
    public function getCapacite()
    {
        return $this->capacite;
    }

    /**
     * Set capacite
     *
     * @param integer $capacite
     *
     * @return Logement
     */
    public function setCapacite($capacite)
    {
        $this->capacite = $capacite;

        return $this;
    }

    /**
     * Get superficieMin
     *
     * @return integer
     */
    public function getSuperficieMin()
    {
        return $this->superficieMin;
    }

    /**
     * Set superficieMin
     *
     * @param integer $superficieMin
     *
     * @return Logement
     */
    public function setSuperficieMin($superficieMin)
    {
        $this->superficieMin = $superficieMin;

        return $this;
    }

    /**
     * Get superficieMax
     *
     * @return integer
     */
    public function getSuperficieMax()
    {
        return $this->superficieMax;
    }

    /**
     * Set superficieMax
     *
     * @param integer $superficieMax
     *
     * @return Logement
     */
    public function setSuperficieMax($superficieMax)
    {
        $this->superficieMax = $superficieMax;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param LogementTraduction $traduction
     */
    public function removeTraduction(LogementTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
        $traduction->setLogement(null);
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
     * @param ArrayCollection $traductions
     * @return Logement $this
     */
    public function setTraductions(ArrayCollection $traductions)
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
     * @param LogementTraduction $traduction
     *
     * @return Logement
     */
    public function addTraduction(LogementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setLogement($this);

        return $this;
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
     * @return Logement
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get logementUnifie
     *
     * @return LogementUnifie
     */
    public function getLogementUnifie()
    {
        return $this->logementUnifie;
    }

    /**
     * Set logementUnifie
     *
     * @param LogementUnifie $logementUnifie
     *
     * @return Logement
     */
    public function setLogementUnifie(LogementUnifie $logementUnifie = null)
    {
        $this->logementUnifie = $logementUnifie;

        return $this;
    }

    /**
     * Get fournisseurHebergement
     *
     * @return FournisseurHebergement
     */
    public function getFournisseurHebergement()
    {
        return $this->fournisseurHebergement;
    }

    /**
     * Set fournisseurHebergement
     *
     * @param FournisseurHebergement $fournisseurHebergement
     *
     * @return Logement
     */
    public function setFournisseurHebergement(
        FournisseurHebergement $fournisseurHebergement = null
    )
    {
        $this->fournisseurHebergement = $fournisseurHebergement;

        return $this;
    }

    /**
     * Add photo
     *
     * @param LogementPhoto $photo
     *
     * @return Logement
     */
    public function addPhoto(LogementPhoto $photo)
    {
        $this->photos[] = $photo->setLogement($this);

        return $this;
    }

    /**
     * Remove photo
     *
     * @param LogementPhoto $photo
     */
    public function removePhoto(LogementPhoto $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return Collection
     */
    public function getPhotos()
    {
        return $this->photos;
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
     * @return Logement
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Add periode
     *
     * @param LogementPeriode $periode
     *
     * @return Logement
     */
    public function addPeriode(LogementPeriode $periode)
    {
        $this->periodes[] = $periode;

        return $this;
    }

    /**
     * Remove periode
     *
     * @param LogementPeriode $periode
     */
    public function removePeriode(LogementPeriode $periode)
    {
        $this->periodes->removeElement($periode);
    }

    /**
     * Get periodes
     *
     * @return Collection
     */
    public function getPeriodes()
    {
        return $this->periodes;
    }

    /**
     * Add prestationAnnexeLogement
     *
     * @param PrestationAnnexeLogement $prestationAnnexeLogement
     *
     * @return Logement
     */
    public function addPrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements[] = $prestationAnnexeLogement;

        return $this;
    }

    /**
     * Remove prestationAnnexeLogement
     *
     * @param PrestationAnnexeLogement $prestationAnnexeLogement
     */
    public function removePrestationAnnexeLogement(PrestationAnnexeLogement $prestationAnnexeLogement)
    {
        $this->prestationAnnexeLogements->removeElement($prestationAnnexeLogement);
    }

    /**
     * Get prestationAnnexeLogements
     *
     * @return Collection
     */
    public function getPrestationAnnexeLogements()
    {
        return $this->prestationAnnexeLogements;
    }

    /**
     * Get nombreDeChambre
     *
     * @return NombreDeChambre
     */
    public function getNombreDeChambre()
    {
        return $this->nombreDeChambre;
    }

    /**
     * Set nombreDeChambre
     *
     * @param NombreDeChambre $nombreDeChambre
     *
     * @return Logement
     */
    public function setNombreDeChambre(NombreDeChambre $nombreDeChambre = null)
    {
        $this->nombreDeChambre = $nombreDeChambre;

        return $this;
    }

    /**
     * Add typePeriode
     *
     * @param TypePeriode $typePeriode
     *
     * @return Logement
     */
    public function addTypePeriode(TypePeriode $typePeriode)
    {
        $this->typePeriodes[] = $typePeriode;

        return $this;
    }

    /**
     * Remove typePeriode
     *
     * @param TypePeriode $typePeriode
     */
    public function removeTypePeriode(TypePeriode $typePeriode)
    {
        $this->typePeriodes->removeElement($typePeriode);
    }

    /**
     * Get typePeriodes
     *
     * @return Collection
     */
    public function getTypePeriodes()
    {
        return $this->typePeriodes;
    }

    /**
     * Add promotionLogement
     *
     * @param PromotionLogement $promotionLogement
     *
     * @return Logement
     */
    public function addPromotionLogement(PromotionLogement $promotionLogement)
    {
        $this->promotionLogements[] = $promotionLogement;

        return $this;
    }

    /**
     * Remove promotionLogement
     *
     * @param PromotionLogement $promotionLogement
     */
    public function removePromotionLogement(PromotionLogement $promotionLogement)
    {
        $this->promotionLogements->removeElement($promotionLogement);
    }

    /**
     * Get promotionLogements
     *
     * @return Collection
     */
    public function getPromotionLogements()
    {
        return $this->promotionLogements;
    }

    /**
     * Add decoteLogement
     *
     * @param DecoteLogement $decoteLogement
     *
     * @return Logement
     */
    public function addDecoteLogement(DecoteLogement $decoteLogement)
    {
        $this->decoteLogements[] = $decoteLogement;

        return $this;
    }

    /**
     * Remove decoteLogement
     *
     * @param DecoteLogement $decoteLogement
     */
    public function removeDecoteLogement(DecoteLogement $decoteLogement)
    {
        $this->decoteLogements->removeElement($decoteLogement);
    }

    /**
     * Get decoteLogements
     *
     * @return Collection
     */
    public function getDecoteLogements()
    {
        return $this->decoteLogements;
    }

    /**
     * Add promotionLogementPeriode
     *
     * @param PromotionLogementPeriode $promotionLogementPeriode
     *
     * @return Logement
     */
    public function addPromotionLogementPeriode(PromotionLogementPeriode $promotionLogementPeriode)
    {
        $this->promotionLogementPeriodes[] = $promotionLogementPeriode;

        return $this;
    }

    /**
     * Remove promotionLogementPeriode
     *
     * @param PromotionLogementPeriode $promotionLogementPeriode
     */
    public function removePromotionLogementPeriode(PromotionLogementPeriode $promotionLogementPeriode)
    {
        $this->promotionLogementPeriodes->removeElement($promotionLogementPeriode);
    }

    /**
     * Get promotionLogementPeriodes
     *
     * @return Collection
     */
    public function getPromotionLogementPeriodes()
    {
        return $this->promotionLogementPeriodes;
    }

    /**
     * Add decoteLogementPeriode
     *
     * @param DecoteLogementPeriode $decoteLogementPeriode
     *
     * @return Logement
     */
    public function addDecoteLogementPeriode(DecoteLogementPeriode $decoteLogementPeriode)
    {
        $this->decoteLogementPeriode[] = $decoteLogementPeriode;

        return $this;
    }

    /**
     * Remove decoteLogementPeriode
     *
     * @param DecoteLogementPeriode $decoteLogementPeriode
     */
    public function removeDecoteLogementPeriode(DecoteLogementPeriode $decoteLogementPeriode)
    {
        $this->decoteLogementPeriode->removeElement($decoteLogementPeriode);
    }

    /**
     * Get decoteLogementPeriode
     *
     * @return Collection
     */
    public function getDecoteLogementPeriode()
    {
        return $this->decoteLogementPeriode;
    }

    /**
     * Add logementPeriodeLocatif
     *
     * @param LogementPeriodeLocatif $logementPeriodeLocatif
     *
     * @return Logement
     */
    public function addLogementPeriodeLocatif(LogementPeriodeLocatif $logementPeriodeLocatif)
    {
        $this->logementPeriodeLocatifs[] = $logementPeriodeLocatif->setLogement($this);

        return $this;
    }

    /**
     * Remove logementPeriodeLocatif
     *
     * @param LogementPeriodeLocatif $logementPeriodeLocatif
     */
    public function removeLogementPeriodeLocatif(LogementPeriodeLocatif $logementPeriodeLocatif)
    {
//        $logementPeriodeLocatif->setPeriode(null);
        $this->logementPeriodeLocatifs->removeElement($logementPeriodeLocatif);
    }

    /**
     * Get logementPeriodeLocatifs
     *
     * @return Collection
     */
    public function getLogementPeriodeLocatifs()
    {
        return $this->logementPeriodeLocatifs;
    }
}
