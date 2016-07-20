<?php

namespace Mondofute\Bundle\ServiceBundle\Entity;

/**
 * TypeServiceTraduction
 */
class TypeServiceTraduction
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
     * @var \Mondofute\Bundle\ServiceBundle\Entity\TypeService
     */
    private $typeService;
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
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return TypeServiceTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get typeService
     *
     * @return \Mondofute\Bundle\ServiceBundle\Entity\TypeService
     */
    public function getTypeService()
    {
        return $this->typeService;
    }

    /**
     * Set typeService
     *
     * @param \Mondofute\Bundle\ServiceBundle\Entity\TypeService $typeService
     *
     * @return TypeServiceTraduction
     */
    public function setTypeService(\Mondofute\Bundle\ServiceBundle\Entity\TypeService $typeService = null)
    {
        $this->typeService = $typeService;

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
     * @return TypeServiceTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
