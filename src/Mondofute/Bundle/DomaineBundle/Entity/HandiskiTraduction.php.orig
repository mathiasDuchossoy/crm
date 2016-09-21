<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

use Mondofute\Bundle\LangueBundle\Entity\Langue;

/**
 * HandiskiTraduction
 */
class HandiskiTraduction
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
     * @var Handiski
     */
    private $handiski;

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
     * @return HandiskiTraduction
     */
    public function setDescription($description)
    {
        $this->description = !empty($description) ? $description : '';

        return $this;
    }

    /**
     * Get handiski
     *
     * @return Handiski
     */
    public function getHandiski()
    {
        return $this->handiski;
    }

    /**
     * Set handiski
     *
     * @param Handiski $handiski
     *
     * @return HandiskiTraduction
     */
    public function setHandiski(Handiski $handiski = null)
    {
        $this->handiski = $handiski;

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
     * @return HandiskiTraduction
     */
    public function setLangue(Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
