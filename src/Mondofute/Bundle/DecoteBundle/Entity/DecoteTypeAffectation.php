<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

/**
 * DecoteTypeAffectation
 */
class DecoteTypeAffectation
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var integer
     */
    private $typeAffectation;
    /**
     * @var Decote
     */
    private $decote;

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
     * @return DecoteTypeAffectation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get typeAffectation
     *
     * @return integer
     */
    public function getTypeAffectation()
    {
        return $this->typeAffectation;
    }

    /**
     * Set typeAffectation
     *
     * @param integer $typeAffectation
     *
     * @return DecoteTypeAffectation
     */
    public function setTypeAffectation($typeAffectation)
    {
        $this->typeAffectation = $typeAffectation;

        return $this;
    }

    /**
     * Get decote
     *
     * @return Decote
     */
    public function getDecote()
    {
        return $this->decote;
    }

    /**
     * Set decote
     *
     * @param Decote $decote
     *
     * @return DecoteTypeAffectation
     */
    public function setDecote(Decote $decote = null)
    {
        $this->decote = $decote;

        return $this;
    }
}
