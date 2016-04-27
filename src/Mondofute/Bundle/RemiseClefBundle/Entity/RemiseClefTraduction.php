<?php

namespace Mondofute\Bundle\RemiseClefBundle\Entity;

/**
 * RemiseClefTraduction
 */
class RemiseClefTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $lieuxRemiseClef;
    /**
     * @var \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef
     */
    private $remiseClef;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

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
     * Get lieuxRemiseClef
     *
     * @return string
     */
    public function getLieuxRemiseClef()
    {
        return $this->lieuxRemiseClef;
    }

    /**
     * Set lieuxRemiseClef
     *
     * @param string $lieuxRemiseClef
     *
     * @return RemiseClefTraduction
     */
    public function setLieuxRemiseClef($lieuxRemiseClef)
    {
        $this->lieuxRemiseClef = $lieuxRemiseClef;

        return $this;
    }

    /**
     * Get remiseClef
     *
     * @return \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef
     */
    public function getRemiseClef()
    {
        return $this->remiseClef;
    }

    /**
     * Set remiseClef
     *
     * @param \Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef $remiseClef
     *
     * @return RemiseClefTraduction
     */
    public function setRemiseClef(\Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef $remiseClef = null)
    {
        $this->remiseClef = $remiseClef;

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

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return RemiseClefTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
