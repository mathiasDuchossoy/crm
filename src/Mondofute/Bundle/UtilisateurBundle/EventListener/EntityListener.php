<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/05/2017
 * Time: 17:12
 */

namespace Mondofute\Bundle\UtilisateurBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Mondofute\Bundle\CommentaireBundle\Entity\CommentaireClient;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EntityListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage = null)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only act on some "GenericEntity" entity
        if (!$entity instanceof CommentaireClient) {
            return;
        }

        if (null !== $currentUser = $this->getUser()) {
            $entity->setUtilisateurModification($currentUser->getUtilisateur());
        } else {
//            $entity->setCreationId(0);
        }
    }

    public function getUser()
    {
        if (!$this->tokenStorage) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
}