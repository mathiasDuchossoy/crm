<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * DomaineCarteIdentiteTraduction
 */
class DomaineCarteIdentiteTraduction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $accroche;

    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    private $domaineCarteIdentite;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;
    /**
     * @var string
     */
    private $description = '';

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
     * Get accroche
     *
     * @return string
     */
    public function getAccroche()
    {
        return $this->accroche;
    }

    /**
     * Set accroche
     *
     * @param string $accroche
     *
     * @return DomaineCarteIdentiteTraduction
     */
    public function setAccroche($accroche)
    {
        $this->accroche = $accroche;

        return $this;
    }

    /**
     * Get domaineCarteIdentite
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    public function getDomaineCarteIdentite()
    {
        return $this->domaineCarteIdentite;
    }

    /**
     * Set domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return DomaineCarteIdentiteTraduction
     */
    public function setDomaineCarteIdentite(
        \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite = null
    ) {
        $this->domaineCarteIdentite = $domaineCarteIdentite;

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
     * @return DomaineCarteIdentiteTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

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
     * Set description
     *
     * @param string $description
     *
     * @return DomaineCarteIdentiteTraduction
     */
    public function setDescription($description)
    {
        $this->description = !empty($description) ? $description : '';

        return $this;
    }
}
