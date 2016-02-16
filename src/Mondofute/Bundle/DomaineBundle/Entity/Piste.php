<?php

namespace Mondofute\Bundle\DomaineBundle\Entity;

/**
 * Piste
 */
class Piste
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $nombre;

    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\TypePiste
     */
    private $typePiste;

    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    private $domaineCarteIdentite;


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
     * Set nombre
     *
     * @param integer $nombre
     *
     * @return Piste
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set typePiste
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\TypePiste $typePiste
     *
     * @return Piste
     */
    public function setTypePiste(\Mondofute\Bundle\DomaineBundle\Entity\TypePiste $typePiste = null)
    {
        $this->typePiste = $typePiste;

        return $this;
    }

    /**
     * Get typePiste
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\TypePiste
     */
    public function getTypePiste()
    {
        return $this->typePiste;
    }

    /**
     * Set domaineCarteIdentite
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite
     *
     * @return Piste
     */
    public function setDomaineCarteIdentite(\Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite $domaineCarteIdentite = null)
    {
        $this->domaineCarteIdentite = $domaineCarteIdentite;

        return $this;
    }

    /**
     * Get domaineCarteIdentite
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite
     */
    public function getDomaineCarteIdentite()
    {
        return $this->domaineCarteIdentite;
    }
}
