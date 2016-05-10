<?php

namespace Mondofute\Bundle\ClientBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Nucleus\ContactBundle\Entity\Physique;

/**
 * Client
 */
class Client extends Physique
{
//    /**
//     * @var int
//     */
//    private $id;


//    /**
//     * Get id
//     *
//     * @return int
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
    /**
     * @var boolean
     */
    private $vip;
    /**
     * @var \DateTime
     */
    private $dateNaissance;

    /**
     * Get vip
     *
     * @return boolean
     */
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * Set vip
     *
     * @param boolean $vip
     *
     * @return Client
     */
    public function setVip($vip)
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Client
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

}
