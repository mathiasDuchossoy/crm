<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

/**
 * CodePromoApplication
 */
class CodePromoApplication
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $application;
    /**
     * @var CodePromo
     */
    private $codePromo;

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
     * Get application
     *
     * @return int
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set application
     *
     * @param integer $application
     *
     * @return CodePromoApplication
     */
    public function setApplication($application)
    {
        $this->application = $application;

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
     * @return CodePromoApplication
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
