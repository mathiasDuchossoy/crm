<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

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
     * @var \HiDev\Bundle\CodePromoBundle\Entity\CodePromo
     */
    private $codePromo;
    /**
     * @var \Mondofute\Bundle\ClientBundle\Entity\Client
     */
    private $client;
    /**
     * @var \HiDev\Bundle\CodePromoBundle\Entity\CodePromoPeriodeValidite
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
     * @return \HiDev\Bundle\CodePromoBundle\Entity\CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param \HiDev\Bundle\CodePromoBundle\Entity\CodePromo $codePromo
     *
     * @return CodePromoClient
     */
    public function setCodePromo(\HiDev\Bundle\CodePromoBundle\Entity\CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Mondofute\Bundle\ClientBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param \Mondofute\Bundle\ClientBundle\Entity\Client $client
     *
     * @return CodePromoClient
     */
    public function setClient(\Mondofute\Bundle\ClientBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get codePromoPeriodeValidite
     *
     * @return \HiDev\Bundle\CodePromoBundle\Entity\CodePromoPeriodeValidite
     */
    public function getCodePromoPeriodeValidite()
    {
        return $this->codePromoPeriodeValidite;
    }

    /**
     * Set codePromoPeriodeValidite
     *
     * @param \HiDev\Bundle\CodePromoBundle\Entity\CodePromoPeriodeValidite $codePromoPeriodeValidite
     *
     * @return CodePromoClient
     */
    public function setCodePromoPeriodeValidite(\HiDev\Bundle\CodePromoBundle\Entity\CodePromoPeriodeValidite $codePromoPeriodeValidite = null)
    {
        $this->codePromoPeriodeValidite = $codePromoPeriodeValidite;

        return $this;
    }
}
