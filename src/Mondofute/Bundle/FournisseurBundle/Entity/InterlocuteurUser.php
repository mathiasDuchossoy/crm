<?php

namespace Mondofute\Bundle\FournisseurBundle\Entity;
use Mondofute\Bundle\CoreBundle\Entity\User;

/**
 * InterlocuteurUser
 */
class InterlocuteurUser extends User
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur
     */
    private $interlocuteur;

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
     * Get interlocuteur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur
     */
    public function getInterlocuteur()
    {
        return $this->interlocuteur;
    }

    /**
     * Set interlocuteur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur $interlocuteur
     *
     * @return InterlocuteurUser
     */
    public function setInterlocuteur(\Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur $interlocuteur = null)
    {
        $this->interlocuteur = $interlocuteur;

        return $this;
    }
}
