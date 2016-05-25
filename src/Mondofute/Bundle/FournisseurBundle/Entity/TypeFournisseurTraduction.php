<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * TypeFournisseurTraduction
 */
class TypeFournisseurTraduction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur
     */
    private $typeFournisseur;

    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;


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
     * @return TypeFournisseurTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get typeFournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur
     */
    public function getTypeFournisseur()
    {
        return $this->typeFournisseur;
    }

    /**
     * Set typeFournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $typeFournisseur
     *
     * @return TypeFournisseurTraduction
     */
    public function setTypeFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur $typeFournisseur = null)
    {
        $this->typeFournisseur = $typeFournisseur;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return TypeFournisseurTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
