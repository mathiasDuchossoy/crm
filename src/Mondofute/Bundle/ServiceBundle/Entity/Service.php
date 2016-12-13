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
     * @var TypeService
     */
    private $type;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $serviceHebergements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->serviceHebergements = new \Doctrine\Common\Collections\ArrayCollection();
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
        $this->tarifs[] = $tarif->setService($this);

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
     * @return TypeService
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param TypeService $type
     *
     * @return Service
     */
    public function setType(TypeService $type = null)
    {
        $this->type = $type;

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return ' ';
    }


    /**
     * Add serviceHebergement
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement $serviceHebergement
     *
     * @return Service
     */
    public function addServiceHebergement(\Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement $serviceHebergement)
    {
        $this->serviceHebergements[] = $serviceHebergement->setService($this);

        return $this;
    }

    /**
     * Remove serviceHebergement
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement $serviceHebergement
     */
    public function removeServiceHebergement(
        \Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement $serviceHebergement
    ) {
        $this->serviceHebergements->removeElement($serviceHebergement);
    }

    /**
     * Get serviceHebergements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceHebergements()
    {
        return $this->serviceHebergements;
    }
}
