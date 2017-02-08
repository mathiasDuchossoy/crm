<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurBundle\Entity\Traits\FournisseurTrait;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseur;
use Mondofute\Bundle\PromotionBundle\Entity\PromotionFournisseurPrestationAnnexe;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Nucleus\ContactBundle\Entity\Moral;


/**
 * Fournisseur
 */
class Fournisseur extends Moral
{
    use FournisseurTrait;

    private $id;
    /**
     * @var Collection
     */
    private $interlocuteurs;
    /**
     * @var FournisseurPasserelle
     */
    private $passerelle;
    /**
     * @var integer
     */
    private $contient = FournisseurContient::PRODUIT;
    /**
     * @var Collection
     */
    private $fournisseurEnfants;
    /**
     * @var Fournisseur
     */
    private $fournisseurParent;
    /**
     * @var Collection
     */
    private $hebergements;
    /**
     * @var Collection
     */
    private $remiseClefs;
    /**
     * @var Collection
     */
    private $receptions;
    /**
     * @var Media
     */
    private $logo;
    /**
     * @var Collection
     */
    private $listeServices;
    /**
     * @var Collection
     */
    private $types;
    /**
     * @var Collection
     */
    private $prestationAnnexes;
    /**
     * @var string
     */
    private $phototheque;
    /**
     * @var string
     */
    private $specificiteCommission;
    /**
     * @var string
     */
    private $retrocommissionMFFinSaison;
    /**
     * @var integer
     */
    private $conditionAnnulation;
    /**
     * @var integer
     */
    private $relocationAnnulation;
    /**
     * @var integer
     */
    private $delaiPaiementFacture;
    /**
     * @var string
     */
    private $lieuRetraitForfaitSki;
    /**
     * @var string
     */
    private $commissionForfaitFamille;
    /**
     * @var string
     */
    private $commissionForfaitPeriode;
    /**
     * @var string
     */
    private $commissionSupportMainLibre;
    /**
     * @var integer
     */
    private $blocageVente;
    /**
     * @var ConditionAnnulationDescription
     */
    private $conditionAnnulationDescription;
    /**
     * @var integer
     */
    private $priorite = Priorite::NC;
    /**
     * @var Collection
     */
    private $promotionFournisseurs;
    /**
     * @var Collection
     */
    private $promotionFournisseurPrestationAnnexes;
    /**
     * @var Collection
     */
    private $prestationAnnexeFournisseurs;

    /**
     * Fournisseur constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->interlocuteurs = new ArrayCollection();
        $this->remiseClefs = new ArrayCollection();
        $this->receptions = new ArrayCollection();
        $this->listeServices = new ArrayCollection();
        $this->prestationAnnexes = new ArrayCollection();
        $this->hebergements = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->fournisseurEnfants = new ArrayCollection();
        $this->promotionFournisseurs = new ArrayCollection();
        $this->promotionFournisseurPrestationAnnexes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add interlocuteur
     *
     * @param FournisseurInterlocuteur $interlocuteur
     *
     * @return Fournisseur
     */
    public function addInterlocuteur(FournisseurInterlocuteur $interlocuteur)
    {
        $this->interlocuteurs[] = $interlocuteur->setFournisseur($this);

        return $this;
    }

    /**
     * Remove interlocuteur
     *
     * @param FournisseurInterlocuteur $interlocuteur
     */
    public function removeInterlocuteur(FournisseurInterlocuteur $interlocuteur)
    {
        $this->interlocuteurs->removeElement($interlocuteur);
    }

    /**
     * Get passerelle
     *
     * @return FournisseurPasserelle
     */
    public function getPasserelle()
    {
        return $this->passerelle;
    }

    /**
     * Set passerelle
     *
     * @param FournisseurPasserelle $passerelle
     *
     * @return Fournisseur
     */
    public function setPasserelle(FournisseurPasserelle $passerelle = null)
    {
        $this->passerelle = $passerelle;

        return $this;
    }

