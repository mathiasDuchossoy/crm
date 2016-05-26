<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\FournisseurBundle\Entity\Traits\FournisseurTrait;
use Nucleus\ContactBundle\Entity\Moral;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;

class FournisseurContient
{
    const PRODUIT = 1; // 1
    const FOURNISSEUR = 2; // 10

    public static $libelles = array(
        FournisseurContient::FOURNISSEUR => 'Fournisseurs',
        FournisseurContient::PRODUIT => 'Produits'
    );

    static public function getLibelle($permission)
    {
        return self::$libelles[$permission];
    }

}

/**
 * Fournisseur
 */
class Fournisseur extends Moral
{
    use FournisseurTrait;

//    const PRODUIT = 1; // 1
//    const FOURNISSEUR = 2; // 10
//    /**
//     * @var integer
//     */
//    private $id;

//    /**
//     * @var string
//     */
//    private $enseigne;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $interlocuteurs;

    /**
     * @var FournisseurPasserelle
     */
    private $passerelle;
    /**
     * @var integer
     */
    private $contient;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fournisseurEnfants;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseurParent;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $hebergements;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $remiseClefs;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $receptions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $types;

    /**
     * Fournisseur constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->interlocuteurs = new ArrayCollection();
        $this->remiseClefs = new ArrayCollection();
        $this->receptions = new ArrayCollection();
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
    public function removeInterlocuteur(
        FournisseurInterlocuteur $interlocuteur
    )
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
        $interlocuteurs = $this->getInterlocuteurs();
        $this->interlocuteurs = new ArrayCollection();
        if (count($interlocuteurs) > 0) {
            foreach ($interlocuteurs as $interlocuteur) {
                $cloneInterlocuteur = clone $interlocuteur;
                $this->interlocuteurs->add($cloneInterlocuteur);
                $cloneInterlocuteur->setFournisseur($this);
            }
        }
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

        return $this;
    }

    /**
     * Get interlocuteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterlocuteurs()
    {
        return $this->interlocuteurs;
    }

    /**
     * Get remiseClefs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRemiseClefs()
    {
        return $this->remiseClefs;
    }

    /**
     * Get receptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceptions()
    {
        return $this->receptions;
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
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurEnfant
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
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurEnfant
     */
    public function removeFournisseurEnfant(Fournisseur $fournisseurEnfant)
    {
        $this->fournisseurEnfants->removeElement($fournisseurEnfant);
    }

    /**
     * Get fournisseurEnfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFournisseurEnfants()
    {
        return $this->fournisseurEnfants;
    }

    /**
     * Get fournisseurParent
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseurParent()
    {
        return $this->fournisseurParent;
    }

    /**
     * Set fournisseurParent
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseurParent
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
     * @return \Doctrine\Common\Collections\Collection
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
     * Add type
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $type
     *
     * @return Fournisseur
     */
    public function addType(\Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $type)
    {
        $this->types[] = $type->setFournisseur($this);

        return $this;
    }

    /**
     * Remove type
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $type
     */
    public function removeType(\Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $type)
    {
        $this->types->removeElement($type);
    }

    /**
     * Get types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypes()
    {
        return $this->types;
    }
}
