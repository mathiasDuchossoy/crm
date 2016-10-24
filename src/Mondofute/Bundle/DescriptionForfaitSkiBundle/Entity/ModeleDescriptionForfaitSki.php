<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


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
     * @var Collection
     */
    private $descriptionForfaitSkis;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->descriptionForfaitSkis = new ArrayCollection();
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add descriptionForfaitSki
     *
     * @param DescriptionForfaitSki $descriptionForfaitSki
     *
     * @return ModeleDescriptionForfaitSki
     */
    public function addDescriptionForfaitSki(DescriptionForfaitSki $descriptionForfaitSki)
    {
        $this->descriptionForfaitSkis[] = $descriptionForfaitSki->setModele($this);

        return $this;
    }

    /**
     * Remove descriptionForfaitSki
     *
     * @param DescriptionForfaitSki $descriptionForfaitSki
     */
    public function removeDescriptionForfaitSki(DescriptionForfaitSki $descriptionForfaitSki)
    {
        $this->descriptionForfaitSkis->removeElement($descriptionForfaitSki);
    }

    public function __clone()
    {
        /** @var DescriptionForfaitSki $descriptionsForfaitSki */
//        $this->id = null;
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
     * @return Collection
     */
    public function getDescriptionForfaitSkis()
    {
        return $this->descriptionForfaitSkis;
    }
}
