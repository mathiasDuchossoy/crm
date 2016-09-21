<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * InterlocuteurFonction
 */
class InterlocuteurFonction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add traduction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonctionTraduction $traduction
     *
     * @return InterlocuteurFonction
     */
    public function addTraduction(\Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonctionTraduction $traduction)
    {
        $this->traductions[] = $traduction->setFonction($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonctionTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonctionTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }
}
