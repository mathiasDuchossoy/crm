<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * TypeFournisseur
 */
class TypeFournisseur
{

    const Hebergement = 1; // 1
    const RemonteesMecaniques = 2; // 10
    const LocationMaterielDeSki = 3; // 10
    const ESF = 4; // 10
    const Assurance = 5; // 10

    public static $libelles = array(
        TypeFournisseur::Hebergement => 'Hébergement',
        TypeFournisseur::RemonteesMecaniques => 'Remontées Mécaniques',
        TypeFournisseur::LocationMaterielDeSki => 'Location Matériel de Ski',
        TypeFournisseur::ESF => 'ESF',
        TypeFournisseur::Assurance => 'Assurance'
    );
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $typeFournisseur;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fournisseurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    static public function getLibelle($permission)
    {
        return self::$libelles[$permission];
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
     * Get typeFournisseur
     *
     * @return integer
     */
    public function getTypeFournisseur()
    {
        return $this->typeFournisseur;
    }

    /**
     * Set typeFournisseur
     *
     * @param integer $typeFournisseur
     *
     * @return TypeFournisseur
     */
    public function setTypeFournisseur($typeFournisseur)
    {
        $this->typeFournisseur = $typeFournisseur;

        return $this;
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
     * @return TypeFournisseur
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
}
