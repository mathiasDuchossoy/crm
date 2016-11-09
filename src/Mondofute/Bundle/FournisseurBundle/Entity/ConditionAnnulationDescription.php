<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * ConditionAnnulationDescription
 */
class ConditionAnnulationDescription
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $description;
    /**
     * @var boolean
     */
    private $standard = false;

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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return ConditionAnnulationDescription
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get standard
     *
     * @return boolean
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set standard
     *
     * @param boolean $standard
     *
     * @return ConditionAnnulationDescription
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }
}
