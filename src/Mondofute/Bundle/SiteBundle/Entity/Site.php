<?php

namespace Mondofute\Bundle\SiteBundle\Entity;

/**
 * Site
 */
class Site
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
     * @var bool
     */
    private $crm;


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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Site
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
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
     * Set crm
     *
     * @param boolean $crm
     *
     * @return Site
     */
    public function setCrm($crm)
    {
        $this->crm = $crm;

        return $this;
    }

    /**
     * Get crm
     *
     * @return bool
     */
    public function getCrm()
    {
        return $this->crm;
    }
}
