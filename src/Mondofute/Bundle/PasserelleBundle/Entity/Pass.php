<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pass
 */
class Pass
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $paramPasserelles;
    /**
     * @var string
     */
    private $classe;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paramPasserelles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Pass
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Add paramPasserelles
     *
     * @param \Mondofute\Bundle\PasserelleBundle\Entity\Passerelle $paramPasserelles
     * @return Pass
     */
    public function addParamPasserelle(\Mondofute\Bundle\PasserelleBundle\Entity\Passerelle $paramPasserelles)
    {
        $this->paramPasserelles[] = $paramPasserelles;

        return $this;
    }

    /**
     * Remove paramPasserelles
     *
     * @param \Mondofute\Bundle\PasserelleBundle\Entity\Passerelle $paramPasserelles
     */
    public function removeParamPasserelle(\Mondofute\Bundle\PasserelleBundle\Entity\Passerelle $paramPasserelles)
    {
        $this->paramPasserelles->removeElement($paramPasserelles);
    }

    /**
     * Get paramPasserelles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParamPasserelles()
    {
        return $this->paramPasserelles;
    }

    /**
     * Get classe
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set classe
     *
     * @param string $classe
     * @return Pass
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }
}
