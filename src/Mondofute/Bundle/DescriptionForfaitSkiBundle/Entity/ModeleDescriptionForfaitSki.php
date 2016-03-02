<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ModeleDescriptionForfaitSki
 */
class ModeleDescriptionForfaitSki
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $descriptionForfaitSkis;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->descriptionForfaitSkis = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Add descriptionForfaitSki
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki $descriptionForfaitSki
     *
     * @return ModeleDescriptionForfaitSki
     */
    public function addDescriptionForfaitSki(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki $descriptionForfaitSki)
    {
        $this->descriptionForfaitSkis[] = $descriptionForfaitSki->setModele($this);

        return $this;
    }

    /**
     * Remove descriptionForfaitSki
     *
     * @param \Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki $descriptionForfaitSki
     */
    public function removeDescriptionForfaitSki(\Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki $descriptionForfaitSki)
    {
        $this->descriptionForfaitSkis->removeElement($descriptionForfaitSki);
    }

    public function __clone()
    {
        /** @var DescriptionForfaitSki $descriptionsForfaitSki */
        $this->id = null;
        $descriptionsForfaitSkis = $this->getDescriptionForfaitSkis();
        $this->descriptionForfaitSkis = new ArrayCollection();
        if (count($descriptionsForfaitSkis) > 0) {
            foreach ($descriptionsForfaitSkis as $descriptionsForfaitSki) {
                $cloneDescriptionForfaitSki = clone $descriptionsForfaitSki;
                $this->descriptionForfaitSkis->add($cloneDescriptionForfaitSki);
                $cloneDescriptionForfaitSki->setModele($this);
            }
        }
    }

    /**
     * Get descriptionForfaitSkis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescriptionForfaitSkis()
    {
        return $this->descriptionForfaitSkis;
    }
}
