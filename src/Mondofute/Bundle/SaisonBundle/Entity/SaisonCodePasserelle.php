<?php

namespace Mondofute\Bundle\SaisonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\PasserelleBundle\Entity\CodePasserelle;

/**
 * SaisonCodePasserelle
 */
class SaisonCodePasserelle
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $codePasserelles;
    /**
     * @var Saison
     */
    private $saison;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->codePasserelles = new ArrayCollection();
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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add codePasserelle
     *
     * @param CodePasserelle $codePasserelle
     *
     * @return SaisonCodePasserelle
     */
    public function addCodePasserelle(CodePasserelle $codePasserelle)
    {
        $this->codePasserelles[] = $codePasserelle->setSaisonCodePasserelle($this);

        return $this;
    }

    /**
     * Remove codePasserelle
     *
     * @param CodePasserelle $codePasserelle
     */
    public function removeCodePasserelle(CodePasserelle $codePasserelle)
    {
        $this->codePasserelles->removeElement($codePasserelle);
    }

    /**
     * Get codePasserelles
     *
     * @return Collection
     */
    public function getCodePasserelles()
    {
        return $this->codePasserelles;
    }

    /**
     * Get saison
     *
     * @return Saison
     */
    public function getSaison()
    {
        return $this->saison;
    }

    /**
     * Set saison
     *
     * @param Saison $saison
     *
     * @return SaisonCodePasserelle
     */
    public function setSaison(Saison $saison = null)
    {
        $this->saison = $saison;

        return $this;
    }
}