    function __clone()
    {
        /** @var FournisseurInterlocuteur $interlocuteur */
//        $this->id = null;
//        $interlocuteurs = $this->getInterlocuteurs();
//        $this->interlocuteurs = new ArrayCollection();
//        if (count($interlocuteurs) > 0) {
//            foreach ($interlocuteurs as $interlocuteur) {
//                $cloneInterlocuteur = clone $interlocuteur;
//                $this->interlocuteurs->add($cloneInterlocuteur);
//                $cloneInterlocuteur->setFournisseur($this);
//            }
//        }
        $remiseClefs = $this->getRemiseClefs();
        $this->remiseClefs = new ArrayCollection();
        if (count($remiseClefs) > 0) {
            foreach ($remiseClefs as $remiseClef) {
                $cloneRemiseClef = clone $remiseClef;
                $this->remiseClefs->add($cloneRemiseClef);
                $cloneRemiseClef->setFournisseur($this);
            }
        }
        $receptions = $this->getReceptions();
        $this->receptions = new ArrayCollection();
        if (count($receptions) > 0) {
            /** @var Reception $reception */
            foreach ($receptions as $reception) {
                $cloneReception = clone $reception;
                $this->receptions->add($cloneReception);
                $cloneReception->setFournisseur($this);
            }
        }
//        $types = $this->getTypes();
//        $this->types = new ArrayCollection();
//        if (count($types) > 0) {
//            /** @var TypeFournisseur $type */
//            foreach ($types as $type) {
//                $cloneType = clone $type;
//                $this->types->add($cloneType);
//                $cloneType->setFournisseur($this);
//            }
//        }
        $listeServices = $this->getListeServices();
        $this->listeServices = new ArrayCollection();
        if (count($listeServices) > 0) {
            /** @var ListeService $listeService */
            foreach ($listeServices as $listeService) {
                $cloneListeService = clone $listeService;
                $this->listeServices->add($cloneListeService);
                $cloneListeService->setFournisseur($this);
            }
        }
        return $this;
    }

    /**
     * Get remiseClefs
     *
     * @return Collection
     */
    public function getRemiseClefs()
    {
        return $this->remiseClefs;
    }

    /**
     * Get receptions
     *
     * @return Collection
     */
    public function getReceptions()
    {
        return $this->receptions;
    }

    /**
     * Get listeServices
     *
     * @return Collection
     */
    public function getListeServices()
    {
        return $this->listeServices;
    }

    /**
     * Get interlocuteurs
     *
     * @return Collection
     */
    public function getInterlocuteurs()
    {
        return $this->interlocuteurs;
    }

    public function triRemiseClefs()
    {
        // Trier les emplacements en fonction de leurs ordre d'affichage
        $remiseClefs = $this->getRemiseClefs(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $remiseClefs->getIterator();
        unset($remiseClefs);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (RemiseClef $a, RemiseClef $b) {
            return strcmp($a->getLibelle(), $b->getLibelle());
        });
        // passer le tableau trié dans une nouvelle collection
        $this->remiseClefs = new ArrayCollection(iterator_to_array($iterator));
        return $this;
    }

