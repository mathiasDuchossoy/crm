<<<<<<< cdf75f69cc351801b699a7cfaa8af214860571ec
<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * FournisseurPrestationAnnexe
 */
class FournisseurPrestationAnnexe
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $type;
    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite
     */
    private $capacite;
    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour
     */
    private $dureeSejour;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifs;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe
     */
    private $prestationAnnexe;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get capacite
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite
     */
    public function getCapacite()
    {
        return $this->capacite;
    }

    /**
     * Set capacite
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite $capacite
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setCapacite(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite $capacite = null)
    {
        $this->capacite = $capacite;

        return $this;
    }

    /**
     * Get dureeSejour
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour
     */
    public function getDureeSejour()
    {
        return $this->dureeSejour;
    }

    /**
     * Set dureeSejour
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour $dureeSejour
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setDureeSejour(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour $dureeSejour = null)
    {
        $this->dureeSejour = $dureeSejour;

        return $this;
    }

    /**
     * Add tarif
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addTarif(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif)
    {
        $this->tarifs[] = $tarif->setPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove tarif
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif
     */
    public function removeTarif(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif)
    {
        $this->tarifs->removeElement($tarif);
    }

    /**
     * Get tarifs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarifs()
    {
        return $this->tarifs;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Add traduction
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeTraduction $traduction
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addTraduction(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeTraduction $traduction)
    {
        $this->traductions[] = $traduction->setPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * Get prestationAnnexe
     *
     * @return \Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe
     */
    public function getPrestationAnnexe()
    {
        return $this->prestationAnnexe;
    }

    /**
     * Set prestationAnnexe
     *
     * @param \Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnexe
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setPrestationAnnexe(\Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe $prestationAnnexe = null)
    {
        $this->prestationAnnexe = $prestationAnnexe;

        return $this;
    }
}
||||||| merged common ancestors
=======
<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * FournisseurPrestationAnnexe
 */
class FournisseurPrestationAnnexe
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;
    /**
     * @var integer
     */
    private $type;
    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite
     */
    private $capacite;
    /**
     * @var \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour
     */
    private $dureeSejour;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifs;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get capacite
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite
     */
    public function getCapacite()
    {
        return $this->capacite;
    }

    /**
     * Set capacite
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite $capacite
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setCapacite(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeCapacite $capacite = null)
    {
        $this->capacite = $capacite;

        return $this;
    }

    /**
     * Get dureeSejour
     *
     * @return \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour
     */
    public function getDureeSejour()
    {
        return $this->dureeSejour;
    }

    /**
     * Set dureeSejour
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour $dureeSejour
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setDureeSejour(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeDureeSejour $dureeSejour = null)
    {
        $this->dureeSejour = $dureeSejour;

        return $this;
    }

    /**
     * Add tarif
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif
     *
     * @return FournisseurPrestationAnnexe
     */
    public function addTarif(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif)
    {
        $this->tarifs[] = $tarif->setPrestationAnnexe($this);

        return $this;
    }

    /**
     * Remove tarif
     *
     * @param \Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif
     */
    public function removeTarif(\Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif $tarif)
    {
        $this->tarifs->removeElement($tarif);
    }

    /**
     * Get tarifs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarifs()
    {
        return $this->tarifs;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return FournisseurPrestationAnnexe
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
}
>>>>>>> creation des entités de fournisseurPrestationAnnexe
