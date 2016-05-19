<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Mondofute\Bundle\UniteBundle\Entity\Distance;

/**
 * EmplacementHebergement
 */
class EmplacementHebergement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Distance
     */
    private $distance1;
    /**
     * @var Distance
     */
    private $distance2;
    /**
     * @var Emplacement
     */
    private $typeEmplacement;
    /**
     * @var Hebergement
     */
    private $hebergement;

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
     * Get distance1
     *
     * @return Distance
     */
    public function getDistance1()
    {
        return $this->distance1;
    }

    /**
     * Set distance1
     *
     * @param Distance $distance1
     *
     * @return EmplacementHebergement
     */
    public function setDistance1(Distance $distance1 = null)
    {
        $this->distance1 = $distance1;

        return $this;
    }

    /**
     * Get distance2
     *
     * @return Distance
     */
    public function getDistance2()
    {
        return $this->distance2;
    }

    /**
     * Set distance2
     *
     * @param Distance $distance2
     *
     * @return EmplacementHebergement
     */
    public function setDistance2(Distance $distance2 = null)
    {
        $this->distance2 = $distance2;

        return $this;
    }

    /**
     * Get typeEmplacement
     *
     * @return Emplacement
     */
    public function getTypeEmplacement()
    {
        return $this->typeEmplacement;
    }

    /**
     * Set typeEmplacement
     *
     * @param Emplacement $typeEmplacement
     *
     * @return EmplacementHebergement
     */
    public function setTypeEmplacement(Emplacement $typeEmplacement = null)
    {
        $this->typeEmplacement = $typeEmplacement;

        return $this;
    }

    /**
     * Get hebergement
     *
     * @return Hebergement
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set hebergement
     *
     * @param Hebergement $hebergement
     *
     * @return EmplacementHebergement
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement;

        return $this;
    }
}
