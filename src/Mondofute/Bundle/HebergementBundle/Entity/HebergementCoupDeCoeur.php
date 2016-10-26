<?php

namespace Mondofute\Bundle\HebergementBundle\Entity;

use Mondofute\Bundle\CoupDeCoeurBundle\Entity\CoupDeCoeur;

/**
 * HebergementCoupDeCoeur
 */
class HebergementCoupDeCoeur extends CoupDeCoeur
{
    /**
     * @var int
     */
    private $id;
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
     * @return HebergementCoupDeCoeur
     */
    public function setHebergement(Hebergement $hebergement = null)
    {
        $this->hebergement = $hebergement->setCoupDeCoeur($this);

        return $this;
    }
}
