<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

/**
 * CodePasserelle
 */
class CodePasserelle
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
     * @var \Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle
     */
    private $saisonCodePasserelle;

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
     * @return CodePasserelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get saisonCodePasserelle
     *
     * @return \Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle
     */
    public function getSaisonCodePasserelle()
    {
        return $this->saisonCodePasserelle;
    }

    /**
     * Set saisonCodePasserelle
     *
     * @param \Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle $saisonCodePasserelle
     *
     * @return CodePasserelle
     */
    public function setSaisonCodePasserelle(\Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle $saisonCodePasserelle = null)
    {
        $this->saisonCodePasserelle = $saisonCodePasserelle;

        return $this;
    }
}
