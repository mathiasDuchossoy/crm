<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * Service
 */
class Service
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $defaut;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifs;
    /**
     * @var \Mondofute\Bundle\ServiceBundle\Entity\ListeService
     */
    private $listeService;
    /**
     * @var \Mondofute\Bundle\ServiceBundle\Entity\CategorieService
     */
    private $categorieService;
    /**
     * @var \Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService
     */
    private $sousCategorieService;
    /**
     * @var \Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement
     */
    private $type;

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
     * Get defaut
     *
     * @return bool
     */
    public function getDefaut()
    {
        return $this->defaut;
    }

    /**
     * Set defaut
     *
     * @param boolean $defaut
     *
     * @return Service
     */
    public function setDefaut($defaut)
    {
        $this->defaut = $defaut;

        return $this;
    }

    /**
     * Add tarif
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\TarifService $tarif
     *
     * @return Service
     */
    public function addTarif(\Mondofute\Bundle\ServiceBundle\Entity\TarifService $tarif)
    {
        $this->tarifs[] = $tarif;

        return $this;
    }

    /**
     * Remove tarif
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\TarifService $tarif
     */
    public function removeTarif(\Mondofute\Bundle\ServiceBundle\Entity\TarifService $tarif)
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
     * Get listeService
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\ListeService
     */
    public function getListeService()
    {
        return $this->listeService;
    }

    /**
     * Set listeService
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\ListeService $listeService
     *
     * @return Service
     */
    public function setListeService(\Mondofute\Bundle\ServiceBundle\Entity\ListeService $listeService = null)
    {
        $this->listeService = $listeService;

        return $this;
    }

    /**
     * Get categorieService
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\CategorieService
     */
    public function getCategorieService()
    {
        return $this->categorieService;
    }

    /**
     * Set categorieService
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\CategorieService $categorieService
     *
     * @return Service
     */
    public function setCategorieService(\Mondofute\Bundle\ServiceBundle\Entity\CategorieService $categorieService = null
    ) {
        $this->categorieService = $categorieService;

        return $this;
    }

    /**
     * Get sousCategorieService
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService
     */
    public function getSousCategorieService()
    {
        return $this->sousCategorieService;
    }

    /**
     * Set sousCategorieService
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService $sousCategorieService
     *
     * @return Service
     */
    public function setSousCategorieService(
        \Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService $sousCategorieService = null
    ) {
        $this->sousCategorieService = $sousCategorieService;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement $type
     *
     * @return Service
     */
    public function setType(\Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement $type = null)
    {
        $this->type = $type;

        return $this;
    }
}