    public function triReceptions()
    {
        // Trier les emplacements en fonction de leurs ordre d'affichage
        $receptions = $this->getReceptions(); // ArrayCollection data.
        if ($receptions->count() > 0) {

            // Recueillir un itérateur de tableau.
            $iterator = $receptions->getIterator();
            unset($receptions);

            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (Reception $a, Reception $b) {
                if (!empty($a->getTranche2())) {
                    $complement1 = $a->getTranche2()->getDebut()->format('Hi') . $a->getTranche2()->getFin()->format('Hi');
                } else {
                    $complement1 = '';
                }
                if (!empty($b->getTranche2())) {
                    $complement2 = $b->getTranche2()->getDebut()->format('Hi') . $b->getTranche2()->getFin()->format('Hi');
                } else {
                    $complement2 = '';
                }
                $libelle1 = (($a->getJour() == 0) ? 7 : $a->getJour()) . $a->getTranche1()->getDebut()->format('Hi') . $a->getTranche1()->getFin()->format('Hi') . $complement1;
                $libelle2 = (($b->getJour() == 0) ? 7 : $b->getJour()) . $b->getTranche1()->getDebut()->format('Hi') . $b->getTranche1()->getFin()->format('Hi') . $complement2;
                return strcmp($libelle1, $libelle2);
            });
            // passer le tableau trié dans une nouvelle collection
            $this->receptions = new ArrayCollection(iterator_to_array($iterator));
        }
        return $this;
    }

    /**
     * Get contient
     *
     * @return integer
     */
    public function getContient()
    {
        return $this->contient;
    }

    /**
     * Set contient
     *
     * @param integer $contient
     *
     * @return Fournisseur
     */
    public function setContient($contient)
    {
        $this->contient = $contient;

        return $this;
    }

    /**
     * Add fournisseurEnfant
     *
     * @param Fournisseur $fournisseurEnfant
     *
     * @return Fournisseur
     */
    public function addFournisseurEnfant(Fournisseur $fournisseurEnfant)
    {
        $this->fournisseurEnfants[] = $fournisseurEnfant;

        return $this;
    }

    /**
     * Remove fournisseurEnfant
     *
     * @param Fournisseur $fournisseurEnfant
     */
    public function removeFournisseurEnfant(Fournisseur $fournisseurEnfant)
    {
        $this->fournisseurEnfants->removeElement($fournisseurEnfant);
    }

    /**
     * Get fournisseurEnfants
     *
     * @return Collection
     */
    public function getFournisseurEnfants()
    {
        return $this->fournisseurEnfants;
    }

    /**
     * Get fournisseurParent
     *
     * @return Fournisseur
     */
    public function getFournisseurParent()
    {
        return $this->fournisseurParent;
    }

    /**
     * Set fournisseurParent
     *
     * @param Fournisseur $fournisseurParent
     *
     * @return Fournisseur
     */
    public function setFournisseurParent(
        Fournisseur $fournisseurParent = null
    )
    {
        $this->fournisseurParent = $fournisseurParent;

        return $this;
    }

    /**
     * Add hebergement
     *
     * @param FournisseurHebergement $hebergement
     *
     * @return Fournisseur
     */
    public function addHebergement(FournisseurHebergement $hebergement)
    {
        $this->hebergements[] = $hebergement;

        return $this;
    }

    /**
     * Remove hebergement
     *
     * @param FournisseurHebergement $hebergement
     */
    public function removeHebergement(FournisseurHebergement $hebergement)
    {
        $this->hebergements->removeElement($hebergement);
    }

    /**
     * Get hebergements
     *
     * @return Collection
     */
    public function getHebergements()
    {
        return $this->hebergements;
    }

    /**
     * Add remiseClef
     *
     * @param RemiseClef $remiseClef
     *
     * @return Fournisseur
     */
    public function addRemiseClef(RemiseClef $remiseClef)
    {
        $this->remiseClefs[] = $remiseClef->setFournisseur($this);
        return $this;
    }

    /**
     * Remove remiseClef
     *
     * @param RemiseClef $remiseClef
     */
    public function removeRemiseClef(RemiseClef $remiseClef)
    {
        $this->remiseClefs->removeElement($remiseClef);
    }

    /**
     * Add reception
     *
     * @param Reception $reception
     *
     * @return Fournisseur
     */
    public function addReception(Reception $reception)
    {
        $this->receptions[] = $reception->setFournisseur($this);

        return $this;
    }

    /**
     * Remove reception
     *
     * @param Reception $reception
     */
    public function removeReception(Reception $reception)
    {
        $this->receptions->removeElement($reception);
    }

    /**
     * Add listeService
     *
     * @param ListeService $listeService
     *
     * @return Fournisseur
     */
    public function addListeService(ListeService $listeService)
    {
        $this->listeServices[] = $listeService;

        return $this;
    }

    /**
     * Remove listeService
     *
     * @param ListeService $listeService
     */
    public function removeListeService(ListeService $listeService)
    {
        $this->listeServices->removeElement($listeService);
    }

    /**
     * Get logo
     *
     * @return Media
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set logo
     *
     * @param Media $logo
     *
     * @return Fournisseur
     */
    public function setLogo(Media $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Add type
     *
     * @param FamillePrestationAnnexe $type
     *
     * @return Fournisseur
     */
    public function addType(FamillePrestationAnnexe $type)
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * Remove type
     *
     * @param FamillePrestationAnnexe $type
     */
    public function removeType(FamillePrestationAnnexe $type)
    {
        $this->types->removeElement($type);
    }

    /**
     * Get types
     *
     * @return Collection
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Remove prestationAnnex
     *
     * @param FournisseurPrestationAnnexe $prestationAnnex
     */
    public function removePrestationAnnex(FournisseurPrestationAnnexe $prestationAnnex)
    {
        $this->prestationAnnexes->removeElement($prestationAnnex);
    }

    /**
     * Get prestationAnnexes
     *
     * @return Collection
     */
    public function getPrestationAnnexes()
    {
        return $this->prestationAnnexes;
    }

    /**
     * @return $this
     */
    public function setPrestationAnnexes()
    {
        $newPrestationAnnexes = new ArrayCollection();
        /** @var FournisseurPrestationAnnexe $prestationAnnex */
        foreach ($this->getPrestationAnnexes() as $prestationAnnex) {
            $newPrestationAnnexes->set($prestationAnnex->getPrestationAnnexe()->getId(), $prestationAnnex);
        }

        $this->getPrestationAnnexes()->clear();

        foreach ($newPrestationAnnexes as $prestationAnnex) {
            $this->addPrestationAnnex($prestationAnnex);
        }
        return $this;
    }

    /**
     * Add prestationAnnex
     *
     * @param FournisseurPrestationAnnexe $prestationAnnex
     *
     * @return Fournisseur
     */
    public function addPrestationAnnex(FournisseurPrestationAnnexe $prestationAnnex)
    {
        $this->prestationAnnexes[] = $prestationAnnex->setFournisseur($this);

        return $this;
    }

    /**
     * Get phototheque
     *
     * @return string
     */
    public function getPhototheque()
    {
        return $this->phototheque;
    }

    /**
     * Set phototheque
     *
     * @param string $phototheque
     *
     * @return Fournisseur
     */
    public function setPhototheque($phototheque)
    {
        $this->phototheque = $phototheque;

        return $this;
    }

    /**
     * Get specificiteCommission
     *
     * @return string
     */
    public function getSpecificiteCommission()
    {
        return $this->specificiteCommission;
    }

    /**
     * Set specificiteCommission
     *
     * @param string $specificiteCommission
     *
     * @return Fournisseur
     */
    public function setSpecificiteCommission($specificiteCommission)
    {
        $this->specificiteCommission = $specificiteCommission;

        return $this;
    }

    /**
     * Get retrocommissionMFFinSaison
     *
     * @return string
     */
    public function getRetrocommissionMFFinSaison()
    {
        return $this->retrocommissionMFFinSaison;
    }

    /**
     * Set retrocommissionMFFinSaison
     *
     * @param string $retrocommissionMFFinSaison
     *
     * @return Fournisseur
     */
    public function setRetrocommissionMFFinSaison($retrocommissionMFFinSaison)
    {
        $this->retrocommissionMFFinSaison = $retrocommissionMFFinSaison;

        return $this;
    }

    /**
     * Get conditionAnnulation
     *
     * @return integer
     */
    public function getConditionAnnulation()
    {
        return $this->conditionAnnulation;
    }

    /**
     * Set conditionAnnulation
     *
     * @param integer $conditionAnnulation
     *
     * @return Fournisseur
     */
    public function setConditionAnnulation($conditionAnnulation)
    {
        $this->conditionAnnulation = $conditionAnnulation;

        return $this;
    }

    /**
     * Get relocationAnnulation
     *
     * @return integer
     */
    public function getRelocationAnnulation()
    {
        return $this->relocationAnnulation;
    }

    /**
     * Set relocationAnnulation
     *
     * @param integer $relocationAnnulation
     *
     * @return Fournisseur
     */
    public function setRelocationAnnulation($relocationAnnulation)
    {
        $this->relocationAnnulation = $relocationAnnulation;

        return $this;
    }

    /**
     * Get delaiPaiementFacture
     *
     * @return integer
     */
    public function getDelaiPaiementFacture()
    {
        return $this->delaiPaiementFacture;
    }

    /**
     * Set delaiPaiementFacture
     *
     * @param integer $delaiPaiementFacture
     *
     * @return Fournisseur
     */
    public function setDelaiPaiementFacture($delaiPaiementFacture)
    {
        $this->delaiPaiementFacture = $delaiPaiementFacture;

        return $this;
    }

    /**
     * Get lieuRetraitForfaitSki
     *
     * @return string
     */
    public function getLieuRetraitForfaitSki()
    {
        return $this->lieuRetraitForfaitSki;
    }

    /**
     * Set lieuRetraitForfaitSki
     *
     * @param string $lieuRetraitForfaitSki
     *
     * @return Fournisseur
     */
    public function setLieuRetraitForfaitSki($lieuRetraitForfaitSki)
    {
        $this->lieuRetraitForfaitSki = $lieuRetraitForfaitSki;

        return $this;
    }

    /**
     * Get commissionForfaitFamille
     *
     * @return string
     */
    public function getCommissionForfaitFamille()
    {
        return $this->commissionForfaitFamille;
    }

    /**
     * Set commissionForfaitFamille
     *
     * @param string $commissionForfaitFamille
     *
     * @return Fournisseur
     */
    public function setCommissionForfaitFamille($commissionForfaitFamille)
    {
        $this->commissionForfaitFamille = $commissionForfaitFamille;

        return $this;
    }

    /**
     * Get commissionForfaitPeriode
     *
     * @return string
     */
    public function getCommissionForfaitPeriode()
    {
        return $this->commissionForfaitPeriode;
    }

    /**
     * Set commissionForfaitPeriode
     *
     * @param string $commissionForfaitPeriode
     *
     * @return Fournisseur
     */
    public function setCommissionForfaitPeriode($commissionForfaitPeriode)
    {
        $this->commissionForfaitPeriode = $commissionForfaitPeriode;

        return $this;
    }

    /**
     * Get commissionSupportMainLibre
     *
     * @return string
     */
    public function getCommissionSupportMainLibre()
    {
        return $this->commissionSupportMainLibre;
    }

    /**
     * Set commissionSupportMainLibre
     *
     * @param string $commissionSupportMainLibre
     *
     * @return Fournisseur
     */
    public function setCommissionSupportMainLibre($commissionSupportMainLibre)
    {
        $this->commissionSupportMainLibre = $commissionSupportMainLibre;

        return $this;
    }

    /**
     * Get blocageVente
     *
     * @return integer
     */
    public function getBlocageVente()
    {
        return $this->blocageVente;
    }

    /**
     * Set blocageVente
     *
     * @param integer $blocageVente
     *
     * @return Fournisseur
     */
    public function setBlocageVente($blocageVente)
    {
        $this->blocageVente = $blocageVente;

        return $this;
    }

    /**
     * Get conditionAnnulationDescription
     *
     * @return ConditionAnnulationDescription
     */
    public function getConditionAnnulationDescription()
    {
        return $this->conditionAnnulationDescription;
    }

    /**
     * Set conditionAnnulationDescription
     *
     * @param ConditionAnnulationDescription $conditionAnnulationDescription
     *
     * @return Fournisseur
     */
    public function setConditionAnnulationDescription(ConditionAnnulationDescription $conditionAnnulationDescription = null)
    {
        $this->conditionAnnulationDescription = $conditionAnnulationDescription;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return integer
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set priorite
     *
     * @param integer $priorite
     *
     * @return Fournisseur
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get prioriteLibelle
     *
     * @return string
     */
    public function getPrioriteLibelle()
    {
        return Priorite::getLibelle($this->priorite);
    }

    /**
     * Add promotionFournisseur
     *
     * @param PromotionFournisseur $promotionFournisseur
     *
     * @return Fournisseur
     */
    public function addPromotionFournisseur(PromotionFournisseur $promotionFournisseur)
    {
        $this->promotionFournisseurs[] = $promotionFournisseur->setFournisseur($this);

        return $this;
    }

    /**
     * Remove promotionFournisseur
     *
     * @param PromotionFournisseur $promotionFournisseur
     */
    public function removePromotionFournisseur(PromotionFournisseur $promotionFournisseur)
    {
        $this->promotionFournisseurs->removeElement($promotionFournisseur);
    }

    /**
     * Get promotionFournisseurs
     *
     * @return Collection
     */
    public function getPromotionFournisseurs()
    {
        return $this->promotionFournisseurs;
    }

    /**
     * Add promotionFournisseurPrestationAnnex
     *
     * @param PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex
     *
     * @return Fournisseur
     */
    public function addPromotionFournisseurPrestationAnnex(PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex)
    {
        $this->promotionFournisseurPrestationAnnexes[] = $promotionFournisseurPrestationAnnex->setFournisseur($this);

        return $this;
    }

    /**
     * Remove promotionFournisseurPrestationAnnex
     *
     * @param PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex
     */
    public function removePromotionFournisseurPrestationAnnex(PromotionFournisseurPrestationAnnexe $promotionFournisseurPrestationAnnex)
    {
        $this->promotionFournisseurPrestationAnnexes->removeElement($promotionFournisseurPrestationAnnex);
    }

    /**
     * Get promotionFournisseurPrestationAnnexes
     *
     * @return Collection
     */
    public function getPromotionFournisseurPrestationAnnexes()
    {
        return $this->promotionFournisseurPrestationAnnexes;
    }

    /**
     * Add prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     *
     * @return Fournisseur
     */
    public function addPrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs[] = $prestationAnnexeFournisseur;

        return $this;
    }

    /**
     * Remove prestationAnnexeFournisseur
     *
     * @param PrestationAnnexeFournisseur $prestationAnnexeFournisseur
     */
    public function removePrestationAnnexeFournisseur(PrestationAnnexeFournisseur $prestationAnnexeFournisseur)
    {
        $this->prestationAnnexeFournisseurs->removeElement($prestationAnnexeFournisseur);
    }

    /**
     * Get prestationAnnexeFournisseurs
     *
     * @return Collection
     */
    public function getPrestationAnnexeFournisseurs()
    {
        return $this->prestationAnnexeFournisseurs;
    }
}
