<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;

/**
 * InterlocuteurFonctionTraduction
 */
class InterlocuteurFonctionTraduction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $libelle;

    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction
     */
    private $fonction;
    /**
     * @var \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    private $langue;

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
     *
     * @return InterlocuteurFonctionTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set fonction
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction $fonction
     *
     * @return InterlocuteurFonctionTraduction
     */
    public function setFonction(\Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \Mondofute\Bundle\LangueBundle\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set langue
     *
     * @param \Mondofute\Bundle\LangueBundle\Entity\Langue $langue
     *
     * @return InterlocuteurFonctionTraduction
     */
    public function setLangue(\Mondofute\Bundle\LangueBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }
}
