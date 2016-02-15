<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * SnowparkTraduction
 */
class SnowparkTraduction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var Snowpark
     */
    private $snowpark;

    /**
     * @var Langue
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SnowparkTraduction
     */
    public function setDescription($description)
    {
        $this->description = !empty($description) ? $description : '';

        return $this;
    }

    /**
     * Get snowpark
     *
     * @return Snowpark
     */
    public function getSnowpark()
    {
        return $this->snowpark;
    }

    /**
     * Set snowpark
     *
     * @param Snowpark $snowpark
     *
     * @return SnowparkTraduction
     */
    public function setSnowpark(Snowpark $snowpark = null)
    {
        $this->snowpark = $snowpark;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return SnowparkTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
