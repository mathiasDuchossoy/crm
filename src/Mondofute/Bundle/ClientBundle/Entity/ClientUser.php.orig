<?php

namespace Mondofute\Bundle\ClientBundle\Entity;

use Mondofute\Bundle\CoreBundle\Entity\User;

/**
 * ClientUser
 */
class ClientUser extends User
{
//    /**
//     * @var int
//     */
//    protected $id;
    /**
     * @var \Mondofute\Bundle\ClientBundle\Entity\Client
     */
    private $client;

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
     * Get client
     *
     * @return \Mondofute\Bundle\ClientBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param \Mondofute\Bundle\ClientBundle\Entity\Client $client
     *
     * @return ClientUser
     */
    public function setClient(\Mondofute\Bundle\ClientBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }
}
