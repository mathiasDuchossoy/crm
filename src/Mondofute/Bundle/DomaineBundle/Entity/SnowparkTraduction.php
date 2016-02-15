<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * SnowparkTraduction
 */
class SnowparkTraduction
{
    /**
     * @var int
     */
    private $id;


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
     * @var string
     */
    private $description = '';

    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\Snowpark
     */
    private $snowpark;

    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;


    /**
     * Set description
     *
     * @param string $description
     *
     * @return SnowparkTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     * Set snowpark
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Snowpark $snowpark
     *
     * @return SnowparkTraduction
     */
    public function setSnowpark(\Mondofute\Bundle\DomaineBundle\Entity\Snowpark $snowpark = null)
    {
        $this->snowpark = $snowpark;

        return $this;
    }

    /**
     * Get snowpark
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\Snowpark
     */
    public function getSnowpark()
    {
        return $this->snowpark;
    }

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return SnowparkTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

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
}
