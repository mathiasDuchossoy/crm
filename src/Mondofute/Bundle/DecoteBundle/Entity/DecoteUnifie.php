<?php

namespace Mondofute\Bundle\DecoteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * DecoteUnifie
 */
class DecoteUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $decotes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->decotes = new ArrayCollection();
    }

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
     * @return DecoteUnifie
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Remove decote
     *
     * @param Decote $decote
     */
    public function removeDecote(Decote $decote)
    {
        $this->decotes->removeElement($decote);
    }

    /**
     * Get decotes
     *
     * @return Collection
     */
    public function getDecotes()
    {
        return $this->decotes;
    }

    /**
     * @param $decotes
     * @return $this
     */
    public function setDecotes($decotes)
    {
        $this->getDecotes()->clear();

        foreach ($decotes as $decote) {
            $this->addDecote($decote);
        }
        return $this;
    }

    /**
     * Add decote
     *
     * @param Decote $decote
     *
     * @return DecoteUnifie
     */
    public function addDecote(Decote $decote)
    {
        $this->decotes[] = $decote->setDecoteUnifie($this);

        return $this;
    }
}
