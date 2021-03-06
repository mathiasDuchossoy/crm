<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

use HiDev\Bundle\CodePromoBundle\Entity\CodePromoPeriodeValidite;
use Mondofute\Bundle\ClientBundle\Entity\Client;

/**
 * CodePromoClient
 */
class CodePromoClient
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var boolean
     */
    private $utilise = false;
    /**
     * @var CodePromo
     */
    private $codePromo;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var CodePromoPeriodeValidite
     */
    private $codePromoPeriodeValidite;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get utilise
     *
     * @return boolean
     */
    public function getUtilise()
    {
        return $this->utilise;
    }

    /**
     * Set utilise
     *
     * @param boolean $utilise
     *
     * @return CodePromoClient
     */
    public function setUtilise($utilise)
    {
        $this->utilise = $utilise;

        return $this;
    }

    /**
     * Get codePromo
     *
     * @return CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param CodePromo $codePromo
     *
     * @return CodePromoClient
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param Client $client
     *
     * @return CodePromoClient
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get codePromoPeriodeValidite
     *
     * @return CodePromoPeriodeValidite
     */
    public function getCodePromoPeriodeValidite()
    {
        return $this->codePromoPeriodeValidite;
    }

    /**
     * Set codePromoPeriodeValidite
     *
     * @param CodePromoPeriodeValidite $codePromoPeriodeValidite
     *
     * @return CodePromoClient
     */
    public function setCodePromoPeriodeValidite(CodePromoPeriodeValidite $codePromoPeriodeValidite = null)
    {
        $this->codePromoPeriodeValidite = $codePromoPeriodeValidite;

        return $this;
    }
}
