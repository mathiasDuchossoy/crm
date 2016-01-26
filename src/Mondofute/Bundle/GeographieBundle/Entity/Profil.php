<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * Profil
 */
class Profil
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie
     */
    private $profilUnifie;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction
     *
     * @return Profil
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction)
    {
        $this->traductions[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\ProfilTraduction $traduction)
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
     * Get site
     *
     * @return \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param \Mondofute\Bundle\SiteBundle\Entity\Site $site
     *
     * @return Profil
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get profilUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie
     */
    public function getProfilUnifie()
    {
        return $this->profilUnifie;
    }

    /**
     * Set profilUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie $profilUnifie
     *
     * @return Profil
     */
    public function setProfilUnifie(\Mondofute\Bundle\GeographieBundle\Entity\ProfilUnifie $profilUnifie = null)
    {
        $this->profilUnifie = $profilUnifie;

        return $this;
    }
}
